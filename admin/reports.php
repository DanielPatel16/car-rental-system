<?php
session_start();
include "../includes/db.php";

// ---- Auth guard: only logged-in admins may access this page ----
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

/* ------------------------------------------------------------------
   REPORT DATA — pulled from: bookings, cars, categories, payments, users
   ------------------------------------------------------------------ */

// ---- Top-line KPIs ----
$totalRevenue = (float) ($conn->query("SELECT COALESCE(SUM(total_amount),0) AS total FROM bookings WHERE payment_status = 'Paid'")->fetch_assoc()['total'] ?? 0);
$totalBookings = (int) $conn->query("SELECT COUNT(*) AS c FROM bookings")->fetch_assoc()['c'];
$completedBookings = (int) $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE booking_status = 'Completed'")->fetch_assoc()['c'];
$cancelledBookings = (int) $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE booking_status = 'Cancelled'")->fetch_assoc()['c'];
$avgBookingValue = (float) ($conn->query("SELECT COALESCE(AVG(total_amount),0) AS avg_val FROM bookings")->fetch_assoc()['avg_val'] ?? 0);
$cancellationRate = $totalBookings ? round(($cancelledBookings / $totalBookings) * 100, 1) : 0;

// ---- Monthly revenue: last 12 months of paid bookings ----
$monthlyRevenue = [];
for ($i = 11; $i >= 0; $i--) {
    $monthStart = date('Y-m-01', strtotime("-$i months"));
    $monthEnd   = date('Y-m-t', strtotime("-$i months"));
    $label      = date('M', strtotime("-$i months"));
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

// ---- CSV export of the 12-month revenue series (?export=csv) ----
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="revenue_report.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Month', 'Revenue (INR)']);
    foreach ($monthlyRevenue as $m) {
        fputcsv($out, [$m['label'], number_format($m['total'], 2, '.', '')]);
    }
    fclose($out);
    $conn->close();
    exit();
}

// ---- Bookings by status ----
$bookingsByStatus = ['Pending' => 0, 'Confirmed' => 0, 'Completed' => 0, 'Cancelled' => 0];
$res = $conn->query("SELECT booking_status, COUNT(*) AS c FROM bookings GROUP BY booking_status");
while ($row = $res->fetch_assoc()) {
    $bookingsByStatus[$row['booking_status']] = (int) $row['c'];
}

// ---- Top 5 vehicles by revenue ----
$topVehicles = [];
$sql = "SELECT c.brand, c.model, c.registration_number,
               COUNT(b.id) AS trips,
               COALESCE(SUM(b.total_amount),0) AS revenue
        FROM cars c
        JOIN bookings b ON b.car_id = c.id AND b.payment_status = 'Paid'
        GROUP BY c.id
        ORDER BY revenue DESC
        LIMIT 5";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $topVehicles[] = $row;
}
$maxVehicleRevenue = max(1, ...array_map(fn($v) => (float) $v['revenue'], $topVehicles ?: [['revenue' => 1]]));

// ---- Payment method breakdown (successful payments) ----
$paymentMethods = [];
$res = $conn->query("SELECT payment_method, COUNT(*) AS c, COALESCE(SUM(amount),0) AS total FROM payments WHERE status = 'Success' GROUP BY payment_method ORDER BY total DESC");
while ($row = $res->fetch_assoc()) {
    $paymentMethods[] = $row;
}
$totalPaymentAmount = max(1, array_sum(array_column($paymentMethods, 'total')));

// ---- Revenue & bookings by vehicle category ----
$categoryPerformance = [];
$sql = "SELECT cat.name,
               COUNT(b.id) AS bookings_count,
               COALESCE(SUM(b.total_amount),0) AS revenue
        FROM categories cat
        LEFT JOIN cars c ON c.category_id = cat.id
        LEFT JOIN bookings b ON b.car_id = c.id AND b.payment_status = 'Paid'
        GROUP BY cat.id
        ORDER BY revenue DESC";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $categoryPerformance[] = $row;
}
$maxCategoryRevenue = max(1, ...array_map(fn($c) => (float) $c['revenue'], $categoryPerformance ?: [['revenue' => 1]]));

