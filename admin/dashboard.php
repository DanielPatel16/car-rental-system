<?php
session_start();
include "../includes/db.php";

// ---- Auth guard: only logged-in admins may access this page ----
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// ---------------------------------------------------------------
// Handle booking status actions from the Recent Bookings table
// (?action=confirm|cancel|complete&id=X) — same behaviour as bookings.php
// ---------------------------------------------------------------
if (isset($_GET['action'], $_GET['id'])) {
    $id  = (int) $_GET['id'];
    $map = ['confirm' => 'Confirmed', 'cancel' => 'Cancelled', 'complete' => 'Completed'];

    if (isset($map[$_GET['action']])) {
        $newStatus = $map[$_GET['action']];
        $stmt = $conn->prepare("UPDATE bookings SET booking_status = ? WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $id);
        $stmt->execute();
        $stmt->close();

        if ($newStatus === 'Cancelled') {
            $conn->query("UPDATE bookings SET payment_status = 'Refunded' WHERE id = $id AND payment_status = 'Paid'");
        }
    }

    header("Location: dashboard.php");
    exit();
}

/* ------------------------------------------------------------------
   DASHBOARD DATA — pulled from: bookings, cars, categories, payments, users
   ------------------------------------------------------------------ */

// ---- Total Revenue (all-time, from paid bookings) ----
$totalRevenue = (float) ($conn->query("SELECT COALESCE(SUM(total_amount),0) AS total FROM bookings WHERE payment_status = 'Paid'")->fetch_assoc()['total'] ?? 0);

// ---- Active Rentals (cars currently marked Rented) ----
$activeRentals = (int) $conn->query("SELECT COUNT(*) AS cnt FROM cars WHERE status = 'Rented'")->fetch_assoc()['cnt'];

// ---- Pending Bookings ----
$pendingBookings = (int) $conn->query("SELECT COUNT(*) AS cnt FROM bookings WHERE booking_status = 'Pending'")->fetch_assoc()['cnt'];

// ---- Total Customers (users with role = user) ----
$totalCustomers = (int) $conn->query("SELECT COUNT(*) AS cnt FROM users WHERE role = 'user'")->fetch_assoc()['cnt'];

// ---- New customers who joined this calendar month ----
$newCustomersThisMonth = (int) $conn->query(
    "SELECT COUNT(*) AS cnt FROM users
     WHERE role = 'user' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())"
)->fetch_assoc()['cnt'];

// ---- Fleet Status breakdown (Available / Rented / Maintenance) ----
$fleetStatus = ['Available' => 0, 'Rented' => 0, 'Maintenance' => 0];
$res = $conn->query("SELECT status, COUNT(*) AS cnt FROM cars GROUP BY status");
while ($row = $res->fetch_assoc()) {
    $fleetStatus[$row['status']] = (int) $row['cnt'];
}
$totalFleet = array_sum($fleetStatus);
$occupancyRate  = $totalFleet ? round(($activeRentals / $totalFleet) * 100) : 0;
$pctRented      = $totalFleet ? round(($fleetStatus['Rented'] / $totalFleet) * 100, 1)      : 0;
$pctAvailable   = $totalFleet ? round(($fleetStatus['Available'] / $totalFleet) * 100, 1)   : 0;
$pctMaintenance = $totalFleet ? round(($fleetStatus['Maintenance'] / $totalFleet) * 100, 1) : 0;

// ---- Revenue Growth Chart: last 6 months of paid bookings ----
$monthlyRevenue = [];
for ($i = 5; $i >= 0; $i--) {
    $monthStart = date('Y-m-01', strtotime("-$i months"));
    $monthEnd   = date('Y-m-t', strtotime("-$i months"));
    $label      = strtoupper(date('M', strtotime("-$i months")));
    $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount),0) AS total FROM bookings WHERE payment_status = 'Paid' AND created_at BETWEEN ? AND ?");
    $endDateTime = $monthEnd . ' 23:59:59';
    $stmt->bind_param("ss", $monthStart, $endDateTime);
    $stmt->execute();
    $total = (float) $stmt->get_result()->fetch_assoc()['total'];
    $monthlyRevenue[] = ['label' => $label, 'total' => $total];
    $stmt->close();
}
$maxMonthly   = max(1, max(array_column($monthlyRevenue, 'total')));
$chartCeiling = ceil(($maxMonthly * 1.15) / 25000) * 25000;
$chartCeiling = $chartCeiling > 0 ? $chartCeiling : 25000;

