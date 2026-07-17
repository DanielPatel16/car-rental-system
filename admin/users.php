<?php
session_start();
include "../includes/db.php";

// ---- Auth guard: only logged-in admins may access this page ----
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$errors  = [];
$success = "";

/*
 * ASSUMPTIONS — please verify against your real schema and adjust if needed:
 *   1. users table has two new columns (see users_module_migration.sql):
 *        status  ENUM('Active','Blocked') DEFAULT 'Active'
 *        phone   VARCHAR(20) NULL
 *   2. bookings table has at least: id, user_id, car_id, status, total_amount, created_at
 *      ("Completed" status is what counts toward Lifetime Spend).
 *   3. cars table has: id, brand, model  (already used elsewhere in the app).
 * If your bookings table uses different column/status names, update the
 * queries below (search for "ADJUST" comments).
 */

// ---------------------------------------------------------------
// Handle Block / Unblock (?block=ID / ?unblock=ID)
// ---------------------------------------------------------------
if (isset($_GET['block']) || isset($_GET['unblock'])) {
    $targetId  = (int) ($_GET['block'] ?? $_GET['unblock']);
    $newStatus = isset($_GET['block']) ? 'Blocked' : 'Active';

    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ? AND role = 'user'");
    $stmt->bind_param("si", $newStatus, $targetId);
    $stmt->execute();
    $stmt->close();

    $redirectQs = $_GET;
    unset($redirectQs['block'], $redirectQs['unblock']);
    header("Location: users.php" . ($redirectQs ? '?' . http_build_query($redirectQs) : ''));
    exit();
}

// ---------------------------------------------------------------
// Handle Add Customer (POST)
// ---------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['action'] ?? '') === 'add_customer') {

    $name     = trim($_POST['user_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        $errors[] = "Name, email and password are required.";
    }
    if ($password !== '' && strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $errors[] = "A user with this email already exists.";
        }
        $check->close();
    }

    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare(
            "INSERT INTO users (user_name, email, phone, password, role, status) VALUES (?,?,?,?,'user','Active')"
        );
        $stmt->bind_param("ssss", $name, $email, $phone, $hashed);

        if ($stmt->execute()) {
            $success = "Customer added successfully.";
        } else {
            $errors[] = "Could not add customer: " . $stmt->error;
        }
        $stmt->close();
    }
}

// ---------------------------------------------------------------
// Handle Edit Profile (POST)
// ---------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST['action'] ?? '') === 'edit_customer') {

    $editId   = (int) ($_POST['user_id'] ?? 0);
    $name     = trim($_POST['user_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? ''); // optional: only set if provided

    if ($editId <= 0) {
        $errors[] = "Invalid customer.";
    }
    if ($name === '' || $email === '') {
        $errors[] = "Name and email are required.";
    }
    if ($password !== '' && strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        // Make sure the email isn't already used by a *different* user
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check->bind_param("si", $email, $editId);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $errors[] = "Another user already uses this email.";
        }
        $check->close();
    }

    if (empty($errors)) {
        if ($password !== '') {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
                "UPDATE users SET user_name = ?, email = ?, phone = ?, password = ? WHERE id = ? AND role = 'user'"
            );
            $stmt->bind_param("ssssi", $name, $email, $phone, $hashed, $editId);
        } else {
            $stmt = $conn->prepare(
                "UPDATE users SET user_name = ?, email = ?, phone = ? WHERE id = ? AND role = 'user'"
            );
            $stmt->bind_param("sssi", $name, $email, $phone, $editId);
        }

        if ($stmt->execute()) {
            $success = "Customer profile updated successfully.";
        } else {
            $errors[] = "Could not update customer: " . $stmt->error;
        }
        $stmt->close();
    }
}

// ---------------------------------------------------------------
// Shared filter-building (used by both the listing query and CSV export)
// ---------------------------------------------------------------
$tab    = $_GET['tab'] ?? 'all';         // all | active | blocked | top
$search = trim($_GET['q'] ?? '');
$sort   = $_GET['sort'] ?? 'newest';     // newest | oldest | az | most_bookings

$where  = ["u.role = 'user'"];
$params = [];
$types  = "";

if ($tab === 'active') {
    $where[] = "u.status = 'Active'";
} elseif ($tab === 'blocked') {
    $where[] = "u.status = 'Blocked'";
}

if ($search !== '') {
    $like = "%$search%";
    $where[]  = "(u.user_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types   .= "sss";
}

$whereSql = "WHERE " . implode(" AND ", $where);
$having   = ($tab === 'top') ? "HAVING booking_count > 0" : "";

$orderBy = "u.created_at DESC";
if ($sort === 'oldest')        $orderBy = "u.created_at ASC";
if ($sort === 'az')            $orderBy = "u.user_name ASC";
if ($sort === 'most_bookings') $orderBy = "booking_count DESC";
if ($tab === 'top')            $orderBy = "lifetime_spend DESC"; // Top Spenders tab always sorts by spend

