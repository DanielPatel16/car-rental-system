<?php
session_start();
include "../includes/db.php";

// ---- Auth guard: only logged-in admins may access this page ----
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// ---------------------------------------------------------------
// Handle status actions (?action=confirm|cancel|complete&id=X)
// ---------------------------------------------------------------
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    $map = ['confirm' => 'Confirmed', 'cancel' => 'Cancelled', 'complete' => 'Completed'];

    if (isset($map[$_GET['action']])) {
        $newStatus = $map[$_GET['action']];

        // Look up the car linked to this booking so we can keep its rental status in sync
        $carLookup = $conn->prepare("SELECT car_id FROM bookings WHERE id = ?");
        $carLookup->bind_param("i", $id);
        $carLookup->execute();
        $carRow = $carLookup->get_result()->fetch_assoc();
        $carLookup->close();

        $stmt = $conn->prepare("UPDATE bookings SET booking_status = ? WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $id);
        $stmt->execute();
        $stmt->close();

        if ($carRow) {
            $carId = (int) $carRow['car_id'];
            if ($newStatus === 'Confirmed') {
                // Booking accepted -> car goes out on rent
                $carStmt = $conn->prepare("UPDATE cars SET status = 'Rented' WHERE id = ?");
                $carStmt->bind_param("i", $carId);
                $carStmt->execute();
                $carStmt->close();
            } elseif ($newStatus === 'Completed' || $newStatus === 'Cancelled') {
                // Trip finished or cancelled -> car is free again (unless it's in maintenance)
                $carStmt = $conn->prepare("UPDATE cars SET status = 'Available' WHERE id = ? AND status != 'Maintenance'");
                $carStmt->bind_param("i", $carId);
                $carStmt->execute();
                $carStmt->close();
            }
        }

        if ($newStatus === 'Cancelled') {
            $conn->query("UPDATE bookings SET payment_status = 'Refunded' WHERE id = $id AND payment_status = 'Paid'");
        }
    }

    $qs = $_GET;
    unset($qs['action'], $qs['id']);
    header("Location: bookings.php?" . http_build_query($qs));
    exit();
}

// ---------------------------------------------------------------
// Filters + pagination
// ---------------------------------------------------------------
$filterStatus = $_GET['status'] ?? 'All Bookings';
$search       = trim($_GET['q'] ?? '');

$where  = [];
$params = [];
$types  = "";

if ($filterStatus !== '' && $filterStatus !== 'All Bookings') {
    $where[]  = "bookings.booking_status = ?";
    $params[] = $filterStatus;
    $types   .= "s";
}
if ($search !== '') {
    $like = "%$search%";
    $where[]  = "(users.user_name LIKE ? OR users.email LIKE ? OR cars.brand LIKE ? OR cars.model LIKE ?)";
    array_push($params, $like, $like, $like, $like);
    $types   .= "ssss";
}

$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

$perPage = 8;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

$baseFrom = "FROM bookings
             JOIN users ON bookings.user_id = users.id
             JOIN cars  ON bookings.car_id  = cars.id
             $whereSql";

$countStmt = $conn->prepare("SELECT COUNT(*) AS total $baseFrom");
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows  = (int) $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = max(1, (int) ceil($totalRows / $perPage));
$countStmt->close();

$sql = "SELECT bookings.*, users.user_name, users.email,
               cars.brand, cars.model, cars.image
        $baseFrom
        ORDER BY bookings.created_at DESC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$allTypes  = $types . "ii";
$allParams = array_merge($params, [$perPage, $offset]);
$stmt->bind_param($allTypes, ...$allParams);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------------------------------------------------------------
// Stat cards
// ---------------------------------------------------------------
$totalBookings = (int) $conn->query("SELECT COUNT(*) c FROM bookings")->fetch_assoc()['c'];
$activeRentals = (int) $conn->query("SELECT COUNT(*) c FROM bookings WHERE booking_status = 'Confirmed' AND CURDATE() BETWEEN pickup_date AND return_date")->fetch_assoc()['c'];
$pendingCount  = (int) $conn->query("SELECT COUNT(*) c FROM bookings WHERE booking_status = 'Pending'")->fetch_assoc()['c'];
$revenueMTD    = (float) ($conn->query("SELECT COALESCE(SUM(total_amount),0) r FROM bookings WHERE payment_status = 'Paid' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())")->fetch_assoc()['r'] ?? 0);