// Month-over-month revenue trend (current month vs previous month)
$currentMonthRevenue  = $monthlyRevenue[5]['total'];
$previousMonthRevenue = $monthlyRevenue[4]['total'];
if ($previousMonthRevenue > 0) {
    $revenueTrendPct = round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1);
} else {
    $revenueTrendPct = $currentMonthRevenue > 0 ? 100 : 0;
}

// ---- Recent Bookings (joined with users + cars) ----
$recentBookings = [];
$sql = "SELECT b.id, b.pickup_date, b.return_date, b.total_amount, b.booking_status,
               u.user_name, c.brand, c.model
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN cars  c ON b.car_id  = c.id
        ORDER BY b.created_at DESC
        LIMIT 5";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $recentBookings[] = $row;
}

// ---- Maintenance Alerts (cars currently in Maintenance) ----
$maintenanceCars = [];
$res = $conn->query("SELECT brand, model, registration_number FROM cars WHERE status = 'Maintenance' ORDER BY created_at DESC LIMIT 3");
while ($row = $res->fetch_assoc()) {
    $maintenanceCars[] = $row;
}

// ---- Recently joined customers ----
$recentCustomers = [];
$res = $conn->query("SELECT user_name, email, created_at FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT 4");
while ($row = $res->fetch_assoc()) {
    $recentCustomers[] = $row;
}

// Helper: initials from a name, e.g. "James Smith" -> "JS"
function initials_from_name(string $name): string {
    $parts   = preg_split('/\s+/', trim($name));
    $letters = array_map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)), array_slice($parts, 0, 2));
    return implode('', $letters) ?: '?';
}

// Helper: relative "time ago" for the recent-signups list
function time_ago(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 2592000) return floor($diff / 86400) . 'd ago';
    return date('M d, Y', strtotime($datetime));
}

// Status badge classes — same mapping used on bookings.php for visual consistency
$statusBadge = [
    'Confirmed' => 'bg-tertiary-container/10 text-tertiary-container border border-tertiary-container/20',
    'Completed' => 'bg-surface-container-highest text-secondary border border-outline-variant',
    'Pending'   => 'bg-error-container/10 text-error border border-error-container/20',
    'Cancelled' => 'bg-surface-container-high text-secondary border border-outline-variant opacity-70 line-through',
];
?>
<!DOCTYPE html><html class="light" lang="en"><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>DriveEase | Admin Dashboard</title>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC;
        }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        .chart-bar { transition: height 1s ease-out; }
    </style>
</head>
<body class="text-on-background">
<!-- Sidebar Container -->
<?php include "sidebar.php"; ?>
<!-- Main Canvas -->
<main class="lg:pl-64 min-h-screen">
<!-- Top Bar -->
<header class="h-16 bg-surface dark:bg-surface-container border-b border-outline-variant dark:border-outline flex items-center justify-between px-4 sm:px-margin-desktop sticky top-0 z-30 shadow-sm gap-2">
<h2 class="text-base sm:text-headline-sm font-headline-sm text-primary truncate pl-12 lg:pl-0">Dashboard Overview</h2>
<div class="flex items-center gap-2 sm:gap-lg shrink-0">
<form id="dashSearchForm" method="GET" action="bookings.php" class="relative group hidden md:block">
<input class="bg-surface-container-low border border-outline-variant rounded-full px-md py-xs pl-10 text-body-sm focus:ring-2 focus:ring-primary focus:outline-none w-48 lg:w-64 transition-all group-focus-within:w-64 lg:group-focus-within:w-80" placeholder="Search bookings by customer, car..." type="text" name="q">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant opacity-60">search</span>
</form>
<a href="bookings.php" class="p-1 rounded-full hover:bg-surface-container-high transition-colors md:hidden">
<span class="material-symbols-outlined text-primary">search</span>
</a>
<button class="relative p-xs rounded-full hover:bg-surface-container-high transition-colors">
<span class="material-symbols-outlined text-primary">notifications</span>
<?php if ($pendingBookings > 0): ?>
<span class="absolute top-1 right-1 w-2 h-2 bg-error rounded-full"></span>
<?php endif; ?>
</button>
<div class="flex items-center gap-sm cursor-pointer border-l border-outline-variant pl-2 sm:pl-lg">
<div class="text-right hidden sm:block">
<p class="text-label-md font-label-md text-on-surface"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></p>
<p class="text-[10px] text-on-surface-variant font-medium">ADMIN ACCESS</p>
</div>
<div class="w-10 h-10 rounded-full border border-primary bg-secondary-container flex items-center justify-center text-primary font-bold text-sm">
<?php echo htmlspecialchars(initials_from_name($_SESSION['user_name'] ?? 'Admin')); ?>
</div>
</div>
</div>
</header>
<div class="p-4 sm:p-6 lg:p-margin-desktop max-w-max-width mx-auto pb-24 lg:pb-margin-desktop">
<!-- Quick Stats Bento Grid -->
<section class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-gutter mb-6 sm:mb-xl">
<!-- Total Revenue -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-3 sm:p-lg shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)] group hover:border-primary transition-all">
<div class="flex justify-between items-start mb-sm">
<div class="p-sm bg-secondary-container rounded-lg text-primary">
<span class="material-symbols-outlined">payments</span>
</div>
<span class="<?php echo $revenueTrendPct >= 0 ? 'text-tertiary' : 'text-error'; ?> font-label-sm flex items-center gap-xs">
<span class="material-symbols-outlined text-[14px]"><?php echo $revenueTrendPct >= 0 ? 'trending_up' : 'trending_down'; ?></span> <?php echo ($revenueTrendPct >= 0 ? '+' : '') . $revenueTrendPct; ?>%
                        </span>