// ---------------------------------------------------------------
// Export CSV (?export=csv) — honours the current filters, no pagination
// ---------------------------------------------------------------
if (isset($_GET['export']) && $_GET['export'] === 'csv') {

    $sql = "SELECT u.id, u.user_name, u.email, u.phone, u.status, u.created_at,
                   COUNT(b.id) AS booking_count,
                   COALESCE(SUM(CASE WHEN b.booking_status = 'Completed' THEN b.total_amount ELSE 0 END), 0) AS lifetime_spend
            FROM users u
            LEFT JOIN bookings b ON b.user_id = u.id
            $whereSql
            GROUP BY u.id
            $having
            ORDER BY $orderBy";
    $stmt = $conn->prepare($sql);
    if ($types) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="customers.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'Name', 'Email', 'Phone', 'Status', 'Joined', 'Bookings', 'Lifetime Spend']);
    foreach ($rows as $r) {
        fputcsv($out, [
            'DE-' . str_pad($r['id'], 4, '0', STR_PAD_LEFT),
            $r['user_name'], $r['email'], $r['phone'], $r['status'],
            date('M d, Y', strtotime($r['created_at'])),
            $r['booking_count'], number_format($r['lifetime_spend'], 2),
        ]);
    }
    fclose($out);
    $conn->close();
    exit();
}

// ---------------------------------------------------------------
// Pagination
// ---------------------------------------------------------------
$perPage = 10;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

$countSql = "SELECT COUNT(*) AS total FROM (
                SELECT u.id, COUNT(b.id) AS booking_count
                FROM users u
                LEFT JOIN bookings b ON b.user_id = u.id
                $whereSql
                GROUP BY u.id
                $having
             ) t";
$countStmt = $conn->prepare($countSql);
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows  = (int) $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = max(1, (int) ceil($totalRows / $perPage));
$countStmt->close();

$sql = "SELECT u.*,
               COUNT(b.id) AS booking_count,
               COALESCE(SUM(CASE WHEN b.booking_status = 'Completed' THEN b.total_amount ELSE 0 END), 0) AS lifetime_spend
        FROM users u
        LEFT JOIN bookings b ON b.user_id = u.id
        $whereSql
        GROUP BY u.id
        $having
        ORDER BY $orderBy
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$allTypes  = $types . "ii";
$allParams = array_merge($params, [$perPage, $offset]);
$stmt->bind_param($allTypes, ...$allParams);
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------------------------------------------------------------
// Recent bookings for the users on this page (for the details panel)
// ---------------------------------------------------------------
$bookingsByUser = [];
if (!empty($users)) {
    $ids = array_column($users, 'id');
    $placeholders = implode(",", array_fill(0, count($ids), "?"));
    $bTypes = str_repeat("i", count($ids));

    $bStmt = $conn->prepare(
        "SELECT b.user_id, b.booking_status, b.total_amount, b.created_at,
                b.pickup_date, b.return_date,
                c.brand, c.model
         FROM bookings b
         LEFT JOIN cars c ON c.id = b.car_id
         WHERE b.user_id IN ($placeholders)
         ORDER BY b.created_at DESC"
    );
    $bStmt->bind_param($bTypes, ...$ids);
    $bStmt->execute();
    $bRes = $bStmt->get_result();
    while ($row = $bRes->fetch_assoc()) {
        $uid = $row['user_id'];
        if (!isset($bookingsByUser[$uid])) $bookingsByUser[$uid] = [];
        $bookingsByUser[$uid][] = $row; // keep full history; UI decides how much to show
    }
    $bStmt->close();
}

// ---------------------------------------------------------------
// Stat cards
// ---------------------------------------------------------------
$totalCustomers = (int) $conn->query("SELECT COUNT(*) c FROM users WHERE role = 'user'")->fetch_assoc()['c'];
$activeCount    = (int) $conn->query("SELECT COUNT(*) c FROM users WHERE role = 'user' AND status = 'Active'")->fetch_assoc()['c'];
$blockedCount   = (int) $conn->query("SELECT COUNT(*) c FROM users WHERE role = 'user' AND status = 'Blocked'")->fetch_assoc()['c'];

$newThisMonth = (int) $conn->query(
    "SELECT COUNT(*) c FROM users WHERE role = 'user' AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')"
)->fetch_assoc()['c'];
$newLastMonth = (int) $conn->query(
    "SELECT COUNT(*) c FROM users WHERE role = 'user'
     AND created_at >= DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01')
     AND created_at <  DATE_FORMAT(NOW(), '%Y-%m-01')"
)->fetch_assoc()['c'];

$monthGrowth = $newLastMonth > 0
    ? round((($newThisMonth - $newLastMonth) / $newLastMonth) * 100, 1)
    : ($newThisMonth > 0 ? 100 : 0);