$statusBadge = [
    'Confirmed' => 'bg-tertiary-container/10 text-tertiary-container border border-tertiary-container/20',
    'Completed' => 'bg-surface-container-highest text-secondary border border-outline-variant',
    'Pending'   => 'bg-error-container/10 text-error border border-error-container/20',
    'Cancelled' => 'bg-surface-container-high text-secondary border border-outline-variant opacity-70 line-through',
];

// Helper: initials from a name, e.g. "James Smith" -> "JS" (same as dashboard.php / reports.php)
function initials_from_name(string $name): string {
    $parts   = preg_split('/\s+/', trim($name));
    $letters = array_map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)), array_slice($parts, 0, 2));
    return implode('', $letters) ?: '?';
}

function bqs($extra) {
    $qs = $_GET;
    unset($qs['action'], $qs['id']);
    foreach ($extra as $k => $v) { $qs[$k] = $v; }
    return 'bookings.php?' . http_build_query($qs);
}
?>
<!DOCTYPE html><html class="light" lang="en"><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>DriveEase Admin - Bookings Management</title>          
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
        }
        body { font-family: 'Inter', sans-serif; }
        .booking-row:hover { background-color: rgba(210, 225, 247, 0.3); }
        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 1);
        }
    </style>
</head>
<body class="bg-background text-on-surface min-h-screen">
<?php include "sidebar.php"; ?>
<header class="fixed top-0 right-0 w-[calc(100%-16rem)] h-16 bg-surface dark:bg-on-background border-b border-outline-variant dark:border-outline z-40">
<div class="flex justify-between items-center px-xl h-full w-full">
<div class="flex items-center gap-md w-1/3">
<form method="GET" class="relative w-full max-w-md">
<span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-secondary" data-icon="search">search</span>
<input class="w-full pl-10 pr-md py-xs bg-surface-container-low border border-outline-variant rounded-lg focus:outline-none focus:border-primary font-body-sm text-body-sm" placeholder="Search bookings, customers, or vehicle..." type="text" name="q" value="<?php echo htmlspecialchars($search); ?>">
<input type="hidden" name="status" value="<?php echo htmlspecialchars($filterStatus); ?>">
</form>
</div>
<div class="flex items-center gap-lg">
<button class="relative text-secondary hover:text-primary transition-colors">
<span class="material-symbols-outlined" data-icon="notifications">notifications</span>
<span class="absolute top-0 right-0 w-2 h-2 bg-error rounded-full"></span>
</button>
<button class="text-secondary hover:text-primary transition-colors">
<span class="material-symbols-outlined" data-icon="help_outline">help_outline</span>
</button>
<div class="flex items-center gap-sm pl-md border-l border-outline-variant">
<div class="text-right">
<p class="font-label-md text-label-md text-on-surface"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></p>
<p class="font-label-sm text-label-sm text-secondary">Admin Access</p>
</div>
<div class="w-10 h-10 rounded-full border border-primary bg-secondary-container flex items-center justify-center text-primary font-bold text-sm">
<?php echo htmlspecialchars(initials_from_name($_SESSION['user_name'] ?? 'Admin')); ?>
</div>
</div>
</div>
</div>
</header>
<main class="ml-64 pt-16 p-xl">
<div class="max-w-[1440px] mx-auto">
<div class="flex justify-between items-end mb-xl">
<div>
<h2 class="font-headline-lg text-headline-lg text-on-surface">Bookings Management</h2>
<p class="font-body-md text-body-md text-secondary mt-xs">Monitor and manage all active, pending, and historical rental transactions.</p>
</div>
</div>

<div class="grid grid-cols-4 gap-lg mb-xl">
<div class="glass-panel p-lg rounded-xl">
<p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Total Bookings</p>
<div class="flex items-baseline gap-sm mt-xs">
<h3 class="font-headline-md text-headline-md text-primary"><?php echo number_format($totalBookings); ?></h3>
</div>
</div>
<div class="glass-panel p-lg rounded-xl">
<p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Active Rentals</p>
<div class="flex items-baseline gap-sm mt-xs">
<h3 class="font-headline-md text-headline-md text-primary"><?php echo number_format($activeRentals); ?></h3>
<span class="font-label-sm text-label-sm text-secondary">Currently on road</span>
</div>
</div>
<div class="glass-panel p-lg rounded-xl">
<p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Pending Approvals</p>
<div class="flex items-baseline gap-sm mt-xs">
<h3 class="font-headline-md text-headline-md text-error"><?php echo number_format($pendingCount); ?></h3>
<span class="font-label-sm text-label-sm text-error">Requires action</span>
</div>
</div>
<div class="glass-panel p-lg rounded-xl">
<p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Revenue (MTD)</p>
<div class="flex items-baseline gap-sm mt-xs">
<h3 class="font-headline-md text-headline-md text-primary">₹<?php echo number_format($revenueMTD, 0); ?></h3>
</div>
</div>
</div>