</div>
<p class="text-label-sm font-label-sm text-on-surface-variant mb-xs">TOTAL REVENUE</p>
<h3 class="text-lg sm:text-headline-md font-headline-md text-on-surface">₹<?php echo number_format($totalRevenue, 2); ?></h3>
<p class="text-[11px] text-on-surface-variant mt-xs">vs last month</p>
</div>
<!-- Active Rentals -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-3 sm:p-lg shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)] group hover:border-primary transition-all">
<div class="flex justify-between items-start mb-sm">
<div class="p-sm bg-primary-container rounded-lg text-on-primary">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">directions_car</span>
</div>
<span class="text-tertiary font-label-sm flex items-center gap-xs">
<?php echo $occupancyRate; ?>% occupied
                        </span>
</div>
<p class="text-label-sm font-label-sm text-on-surface-variant mb-xs">ACTIVE RENTALS</p>
<h3 class="text-headline-md font-headline-md text-on-surface"><?php echo $activeRentals; ?></h3>
<p class="text-[11px] text-on-surface-variant mt-xs"><?php echo $activeRentals; ?> of <?php echo $totalFleet; ?> vehicles</p>
</div>
<!-- Pending Bookings -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-3 sm:p-lg shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)] group hover:border-primary transition-all">
<div class="flex justify-between items-start mb-sm">
<div class="p-sm bg-tertiary-fixed rounded-lg text-on-tertiary-fixed-variant">
<span class="material-symbols-outlined">pending_actions</span>
</div>
<?php if ($pendingBookings > 0): ?>
<span class="text-error font-label-sm flex items-center gap-xs">
<span class="material-symbols-outlined text-[14px]">warning</span> Action Req.
                        </span>
<?php endif; ?>
</div>
<p class="text-label-sm font-label-sm text-on-surface-variant mb-xs">PENDING BOOKINGS</p>
<h3 class="text-headline-md font-headline-md text-on-surface"><?php echo $pendingBookings; ?></h3>
<a href="bookings.php?status=Pending" class="text-[11px] text-primary hover:underline mt-xs inline-block">Review requests →</a>
</div>
<!-- Total Customers -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-3 sm:p-lg shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)] group hover:border-primary transition-all">
<div class="flex justify-between items-start mb-sm">
<div class="p-sm bg-surface-container-highest rounded-lg text-secondary">
<span class="material-symbols-outlined">group</span>
</div>
<?php if ($newCustomersThisMonth > 0): ?>
<span class="text-tertiary font-label-sm flex items-center gap-xs">
<span class="material-symbols-outlined text-[14px]">trending_up</span> +<?php echo $newCustomersThisMonth; ?>
                        </span>
