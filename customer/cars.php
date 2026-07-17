<?php
session_start();
include "../includes/db.php";

// ---------------------------------------------------------------
// Read filters from the query string
// ---------------------------------------------------------------
$selectedBrands = $_GET['brand'] ?? [];
if (!is_array($selectedBrands)) $selectedBrands = [$selectedBrands];

$selectedFuel         = $_GET['fuel_type'] ?? '';
$selectedTransmission = $_GET['transmission'] ?? '';
$selectedSeats        = $_GET['seats'] ?? '';
$priceMax             = (isset($_GET['price_max']) && $_GET['price_max'] !== '') ? (float) $_GET['price_max'] : null;
$sort                 = $_GET['sort'] ?? 'popularity';

// ---------------------------------------------------------------
// Build the WHERE clause safely with prepared statements
// ---------------------------------------------------------------
$where  = ["status = 'Available'"];
$params = [];
$types  = "";

if (!empty($selectedBrands)) {
    $placeholders = implode(",", array_fill(0, count($selectedBrands), "?"));
    $where[] = "brand IN ($placeholders)";
    foreach ($selectedBrands as $b) { $params[] = $b; $types .= "s"; }
}
if ($selectedFuel !== '') {
    $where[]  = "fuel_type = ?";
    $params[] = $selectedFuel;
    $types   .= "s";
}
if ($selectedTransmission !== '') {
    $where[]  = "transmission = ?";
    $params[] = $selectedTransmission;
    $types   .= "s";
}
if ($selectedSeats !== '') {
    if ($selectedSeats === '7+') {
        $where[] = "seats >= 7";
    } else {
        $where[]  = "seats = ?";
        $params[] = (int) $selectedSeats;
        $types   .= "i";
    }
}
if ($priceMax !== null) {
    $where[]  = "price_per_day <= ?";
    $params[] = $priceMax;
    $types   .= "d";
}

$whereSql = implode(" AND ", $where);

$orderBy = "created_at DESC";
if ($sort === 'price_asc')  $orderBy = "price_per_day ASC";
if ($sort === 'price_desc') $orderBy = "price_per_day DESC";
if ($sort === 'newest')     $orderBy = "created_at DESC";

// ---------------------------------------------------------------
// Pagination
// ---------------------------------------------------------------
$perPage = 9;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

$countStmt = $conn->prepare("SELECT COUNT(*) AS total FROM cars WHERE $whereSql");
if ($types) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$totalRows  = (int) $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = max(1, (int) ceil($totalRows / $perPage));
$countStmt->close();

$stmt = $conn->prepare("SELECT * FROM cars WHERE $whereSql ORDER BY $orderBy LIMIT ? OFFSET ?");
$allTypes  = $types . "ii";
$allParams = array_merge($params, [$perPage, $offset]);
$stmt->bind_param($allTypes, ...$allParams);
$stmt->execute();
$cars = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Distinct brands present in stock, for the Brand checkbox list
$brandOptions = [];
$brandRes = $conn->query("SELECT DISTINCT brand FROM cars WHERE status = 'Available' ORDER BY brand");
while ($row = $brandRes->fetch_assoc()) $brandOptions[] = $row['brand'];

$fuelBadge = [
    'Electric' => 'bg-tertiary text-on-tertiary',
    'Petrol'   => 'bg-secondary text-on-secondary',
    'Diesel'   => 'bg-on-secondary-container text-white',
    'Hybrid'   => 'bg-primary text-on-primary',
];

function qs($extra) {
    $qs = $_GET;
    foreach ($extra as $k => $v) {
        if ($v === null) { unset($qs[$k]); } else { $qs[$k] = $v; }
    }
    return 'cars.php?' . http_build_query($qs);
}
include "../includes/header.php";
?>
<!-- Main Content Layout -->
<main class="flex-grow pt-16 flex flex-col lg:flex-row max-w-max-width mx-auto w-full px-margin-desktop gap-gutter py-lg">
<!-- Sidebar Filter -->
<aside class="w-full lg:w-72 shrink-0 space-y-lg">
<form method="GET" class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg sticky top-24">
<div class="flex items-center justify-between mb-md">
<h2 class="text-headline-sm font-headline-sm text-on-surface">Filters</h2>
<a href="cars.php" class="text-primary text-label-sm font-label-sm hover:underline">Reset All</a>
</div>
<!-- Brand Filter -->
<div class="mb-lg">
<label class="text-label-md font-label-md text-on-surface-variant block mb-sm">Brand</label>
<div class="space-y-xs">
<?php foreach ($brandOptions as $brand): ?>
<label class="flex items-center gap-sm text-body-sm text-on-surface cursor-pointer">
<input type="checkbox" name="brand[]" value="<?php echo htmlspecialchars($brand); ?>" onchange="this.form.submit()" <?php echo in_array($brand, $selectedBrands) ? 'checked' : ''; ?> class="rounded border-outline-variant text-primary focus:ring-primary"/> <?php echo htmlspecialchars($brand); ?>
                        </label>
