<?php
session_start();
include "../includes/db.php";

// ---- Auth guard: only logged-in admins may access this page ----
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$upload_dir = "../uploads/cars/";
$errors = [];
$success = "";

// ---------------------------------------------------------------
// Handle DELETE (?delete=ID)
// ---------------------------------------------------------------
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    $imgStmt = $conn->prepare("SELECT image FROM cars WHERE id = ?");
    $imgStmt->bind_param("i", $id);
    $imgStmt->execute();
    $imgRow = $imgStmt->get_result()->fetch_assoc();
    $imgStmt->close();

    if ($imgRow && !empty($imgRow['image']) && file_exists($upload_dir . $imgRow['image'])) {
        unlink($upload_dir . $imgRow['image']);
    }

    $del = $conn->prepare("DELETE FROM cars WHERE id = ?");
    $del->bind_param("i", $id);
    $del->execute();
    $del->close();

    header("Location: cars.php");
    exit();
}

// ---------------------------------------------------------------
// Handle ADD / EDIT (POST)
// ---------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {

    $brand               = trim($_POST['brand'] ?? '');
    $model               = trim($_POST['model'] ?? '');
    $category_id         = !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null;
    $year                = (int) ($_POST['year'] ?? 0);
    $fuel_type           = $_POST['fuel_type'] ?? 'Petrol';
    $transmission        = $_POST['transmission'] ?? 'Automatic';
    $seats               = (int) ($_POST['seats'] ?? 4);
    $price_per_day       = (float) ($_POST['price_per_day'] ?? 0);
    $registration_number = trim($_POST['registration_number'] ?? '');
    $description         = trim($_POST['description'] ?? '');
    $status              = $_POST['status'] ?? 'Available';
    $car_id              = (int) ($_POST['car_id'] ?? 0);
    $image_name          = $_POST['existing_image'] ?? null;

    if ($brand === '' || $model === '' || $registration_number === '' || $year <= 0) {
        $errors[] = "Brand, Model, Year and Registration Number are required.";
    }

    // ---- Image upload: rename with a hash so files never collide/overwrite each other ----
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            $errors[] = "Only JPG, PNG or WEBP images are allowed.";
        } else {
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $hashedName = hash('sha256', uniqid((string) mt_rand(), true)) . '.' . $ext;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $hashedName)) {
                // remove old image when replacing on edit
                if ($_POST['action'] === 'edit_car' && !empty($_POST['existing_image']) && file_exists($upload_dir . $_POST['existing_image'])) {
                    unlink($upload_dir . $_POST['existing_image']);
                }
                $image_name = $hashedName;
            } else {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    if (empty($errors)) {

        if ($_POST['action'] === 'add_car') {

            $stmt = $conn->prepare(
                "INSERT INTO cars
                    (brand, model, category_id, year, fuel_type, transmission, seats, price_per_day, registration_number, image, description, status)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"
            );
            $stmt->bind_param(
                "ssiissidssss",
                $brand, $model, $category_id, $year, $fuel_type, $transmission,
                $seats, $price_per_day, $registration_number, $image_name, $description, $status
            );

            if ($stmt->execute()) {
                $success = "Vehicle added successfully.";
            } else {
                $errors[] = ($conn->errno === 1062)
                    ? "A vehicle with this registration number already exists."
                    : "Could not add vehicle: " . $stmt->error;
            }
            $stmt->close();

        } elseif ($_POST['action'] === 'edit_car' && $car_id > 0) {

            $stmt = $conn->prepare(
                "UPDATE cars SET
                    brand = ?, model = ?, category_id = ?, year = ?, fuel_type = ?, transmission = ?,
                    seats = ?, price_per_day = ?, registration_number = ?, image = ?, description = ?, status = ?
                 WHERE id = ?"
            );
            $stmt->bind_param(
                "ssiissidssssi",
                $brand, $model, $category_id, $year, $fuel_type, $transmission,
                $seats, $price_per_day, $registration_number, $image_name, $description, $status, $car_id
            );

            if ($stmt->execute()) {
                $success = "Vehicle updated successfully.";
            } else {
                $errors[] = ($conn->errno === 1062)
                    ? "A vehicle with this registration number already exists."
                    : "Could not update vehicle: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// ---------------------------------------------------------------
// Categories (for the Add/Edit form and the filter dropdown)
// ---------------------------------------------------------------
$categories = [];
$catResult = $conn->query("SELECT id, name FROM categories ORDER BY name");
while ($row = $catResult->fetch_assoc()) {
    $categories[] = $row;
}

// ---------------------------------------------------------------
// Filters + Pagination for the fleet table
// ---------------------------------------------------------------
$filterCategory = $_GET['category'] ?? '';
$filterStatus   = $_GET['status'] ?? '';
$search         = trim($_GET['q'] ?? '');

$where  = [];
$params = [];
$types  = "";

if ($filterCategory !== '' && $filterCategory !== 'All Categories') {
    $where[]  = "categories.name = ?";
    $params[] = $filterCategory;
    $types   .= "s";
}
if ($filterStatus !== '' && $filterStatus !== 'All Statuses') {
    $where[]  = "cars.status = ?";
    $params[] = $filterStatus;
    $types   .= "s";
}
if ($search !== '') {
    $like = "%$search%";
    $where[]  = "(cars.brand LIKE ? OR cars.model LIKE ? OR cars.registration_number LIKE ?)";
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types   .= "sss";
}

$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

$perPage = 5;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

$countSql  = "SELECT COUNT(*) AS total FROM cars LEFT JOIN categories ON cars.category_id = categories.id $whereSql";
$countStmt = $conn->prepare($countSql);
if ($types) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRows  = (int) $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = max(1, (int) ceil($totalRows / $perPage));
$countStmt->close();

$sql = "SELECT cars.*, categories.name AS category_name
        FROM cars LEFT JOIN categories ON cars.category_id = categories.id
        $whereSql
        ORDER BY cars.created_at DESC
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$allTypes  = $types . "ii";
$allParams = array_merge($params, [$perPage, $offset]);
$stmt->bind_param($allTypes, ...$allParams);
$stmt->execute();
$cars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ---------------------------------------------------------------
// Stat cards
// ---------------------------------------------------------------
$statCounts = ['Available' => 0, 'Rented' => 0, 'Maintenance' => 0];
$statResult = $conn->query("SELECT status, COUNT(*) AS c FROM cars GROUP BY status");
while ($row = $statResult->fetch_assoc()) {
    $statCounts[$row['status']] = (int) $row['c'];
}
$totalFleet = array_sum($statCounts);
?>
<!DOCTYPE html><html class="light" lang="en"><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>DriveEase Admin - Fleet Management</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
            vertical-align: middle;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #faf8ff;
        }
        /* Custom scrollbar for data heavy tables */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f2f3ff;
        }
        ::-webkit-scrollbar-thumb {
            background: #c4c5d5;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #757684;
        }
    </style>
<script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              "colors": {
                      "surface-container-low": "#f2f3ff",
                      "surface-dim": "#d2d9f4",
                      "primary-container": "#1e40af",
                      "on-background": "#131b2e",
                      "on-surface-variant": "#444653",
                      "on-primary": "#ffffff",
                      "tertiary-fixed-dim": "#4edea3",
                      "on-tertiary-container": "#3fd298",
                      "secondary": "#516072",
                      "surface-container": "#eaedff",
                      "on-secondary-container": "#556477",
                      "on-error": "#ffffff",
                      "tertiary-fixed": "#6ffbbe",
                      "surface-container-high": "#e2e7ff",
                      "secondary-container": "#d2e1f7",
                      "outline-variant": "#c4c5d5",
                      "on-primary-container": "#a8b8ff",
                      "surface-container-highest": "#dae2fd",
                      "surface": "#faf8ff",
                      "on-tertiary-fixed": "#002113",
                      "surface-tint": "#3755c3",
                      "on-primary-fixed-variant": "#173bab",
                      "on-tertiary": "#ffffff",
                      "primary-fixed-dim": "#b8c4ff",
                      "on-surface": "#131b2e",
                      "primary": "#00288e",
                      "secondary-fixed": "#d4e4fa",
                      "on-error-container": "#93000a",
                      "primary-fixed": "#dde1ff",
                      "on-primary-fixed": "#001453",
                      "secondary-fixed-dim": "#b9c8de",
                      "on-secondary-fixed": "#0d1c2d",
                      "error-container": "#ffdad6",
                      "on-tertiary-fixed-variant": "#005236",
                      "surface-container-lowest": "#ffffff",
                      "inverse-on-surface": "#eef0ff",
                      "inverse-surface": "#283044",
                      "inverse-primary": "#b8c4ff",
                      "tertiary": "#003d27",
                      "surface-variant": "#dae2fd",
                      "error": "#ba1a1a",
                      "background": "#faf8ff",
                      "tertiary-container": "#00563a",
                      "on-secondary": "#ffffff",
                      "surface-bright": "#faf8ff",
                      "on-secondary-fixed-variant": "#39485a",
                      "outline": "#757684"
              },
              "borderRadius": {
                      "DEFAULT": "0.25rem",
                      "lg": "0.5rem",
                      "xl": "0.75rem",
                      "full": "9999px"
              },
              "spacing": {
                      "margin-desktop": "32px",
                      "lg": "24px",
                      "gutter": "24px",
                      "max-width": "1440px",
                      "xl": "32px",
                      "sm": "8px",
                      "md": "16px",
                      "margin-mobile": "16px",
                      "xs": "4px",
                      "base": "4px",
                      "2xl": "48px"
              },
              "fontFamily": {
                      "label-sm": ["Inter"],
                      "body-sm": ["Inter"],
                      "label-md": ["Inter"],
                      "body-lg": ["Inter"],
                      "body-md": ["Inter"],
                      "headline-sm": ["Inter"],
                      "headline-md": ["Inter"],
                      "headline-lg-mobile": ["Inter"],
                      "headline-lg": ["Inter"]
              },
              "fontSize": {
                      "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "500"}],
                      "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                      "label-md": ["14px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                      "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                      "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                      "headline-sm": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                      "headline-md": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                      "headline-lg-mobile": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "700"}],
                      "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700"}]
              }
            },
          },
        }
      </script>
