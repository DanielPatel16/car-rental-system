<?php
session_start();
include "../includes/db.php";

// ---- Auth guard: only logged-in customers ----
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

// ---------------------------------------------------------------
// Handle self-service cancellation of a still-Pending booking
// ---------------------------------------------------------------
if (isset($_GET['cancel'])) {
    $bid = (int) $_GET['cancel'];
    $stmt = $conn->prepare("UPDATE bookings SET booking_status = 'Cancelled' WHERE id = ? AND user_id = ? AND booking_status = 'Pending'");
    $stmt->bind_param("ii", $bid, $user_id);
    $stmt->execute();
    $stmt->close();
    $conn->query("UPDATE bookings SET payment_status = 'Refunded' WHERE id = $bid AND user_id = $user_id AND payment_status = 'Paid'");
    header("Location: my_bookings.php");
    exit();
}

// ---------------------------------------------------------------
// Filters + pagination
// ---------------------------------------------------------------
$filterStatus = $_GET['status'] ?? 'All';
$where  = ["bookings.user_id = ?"];
$params = [$user_id];
$types  = "i";

if ($filterStatus !== '' && $filterStatus !== 'All') {
    $where[]  = "bookings.booking_status = ?";
    $params[] = $filterStatus;
    $types   .= "s";
}
$whereSql = "WHERE " . implode(" AND ", $where);

$perPage = 6;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

$baseFrom = "FROM bookings JOIN cars ON bookings.car_id = cars.id $whereSql";

$countStmt = $conn->prepare("SELECT COUNT(*) AS total $baseFrom");
$countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows  = (int) $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = max(1, (int) ceil($totalRows / $perPage));
$countStmt->close();

$sql = "SELECT bookings.*, cars.brand, cars.model, cars.image
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

$statusBadge = [
    'Confirmed' => 'bg-tertiary text-on-tertiary',
    'Completed' => 'bg-surface-container-highest text-on-surface-variant',
    'Pending'   => 'bg-secondary-container text-on-secondary-container',
    'Cancelled' => 'bg-error-container text-on-error-container',
];
$payBadge = [
    'Paid'     => 'text-tertiary',
    'Pending'  => 'text-secondary',
    'Failed'   => 'text-error',
    'Refunded' => 'text-secondary',
];

function mqs($extra) {
    $qs = $_GET;
    unset($qs['cancel']);
    foreach ($extra as $k => $v) { $qs[$k] = $v; }
    return 'my_bookings.php?' . http_build_query($qs);
}
?>
<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>DriveEase | My Bookings</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                "on-secondary": "#ffffff",
                "secondary-container": "#d2e1f7",
                "background": "#faf8ff",
                "surface-tint": "#3755c3",
                "tertiary-container": "#00563a",
                "on-primary-container": "#a8b8ff",
                "surface-container-low": "#f2f3ff",
                "surface-variant": "#dae2fd",
                "inverse-on-surface": "#eef0ff",
                "surface-container-lowest": "#ffffff",
                "on-secondary-fixed": "#0d1c2d",
                "tertiary-fixed": "#6ffbbe",
                "primary-container": "#1e40af",
                "surface-container-highest": "#dae2fd",
                "primary": "#00288e",
                "primary-fixed-dim": "#b8c4ff",
                "on-error-container": "#93000a",
                "tertiary-fixed-dim": "#4edea3",
                "outline": "#757684",
                "inverse-surface": "#283044",
                "on-secondary-container": "#556477",
                "surface-container": "#eaedff",
                "on-secondary-fixed-variant": "#39485a",
                "inverse-primary": "#b8c4ff",
                "on-background": "#131b2e",
                "on-primary": "#ffffff",
                "on-error": "#ffffff",
                "secondary-fixed-dim": "#b9c8de",
                "secondary-fixed": "#d4e4fa",
                "on-primary-fixed-variant": "#173bab",
                "error": "#ba1a1a",
                "on-surface": "#131b2e",
                "on-tertiary": "#ffffff",
                "on-primary-fixed": "#001453",
                "tertiary": "#003d27",
                "primary-fixed": "#dde1ff",
                "surface-container-high": "#e2e7ff",
                "error-container": "#ffdad6",
                "on-tertiary-fixed": "#002113",
                "surface": "#faf8ff",
                "secondary": "#516072",
                "on-tertiary-fixed-variant": "#005236",
                "surface-bright": "#faf8ff",
                "surface-dim": "#d2d9f4",
                "on-tertiary-container": "#3fd298",
                "outline-variant": "#c4c5d5",
                "on-surface-variant": "#444653"
            },
            "borderRadius": {
                "DEFAULT": "0.25rem",
                "lg": "0.5rem",
                "xl": "0.75rem",
                "full": "9999px"
            },
            "spacing": {
                "xs": "4px",
                "xl": "32px",
                "base": "4px",
                "margin-mobile": "16px",
                "margin-desktop": "32px",
                "md": "16px",
                "gutter": "24px",
                "lg": "24px",
                "sm": "8px",
                "max-width": "1440px",
                "2xl": "48px"
            },
            "fontFamily": {
                "headline-sm": ["Inter"],
                "headline-md": ["Inter"],
                "body-lg": ["Inter"],
                "label-sm": ["Inter"],
                "body-sm": ["Inter"],
                "headline-lg": ["Inter"],
                "label-md": ["Inter"],
                "headline-lg-mobile": ["Inter"],
                "body-md": ["Inter"]
            },
            "fontSize": {
                "headline-sm": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                "headline-md": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "500"}],
                "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                "label-md": ["14px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                "headline-lg-mobile": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "700"}],
                "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}]
            }
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        body { font-family: 'Inter', sans-serif; }
        .booking-card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .booking-card-hover:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