$activePercent = $totalCustomers > 0 ? round(($activeCount / $totalCustomers) * 100) : 0;

function qs($extra) {
    $qs = $_GET;
    foreach ($extra as $k => $v) {
        if ($v === null) unset($qs[$k]); else $qs[$k] = $v;
    }
    return 'users.php?' . http_build_query($qs);
}

function initials($name) {
    $parts = preg_split('/\s+/', trim($name));
    $ini = '';
    foreach (array_slice($parts, 0, 2) as $p) $ini .= mb_strtoupper(mb_substr($p, 0, 1));
    return $ini ?: '?';
}
?>
<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>DriveEase Admin - Customer Management</title>
</head>
<body class="bg-background text-on-surface font-body-md overflow-hidden">
<!-- Shell Container -->
<div class="flex h-screen w-full">
<!-- SideNavBar -->
<?php include "sidebar.php"; ?>
<!-- Main Content Area -->
<main class="ml-64 flex-1 flex flex-col h-full bg-background relative overflow-hidden">
<!-- TopNavBar -->
<header class="h-16 flex justify-between items-center px-xl w-full bg-surface border-b border-outline-variant sticky top-0 z-20">
<div class="flex items-center gap-xl w-1/2">
<form method="GET" class="relative w-full max-w-md group">
<?php foreach (['tab','sort'] as $k) if (isset($_GET[$k])) echo '<input type="hidden" name="' . $k . '" value="' . htmlspecialchars($_GET[$k]) . '">'; ?>
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-secondary">search</span>
<input class="w-full pl-10 pr-md py-sm bg-surface-container-lowest border border-outline-variant rounded-lg font-body-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all" placeholder="Search customers by name, email or phone..." type="text" name="q" value="<?php echo htmlspecialchars($search); ?>"/>
</form>
</div>
<div class="flex items-center gap-lg">
<button class="p-base text-secondary hover:text-primary transition-colors active:scale-95">
<span class="material-symbols-outlined">notifications</span>
</button>
<button class="p-base text-secondary hover:text-primary transition-colors active:scale-95">
<span class="material-symbols-outlined">help_outline</span>
</button>
<div class="w-px h-6 bg-outline-variant mx-sm"></div>
<div class="flex items-center gap-sm">
<span class="font-label-md text-on-surface"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></span>
<div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-on-primary-fixed font-bold text-xs">
                            <?php echo htmlspecialchars(initials($_SESSION['user_name'] ?? 'Admin')); ?>
                        </div>
</div>
</div>
</header>
<!-- Dashboard Content -->
<div class="flex-1 overflow-y-auto p-xl custom-scrollbar">

<?php if ($success): ?>
<div class="mb-lg p-md bg-tertiary-fixed/40 border border-tertiary text-on-tertiary-fixed-variant rounded-lg font-body-md text-body-md"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>
<?php if ($errors): ?>
<div class="mb-lg p-md bg-error-container border border-error text-on-error-container rounded-lg font-body-md text-body-md">
<?php foreach ($errors as $e) echo "<p>" . htmlspecialchars($e) . "</p>"; ?>
</div>
<?php endif; ?>

<!-- Page Title & Quick Actions -->
<div class="flex justify-between items-end mb-xl">
<div>
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs">Customer Directory</h2>
<p class="font-body-md text-secondary">Manage registered users, view rental history, and adjust account statuses.</p>
</div>
<div class="flex gap-md">
<button type="button" onclick="window.location='<?php echo qs(['export' => 'csv']); ?>'" class="flex items-center gap-sm px-lg py-md border border-outline bg-surface rounded-lg font-label-md text-secondary hover:bg-surface-container-high transition-colors">
<span class="material-symbols-outlined">download</span>
                            Export CSV
                        </button>
<button type="button" onclick="openAddCustomerModal()" class="flex items-center gap-sm px-lg py-md bg-primary text-on-primary rounded-lg font-label-md shadow-sm hover:opacity-90 transition-all">
<span class="material-symbols-outlined">person_add</span>
                            Add Customer
                        </button>