// Helper: initials from a name
function initials_from_name(string $name): string {
    $parts   = preg_split('/\s+/', trim($name));
    $letters = array_map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)), array_slice($parts, 0, 2));
    return implode('', $letters) ?: '?';
}
?>
<!DOCTYPE html><html class="light" lang="en"><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>DriveEase Admin - Reports &amp; Analytics</title>
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
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f2f3ff; }
        ::-webkit-scrollbar-thumb { background: #c4c5d5; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #757684; }

        .chart-bar { transition: height 1s ease-out; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 1);
        }
    </style>
</head>
<body class="bg-surface text-on-surface">
<!-- SideNavBar -->
<?php include "sidebar.php"; ?>
<!-- TopNavBar -->
<header class="fixed top-0 right-0 w-[calc(100%-16rem)] h-16 bg-surface dark:bg-on-background border-b border-outline-variant dark:border-outline flex justify-between items-center px-xl w-full z-20">
<div>
<p class="font-label-md text-label-md text-on-surface">Reports &amp; Analytics</p>
<p class="font-label-sm text-label-sm text-secondary">Performance overview across your fleet</p>
</div>
<div class="flex items-center gap-lg">
<a href="reports.php?export=csv" class="flex items-center gap-xs bg-primary text-on-primary font-label-md text-label-md py-sm px-lg rounded-lg shadow-sm hover:shadow-md transition-all active:scale-95">
<span class="material-symbols-outlined text-[18px]">download</span>
Export CSV
</a>
<button class="text-secondary hover:text-primary transition-colors relative">
<span class="material-symbols-outlined">notifications</span>
</button>
<button class="text-secondary hover:text-primary transition-colors">
<span class="material-symbols-outlined">help_outline</span>
</button>
<div class="h-8 w-px bg-outline-variant"></div>
<div class="flex items-center gap-md">
<div class="text-right hidden sm:block">
<p class="font-label-md text-label-md text-on-surface"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></p>
<p class="font-label-sm text-label-sm text-secondary">Admin Access</p>
</div>
<div class="w-10 h-10 rounded-full border border-outline-variant bg-secondary-container flex items-center justify-center text-primary font-bold text-sm">
<?php echo htmlspecialchars(initials_from_name($_SESSION['user_name'] ?? 'Admin')); ?>
</div>
</div>
</div>
</header>
<!-- Main Content Canvas -->
<main class="ml-64 pt-16 min-h-screen">
<div class="p-xl max-w-max-width mx-auto">

<div class="flex flex-col md:flex-row md:items-end justify-between gap-lg mb-xl">
<div>
<h2 class="font-headline-lg text-headline-lg text-primary">Reports &amp; Analytics</h2>
<p class="font-body-md text-body-md text-secondary mt-xs">Revenue, bookings, and fleet performance, calculated live from your data.</p>
</div>
</div>