<header class="fixed top-0 w-full z-50 bg-surface border-b border-outline-variant shadow-sm h-16">
<div class="flex justify-between items-center w-full px-margin-desktop max-w-max-width mx-auto h-full">
<div class="text-headline-md font-headline-md font-bold text-primary">DriveEase</div>
<nav class="hidden md:flex items-center gap-xl">
<a class="text-on-surface-variant hover:text-primary transition-colors duration-200 text-label-md font-label-md" href="../index.php">Home</a>
<a class="text-on-surface-variant hover:text-primary transition-colors duration-200 text-label-md font-label-md" href="cars.php">Cars</a>
<a class="text-primary border-b-2 border-primary pb-1 text-label-md font-label-md" href="my_bookings.php">My Bookings</a>
</nav>
<div class="flex items-center gap-md">
<span class="text-label-md font-label-md text-on-surface-variant"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
</div>
</div>
</header>

<main class="flex-grow pt-16 max-w-max-width mx-auto w-full px-margin-desktop py-lg">

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-md mb-lg">
<div>
<h1 class="text-headline-md font-headline-md text-on-surface">My Bookings <span class="text-on-surface-variant font-normal text-body-lg">(<?php echo $totalRows; ?>)</span></h1>
</div>
<div class="flex items-center gap-sm flex-wrap">
<?php foreach (['All', 'Pending', 'Confirmed', 'Completed', 'Cancelled'] as $s):
    $active = $filterStatus === $s;
?>
<a href="<?php echo mqs(['status' => $s, 'page' => 1]); ?>" class="px-md py-2 rounded-full text-label-sm font-label-sm border <?php echo $active ? 'bg-primary text-on-primary border-primary' : 'border-outline-variant text-on-surface-variant hover:bg-surface-container-high'; ?>"><?php echo $s; ?></a>
<?php endforeach; ?>
</div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-gutter">
<?php if (empty($bookings)): ?>
<div class="col-span-full text-center py-2xl text-on-surface-variant font-body-md text-body-md">
    You don't have any bookings yet.
    <a href="cars.php" class="text-primary font-label-md hover:underline block mt-sm">Browse available cars &rarr;</a>
</div>
<?php endif; ?>
<?php foreach ($bookings as $b):
    $badge = $statusBadge[$b['booking_status']] ?? 'bg-secondary text-on-secondary';
    $payClass = $payBadge[$b['payment_status']] ?? 'text-secondary';
    $imgSrc = $b['image'] ? "../uploads/cars/" . htmlspecialchars($b['image']) : "https://placehold.co/400x300?text=" . urlencode($b['brand']);