</div>
</div>
<!-- Metrics Bento Grid Section -->
<div class="grid grid-cols-4 gap-lg mb-xl">
<div class="bg-surface-container-lowest p-lg rounded-xl border border-outline-variant shadow-sm flex flex-col gap-sm">
<div class="flex items-center justify-between">
<span class="font-label-md text-secondary uppercase tracking-wider">Total Customers</span>
<span class="material-symbols-outlined text-primary">groups</span>
</div>
<div class="flex items-baseline gap-sm">
<span class="font-headline-lg text-headline-lg"><?php echo number_format($totalCustomers); ?></span>
<span class="font-label-sm <?php echo $monthGrowth >= 0 ? 'text-tertiary-container' : 'text-error'; ?> flex items-center"><?php echo ($monthGrowth >= 0 ? '+' : '') . $monthGrowth; ?>% <span class="material-symbols-outlined text-xs"><?php echo $monthGrowth >= 0 ? 'trending_up' : 'trending_down'; ?></span></span>
</div>
</div>
<div class="bg-surface-container-lowest p-lg rounded-xl border border-outline-variant shadow-sm flex flex-col gap-sm">
<div class="flex items-center justify-between">
<span class="font-label-md text-secondary uppercase tracking-wider">Active Users</span>
<span class="material-symbols-outlined text-tertiary-fixed-dim">verified_user</span>
</div>
<div class="flex items-baseline gap-sm">
<span class="font-headline-lg text-headline-lg"><?php echo number_format($activeCount); ?></span>
<span class="font-label-sm text-secondary"><?php echo $activePercent; ?>% of total</span>
</div>
</div>
<div class="bg-surface-container-lowest p-lg rounded-xl border border-outline-variant shadow-sm flex flex-col gap-sm">
<div class="flex items-center justify-between">
<span class="font-label-md text-secondary uppercase tracking-wider">New This Month</span>
<span class="material-symbols-outlined text-on-primary-fixed-variant">person_add_alt</span>
</div>
<div class="flex items-baseline gap-sm">
<span class="font-headline-lg text-headline-lg"><?php echo number_format($newThisMonth); ?></span>
<span class="font-label-sm <?php echo $monthGrowth >= 0 ? 'text-tertiary-container' : 'text-error'; ?> flex items-center"><?php echo ($monthGrowth >= 0 ? '+' : '') . $monthGrowth; ?>% <span class="material-symbols-outlined text-xs"><?php echo $monthGrowth >= 0 ? 'trending_up' : 'trending_down'; ?></span></span>
</div>
</div>
<div class="bg-surface-container-lowest p-lg rounded-xl border border-outline-variant shadow-sm flex flex-col gap-sm">
<div class="flex items-center justify-between">
<span class="font-label-md text-secondary uppercase tracking-wider">Blocked Accounts</span>
<span class="material-symbols-outlined text-error">block</span>
</div>
<div class="flex items-baseline gap-sm">
<span class="font-headline-lg text-headline-lg"><?php echo number_format($blockedCount); ?></span>
<span class="font-label-sm text-secondary">Flagged for review</span>
</div>
</div>
</div>
<!-- Filters Bar -->
<div class="flex items-center justify-between bg-surface-container-low p-md rounded-lg mb-lg">
<div class="flex items-center gap-md">
<a href="<?php echo qs(['tab' => null, 'page' => null]); ?>" class="px-md py-sm <?php echo $tab === 'all' ? 'bg-surface-container-highest text-primary border border-primary-container' : 'text-secondary hover:text-on-surface'; ?> font-label-md rounded-md transition-colors">All Users</a>
<a href="<?php echo qs(['tab' => 'active', 'page' => null]); ?>" class="px-md py-sm <?php echo $tab === 'active' ? 'bg-surface-container-highest text-primary border border-primary-container' : 'text-secondary hover:text-on-surface'; ?> font-label-md rounded-md transition-colors">Active</a>
<a href="<?php echo qs(['tab' => 'blocked', 'page' => null]); ?>" class="px-md py-sm <?php echo $tab === 'blocked' ? 'bg-surface-container-highest text-primary border border-primary-container' : 'text-secondary hover:text-on-surface'; ?> font-label-md rounded-md transition-colors">Blocked</a>
<a href="<?php echo qs(['tab' => 'top', 'page' => null]); ?>" class="px-md py-sm <?php echo $tab === 'top' ? 'bg-surface-container-highest text-primary border border-primary-container' : 'text-secondary hover:text-on-surface'; ?> font-label-md rounded-md transition-colors">Top Spenders</a>
</div>
<form method="GET" class="flex items-center gap-sm">
<?php foreach (['tab','q'] as $k) if (isset($_GET[$k])) echo '<input type="hidden" name="' . $k . '" value="' . htmlspecialchars($_GET[$k]) . '">'; ?>
<span class="font-label-sm text-secondary">Sort by:</span>
<select name="sort" onchange="this.form.submit()" class="bg-transparent border-none font-label-md text-on-surface focus:ring-0 cursor-pointer">
<option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Joined Date (Newest)</option>
<option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Joined Date (Oldest)</option>
<option value="az" <?php echo $sort === 'az' ? 'selected' : ''; ?>>Alphabetical A-Z</option>
<option value="most_bookings" <?php echo $sort === 'most_bookings' ? 'selected' : ''; ?>>Most Bookings</option>
</select>
</form>
</div>
<!-- Customer Table Container -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container border-b border-outline-variant">
<th class="p-lg font-label-md text-on-surface-variant uppercase text-[11px] tracking-widest">User</th>
<th class="p-lg font-label-md text-on-surface-variant uppercase text-[11px] tracking-widest">Contact Information</th>
<th class="p-lg font-label-md text-on-surface-variant uppercase text-[11px] tracking-widest">Joined Date</th>
<th class="p-lg font-label-md text-on-surface-variant uppercase text-[11px] tracking-widest">Bookings</th>
<th class="p-lg font-label-md text-on-surface-variant uppercase text-[11px] tracking-widest">Status</th>
<th class="p-lg font-label-md text-on-surface-variant uppercase text-[11px] tracking-widest text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant">
<?php if (empty($users)): ?>
<tr>
<td colspan="6" class="p-2xl text-center text-secondary font-body-md text-body-md">No customers found.</td>
</tr>
<?php endif; ?>
<?php foreach ($users as $u):
    $isBlocked = $u['status'] === 'Blocked';
    $panelData = [
        'id' => $u['id'],
        'name' => $u['user_name'],
        'email' => $u['email'],
        'phone' => $u['phone'] ?? '',
        'joined' => date('M d, Y', strtotime($u['created_at'])),
        'status' => $u['status'],
        'spend' => number_format((float) $u['lifetime_spend'], 2),
        'bookings' => array_map(function ($b) {
            return [
                'car' => trim(($b['brand'] ?? '') . ' ' . ($b['model'] ?? '')) ?: 'Vehicle',
                'range' => date('M d', strtotime($b['pickup_date'])) . ' - ' . date('M d', strtotime($b['return_date'])),
                'status' => $b['booking_status'],
                'amount' => number_format((float) $b['total_amount'], 2),
                'booked_on' => date('M d, Y', strtotime($b['created_at'])),
            ];
        }, $bookingsByUser[$u['id']] ?? []),
    ];