<?php endif; ?>
</div>
<p class="text-label-sm font-label-sm text-on-surface-variant mb-xs">TOTAL CUSTOMERS</p>
<h3 class="text-headline-md font-headline-md text-on-surface"><?php echo number_format($totalCustomers); ?></h3>
<p class="text-[11px] text-on-surface-variant mt-xs">Lifetime members</p>
</div>
</section>
<!-- Main Charts & Tables Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-gutter">
<!-- Revenue Growth Chart & Recent Bookings (Left/Middle Column span) -->
<div class="lg:col-span-2 space-y-gutter">
<!-- Revenue Growth Chart Card -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)]">
<div class="flex justify-between items-center mb-lg">
<div>
<h4 class="text-headline-sm font-headline-sm text-on-surface">Revenue Growth</h4>
<p class="text-body-sm font-body-sm text-on-surface-variant">Monthly revenue from paid bookings</p>
</div>
<span class="bg-surface-container-low border border-outline-variant rounded-lg text-label-md font-label-md px-md py-xs text-on-surface-variant">Last 6 Months</span>
</div>
<div class="h-48 sm:h-64 w-full flex items-end gap-md px-md pt-lg relative overflow-x-auto">
<div class="absolute left-0 h-full flex flex-col justify-between text-[10px] text-on-surface-variant font-bold">
<span class=""><?php echo number_format($chartCeiling); ?></span>
<span class=""><?php echo number_format($chartCeiling * 0.75); ?></span>
<span class=""><?php echo number_format($chartCeiling * 0.5); ?></span>
<span class=""><?php echo number_format($chartCeiling * 0.25); ?></span>
<span class="">0</span>
</div>
<div class="absolute inset-0 flex flex-col justify-between py-xs pointer-events-none ml-8">
<div class="border-t border-outline-variant border-dashed w-full h-0 opacity-40"></div>
<div class="border-t border-outline-variant border-dashed w-full h-0 opacity-40"></div>
<div class="border-t border-outline-variant border-dashed w-full h-0 opacity-40"></div>
<div class="border-t border-outline-variant border-dashed w-full h-0 opacity-40"></div>
<div class="border-t border-outline-variant w-full h-0"></div>
</div>
<div class="flex-grow ml-8 h-full flex items-end justify-between z-10">
<?php
$lastIndex = count($monthlyRevenue) - 1;
foreach ($monthlyRevenue as $i => $m):
    $heightPct = max(2, round(($m['total'] / $chartCeiling) * 100));
    $barClasses = ($i === $lastIndex) ? 'bg-primary' : 'bg-secondary-container hover:bg-primary';
?>
<div class="chart-bar w-6 sm:w-12 <?php echo $barClasses; ?> rounded-t-lg transition-colors cursor-pointer" style="height: <?php echo $heightPct; ?>%;" title="<?php echo $m['label'] . ': ₹' . number_format($m['total'], 2); ?>"></div>
<?php endforeach; ?>
</div>
</div>
<div class="flex justify-between ml-16 mt-sm text-[10px] text-on-surface-variant font-bold">
<?php foreach ($monthlyRevenue as $m): ?>
<span class=""><?php echo $m['label']; ?></span>
<?php endforeach; ?>
</div>
</div>
<!-- Recent Bookings Table Card -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)] overflow-hidden">
<div class="p-lg border-b border-outline-variant flex justify-between items-center">
<h4 class="text-headline-sm font-headline-sm text-on-surface">Recent Bookings</h4>
<a href="bookings.php" class="text-primary font-label-md hover:underline">View All</a>
</div>
<div class="overflow-x-auto -mx-1 px-1">
<table class="w-full min-w-[640px] text-left">
<thead class="bg-surface-container-low text-label-sm font-label-sm text-on-surface-variant uppercase tracking-wider">
<tr>
<th class="px-3 sm:px-lg py-2 sm:py-md">Customer</th>
<th class="px-3 sm:px-lg py-2 sm:py-md">Vehicle</th>
<th class="px-3 sm:px-lg py-2 sm:py-md">Date Range</th>
<th class="px-3 sm:px-lg py-2 sm:py-md">Amount</th>
<th class="px-3 sm:px-lg py-2 sm:py-md">Status</th>
<th class="px-3 sm:px-lg py-2 sm:py-md text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/30 text-body-sm">
<?php if (empty($recentBookings)): ?>
<tr>
<td class="px-3 sm:px-lg py-6 text-center text-on-surface-variant" colspan="6">No bookings yet.</td>
</tr>
<?php else: foreach ($recentBookings as $bk):
    $badgeClass = $statusBadge[$bk['booking_status']] ?? 'bg-surface-container-high text-on-surface-variant';