<?php endforeach; ?>
<?php if (empty($brandOptions)): ?>
<p class="text-label-sm text-outline">No cars in stock yet.</p>
<?php endif; ?>
</div>
</div>
<!-- Price Range -->
<div class="mb-lg">
<label class="text-label-md font-label-md text-on-surface-variant block mb-sm">Price Range (Daily)</label>
<input type="range" name="price_max" value="<?php echo htmlspecialchars($priceMax ?? 1000); ?>" onchange="this.form.submit()" class="w-full h-2 bg-secondary-container rounded-lg appearance-none cursor-pointer accent-primary" max="1000" min="50" step="10"/>
<div class="flex justify-between mt-sm text-label-sm font-label-sm text-on-surface-variant">
<span class="">₹50</span>
<span class="">₹<?php echo htmlspecialchars($priceMax ?? 1000); ?><?php echo ($priceMax === null || $priceMax >= 1000) ? '+' : ''; ?></span>
</div>
</div>
<!-- Fuel Type -->
<div class="mb-lg">
<label class="text-label-md font-label-md text-on-surface-variant block mb-sm">Fuel Type</label>
<div class="flex flex-wrap gap-xs">
<?php foreach (['Electric', 'Petrol', 'Diesel'] as $fuel):
    $active = $selectedFuel === $fuel;
?>
<button type="submit" name="fuel_type" value="<?php echo $selectedFuel === $fuel ? '' : $fuel; ?>" class="px-sm py-1 rounded-full border <?php echo $active ? 'border-primary bg-primary text-on-primary' : 'border-outline-variant text-on-surface-variant hover:bg-surface-container-high'; ?> text-label-sm font-label-sm"><?php echo $fuel; ?></button>
<?php endforeach; ?>
</div>
</div>
<!-- Transmission -->
<div class="mb-lg">
<label class="text-label-md font-label-md text-on-surface-variant block mb-sm">Transmission</label>
<div class="grid grid-cols-2 gap-sm">
<?php foreach (['Automatic', 'Manual'] as $trans):
    $active = $selectedTransmission === $trans;
?>
<button type="submit" name="transmission" value="<?php echo $selectedTransmission === $trans ? '' : $trans; ?>" class="py-2 rounded-lg border <?php echo $active ? 'border-primary bg-secondary-container text-on-secondary-container' : 'border-outline-variant text-on-surface-variant hover:bg-surface-container-high'; ?> text-label-sm font-label-sm"><?php echo $trans; ?></button>
<?php endforeach; ?>
</div>
</div>
<!-- Seating -->
<div class="mb-lg">
<label class="text-label-md font-label-md text-on-surface-variant block mb-sm">Seating Capacity</label>
<div class="flex gap-sm">
<?php foreach (['2', '4', '5', '7+'] as $seat):
    $active = $selectedSeats === $seat;
?>
<button type="submit" name="seats" value="<?php echo $active ? '' : $seat; ?>" class="w-10 h-10 rounded-lg border <?php echo $active ? 'border-primary bg-secondary-container text-on-secondary-container' : 'border-outline-variant'; ?> flex items-center justify-center text-label-sm font-label-sm"><?php echo $seat; ?></button>
<?php endforeach; ?>
</div>
</div>
<!-- preserve currently chosen single-select values as hidden fields so brand-checkbox submits don't clear them -->
<?php if ($selectedFuel !== ''): ?><input type="hidden" name="fuel_type" value="<?php echo htmlspecialchars($selectedFuel); ?>"><?php endif; ?>
<?php if ($selectedTransmission !== ''): ?><input type="hidden" name="transmission" value="<?php echo htmlspecialchars($selectedTransmission); ?>"><?php endif; ?>
<?php if ($selectedSeats !== ''): ?><input type="hidden" name="seats" value="<?php echo htmlspecialchars($selectedSeats); ?>"><?php endif; ?>
</form>
</aside>
<!-- Main Listing Grid -->
<section class="flex-grow">
<!-- Grid Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-md mb-lg">
<div>
<h1 class="text-headline-md font-headline-md text-on-surface">Available Fleet <span class="text-on-surface-variant font-normal text-body-lg">(<?php echo $totalRows; ?> cars)</span></h1>
</div>
<div class="flex items-center gap-sm">
<span class="text-label-md font-label-md text-on-surface-variant">Sort by:</span>
<form method="GET" id="sortForm">
<?php foreach ($_GET as $k => $v) {
    if ($k === 'sort') continue;
    if ($k === 'brand') { foreach ((array)$v as $bv) echo '<input type="hidden" name="brand[]" value="' . htmlspecialchars($bv) . '">'; continue; }
    echo '<input type="hidden" name="' . htmlspecialchars($k) . '" value="' . htmlspecialchars($v) . '">';
} ?>
<select name="sort" onchange="this.form.submit()" class="bg-surface-container-lowest border border-outline-variant rounded-lg px-md py-2 text-label-md font-label-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/20">
<option value="popularity" <?php echo $sort === 'popularity' ? 'selected' : ''; ?>>Popularity</option>
<option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
<option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
<option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest Arrivals</option>
</select>
</form>
</div>
</div>
<!-- Bento / Grid Content -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-gutter">
<?php if (empty($cars)): ?>
<div class="col-span-full text-center py-2xl text-on-surface-variant font-body-md text-body-md">
    No cars match your filters. Try resetting them.