?>
<!-- User Row -->
<tr class="hover:bg-surface-container-low transition-colors group">
<td class="p-lg">
<div class="flex items-center gap-md">
<div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center text-on-secondary-container font-bold <?php echo $isBlocked ? 'grayscale opacity-80' : ''; ?>"><?php echo htmlspecialchars(initials($u['user_name'])); ?></div>
<div>
<p class="font-label-md text-on-surface"><?php echo htmlspecialchars($u['user_name']); ?></p>
<p class="font-label-sm text-secondary">ID: DE-<?php echo str_pad($u['id'], 4, '0', STR_PAD_LEFT); ?></p>
</div>
</div>
</td>
<td class="p-lg">
<p class="font-body-sm text-on-surface"><?php echo htmlspecialchars($u['email']); ?></p>
<p class="font-body-sm text-secondary"><?php echo htmlspecialchars($u['phone'] ?: '—'); ?></p>
</td>
<td class="p-lg">
<p class="font-body-sm text-on-surface"><?php echo date('M d, Y', strtotime($u['created_at'])); ?></p>
<p class="font-label-sm text-secondary"><?php echo date('h:i A', strtotime($u['created_at'])); ?></p>
</td>
<td class="p-lg">
<div class="flex items-center gap-xs">
<span class="font-label-md text-on-surface"><?php echo (int) $u['booking_count']; ?></span>
<?php if ($u['booking_count'] > 0): ?><span class="material-symbols-outlined text-sm text-tertiary-fixed-dim" style="font-variation-settings: 'FILL' 1;">star</span><?php endif; ?>
</div>
</td>
<td class="p-lg">
<?php if (!$isBlocked): ?>
<span class="px-md py-xs rounded-full bg-tertiary-fixed/20 text-on-tertiary-fixed-variant font-label-sm inline-flex items-center gap-xs">
<span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-fixed-variant"></span>
                                        Active
                                    </span>
<?php else: ?>
<span class="px-md py-xs rounded-full bg-error-container text-on-error-container font-label-sm inline-flex items-center gap-xs">
<span class="w-1.5 h-1.5 rounded-full bg-on-error-container"></span>
                                        Blocked
                                    </span>