?>
<tr class="hover:bg-primary-container/5 transition-colors cursor-default">
<td class="px-3 sm:px-lg py-2 sm:py-md">
<div class="flex items-center gap-sm">
<div class="w-8 h-8 rounded-full bg-secondary-container text-primary flex items-center justify-center font-bold text-xs"><?php echo htmlspecialchars(initials_from_name($bk['user_name'])); ?></div>
<span class="font-medium"><?php echo htmlspecialchars($bk['user_name']); ?></span>
</div>
</td>
<td class="px-3 sm:px-lg py-2 sm:py-md"><?php echo htmlspecialchars($bk['brand'] . ' ' . $bk['model']); ?></td>
<td class="px-3 sm:px-lg py-2 sm:py-md"><?php echo date('M d', strtotime($bk['pickup_date'])) . ' - ' . date('M d', strtotime($bk['return_date'])); ?></td>
<td class="px-3 sm:px-lg py-2 sm:py-md font-semibold">₹<?php echo number_format($bk['total_amount'], 2); ?></td>
<td class="px-3 sm:px-lg py-2 sm:py-md">
<span class="px-sm py-1 <?php echo $badgeClass; ?> rounded-full text-[10px] font-bold"><?php echo strtoupper($bk['booking_status']); ?></span>
</td>
<td class="px-3 sm:px-lg py-2 sm:py-md text-right">
<div class="flex justify-end gap-xs">
<?php if ($bk['booking_status'] === 'Pending'): ?>
<a href="dashboard.php?action=confirm&id=<?php echo (int) $bk['id']; ?>" title="Confirm" class="p-1 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">check_circle</span></a>
<a href="dashboard.php?action=cancel&id=<?php echo (int) $bk['id']; ?>" onclick="return confirm('Cancel this booking?')" title="Cancel" class="p-1 hover:text-error transition-colors"><span class="material-symbols-outlined text-[20px]">cancel</span></a>
<?php elseif ($bk['booking_status'] === 'Confirmed'): ?>
<a href="dashboard.php?action=complete&id=<?php echo (int) $bk['id']; ?>" title="Mark Completed" class="p-1 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">task_alt</span></a>
<a href="dashboard.php?action=cancel&id=<?php echo (int) $bk['id']; ?>" onclick="return confirm('Cancel this booking?')" title="Cancel" class="p-1 hover:text-error transition-colors"><span class="material-symbols-outlined text-[20px]">cancel</span></a>
<?php else: ?>
<span class="text-on-surface-variant text-[11px] italic">No actions</span>
<?php endif; ?>
</div>
</td>
</tr>
<?php endforeach; endif; ?>
</tbody>
</table>
</div>
</div>
</div>
<!-- Right Sidebar Content -->
<div class="space-y-gutter">
<!-- Fleet Status Summary -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)]">
<h4 class="text-headline-sm font-headline-sm text-on-surface mb-md">Fleet Status</h4>
<div class="relative h-36 w-36 sm:h-48 sm:w-48 mx-auto mb-lg">
<svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
<circle class="stroke-surface-container-high" cx="18" cy="18" fill="none" r="16" stroke-width="3"></circle>
<circle class="stroke-primary" cx="18" cy="18" fill="none" r="16" stroke-dasharray="<?php echo $pctRented; ?>, 100" stroke-width="3"></circle>
<circle class="stroke-tertiary-fixed-dim" cx="18" cy="18" fill="none" r="16" stroke-dasharray="<?php echo $pctAvailable; ?>, 100" stroke-dashoffset="-<?php echo $pctRented; ?>" stroke-width="3"></circle>
<circle class="stroke-error" cx="18" cy="18" fill="none" r="16" stroke-dasharray="<?php echo $pctMaintenance; ?>, 100" stroke-dashoffset="-<?php echo $pctRented + $pctAvailable; ?>" stroke-width="3"></circle>
</svg>
<div class="absolute inset-0 flex flex-col items-center justify-center">
<span class="text-headline-md font-headline-md text-on-surface"><?php echo $totalFleet; ?></span>
<span class="text-[10px] font-bold text-on-surface-variant uppercase">Total Fleet</span>
</div>
</div>
<div class="space-y-sm">
<div class="flex items-center justify-between text-body-sm">
<div class="flex items-center gap-sm">
<span class="w-3 h-3 rounded-full bg-primary"></span>
<span class="">Rented</span>
</div>
<span class="font-bold"><?php echo $fleetStatus['Rented']; ?></span>
</div>
<div class="flex items-center justify-between text-body-sm">
<div class="flex items-center gap-sm">
<span class="w-3 h-3 rounded-full bg-tertiary-fixed-dim"></span>
<span class="">Available</span>
</div>
<span class="font-bold"><?php echo $fleetStatus['Available']; ?></span>
</div>
<div class="flex items-center justify-between text-body-sm">
<div class="flex items-center gap-sm">
<span class="w-3 h-3 rounded-full bg-error"></span>
<span class="">Maintenance</span>
</div>
<span class="font-bold"><?php echo $fleetStatus['Maintenance']; ?></span>
</div>
</div>
<a href="cars.php" class="w-full mt-lg py-sm border border-primary text-primary font-label-md rounded-lg hover:bg-primary-container/5 transition-colors flex items-center justify-center">
                            Manage Fleet Details
                        </a>
