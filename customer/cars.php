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
?>
<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>DriveEase | Find Your Perfect Drive</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<!-- Tailwind Theme Configuration -->
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
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #c4c5d5; border-radius: 10px; }
        .car-card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .car-card-hover:hover { transform: translateY(-4px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col">
<!-- TopNavBar -->
<header class="fixed top-0 w-full z-50 bg-surface border-b border-outline-variant shadow-sm h-16">
<div class="flex justify-between items-center w-full px-margin-desktop max-w-max-width mx-auto h-full">
<div class="text-headline-md font-headline-md font-bold text-primary">DriveEase</div>
<nav class="hidden md:flex items-center gap-xl">
<a class="text-on-surface-variant hover:text-primary transition-colors duration-200 text-label-md font-label-md" href="../index.php">Home</a>
<a class="text-primary border-b-2 border-primary pb-1 text-label-md font-label-md" href="cars.php">Cars</a>
<a class="text-on-surface-variant hover:text-primary transition-colors duration-200 text-label-md font-label-md" href="#">How it Works</a>
<a class="text-on-surface-variant hover:text-primary transition-colors duration-200 text-label-md font-label-md" href="#">About</a>
<a class="text-on-surface-variant hover:text-primary transition-colors duration-200 text-label-md font-label-md" href="#">Contact</a>
</nav>
<div class="flex items-center gap-md">
<div class="relative hidden sm:block">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
<input class="bg-surface-container border border-outline-variant rounded-full py-1.5 pl-10 pr-4 text-body-sm focus:outline-none focus:ring-2 focus:ring-primary/20 w-48 lg:w-64" placeholder="Search cars..." type="text"/>
</div>
<button class="material-symbols-outlined text-on-surface-variant p-2 hover:bg-surface-container-high rounded-full">notifications</button>
<div class="w-8 h-8 rounded-full overflow-hidden bg-outline-variant">
<img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA3h7SUydLE-kG95NcZFOHeKxXAlFeucb0OtlMONQ0lT82durfthXRHWKA1O5tHVrW3TZRXh6AOe3LOfHoTWsvMRy6JeXlaC7vqX1RbdzOzHtpNtQyshxS5VCjGbzbIWdFLk-aRhKPhXHOYaoWCuJliowt2HTeazVejGvT-P1CZziGEFoVthyMM8W7iPmTNae8uwEtpzUtM3GjjRzlJtSrMP6d3x4lMw6oeN3WfRE6uJqQIGSm55HajsFeLBfDhA3RtgrSAiZe7qn0"/>
</div>
</div>
</div>
</header>
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
<button class="w-full bg-primary text-on-primary py-3 rounded-lg font-label-md text-label-md hover:bg-primary-container transition-colors mt-auto">Book Now</button>
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
<footer class="w-full mt-auto bg-surface-container-highest border-t border-outline-variant">
<div class="w-full py-xl px-margin-desktop grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 items-start max-w-max-width mx-auto gap-xl">
<div class="space-y-md">
<div class="text-headline-sm font-headline-sm font-bold text-on-surface">DriveEase</div>
<p class="text-body-sm text-on-surface-variant max-w-xs">Premium car rental solutions for business and leisure. Experience the road like never before with our elite fleet.</p>
</div>
<div class="space-y-md">
<h4 class="text-label-md font-label-md text-on-surface">Quick Links</h4>
<div class="flex flex-col gap-sm">
<a class="text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" href="#">Our Fleet</a>
<a class="text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" href="#">Pricing Plans</a>
<a class="text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" href="#">Safety Protocols</a>
<a class="text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" href="#">FAQ</a>
</div>
</div>
<div class="space-y-md">
<h4 class="text-label-md font-label-md text-on-surface">Legal</h4>
<div class="flex flex-col gap-sm">
<a class="text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" href="#">Privacy Policy</a>
<a class="text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" href="#">Terms of Service</a>
<a class="text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" href="#">Cookie Policy</a>
<a class="text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" href="#">Refund Policy</a>
</div>
</div>
<div class="space-y-md">
<h4 class="text-label-md font-label-md text-on-surface">Subscribe</h4>
<p class="text-body-sm text-on-surface-variant">Stay updated with our newest arrivals and offers.</p>
<div class="flex gap-xs">
<input class="bg-surface border border-outline-variant rounded-lg px-md py-2 text-body-sm flex-grow focus:outline-none focus:ring-1 focus:ring-primary" placeholder="Email address" type="email"/>
<button class="bg-primary text-on-primary px-md py-2 rounded-lg font-label-md hover:bg-primary-container transition-colors">Join</button>
</div>
</div>
</div>
<div class="w-full border-t border-outline-variant py-lg px-margin-desktop max-w-max-width mx-auto flex flex-col md:flex-row justify-between items-center gap-md">
<span class="text-label-sm font-label-sm text-on-surface-variant">© 2024 DriveEase Car Rental Systems. All rights reserved.</span>
<div class="flex gap-lg">
<a class="material-symbols-outlined text-on-surface-variant hover:text-primary" href="#">facebook</a>
<a class="material-symbols-outlined text-on-surface-variant hover:text-primary" href="#">language</a>
<a class="material-symbols-outlined text-on-surface-variant hover:text-primary" href="#">public</a>
</div>
</div>
</footer>
</body></html>