<?php endif; ?>
</td>
<td class="p-lg text-right">
<div class="flex justify-end gap-xs">
<button type="button" onclick='openDetails(<?php echo json_encode($panelData, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' class="p-sm text-secondary hover:text-primary transition-colors rounded-md hover:bg-surface-container-high" title="View Details">
<span class="material-symbols-outlined">visibility</span>
</button>
<?php if (!$isBlocked): ?>
<a href="<?php echo qs(['block' => $u['id']]); ?>" onclick="return confirm('Block this customer? They will be prevented from logging in.')" class="p-sm text-secondary hover:text-error transition-colors rounded-md hover:bg-error-container" title="Block User">
<span class="material-symbols-outlined">block</span>
</a>
<?php else: ?>
<a href="<?php echo qs(['unblock' => $u['id']]); ?>" class="p-sm text-tertiary hover:text-primary-fixed-dim transition-colors rounded-md hover:bg-surface-container-high" title="Unblock User">
<span class="material-symbols-outlined">check_circle</span>
</a>
<?php endif; ?>
<button class="p-sm text-secondary hover:text-on-surface transition-colors rounded-md hover:bg-surface-container-high">
<span class="material-symbols-outlined">more_vert</span>
</button>
</div>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<!-- Pagination -->
<div class="flex items-center justify-between mt-xl pb-xl">
<p class="font-body-sm text-secondary">Showing <span class="font-bold text-on-surface"><?php echo $totalRows ? ($offset + 1) : 0; ?>-<?php echo min($offset + $perPage, $totalRows); ?></span> of <span class="font-bold text-on-surface"><?php echo number_format($totalRows); ?></span> customers</p>
<div class="flex items-center gap-sm">
<a href="<?php echo qs(['page' => max(1, $page - 1)]); ?>" class="p-md border border-outline-variant rounded-lg text-secondary hover:bg-surface-container-high transition-colors <?php echo $page <= 1 ? 'pointer-events-none opacity-30' : ''; ?>">
<span class="material-symbols-outlined">chevron_left</span>
</a>
<?php
$start = max(1, $page - 2);
$end   = min($totalPages, $page + 2);
if ($start > 1) echo '<button class="w-10 h-10 flex items-center justify-center text-secondary hover:bg-surface-container-high rounded-lg font-label-md transition-colors" disabled>…</button>';
for ($p = $start; $p <= $end; $p++): ?>
<a href="<?php echo qs(['page' => $p]); ?>" class="w-10 h-10 flex items-center justify-center <?php echo $p === $page ? 'bg-primary text-on-primary' : 'text-secondary hover:bg-surface-container-high'; ?> rounded-lg font-label-md transition-colors"><?php echo $p; ?></a>
<?php endfor;
if ($end < $totalPages) echo '<span class="text-secondary px-sm">...</span><a href="' . qs(['page' => $totalPages]) . '" class="w-10 h-10 flex items-center justify-center text-secondary hover:bg-surface-container-high rounded-lg font-label-md transition-colors">' . $totalPages . '</a>';
?>
<a href="<?php echo qs(['page' => min($totalPages, $page + 1)]); ?>" class="p-md border border-outline-variant rounded-lg text-secondary hover:bg-surface-container-high transition-colors <?php echo $page >= $totalPages ? 'pointer-events-none opacity-30' : ''; ?>">
<span class="material-symbols-outlined">chevron_right</span>
</a>
</div>
</div>
</div>
<!-- Footer Detail Pane (Hidden by default, triggered by JS) -->
<div class="absolute bottom-0 left-0 right-0 h-1/2 bg-surface border-t border-outline translate-y-full transition-transform duration-300 z-40 flex flex-col shadow-2xl" id="detail-panel">
<div class="p-lg flex justify-between items-center border-b border-outline-variant">
<h3 class="font-headline-sm text-on-surface">Customer Profile Details</h3>
<button class="p-sm hover:bg-surface-container-high rounded-full transition-colors" onclick="togglePanel()">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<div class="flex-1 p-xl grid grid-cols-3 gap-xl overflow-y-auto custom-scrollbar">
<div class="space-y-lg">
<div class="flex items-center gap-lg">
<div class="w-24 h-24 rounded-xl bg-surface-container-highest border-2 border-primary-fixed-dim overflow-hidden flex items-center justify-center text-headline-md font-headline-md text-primary" id="panel-avatar">
</div>
<div>
<h4 class="font-headline-sm text-on-surface" id="panel-name"></h4>
<p class="text-secondary font-label-md" id="panel-since"></p>
<span class="inline-block mt-sm px-md py-xs bg-tertiary-fixed/20 text-on-tertiary-fixed-variant rounded-full font-label-sm" id="panel-status-badge"></span>
</div>
</div>
<div class="bg-surface-container-low p-md rounded-lg space-y-sm">
<p class="font-label-sm text-secondary uppercase">Personal Information</p>
<p class="font-body-sm text-on-surface flex justify-between"><span>Email:</span> <span class="font-bold" id="panel-email"></span></p>
<p class="font-body-sm text-on-surface flex justify-between"><span>Phone:</span> <span class="font-bold" id="panel-phone"></span></p>
<p class="font-body-sm text-on-surface flex justify-between"><span>Customer ID:</span> <span class="font-bold" id="panel-id"></span></p>
</div>
</div>
<div class="space-y-lg">
<p class="font-label-md text-secondary uppercase tracking-widest border-b border-outline-variant pb-xs">Recent Activity</p>
<div class="space-y-md" id="panel-bookings">
</div>
</div>
<div class="space-y-lg">
<p class="font-label-md text-secondary uppercase tracking-widest border-b border-outline-variant pb-xs">Account Settings</p>
<div class="space-y-sm">
<button type="button" id="panel-edit-btn" class="w-full flex items-center justify-between p-md border border-outline-variant rounded-lg hover:bg-surface-container-high transition-colors">
<span class="flex items-center gap-sm font-label-md text-on-surface"><span class="material-symbols-outlined text-secondary">edit</span> Edit Profile</span>
<span class="material-symbols-outlined text-sm text-outline">chevron_right</span>
</button>
<button type="button" id="panel-bookings-btn" class="w-full flex items-center justify-between p-md border border-outline-variant rounded-lg hover:bg-surface-container-high transition-colors">
<span class="flex items-center gap-sm font-label-md text-on-surface"><span class="material-symbols-outlined text-secondary">history</span> View All Bookings</span>
<span class="material-symbols-outlined text-sm text-outline">chevron_right</span>
</button>
<button class="w-full flex items-center justify-between p-md border border-error-container rounded-lg bg-error-container/10 hover:bg-error-container transition-colors group" id="panel-block-btn">
<span class="flex items-center gap-sm font-label-md text-error"><span class="material-symbols-outlined">block</span> <span id="panel-block-label">Block Account</span></span>
<span class="material-symbols-outlined text-sm text-error">chevron_right</span>
</button>
</div>
<div class="bg-secondary-fixed/30 p-md rounded-lg">
<p class="font-label-md text-on-secondary-fixed mb-xs">Total Lifetime Spend</p>
<p class="font-headline-md text-primary" id="panel-spend"></p>
</div>
</div>
</div>
</div>
</main>
</div>