</head>
<body class="bg-surface text-on-surface">
<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container-lowest dark:bg-on-background border-r border-outline-variant dark:border-outline shadow-sm dark:shadow-none flex flex-col h-full p-md space-y-sm z-30">
<div class="mb-xl px-sm">
<h1 class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed">DriveEase Admin</h1>
<p class="text-secondary font-label-sm text-label-sm">Fleet Management</p>
</div>
<nav class="flex-1 space-y-xs">
<a class="flex items-center gap-md p-md text-secondary dark:text-secondary-fixed-dim hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant rounded-lg transition-colors font-label-md text-label-md" href="dashboard.php">
<span class="material-symbols-outlined">dashboard</span>
                Dashboard
            </a>
<a class="flex items-center gap-md p-md text-primary dark:text-primary-fixed-dim font-bold bg-secondary-container dark:bg-primary-container rounded-lg transition-colors font-label-md text-label-md" href="cars.php">
<span class="material-symbols-outlined">directions_car</span>
                Fleet
            </a>
<a class="flex items-center gap-md p-md text-secondary dark:text-secondary-fixed-dim hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant rounded-lg transition-colors font-label-md text-label-md" href="bookings.php">
<span class="material-symbols-outlined">calendar_month</span>
                Bookings
            </a>