</div>
<?php endif; ?>
<?php foreach ($cars as $car):
    $badge = $fuelBadge[$car['fuel_type']] ?? 'bg-secondary text-on-secondary';
    $imgSrc = $car['image'] ? "../uploads/cars/" . htmlspecialchars($car['image']) : "https://placehold.co/400x300?text=" . urlencode($car['brand']);
?>
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden car-card-hover flex flex-col shadow-sm">
<div class="relative h-48 w-full">
<img class="w-full h-full object-cover" src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>"/>
<div class="absolute top-3 left-3 <?php echo $badge; ?> px-sm py-1 rounded-full text-label-sm font-label-sm"><?php echo htmlspecialchars($car['fuel_type']); ?></div>
<button class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/80 backdrop-blur-sm flex items-center justify-center text-on-surface-variant hover:text-error hover:bg-white transition-colors">
<span class="material-symbols-outlined">favorite</span>
</button>
</div>
<div class="p-lg flex flex-col flex-grow">
<div class="flex justify-between items-start mb-xs">
<h3 class="text-headline-sm font-headline-sm text-on-surface"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h3>
<div class="text-right">
<span class="text-headline-sm font-headline-sm text-primary">₹<?php echo number_format($car['price_per_day'], 0); ?></span>
<span class="text-label-sm font-label-sm text-on-surface-variant block">/ day</span>
</div>
</div>
<div class="flex gap-md mt-md mb-lg text-on-surface-variant">
<div class="flex items-center gap-xs">
<span class="material-symbols-outlined text-sm">ac_unit</span>
<span class="text-label-sm font-label-sm">AC</span>
</div>
<div class="flex items-center gap-xs">
<span class="material-symbols-outlined text-sm">settings</span>
<span class="text-label-sm font-label-sm"><?php echo $car['transmission'] === 'Automatic' ? 'Auto' : 'Manual'; ?></span>
</div>
<div class="flex items-center gap-xs">
<span class="material-symbols-outlined text-sm">group</span>
<span class="text-label-sm font-label-sm"><?php echo (int) $car['seats']; ?> seats</span>
</div>
</div>
<button onclick="window.location='booking.php?car_id=<?php echo (int) $car['id']; ?>'" class="w-full bg-primary text-on-primary py-3 rounded-lg font-label-md text-label-md hover:bg-primary-container transition-colors mt-auto">Book Now</button>
</div>
</div>
<?php endforeach; ?>
</div>
<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="mt-2xl flex justify-center items-center gap-sm">
<a href="<?php echo qs(['page' => max(1, $page - 1)]); ?>" class="w-10 h-10 flex items-center justify-center rounded-full border border-outline-variant text-on-surface-variant hover:bg-surface-container <?php echo $page <= 1 ? 'pointer-events-none opacity-30' : ''; ?>">
<span class="material-symbols-outlined">chevron_left</span>
</a>
<?php for ($p = 1; $p <= $totalPages; $p++): ?>
<a href="<?php echo qs(['page' => $p]); ?>" class="w-10 h-10 flex items-center justify-center rounded-full font-label-md <?php echo $p === $page ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-container'; ?>"><?php echo $p; ?></a>
<?php endfor; ?>
<a href="<?php echo qs(['page' => min($totalPages, $page + 1)]); ?>" class="w-10 h-10 flex items-center justify-center rounded-full border border-outline-variant text-on-surface-variant hover:bg-surface-container <?php echo $page >= $totalPages ? 'pointer-events-none opacity-30' : ''; ?>">
<span class="material-symbols-outlined">chevron_right</span>
</a>
</div>
<?php endif; ?>
</section>
</main>
<!-- Footer -->
<?php include "../includes/footer.php"; ?>
</body></html>