<!-- Add Customer Modal -->
<div id="addCustomerOverlay" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-md">
<div class="bg-surface-container-lowest rounded-xl shadow-lg w-full max-w-md">
<div class="flex items-center justify-between p-xl border-b border-outline-variant">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Add New Customer</h3>
<button type="button" onclick="closeAddCustomerModal()" class="text-secondary hover:text-error">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<form method="POST" class="p-xl space-y-md">
<input type="hidden" name="action" value="add_customer">
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Full Name</label>
<input type="text" name="user_name" required class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Email</label>
<input type="email" name="email" required class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Phone</label>
<input type="tel" name="phone" class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Temporary Password</label>
<input type="password" name="password" required minlength="6" class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
</div>
<div class="flex justify-end gap-md pt-md">
<button type="button" onclick="closeAddCustomerModal()" class="px-lg py-md border border-outline-variant rounded-lg font-label-md text-label-md">Cancel</button>
<button type="submit" class="px-lg py-md bg-primary text-on-primary rounded-lg font-label-md text-label-md">Save Customer</button>
</div>
</form>
</div>
</div>

<!-- Edit Profile Modal -->
<div id="editCustomerOverlay" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-md">
<div class="bg-surface-container-lowest rounded-xl shadow-lg w-full max-w-md">
<div class="flex items-center justify-between p-xl border-b border-outline-variant">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Edit Customer Profile</h3>
<button type="button" onclick="closeEditCustomerModal()" class="text-secondary hover:text-error">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<form method="POST" class="p-xl space-y-md">
<input type="hidden" name="action" value="edit_customer">
<input type="hidden" name="user_id" id="edit-user-id">
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Full Name</label>
<input type="text" name="user_name" id="edit-user-name" required class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Email</label>
<input type="email" name="email" id="edit-user-email" required class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Phone</label>
<input type="tel" name="phone" id="edit-user-phone" class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">New Password <span class="text-outline">(leave blank to keep current)</span></label>
<input type="password" name="password" minlength="6" class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
</div>
<div class="flex justify-end gap-md pt-md">
<button type="button" onclick="closeEditCustomerModal()" class="px-lg py-md border border-outline-variant rounded-lg font-label-md text-label-md">Cancel</button>
<button type="submit" class="px-lg py-md bg-primary text-on-primary rounded-lg font-label-md text-label-md">Save Changes</button>
</div>
</form>
</div>
</div>

<!-- View All Bookings Modal -->
<div id="allBookingsOverlay" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-md">
<div class="bg-surface-container-lowest rounded-xl shadow-lg w-full max-w-lg max-h-[80vh] flex flex-col">
<div class="flex items-center justify-between p-xl border-b border-outline-variant">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Booking History — <span id="allBookings-name"></span></h3>
<button type="button" onclick="closeAllBookingsModal()" class="text-secondary hover:text-error">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<div class="p-xl space-y-md overflow-y-auto custom-scrollbar" id="allBookings-list"></div>
</div>
</div>