<div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
<div class="p-lg border-b border-outline-variant flex flex-wrap items-center justify-between gap-md bg-surface-bright">
<div class="flex items-center gap-md">
<?php foreach (['All Bookings', 'Pending', 'Confirmed', 'Completed', 'Cancelled'] as $s):
    $active = $filterStatus === $s;
?>
<a href="<?php echo bqs(['status' => $s, 'page' => 1]); ?>" class="px-md py-sm rounded-full font-label-md text-label-md transition-colors <?php echo $active ? 'bg-primary-fixed text-on-primary-fixed' : 'text-secondary hover:bg-surface-container-high'; ?>"><?php echo $s; ?></a>
<?php endforeach; ?>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container text-on-surface-variant">
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Booking ID</th>
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Customer</th>
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Vehicle</th>
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Dates</th>
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Total Amount</th>
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Identity</th>
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Status</th>
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Action</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant">
<?php if (empty($bookings)): ?>
<tr><td colspan="8" class="px-lg py-2xl text-center text-secondary font-body-md text-body-md">No bookings found.</td></tr>
<?php endif; ?>
<?php foreach ($bookings as $b):
    $initials = strtoupper(substr($b['user_name'], 0, 1) . substr(strrchr($b['user_name'], ' ') ?: '', 1, 1));
    $badgeClass = $statusBadge[$b['booking_status']] ?? 'bg-surface-container-high text-secondary border border-outline-variant';
    $days = (int) $b['total_days'];
    $idProofSrc = $b['id_proof_image'] ? "../uploads/documents/" . htmlspecialchars($b['id_proof_image']) : "";