<!-- KPI Bento -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-lg mb-xl">
<div class="glass-panel p-lg rounded-xl">
<p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Total Revenue</p>
<h3 class="font-headline-md text-headline-md text-primary mt-xs">₹<?php echo number_format($totalRevenue, 2); ?></h3>
<p class="font-label-sm text-label-sm text-secondary mt-xs">From paid bookings</p>
</div>
<div class="glass-panel p-lg rounded-xl">
<p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Total Bookings</p>
<h3 class="font-headline-md text-headline-md text-primary mt-xs"><?php echo number_format($totalBookings); ?></h3>
<p class="font-label-sm text-label-sm text-secondary mt-xs"><?php echo $completedBookings; ?> completed</p>
</div>
<div class="glass-panel p-lg rounded-xl">
<p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Avg. Booking Value</p>
<h3 class="font-headline-md text-headline-md text-primary mt-xs">₹<?php echo number_format($avgBookingValue, 2); ?></h3>
<p class="font-label-sm text-label-sm text-secondary mt-xs">Across all bookings</p>
</div>
<div class="glass-panel p-lg rounded-xl">
<p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Cancellation Rate</p>
<h3 class="font-headline-md text-headline-md <?php echo $cancellationRate > 15 ? 'text-error' : 'text-primary'; ?> mt-xs"><?php echo $cancellationRate; ?>%</h3>
<p class="font-label-sm text-label-sm text-secondary mt-xs"><?php echo $cancelledBookings; ?> of <?php echo $totalBookings; ?> bookings</p>
</div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-lg mb-xl">
<!-- Revenue Trend (12 months) -->
<div class="lg:col-span-2 bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm">
<div class="flex justify-between items-center mb-lg">
<div>
<h4 class="font-headline-sm text-headline-sm text-on-surface">Revenue Trend</h4>
<p class="font-body-sm text-body-sm text-secondary">Last 12 months, paid bookings only</p>
</div>
</div>
<div class="h-56 w-full flex items-end gap-sm px-md pt-lg relative overflow-x-auto">
<div class="absolute left-0 h-full flex flex-col justify-between text-[10px] text-secondary font-bold">
<span><?php echo number_format($chartCeiling); ?></span>
<span><?php echo number_format($chartCeiling * 0.5); ?></span>
<span>0</span>
</div>
<div class="absolute inset-0 flex flex-col justify-between py-xs pointer-events-none ml-10">
<div class="border-t border-outline-variant border-dashed w-full h-0 opacity-40"></div>
<div class="border-t border-outline-variant border-dashed w-full h-0 opacity-40"></div>
<div class="border-t border-outline-variant w-full h-0"></div>
</div>
<div class="flex-grow ml-10 h-full flex items-end justify-between z-10 gap-1">
<?php
$lastIndex = count($monthlyRevenue) - 1;
foreach ($monthlyRevenue as $i => $m):
    $heightPct = max(2, round(($m['total'] / $chartCeiling) * 100));
    $barClasses = ($i === $lastIndex) ? 'bg-primary' : 'bg-secondary-container hover:bg-primary';
?>
<div class="chart-bar flex-1 <?php echo $barClasses; ?> rounded-t-lg transition-colors cursor-pointer" style="height: <?php echo $heightPct; ?>%;" title="<?php echo $m['label'] . ': ₹' . number_format($m['total'], 2); ?>"></div>
<?php endforeach; ?>
</div>
</div>
<div class="flex justify-between ml-10 mt-sm text-[10px] text-secondary font-bold">
<?php foreach ($monthlyRevenue as $m): ?>
<span><?php echo $m['label']; ?></span>
<?php endforeach; ?>
</div>
</div>

<!-- Bookings by Status -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm">
<h4 class="font-headline-sm text-headline-sm text-on-surface mb-md">Bookings by Status</h4>
<div class="space-y-md">
<?php
$statusColors = [
    'Confirmed' => 'bg-tertiary-container',
    'Completed' => 'bg-primary',
    'Pending'   => 'bg-secondary-fixed-dim',
    'Cancelled' => 'bg-error',
];
foreach ($bookingsByStatus as $status => $count):
    $pct = $totalBookings ? round(($count / $totalBookings) * 100) : 0;
?>
<div>
<div class="flex justify-between items-center mb-xs">
<span class="font-label-md text-label-md text-on-surface"><?php echo $status; ?></span>
<span class="font-label-sm text-label-sm text-secondary"><?php echo $count; ?> (<?php echo $pct; ?>%)</span>
</div>
<div class="w-full h-2 bg-surface-container-high rounded-full overflow-hidden">
<div class="h-full <?php echo $statusColors[$status] ?? 'bg-secondary'; ?> rounded-full" style="width: <?php echo $pct; ?>%;"></div>
</div>
</div>
<?php endforeach; ?>
</div>
</div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-lg mb-xl">
<!-- Top Vehicles -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
<div class="p-lg border-b border-outline-variant">
<h4 class="font-headline-sm text-headline-sm text-on-surface">Top Performing Vehicles</h4>
<p class="font-body-sm text-body-sm text-secondary">Ranked by revenue from paid bookings</p>
</div>
<div class="p-lg space-y-md">
<?php if (empty($topVehicles)): ?>
<p class="font-body-sm text-body-sm text-secondary">No paid bookings yet.</p>
<?php else: foreach ($topVehicles as $v):
    $barPct = round(((float) $v['revenue'] / $maxVehicleRevenue) * 100);