<!-- Micro-interactions Script -->
<script>
        function togglePanel() {
            const panel = document.getElementById('detail-panel');
            panel.classList.toggle('translate-y-full');
        }

        let currentPanelUser = null;

        function openDetails(u) {
            currentPanelUser = u;
            document.getElementById('panel-avatar').textContent = u.name.split(' ').slice(0,2).map(w => w[0]).join('').toUpperCase();
            document.getElementById('panel-name').textContent = u.name;
            document.getElementById('panel-since').textContent = 'Member since ' + u.joined;
            document.getElementById('panel-email').textContent = u.email;
            document.getElementById('panel-phone').textContent = u.phone || '—';
            document.getElementById('panel-id').textContent = 'DE-' + String(u.id).padStart(4, '0');
            document.getElementById('panel-spend').textContent = '₹' + u.spend;

            const badge = document.getElementById('panel-status-badge');
            badge.textContent = u.status === 'Blocked' ? 'Blocked' : 'Active Member';
            badge.className = u.status === 'Blocked'
                ? 'inline-block mt-sm px-md py-xs bg-error-container text-on-error-container rounded-full font-label-sm'
                : 'inline-block mt-sm px-md py-xs bg-tertiary-fixed/20 text-on-tertiary-fixed-variant rounded-full font-label-sm';

            const blockLabel = document.getElementById('panel-block-label');
            blockLabel.textContent = u.status === 'Blocked' ? 'Unblock Account' : 'Block Account';
            const blockBtn = document.getElementById('panel-block-btn');
            blockBtn.onclick = function () {
                window.location = (u.status === 'Blocked' ? '?unblock=' : '?block=') + u.id;
            };

            const list = document.getElementById('panel-bookings');
            list.innerHTML = '';
            if (u.bookings.length === 0) {
                list.innerHTML = '<p class="font-body-sm text-secondary">No bookings yet.</p>';
            } else {
                u.bookings.slice(0, 3).forEach(b => {
                    const row = document.createElement('div');
                    row.className = 'flex gap-md';
                    row.innerHTML = `<span class="material-symbols-outlined text-primary">directions_car</span>
                        <div>
                            <p class="font-label-md text-on-surface">${b.car}</p>
                            <p class="font-label-sm text-secondary">${b.status} • ${b.range}</p>
                        </div>`;
                    list.appendChild(row);
                });
            }

            document.getElementById('detail-panel').classList.remove('translate-y-full');
        }

        // ---- Edit Profile ----
        function openEditCustomerModal() {
            if (!currentPanelUser) return;
            document.getElementById('edit-user-id').value = currentPanelUser.id;
            document.getElementById('edit-user-name').value = currentPanelUser.name;
            document.getElementById('edit-user-email').value = currentPanelUser.email;
            document.getElementById('edit-user-phone').value = currentPanelUser.phone === '—' ? '' : (currentPanelUser.phone || '');
            document.getElementById('editCustomerOverlay').classList.remove('hidden');
        }
        function closeEditCustomerModal() {
            document.getElementById('editCustomerOverlay').classList.add('hidden');
        }
        document.getElementById('panel-edit-btn').addEventListener('click', openEditCustomerModal);

        // ---- View All Bookings ----
        function openAllBookingsModal() {
            if (!currentPanelUser) return;
            document.getElementById('allBookings-name').textContent = currentPanelUser.name;
            const list = document.getElementById('allBookings-list');
            list.innerHTML = '';
            if (!currentPanelUser.bookings || currentPanelUser.bookings.length === 0) {
                list.innerHTML = '<p class="font-body-sm text-secondary">No bookings yet.</p>';
            } else {
                currentPanelUser.bookings.forEach(b => {
                    const row = document.createElement('div');
                    row.className = 'flex items-center justify-between gap-md p-md border border-outline-variant rounded-lg';
                    row.innerHTML = `
                        <div class="flex items-center gap-md">
                            <span class="material-symbols-outlined text-primary">directions_car</span>
                            <div>
                                <p class="font-label-md text-on-surface">${b.car}</p>
                                <p class="font-label-sm text-secondary">${b.range} • Booked ${b.booked_on}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-label-md text-on-surface">₹${b.amount}</p>
                            <p class="font-label-sm text-secondary">${b.status}</p>
                        </div>`;
                    list.appendChild(row);
                });
            }
            document.getElementById('allBookingsOverlay').classList.remove('hidden');
        }
        function closeAllBookingsModal() {
            document.getElementById('allBookingsOverlay').classList.add('hidden');
        }
        document.getElementById('panel-bookings-btn').addEventListener('click', openAllBookingsModal);

        function openAddCustomerModal() {
            document.getElementById('addCustomerOverlay').classList.remove('hidden');
        }
        function closeAddCustomerModal() {
            document.getElementById('addCustomerOverlay').classList.add('hidden');
        }

        // Search Bar Focus Effect
        const searchInput = document.querySelector('input[type="text"][name="q"]');
        if (searchInput) {
            searchInput.addEventListener('focus', () => {
                searchInput.parentElement.classList.add('ring-2', 'ring-primary/20');
            });
            searchInput.addEventListener('blur', () => {
                searchInput.parentElement.classList.remove('ring-2', 'ring-primary/20');
            });
        }
    </script>
</body></html>
<?php $conn->close(); ?>