<a class="flex items-center gap-md p-md text-secondary dark:text-secondary-fixed-dim hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant rounded-lg transition-colors font-label-md text-label-md" href="users.php">
<span class="material-symbols-outlined">group</span>
                Customers
            </a>
</nav>
<div class="pt-md border-t border-outline-variant">
<a href="../logout.php" class="w-full flex items-center justify-center gap-sm bg-primary text-on-primary font-label-md text-label-md py-md px-lg rounded-lg hover:opacity-90 transition-all active:scale-95">
<span class="material-symbols-outlined">logout</span>
                Logout
            </a>
</div>
</aside>
<!-- TopNavBar -->
<header class="fixed top-0 right-0 w-[calc(100%-16rem)] h-16 bg-surface dark:bg-on-background border-b border-outline-variant dark:border-outline flex justify-between items-center px-xl w-full z-20">
<div class="flex items-center justify-center gap-xl flex-1">
<form method="GET" class="relative w-96">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-secondary">search</span>
<input class="w-full pl-10 pr-4 py-2 bg-surface-container-low border border-outline-variant rounded-full text-body-md font-body-md focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Search fleet or plate number..." type="text" name="q" value="<?php echo htmlspecialchars($search); ?>">
</form>
</div>
<div class="flex items-center gap-lg">
<button class="text-secondary hover:text-primary transition-colors relative">
<span class="material-symbols-outlined">notifications</span>
<span class="absolute top-0 right-0 w-2 h-2 bg-error rounded-full border-2 border-surface"></span>
</button>
<button class="text-secondary hover:text-primary transition-colors">
<span class="material-symbols-outlined">help_outline</span>
</button>
<div class="h-8 w-px bg-outline-variant"></div>
<div class="flex items-center gap-md">
<div class="text-right hidden sm:block">
<p class="font-label-md text-label-md text-on-surface"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></p>
<p class="font-label-sm text-label-sm text-secondary">Fleet Supervisor</p>
</div>
<img class="w-10 h-10 rounded-full border border-outline-variant object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDfr-N5WQrwD7BXENoPngmdABwO4G73_E1-AMFCbKBF5kmT7ETZ32TJ5-rN6Z4mB3pktLcpvgRRYZHW-GMrvRBwUAYKDRkm0zedx1Odj3tVLOD3uNyaSgCzIGMElCc7ABW5TYPUSqjNFG-Gcz3A6y-0YmCTykS3pfZ_mg4shcEoE3ZKormDzLeWFWFB6HPy4yLx5KizGrRaoeFAC9INXjgYmy8QByJf_FJDhcT7MMEKb4pv1cNafaqIyGeMhSSTaseKa-rsGEmy6TA">
</div>
</div>
</header>
<!-- Main Content Canvas -->
<main class="ml-64 pt-16 min-h-screen">
<div class="p-xl max-w-max-width mx-auto">