</div>
<!-- Maintenance Alerts -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)]">
<div class="flex justify-between items-center mb-md">
<h4 class="text-headline-sm font-headline-sm text-on-surface">Maintenance Alerts</h4>
<?php if (!empty($maintenanceCars)): ?>
<a href="cars.php?status=Maintenance" class="text-primary font-label-sm hover:underline">View All</a>
<?php endif; ?>
</div>
<div class="space-y-md">
<?php if (empty($maintenanceCars)): ?>
<p class="text-body-sm text-on-surface-variant">No vehicles currently in maintenance.</p>
<?php else: foreach ($maintenanceCars as $idx => $mc): ?>
<div class="flex gap-md<?php echo $idx > 0 ? ' border-t border-outline-variant/30 pt-md' : ''; ?>">
<div class="<?php echo $idx === 0 ? 'text-error' : 'text-secondary'; ?> mt-1">
<span class="material-symbols-outlined"><?php echo $idx === 0 ? 'warning' : 'build'; ?></span>
</div>
<div>
<p class="text-label-md font-label-md text-on-surface">Vehicle In Maintenance</p>
<p class="text-body-sm text-on-surface-variant"><?php echo htmlspecialchars($mc['brand'] . ' ' . $mc['model']); ?> (Plate: <?php echo htmlspecialchars($mc['registration_number']); ?>)</p>
</div>
</div>
<?php endforeach; endif; ?>
</div>
</div>
<!-- Recently Joined Customers -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)]">
<div class="flex justify-between items-center mb-md">
<h4 class="text-headline-sm font-headline-sm text-on-surface">Recently Joined</h4>
<a href="users.php" class="text-primary font-label-sm hover:underline">View All</a>
</div>
<div class="space-y-md">
<?php if (empty($recentCustomers)): ?>
<p class="text-body-sm text-on-surface-variant">No customers yet.</p>
<?php else: foreach ($recentCustomers as $cust): ?>
<div class="flex items-center gap-sm">
<div class="w-8 h-8 rounded-full bg-surface-container-highest text-on-surface-variant flex items-center justify-center font-bold text-xs shrink-0"><?php echo htmlspecialchars(initials_from_name($cust['user_name'])); ?></div>
<div class="min-w-0 flex-1">
<p class="text-label-md font-label-md text-on-surface truncate"><?php echo htmlspecialchars($cust['user_name']); ?></p>
<p class="text-[11px] text-on-surface-variant truncate"><?php echo htmlspecialchars($cust['email']); ?></p>
</div>
<span class="text-[11px] text-on-surface-variant shrink-0"><?php echo time_ago($cust['created_at']); ?></span>
</div>
<?php endforeach; endif; ?>
</div>
</div>
</div>
</div>
</div>
</main>
<script>
        // Simple animation trigger for chart bars
        window.addEventListener('DOMContentLoaded', () => {
            const bars = document.querySelectorAll('.chart-bar');
            bars.forEach(bar => {
                const targetHeight = bar.style.height;
                bar.style.height = '0%';
                setTimeout(() => {
                    bar.style.height = targetHeight;
                }, 200);
            });
        });
    </script>
</body></html>
<?php $conn->close(); ?>