?>
<tr class="booking-row transition-colors">
<td class="px-lg py-lg font-label-md text-label-md text-primary">#BK-<?php echo str_pad($b['id'], 4, '0', STR_PAD_LEFT); ?></td>
<td class="px-lg py-lg">
<div class="flex items-center gap-md">
<div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center text-primary font-bold text-xs"><?php echo htmlspecialchars($initials); ?></div>
<div>
<p class="font-label-md text-label-md"><?php echo htmlspecialchars($b['user_name']); ?></p>
<p class="font-body-sm text-body-sm text-secondary"><?php echo htmlspecialchars($b['email']); ?></p>
</div>
</div>
</td>
<td class="px-lg py-lg">
<div class="flex items-center gap-sm">
<span class="material-symbols-outlined text-secondary" data-icon="directions_car">directions_car</span>
<div>
<p class="font-label-md text-label-md"><?php echo htmlspecialchars($b['brand'] . ' ' . $b['model']); ?></p>
</div>
</div>
</td>
<td class="px-lg py-lg">
<p class="font-label-sm text-label-sm"><?php echo date('M d', strtotime($b['pickup_date'])); ?> - <?php echo date('M d', strtotime($b['return_date'])); ?></p>
<p class="font-body-sm text-body-sm text-secondary"><?php echo $days; ?> Day<?php echo $days === 1 ? '' : 's'; ?></p>
</td>
<td class="px-lg py-lg font-label-md text-label-md">₹<?php echo number_format($b['total_amount'], 2); ?></td>
<td class="px-lg py-lg">
<button type="button" onclick='openIdModal(<?php echo json_encode([
    "name" => $b['user_name'],
    "aadhar" => $b['aadhar_number'],
    "license" => $b['license_number'],
    "image" => $idProofSrc,
], JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' class="flex items-center gap-xs text-primary hover:underline font-label-sm text-label-sm">
<span class="material-symbols-outlined text-[18px]">badge</span> View ID
</button>
</td>
<td class="px-lg py-lg">
<span class="px-sm py-xs rounded-full font-label-sm text-label-sm <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($b['booking_status']); ?></span>
</td>
<td class="px-lg py-lg">
<div class="flex items-center gap-xs">
<?php if ($b['booking_status'] === 'Pending'): ?>
<a href="<?php echo bqs(['action' => 'confirm', 'id' => $b['id']]); ?>" title="Confirm" class="p-1 text-secondary hover:text-tertiary-container transition-colors"><span class="material-symbols-outlined text-[20px]">check_circle</span></a>
<a href="<?php echo bqs(['action' => 'cancel', 'id' => $b['id']]); ?>" onclick="return confirm('Cancel this booking?')" title="Cancel" class="p-1 text-secondary hover:text-error transition-colors"><span class="material-symbols-outlined text-[20px]">cancel</span></a>
<?php elseif ($b['booking_status'] === 'Confirmed'): ?>
<a href="<?php echo bqs(['action' => 'complete', 'id' => $b['id']]); ?>" title="Mark Completed" class="p-1 text-secondary hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">task_alt</span></a>
<a href="<?php echo bqs(['action' => 'cancel', 'id' => $b['id']]); ?>" onclick="return confirm('Cancel this booking?')" title="Cancel" class="p-1 text-secondary hover:text-error transition-colors"><span class="material-symbols-outlined text-[20px]">cancel</span></a>
<?php else: ?>
<span class="text-secondary text-[12px] italic">No actions</span>
<?php endif; ?>
</div>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<div class="p-lg border-t border-outline-variant flex items-center justify-between bg-surface-bright">
<p class="font-body-sm text-body-sm text-secondary">Showing <?php echo $totalRows ? ($offset + 1) : 0; ?> to <?php echo min($offset + $perPage, $totalRows); ?> of <?php echo $totalRows; ?> entries</p>
<div class="flex items-center gap-xs">
<a href="<?php echo bqs(['page' => max(1, $page - 1)]); ?>" class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-surface-container-high transition-colors <?php echo $page <= 1 ? 'pointer-events-none opacity-30' : ''; ?>">
<span class="material-symbols-outlined" data-icon="chevron_left">chevron_left</span>
</a>
<?php for ($p = 1; $p <= $totalPages; $p++): ?>
<a href="<?php echo bqs(['page' => $p]); ?>" class="w-10 h-10 flex items-center justify-center rounded-lg font-label-md text-label-md <?php echo $p === $page ? 'bg-primary text-on-primary' : 'border border-outline-variant hover:bg-surface-container-high'; ?>"><?php echo $p; ?></a>
<?php endfor; ?>
<a href="<?php echo bqs(['page' => min($totalPages, $page + 1)]); ?>" class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-surface-container-high transition-colors <?php echo $page >= $totalPages ? 'pointer-events-none opacity-30' : ''; ?>">
<span class="material-symbols-outlined" data-icon="chevron_right">chevron_right</span>
</a>
</div>
</div>
</div>
</div>
</main>

<!-- Identity Document Modal -->
<div id="idModalOverlay" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-md">
<div class="bg-surface-container-lowest rounded-xl shadow-lg w-full max-w-lg max-h-[90vh] overflow-y-auto">
<div class="flex items-center justify-between p-xl border-b border-outline-variant">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Identity Verification</h3>
<button type="button" onclick="closeIdModal()" class="text-secondary hover:text-error">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<div class="p-xl space-y-md">
<div>
<p class="font-label-sm text-label-sm text-secondary uppercase">Customer</p>
<p id="idModalName" class="font-body-md text-body-md text-on-surface font-bold"></p>
</div>
<div class="grid grid-cols-2 gap-md">
<div>
<p class="font-label-sm text-label-sm text-secondary uppercase">Aadhar Number</p>
<p id="idModalAadhar" class="font-body-md text-body-md text-on-surface"></p>
</div>
<div>
<p class="font-label-sm text-label-sm text-secondary uppercase">License Number</p>
<p id="idModalLicense" class="font-body-md text-body-md text-on-surface"></p>
</div>
</div>
<div>
<p class="font-label-sm text-label-sm text-secondary uppercase mb-xs">Uploaded ID Photo</p>
<img id="idModalImage" src="" alt="ID Proof" class="w-full rounded-lg border border-outline-variant object-contain max-h-96 bg-surface-container">
</div>
</div>
</div>
</div>

<script>
        function openIdModal(data) {
            document.getElementById('idModalName').textContent = data.name;
            document.getElementById('idModalAadhar').textContent = data.aadhar;
            document.getElementById('idModalLicense').textContent = data.license;
            document.getElementById('idModalImage').src = data.image;
            document.getElementById('idModalOverlay').classList.remove('hidden');
        }
        function closeIdModal() {
            document.getElementById('idModalOverlay').classList.add('hidden');
        }
    </script>
</body></html>