<?php if ($success): ?>
<div class="mb-lg p-md bg-tertiary-fixed/40 border border-tertiary text-on-tertiary-fixed-variant rounded-lg font-body-md text-body-md"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>
<?php if ($errors): ?>
<div class="mb-lg p-md bg-error-container border border-error text-on-error-container rounded-lg font-body-md text-body-md">
<?php foreach ($errors as $e) echo "<p>" . htmlspecialchars($e) . "</p>"; ?>
</div>
<?php endif; ?>

<!-- Page Header Actions -->
<div class="flex flex-col md:flex-row md:items-end justify-between gap-lg mb-xl">
<div>
<h2 class="font-headline-lg text-headline-lg text-primary">Fleet Inventory</h2>
<p class="font-body-md text-body-md text-secondary mt-xs">Manage and monitor all vehicles in your local branch.</p>
</div>
<button type="button" onclick="openAddModal()" class="flex items-center gap-sm bg-primary text-on-primary font-label-md text-label-md py-lg px-xl rounded-lg shadow-sm hover:shadow-md transition-all active:scale-95">
<span class="material-symbols-outlined">add_circle</span>
                    Add New Vehicle
                </button>
</div>
<!-- Stats Overview - Bento Style -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-lg mb-xl">
<div class="bg-surface-container-lowest p-xl rounded-xl border border-outline-variant shadow-sm flex items-center gap-lg">
<div class="w-12 h-12 rounded-lg bg-primary-fixed flex items-center justify-center text-primary">
<span class="material-symbols-outlined">directions_car</span>
</div>
<div>
<p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Total Fleet</p>
<p class="font-headline-md text-headline-md"><?php echo $totalFleet; ?></p>
</div>
</div>
<div class="bg-surface-container-lowest p-xl rounded-xl border border-outline-variant shadow-sm flex items-center gap-lg">
<div class="w-12 h-12 rounded-lg bg-tertiary-fixed flex items-center justify-center text-tertiary">
<span class="material-symbols-outlined">check_circle</span>
</div>
<div>
<p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Available</p>
<p class="font-headline-md text-headline-md"><?php echo $statCounts['Available']; ?></p>
</div>
</div>
<div class="bg-surface-container-lowest p-xl rounded-xl border border-outline-variant shadow-sm flex items-center gap-lg">
<div class="w-12 h-12 rounded-lg bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed-variant">
<span class="material-symbols-outlined">key</span>
</div>
<div>
<p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Rented</p>
<p class="font-headline-md text-headline-md"><?php echo $statCounts['Rented']; ?></p>
</div>
</div>
<div class="bg-surface-container-lowest p-xl rounded-xl border border-outline-variant shadow-sm flex items-center gap-lg">
<div class="w-12 h-12 rounded-lg bg-error-container flex items-center justify-center text-error">
<span class="material-symbols-outlined">build</span>
</div>
<div>
<p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Maintenance</p>
<p class="font-headline-md text-headline-md"><?php echo $statCounts['Maintenance']; ?></p>
</div>
</div>
</div>
<!-- Table Filters Bar -->
<form method="GET" class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md mb-md flex flex-wrap items-center justify-between gap-md">
<input type="hidden" name="q" value="<?php echo htmlspecialchars($search); ?>">
<div class="flex items-center gap-md flex-1 min-w-[300px]">
<div class="flex items-center gap-xs px-md py-sm bg-surface-container border border-outline-variant rounded-lg">
<span class="material-symbols-outlined text-secondary text-sm">filter_list</span>
<span class="font-label-md text-label-md text-on-surface">Filter By:</span>
</div>
<select name="category" onchange="this.form.submit()" class="bg-transparent border-none font-body-md text-body-md focus:ring-0 cursor-pointer">
<option value="All Categories">All Categories</option>
<?php foreach ($categories as $cat): ?>
<option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo ($filterCategory === $cat['name']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
<?php endforeach; ?>
</select>
<select name="status" onchange="this.form.submit()" class="bg-transparent border-none font-body-md text-body-md focus:ring-0 cursor-pointer">
<option value="All Statuses">All Statuses</option>
<option <?php echo ($filterStatus === 'Available') ? 'selected' : ''; ?>>Available</option>
<option <?php echo ($filterStatus === 'Rented') ? 'selected' : ''; ?>>Rented</option>
<option <?php echo ($filterStatus === 'Maintenance') ? 'selected' : ''; ?>>Maintenance</option>
</select>
</div>
<div class="flex items-center gap-sm">
<a href="cars.php" class="p-md text-secondary hover:bg-surface-container-high rounded-lg transition-colors" title="Reset filters">
<span class="material-symbols-outlined">restart_alt</span>
</a>
</div>
</form>
<!-- Fleet Table -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low border-b border-outline-variant">
<th class="px-xl py-lg font-label-md text-label-md text-secondary uppercase tracking-wider">Vehicle</th>
<th class="px-xl py-lg font-label-md text-label-md text-secondary uppercase tracking-wider">Number Plate</th>
<th class="px-xl py-lg font-label-md text-label-md text-secondary uppercase tracking-wider">Category</th>
<th class="px-xl py-lg font-label-md text-label-md text-secondary uppercase tracking-wider">Status</th>
<th class="px-xl py-lg font-label-md text-label-md text-secondary uppercase tracking-wider">Price/Day</th>
<th class="px-xl py-lg font-label-md text-label-md text-secondary uppercase tracking-wider text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant">
<?php if (empty($cars)): ?>
<tr>
<td colspan="6" class="px-xl py-2xl text-center text-secondary font-body-md text-body-md">No vehicles found. Click "Add New Vehicle" to get started.</td>
</tr>
<?php endif; ?>
<?php foreach ($cars as $car):
    $statusDot = ['Available' => 'bg-tertiary-fixed-dim', 'Rented' => 'bg-secondary-fixed-dim', 'Maintenance' => 'bg-error'][$car['status']] ?? 'bg-secondary';
    $statusText = ['Available' => 'text-on-tertiary-fixed-variant', 'Rented' => 'text-on-secondary-fixed', 'Maintenance' => 'text-on-error-container'][$car['status']] ?? 'text-secondary';
    $imgSrc = $car['image'] ? "../uploads/cars/" . htmlspecialchars($car['image']) : "https://placehold.co/128x96?text=No+Image";
?>
<tr class="hover:bg-surface-container-low transition-colors group">
<td class="px-xl py-lg">
<div class="flex items-center gap-lg">
<div class="w-16 h-12 rounded-lg bg-surface-container-high overflow-hidden border border-outline-variant">
<img class="w-full h-full object-cover" src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
</div>
<div>
<p class="font-body-md text-body-md font-bold text-on-surface"><?php echo htmlspecialchars($car['brand']); ?> <?php echo htmlspecialchars($car['model']); ?></p>
<p class="font-label-sm text-label-sm text-secondary"><?php echo (int) $car['year']; ?> &middot; <?php echo htmlspecialchars($car['fuel_type']); ?> &middot; <?php echo htmlspecialchars($car['transmission']); ?></p>
</div>
</div>
</td>
<td class="px-xl py-lg font-body-md text-body-md font-mono text-on-surface"><?php echo htmlspecialchars($car['registration_number']); ?></td>
<td class="px-xl py-lg">
<span class="px-md py-xs bg-surface-container rounded-full text-secondary font-label-sm text-label-sm"><?php echo htmlspecialchars($car['category_name'] ?? 'Uncategorized'); ?></span>
</td>
<td class="px-xl py-lg">
<div class="flex items-center gap-xs <?php echo $statusText; ?>">
<span class="w-2 h-2 rounded-full <?php echo $statusDot; ?>"></span>
<span class="font-label-md text-label-md"><?php echo htmlspecialchars($car['status']); ?></span>
</div>
</td>
<td class="px-xl py-lg font-body-md text-body-md font-bold">₹<?php echo number_format($car['price_per_day'], 2); ?></td>
<td class="px-xl py-lg text-right">
<div class="flex items-center justify-end gap-sm">
<button type="button" onclick='openEditModal(<?php echo json_encode($car, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' class="p-sm text-secondary hover:text-primary hover:bg-primary-fixed rounded transition-all">
<span class="material-symbols-outlined text-[20px]">edit</span>
</button>
<a href="cars.php?delete=<?php echo (int) $car['id']; ?>" onclick="return confirm('Delete this vehicle? This cannot be undone.')" class="p-sm text-secondary hover:text-error hover:bg-error-container rounded transition-all">
<span class="material-symbols-outlined text-[20px]">delete</span>
</a>
</div>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<!-- Pagination -->
<div class="px-xl py-lg bg-surface-container-low border-t border-outline-variant flex items-center justify-between">
<p class="font-body-sm text-body-sm text-secondary">Showing <?php echo $totalRows ? ($offset + 1) : 0; ?> to <?php echo min($offset + $perPage, $totalRows); ?> of <?php echo $totalRows; ?> vehicles</p>
<div class="flex items-center gap-xs">
<?php
$qs = $_GET;
function pageUrl($qs, $p) { $qs['page'] = $p; return 'cars.php?' . http_build_query($qs); }
?>
<a href="<?php echo pageUrl($qs, max(1, $page - 1)); ?>" class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant text-secondary hover:bg-surface-container-high <?php echo $page <= 1 ? 'pointer-events-none opacity-30' : ''; ?>">
<span class="material-symbols-outlined">chevron_left</span>
</a>
<?php for ($p = 1; $p <= $totalPages; $p++): ?>
<a href="<?php echo pageUrl($qs, $p); ?>" class="w-10 h-10 flex items-center justify-center rounded-lg font-label-md text-label-md <?php echo $p === $page ? 'bg-primary text-on-primary' : 'border border-outline-variant text-secondary hover:bg-surface-container-high'; ?>"><?php echo $p; ?></a>
<?php endfor; ?>
<a href="<?php echo pageUrl($qs, min($totalPages, $page + 1)); ?>" class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant text-secondary hover:bg-surface-container-high <?php echo $page >= $totalPages ? 'pointer-events-none opacity-30' : ''; ?>">
<span class="material-symbols-outlined">chevron_right</span>
</a>
</div>
</div>
</div>
</div>
</main>

<!-- Add / Edit Vehicle Modal -->
<div id="carModalOverlay" class="hidden fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-md">
<div class="bg-surface-container-lowest rounded-xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
<div class="flex items-center justify-between p-xl border-b border-outline-variant">
<h3 id="modalTitle" class="font-headline-sm text-headline-sm text-on-surface">Add New Vehicle</h3>
<button type="button" onclick="closeCarModal()" class="text-secondary hover:text-error">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<form id="carForm" method="POST" enctype="multipart/form-data" class="p-xl space-y-md">
<input type="hidden" name="action" id="formAction" value="add_car">
<input type="hidden" name="car_id" id="formCarId" value="">
<input type="hidden" name="existing_image" id="formExistingImage" value="">

<div class="grid grid-cols-1 md:grid-cols-2 gap-md">
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Brand</label>
<input type="text" name="brand" id="brand" required class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Model</label>
<input type="text" name="model" id="model" required class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Category</label>
<select name="category_id" id="category_id" class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
<option value="">Uncategorized</option>
<?php foreach ($categories as $cat): ?>
<option value="<?php echo (int) $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
<?php endforeach; ?>
</select>
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Year</label>
<input type="number" name="year" id="year" min="1980" max="2100" required class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Fuel Type</label>
<select name="fuel_type" id="fuel_type" class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
<option>Petrol</option>
<option>Diesel</option>
<option>Electric</option>
<option>Hybrid</option>
</select>
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Transmission</label>
<select name="transmission" id="transmission" class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
<option>Automatic</option>
<option>Manual</option>
</select>
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Seats</label>
<input type="number" name="seats" id="seats" min="1" max="20" required class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Price / Day (₹)</label>
<input type="number" step="0.01" name="price_per_day" id="price_per_day" min="0" required class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Registration Number</label>
<input type="text" name="registration_number" id="registration_number" required class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Status</label>
<select name="status" id="status" class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20">
<option>Available</option>
<option>Rented</option>
<option>Maintenance</option>
</select>
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Image</label>
<input type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.webp" class="w-full text-body-sm font-body-sm">
</div>
</div>
<div>
<label class="font-label-md text-label-md text-secondary block mb-xs">Description</label>
<textarea name="description" id="description" rows="3" class="w-full px-md py-sm border border-outline-variant rounded-lg font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary/20"></textarea>
</div>

<div class="flex justify-end gap-md pt-md">
<button type="button" onclick="closeCarModal()" class="px-lg py-md border border-outline-variant rounded-lg font-label-md text-label-md">Cancel</button>
<button type="submit" class="px-lg py-md bg-primary text-on-primary rounded-lg font-label-md text-label-md">Save Vehicle</button>
</div>
</form>
</div>
</div>

<script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Vehicle';
            document.getElementById('carForm').reset();
            document.getElementById('formAction').value = 'add_car';
            document.getElementById('formCarId').value = '';
            document.getElementById('formExistingImage').value = '';
            document.getElementById('carModalOverlay').classList.remove('hidden');
        }

        function openEditModal(car) {
            document.getElementById('modalTitle').textContent = 'Edit Vehicle';
            document.getElementById('formAction').value = 'edit_car';
            document.getElementById('formCarId').value = car.id;
            document.getElementById('formExistingImage').value = car.image || '';
            document.getElementById('brand').value = car.brand;
            document.getElementById('model').value = car.model;
            document.getElementById('category_id').value = car.category_id || '';
            document.getElementById('year').value = car.year;
            document.getElementById('fuel_type').value = car.fuel_type;
            document.getElementById('transmission').value = car.transmission;
            document.getElementById('seats').value = car.seats;
            document.getElementById('price_per_day').value = car.price_per_day;
            document.getElementById('registration_number').value = car.registration_number;
            document.getElementById('status').value = car.status;
            document.getElementById('description').value = car.description || '';
            document.getElementById('carModalOverlay').classList.remove('hidden');
        }

        function closeCarModal() {
            document.getElementById('carModalOverlay').classList.add('hidden');
        }

        // Simple micro-interaction for rows
        document.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('mousedown', () => {
                row.classList.add('scale-[0.995]', 'transition-transform', 'duration-75');
            });
            row.addEventListener('mouseup', () => {
                row.classList.remove('scale-[0.995]');
            });
        });
    </script>
</body></html>