?>
<div>
<div class="flex justify-between items-center mb-xs">
<span class="font-label-md text-label-md text-on-surface"><?php echo htmlspecialchars($v['brand'] . ' ' . $v['model']); ?> <span class="text-secondary font-body-sm">(<?php echo htmlspecialchars($v['registration_number']); ?>)</span></span>
<span class="font-label-sm text-label-sm text-primary font-bold">₹<?php echo number_format($v['revenue'], 2); ?></span>
</div>
<div class="w-full h-2 bg-surface-container-high rounded-full overflow-hidden">
<div class="h-full bg-primary rounded-full" style="width: <?php echo $barPct; ?>%;"></div>
</div>
<p class="text-[11px] text-secondary mt-xs"><?php echo $v['trips']; ?> completed trip<?php echo $v['trips'] == 1 ? '' : 's'; ?></p>
</div>
<?php endforeach; endif; ?>
</div>
</div>

<!-- Payment Methods -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
<div class="p-lg border-b border-outline-variant">
<h4 class="font-headline-sm text-headline-sm text-on-surface">Payment Methods</h4>
<p class="font-body-sm text-body-sm text-secondary">Successful payments only</p>
</div>
<div class="p-lg space-y-md">
<?php if (empty($paymentMethods)): ?>
<p class="font-body-sm text-body-sm text-secondary">No successful payments yet.</p>
<?php else: foreach ($paymentMethods as $pm):
    $pct = round(((float) $pm['total'] / $totalPaymentAmount) * 100);
?>
<div>
<div class="flex justify-between items-center mb-xs">
<span class="font-label-md text-label-md text-on-surface"><?php echo htmlspecialchars($pm['payment_method']); ?></span>
<span class="font-label-sm text-label-sm text-secondary">₹<?php echo number_format($pm['total'], 2); ?> · <?php echo $pm['c']; ?> txns</span>
</div>
<div class="w-full h-2 bg-surface-container-high rounded-full overflow-hidden">
<div class="h-full bg-tertiary-container rounded-full" style="width: <?php echo $pct; ?>%;"></div>
</div>
</div>
<?php endforeach; endif; ?>
</div>
</div>
</div>

<!-- Category Performance -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden mb-xl">
<div class="p-lg border-b border-outline-variant">
<h4 class="font-headline-sm text-headline-sm text-on-surface">Revenue by Vehicle Category</h4>
<p class="font-body-sm text-body-sm text-secondary">Which categories your fleet earns the most from</p>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container text-secondary">
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Category</th>
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Bookings</th>
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Revenue</th>
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Share</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant">
<?php if (empty($categoryPerformance)): ?>
<tr><td colspan="4" class="px-lg py-xl text-center text-secondary font-body-md text-body-md">No categories found.</td></tr>
<?php else: foreach ($categoryPerformance as $cat):
    $barPct = round(((float) $cat['revenue'] / $maxCategoryRevenue) * 100);
?>
<tr>
<td class="px-lg py-lg font-label-md text-label-md"><?php echo htmlspecialchars($cat['name']); ?></td>
<td class="px-lg py-lg font-body-md text-body-md text-secondary"><?php echo (int) $cat['bookings_count']; ?></td>
<td class="px-lg py-lg font-label-md text-label-md">₹<?php echo number_format($cat['revenue'], 2); ?></td>
<td class="px-lg py-lg w-1/3">
<div class="w-full h-2 bg-surface-container-high rounded-full overflow-hidden">
<div class="h-full bg-primary rounded-full" style="width: <?php echo $barPct; ?>%;"></div>
</div>
</td>
</tr>
<?php endforeach; endif; ?>
</tbody>
</table>
</div>
</div>

</div>
</main>
<script>
        window.addEventListener('DOMContentLoaded', () => {
            const bars = document.querySelectorAll('.chart-bar');
            bars.forEach(bar => {
                const targetHeight = bar.style.height;
                bar.style.height = '0%';
                setTimeout(() => { bar.style.height = targetHeight; }, 200);
            });
        });
    </script>
</body></html>
<?php $conn->close(); ?>