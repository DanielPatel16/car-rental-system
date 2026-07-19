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
    header("Location: booking_history.php");
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
    return 'booking_history.php?' . http_build_query($qs);
}
include "../includes/header.php";
?>

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

<?php include "../includes/footer.php"; ?>