?>
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden booking-card-hover flex flex-col shadow-sm">
<div class="relative h-40 w-full">
<img class="w-full h-full object-cover" src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($b['brand'] . ' ' . $b['model']); ?>"/>
<div class="absolute top-3 left-3 <?php echo $badge; ?> px-sm py-1 rounded-full text-label-sm font-label-sm"><?php echo htmlspecialchars($b['booking_status']); ?></div>
</div>
<div class="p-lg flex flex-col flex-grow">
<div class="flex justify-between items-start mb-xs">
<h3 class="text-headline-sm font-headline-sm text-on-surface"><?php echo htmlspecialchars($b['brand'] . ' ' . $b['model']); ?></h3>
<span class="text-label-sm font-label-sm text-on-surface-variant">#BK-<?php echo str_pad($b['id'], 4, '0', STR_PAD_LEFT); ?></span>
</div>
<div class="flex flex-col gap-xs mt-sm mb-md text-on-surface-variant">
<div class="flex items-center gap-xs">
<span class="material-symbols-outlined text-sm">calendar_today</span>
<span class="text-label-sm font-label-sm"><?php echo date('M d, Y', strtotime($b['pickup_date'])); ?> &rarr; <?php echo date('M d, Y', strtotime($b['return_date'])); ?></span>
</div>
<div class="flex items-center gap-xs">
<span class="material-symbols-outlined text-sm">location_on</span>
<span class="text-label-sm font-label-sm"><?php echo htmlspecialchars($b['pickup_location']); ?></span>
</div>
<div class="flex items-center gap-xs">
<span class="material-symbols-outlined text-sm">payments</span>
<span class="text-label-sm font-label-sm <?php echo $payClass; ?>">Payment: <?php echo htmlspecialchars($b['payment_status']); ?></span>
</div>
</div>
<div class="flex justify-between items-center mt-auto pt-md border-t border-outline-variant">
<span class="text-headline-sm font-headline-sm text-primary">₹<?php echo number_format($b['total_amount'], 0); ?></span>
<?php if ($b['booking_status'] === 'Pending'): ?>
<a href="<?php echo mqs(['cancel' => $b['id']]); ?>" onclick="return confirm('Cancel this booking?')" class="text-error text-label-sm font-label-sm hover:underline">Cancel</a>
<?php endif; ?>
</div>
</div>
</div>
<?php endforeach; ?>
</div>

<?php if ($totalPages > 1): ?>
<div class="mt-2xl flex justify-center items-center gap-sm">
<a href="<?php echo mqs(['page' => max(1, $page - 1)]); ?>" class="w-10 h-10 flex items-center justify-center rounded-full border border-outline-variant text-on-surface-variant hover:bg-surface-container <?php echo $page <= 1 ? 'pointer-events-none opacity-30' : ''; ?>">
<span class="material-symbols-outlined">chevron_left</span>
</a>
<?php for ($p = 1; $p <= $totalPages; $p++): ?>
<a href="<?php echo mqs(['page' => $p]); ?>" class="w-10 h-10 flex items-center justify-center rounded-full font-label-md <?php echo $p === $page ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-container'; ?>"><?php echo $p; ?></a>
<?php endfor; ?>
<a href="<?php echo mqs(['page' => min($totalPages, $page + 1)]); ?>" class="w-10 h-10 flex items-center justify-center rounded-full border border-outline-variant text-on-surface-variant hover:bg-surface-container <?php echo $page >= $totalPages ? 'pointer-events-none opacity-30' : ''; ?>">
<span class="material-symbols-outlined">chevron_right</span>
</a>
</div>
<?php endif; ?>

</main>

<footer class="w-full mt-auto bg-surface-container-highest border-t border-outline-variant">
<div class="w-full py-lg px-margin-desktop flex flex-col md:flex-row justify-between items-center max-w-max-width mx-auto gap-md">
<span class="text-label-sm font-label-sm text-on-surface-variant">© 2024 DriveEase Car Rental Systems. All rights reserved.</span>
</div>
</footer>
</body></html>