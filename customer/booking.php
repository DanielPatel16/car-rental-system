<?php
session_start();
include "../includes/db.php";

// ---------------------------------------------------------------
// Auth guard: must be a logged-in customer
// ---------------------------------------------------------------
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$errors  = [];
$success = null;

// Earliest allowed pick-up date is TOMORROW — today and past dates are blocked
$minPickup = date('Y-m-d', strtotime('+1 day'));

// ---------------------------------------------------------------
// Load the selected car
// ---------------------------------------------------------------
$car_id = (int) ($_GET['car_id'] ?? $_POST['car_id'] ?? 0);

$carStmt = $conn->prepare("SELECT * FROM cars WHERE id = ? AND status = 'Available'");
$carStmt->bind_param("i", $car_id);
$carStmt->execute();
$car = $carStmt->get_result()->fetch_assoc();
$carStmt->close();

if (!$car) {
    header("Location: cars.php");
    exit();
}

// ---------------------------------------------------------------
// Defaults for the editable fields
// ---------------------------------------------------------------
$pickup_date     = $_POST['pickup_date']     ?? $minPickup;
$return_date     = $_POST['return_date']     ?? date('Y-m-d', strtotime($minPickup . ' +3 day'));
$pickup_location = $_POST['pickup_location'] ?? '';
$return_location = $_POST['return_location'] ?? '';
$payment_method  = $_POST['payment_method']  ?? 'card';
$aadhar_number   = $_POST['aadhar_number']   ?? '';
$license_number  = $_POST['license_number']  ?? '';

// ---------------------------------------------------------------
// Handle booking submission
// ---------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm_booking') {

    $pickup_location = trim($pickup_location);
    $return_location = trim($return_location);
    $aadhar_number   = trim($aadhar_number);
    $license_number  = trim($license_number);

    $pickupTs = strtotime($pickup_date);
    $returnTs = strtotime($return_date);
    $minTs    = strtotime($minPickup);

    if ($pickup_location === '' || $return_location === '') {
        $errors[] = "Pick-up and drop-off locations are required.";
    }
    if (!$pickupTs || !$returnTs) {
        $errors[] = "Please provide valid pick-up and drop-off dates.";
    } else {
        if ($pickupTs < $minTs) {
            $errors[] = "Pick-up date cannot be today or a past date. The earliest available date is " . date('M j, Y', $minTs) . ".";
        }
        if ($returnTs <= $pickupTs) {
            $errors[] = "Drop-off date must be after the pick-up date.";
        }
    }
    if (!in_array($payment_method, ['upi', 'card', 'cash'])) {
        $errors[] = "Please select a valid payment method.";
    }

    // ---- Identity verification ----
    if (!preg_match('/^\d{12}$/', $aadhar_number)) {
        $errors[] = "Please enter a valid 12-digit Aadhar number.";
    }
    if ($license_number === '' || strlen($license_number) < 5) {
        $errors[] = "Please enter a valid driving license number.";
    }

    $id_proof_filename = null;
    $upload_dir = "../uploads/documents/";
    $maxFileSize = 5 * 1024 * 1024; // 5MB

    if (!isset($_FILES['id_proof']) || $_FILES['id_proof']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = "Please upload a photo of your ID (Aadhar card or driving license).";
    } elseif ($_FILES['id_proof']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "There was a problem uploading your ID photo. Please try again.";
    } else {
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['id_proof']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            $errors[] = "ID photo must be a JPG, PNG or WEBP image.";
        } elseif ($_FILES['id_proof']['size'] > $maxFileSize) {
            $errors[] = "ID photo must be smaller than 5MB.";
        } else {
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $id_proof_filename = hash('sha256', uniqid((string) mt_rand(), true)) . '.' . $ext;

            if (!move_uploaded_file($_FILES['id_proof']['tmp_name'], $upload_dir . $id_proof_filename)) {
                $errors[] = "Failed to save your ID photo. Please try again.";
                $id_proof_filename = null;
            }
        }
    }

    if (empty($errors)) {
        // Server-side total calculation (never trust the client for this)
        $total_days   = (int) ceil(($returnTs - $pickupTs) / 86400);
        $subtotal     = $total_days * (float) $car['price_per_day'];
        $gst          = round($subtotal * 0.18, 2);
        $total_amount = round($subtotal + $gst, 2);

        $isCash         = ($payment_method === 'cash');
        $bookingStatus  = 'Pending';
        $paymentStatus  = $isCash ? 'Pending' : 'Paid';
        $paymentRowStat = $isCash ? 'Pending' : 'Success';

        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare(
                "INSERT INTO bookings
                    (user_id, car_id, pickup_date, return_date, pickup_location, return_location,
                     total_days, total_amount, booking_status, payment_status,
                     aadhar_number, license_number, id_proof_image)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)"
            );
            $stmt->bind_param(
                "iisssiidsssss",
                $user_id, $car_id, $pickup_date, $return_date, $pickup_location, $return_location,
                $total_days, $total_amount, $bookingStatus, $paymentStatus,
                $aadhar_number, $license_number, $id_proof_filename
            );
            $stmt->execute();
            $booking_id = $stmt->insert_id;
            $stmt->close();

            $methodLabel  = ['upi' => 'UPI', 'card' => 'Card', 'cash' => 'Cash'][$payment_method];
            $transaction_id = $isCash ? null : strtoupper('TXN' . bin2hex(random_bytes(6)));

            $pstmt = $conn->prepare(
                "INSERT INTO payments (booking_id, payment_method, transaction_id, amount, status)
                 VALUES (?,?,?,?,?)"
            );
            $pstmt->bind_param(
                "issds",
                $booking_id, $methodLabel, $transaction_id, $total_amount, $paymentRowStat
            );
            $pstmt->execute();
            $pstmt->close();

            $conn->commit();

            $success = [
                'booking_id'     => $booking_id,
                'total_amount'   => $total_amount,
                'payment_status' => $paymentStatus,
            ];
        } catch (Exception $e) {
            $conn->rollback();
            if ($id_proof_filename && file_exists($upload_dir . $id_proof_filename)) {
                unlink($upload_dir . $id_proof_filename);
            }
            $errors[] = "Something went wrong while creating your booking. Please try again.";
        }
    }
}

// ---------------------------------------------------------------
// Values used for the (re)rendered price summary
// ---------------------------------------------------------------
$rate       = (float) $car['price_per_day'];
$daysShown  = max(1, (int) ceil((strtotime($return_date) - strtotime($pickup_date)) / 86400));
$subtotal   = $daysShown * $rate;
$gstShown   = round($subtotal * 0.18, 2);
$totalShown = round($subtotal + $gstShown, 2);
$minReturn  = date('Y-m-d', strtotime($pickup_date . ' +1 day'));

$imgSrc = $car['image'] ? "../uploads/cars/" . htmlspecialchars($car['image']) : "https://placehold.co/400x300?text=" . urlencode($car['brand']);
?>
<!DOCTYPE html>

<html class="scroll-smooth" lang="en" style=""><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>DriveEase - Complete Your Booking</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "surface-bright": "#faf8ff",
                    "surface-container-high": "#e2e7ff",
                    "surface-variant": "#dae2fd",
                    "primary": "#00288e",
                    "on-background": "#131b2e",
                    "tertiary-container": "#00563a",
                    "surface-container-highest": "#dae2fd",
                    "inverse-surface": "#283044",
                    "tertiary-fixed-dim": "#4edea3",
                    "outline-variant": "#c4c5d5",
                    "secondary-fixed": "#d4e4fa",
                    "on-secondary": "#ffffff",
                    "secondary": "#516072",
                    "on-tertiary-fixed": "#002113",
                    "error-container": "#ffdad6",
                    "tertiary": "#003d27",
                    "on-primary-fixed-variant": "#173bab",
                    "on-primary": "#ffffff",
                    "on-error": "#ffffff",
                    "surface-container": "#eaedff",
                    "background": "#faf8ff",
                    "secondary-container": "#d2e1f7",
                    "on-primary-fixed": "#001453",
                    "on-secondary-container": "#556477",
                    "on-surface": "#131b2e",
                    "secondary-fixed-dim": "#b9c8de",
                    "primary-fixed": "#dde1ff",
                    "on-tertiary-container": "#3fd298",
                    "on-primary-container": "#a8b8ff",
                    "inverse-primary": "#b8c4ff",
                    "surface-tint": "#3755c3",
                    "error": "#ba1a1a",
                    "tertiary-fixed": "#6ffbbe",
                    "surface": "#faf8ff",
                    "primary-container": "#1e40af",
                    "on-error-container": "#93000a",
                    "outline": "#757684",
                    "on-tertiary-fixed-variant": "#005236",
                    "on-surface-variant": "#444653",
                    "inverse-on-surface": "#eef0ff",
                    "surface-container-lowest": "#ffffff",
                    "surface-container-low": "#f2f3ff",
                    "on-tertiary": "#ffffff",
                    "primary-fixed-dim": "#b8c4ff",
                    "surface-dim": "#d2d9f4",
                    "on-secondary-fixed-variant": "#39485a",
                    "on-secondary-fixed": "#0d1c2d"
            },
            "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
            },
            "spacing": {
                    "sm": "8px",
                    "2xl": "48px",
                    "xs": "4px",
                    "md": "16px",
                    "base": "4px",
                    "lg": "24px",
                    "margin-desktop": "32px",
                    "margin-mobile": "16px",
                    "gutter": "24px",
                    "xl": "32px",
                    "max-width": "1440px"
            },
            "fontFamily": {
                    "label-sm": ["Inter"],
                    "headline-sm": ["Inter"],
                    "headline-lg-mobile": ["Inter"],
                    "body-sm": ["Inter"],
                    "body-md": ["Inter"],
                    "headline-lg": ["Inter"],
                    "label-md": ["Inter"],
                    "body-lg": ["Inter"],
                    "headline-md": ["Inter"]
            },
            "fontSize": {
                    "label-sm": ["12px", { "lineHeight": "16px", "fontWeight": "500" }],
                    "headline-sm": ["20px", { "lineHeight": "28px", "fontWeight": "600" }],
                    "headline-lg-mobile": ["24px", { "lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "700" }],
                    "body-sm": ["14px", { "lineHeight": "20px", "fontWeight": "400" }],
                    "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                    "headline-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                    "label-md": ["14px", { "lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600" }],
                    "body-lg": ["18px", { "lineHeight": "28px", "fontWeight": "400" }],
                    "headline-md": ["24px", { "lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600" }]
            }
    },
        },
      }
    </script>
<style>
        body { background-color: #F8FAFC; }
        .card-shadow { box-shadow: 0px 4px 6px -1px rgba(0, 40, 142, 0.05); }
    </style>
</head>
<body class="bg-background text-on-background font-body-md min-h-screen flex flex-col antialiased">
<header class="bg-surface-bright dark:bg-on-background border-b border-outline-variant sticky top-0 z-50">
<div class="flex justify-between items-center w-full px-margin-desktop max-w-max-width mx-auto h-16">
<div class="flex items-center gap-xl">
<a class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed" href="../index.php">DriveEase</a>
<nav class="hidden md:flex gap-lg h-16 items-center">
<a class="text-on-surface-variant dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed transition-colors font-body-md text-body-md cursor-pointer active:scale-95 transition-transform flex items-center h-full" href="cars.php">Fleet</a>
<a class="text-primary dark:text-primary-fixed border-b-2 border-primary dark:border-primary-fixed pb-1 hover:text-primary dark:hover:text-primary-fixed transition-colors font-body-md text-body-md cursor-pointer active:scale-95 transition-transform flex items-center h-[calc(100%-2px)] mt-[2px]" href="my_bookings.php">My Bookings</a>
<a class="text-on-surface-variant dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed transition-colors font-body-md text-body-md cursor-pointer active:scale-95 transition-transform flex items-center h-full" href="#">Support</a>
</nav>
</div>
<div class="flex items-center gap-md">
<span class="font-body-sm text-body-sm text-on-surface-variant"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
</div>
</div>
</header>

<main class="flex-grow w-full px-margin-mobile md:px-margin-desktop py-xl max-w-max-width mx-auto">

<?php if ($success): ?>
<div class="mb-lg bg-tertiary-fixed/30 border border-tertiary text-on-tertiary-fixed-variant rounded-lg p-lg card-shadow">
    <h2 class="font-headline-sm text-headline-sm mb-xs">Booking Confirmed 🎉</h2>
    <p class="font-body-md text-body-md">
        Your booking <strong>#<?php echo (int) $success['booking_id']; ?></strong> for the
        <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?> was created successfully.
        Total amount: <strong>₹<?php echo number_format($success['total_amount'], 2); ?></strong>
        &middot; Payment status: <strong><?php echo htmlspecialchars($success['payment_status']); ?></strong>
    </p>
    <a href="booking_history.php" class="inline-block mt-md text-primary font-label-md hover:underline">View My Bookings &rarr;</a>
</div>
<?php else: ?>

<div class="mb-lg">
<h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-background mb-xs">Complete Your Booking</h1>
<p class="font-body-md text-body-md text-on-surface-variant">Review your details and complete secure payment.</p>
</div>

<?php if ($errors): ?>
<div class="mb-lg bg-error-container border border-error text-on-error-container rounded-lg p-lg card-shadow">
<?php foreach ($errors as $e) echo "<p class='font-body-sm text-body-sm'>" . htmlspecialchars($e) . "</p>"; ?>
</div>
<?php endif; ?>

<form method="POST" action="booking.php" id="bookingForm" enctype="multipart/form-data">
<input type="hidden" name="action" value="confirm_booking">
<input type="hidden" name="car_id" value="<?php echo (int) $car['id']; ?>">

<div class="flex flex-col lg:flex-row gap-gutter">
<div class="w-full lg:w-2/3 flex flex-col gap-lg">

<section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-lg card-shadow">
<h2 class="font-headline-sm text-headline-sm text-on-background border-b border-surface-container-high pb-sm mb-md">Selected Vehicle</h2>
<div class="flex flex-col md:flex-row gap-lg items-start">
<div class="w-full md:w-1/3 aspect-[4/3] rounded bg-surface-container overflow-hidden">
<img alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>" class="w-full h-full object-cover" src="<?php echo $imgSrc; ?>"/>
</div>
<div class="w-full md:w-2/3 flex flex-col gap-sm">
<div class="flex justify-between items-start">
<div>
<h3 class="font-headline-md text-headline-md text-on-background"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h3>
<p class="font-body-sm text-body-sm text-secondary"><?php echo (int) $car['year']; ?> &middot; ₹<?php echo number_format($rate, 0); ?> / day</p>
</div>
<span class="bg-surface-container-low text-primary px-sm py-xs rounded-full font-label-sm text-label-sm flex items-center gap-xs border border-primary-fixed-dim">
<span class="material-symbols-outlined text-[16px]">verified</span> <?php echo htmlspecialchars($car['status']); ?>
</span>
</div>
<div class="grid grid-cols-2 md:grid-cols-4 gap-sm mt-sm">
<div class="flex items-center gap-xs text-on-surface-variant">
<span class="material-symbols-outlined text-[20px]">airline_seat_recline_normal</span>
<span class="font-body-sm text-body-sm"><?php echo (int) $car['seats']; ?> Seats</span>
</div>
<div class="flex items-center gap-xs text-on-surface-variant">
<span class="material-symbols-outlined text-[20px]">local_gas_station</span>
<span class="font-body-sm text-body-sm"><?php echo htmlspecialchars($car['fuel_type']); ?></span>
</div>
<div class="flex items-center gap-xs text-on-surface-variant">
<span class="material-symbols-outlined text-[20px]">settings</span>
<span class="font-body-sm text-body-sm"><?php echo htmlspecialchars($car['transmission']); ?></span>
</div>
<div class="flex items-center gap-xs text-on-surface-variant">
<span class="material-symbols-outlined text-[20px]">ac_unit</span>
<span class="font-body-sm text-body-sm">A/C</span>
</div>
</div>
</div>
</div>
</section>

<section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-lg card-shadow">
<h2 class="font-headline-sm text-headline-sm text-on-background border-b border-surface-container-high pb-sm mb-md">Trip Details</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-lg relative">
<div class="hidden md:block absolute left-1/2 top-4 bottom-4 w-px bg-surface-container-high transform -translate-x-1/2"></div>
<div class="flex flex-col gap-sm">
<div class="flex items-center gap-sm text-primary">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">flight_land</span>
<h4 class="font-label-md text-label-md">Pick-up</h4>
</div>
<div>
<label class="font-body-sm text-body-sm text-secondary block mb-xs">Pick-up Location</label>
<input class="w-full font-body-lg text-body-lg text-on-background font-medium bg-surface-container-low p-sm rounded border border-surface-container-high" type="text" name="pickup_location" placeholder="e.g. New Delhi Airport (DEL)" value="<?php echo htmlspecialchars($pickup_location); ?>" required>
</div>
<div class="bg-surface-container-low p-sm rounded border border-surface-container-high mt-xs flex items-center gap-sm">
<span class="material-symbols-outlined text-secondary text-[20px]">calendar_today</span>
<input class="bg-transparent font-body-sm text-body-sm text-on-background font-medium w-full outline-none" type="date" name="pickup_date" id="pickup_date" value="<?php echo htmlspecialchars($pickup_date); ?>" min="<?php echo $minPickup; ?>" required>
</div>
<p class="font-label-sm text-label-sm text-secondary">Earliest pick-up: <?php echo date('M j, Y', strtotime($minPickup)); ?> (today and past dates aren't bookable)</p>
</div>
<div class="flex flex-col gap-sm">
<div class="flex items-center gap-sm text-primary">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">flight_takeoff</span>
<h4 class="font-label-md text-label-md">Drop-off</h4>
</div>
<div>
<label class="font-body-sm text-body-sm text-secondary block mb-xs">Drop-off Location</label>
<input class="w-full font-body-lg text-body-lg text-on-background font-medium bg-surface-container-low p-sm rounded border border-surface-container-high" type="text" name="return_location" placeholder="e.g. New Delhi Airport (DEL)" value="<?php echo htmlspecialchars($return_location); ?>" required>
</div>
<div class="bg-surface-container-low p-sm rounded border border-surface-container-high mt-xs flex items-center gap-sm">
<span class="material-symbols-outlined text-secondary text-[20px]">calendar_today</span>
<input class="bg-transparent font-body-sm text-body-sm text-on-background font-medium w-full outline-none" type="date" name="return_date" id="return_date" value="<?php echo htmlspecialchars($return_date); ?>" min="<?php echo $minReturn; ?>" required>
</div>
</div>
</div>
</section>

<section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-lg card-shadow">
<h2 class="font-headline-sm text-headline-sm text-on-background border-b border-surface-container-high pb-sm mb-md">Identity Verification</h2>
<p class="font-body-sm text-body-sm text-secondary mb-md">Required for vehicle pickup. This information is only visible to DriveEase staff.</p>
<div class="grid grid-cols-1 md:grid-cols-2 gap-md">
<div>
<label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs">Aadhar Number</label>
<input class="block w-full px-3 py-2 border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-surface-container-lowest text-on-background font-body-md text-body-md transition-shadow" type="text" name="aadhar_number" placeholder="12-digit Aadhar number" maxlength="12" pattern="\d{12}" value="<?php echo htmlspecialchars($aadhar_number); ?>" required>
</div>
<div>
<label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs">Driving License Number</label>
<input class="block w-full px-3 py-2 border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-surface-container-lowest text-on-background font-body-md text-body-md transition-shadow" type="text" name="license_number" placeholder="e.g. DL-1420110012345" value="<?php echo htmlspecialchars($license_number); ?>" required>
</div>
<div class="md:col-span-2">
<label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs">Upload ID Photo (Aadhar or License) &mdash; max 5MB</label>
<input class="block w-full text-body-sm font-body-sm border border-outline-variant rounded px-3 py-2 bg-surface-container-lowest" type="file" name="id_proof" id="id_proof" accept=".jpg,.jpeg,.png,.webp" required>
<p class="font-label-sm text-label-sm text-secondary mt-xs">JPG, PNG or WEBP only.</p>
</div>
</div>
</section>

</div>

<div class="w-full lg:w-1/3">
<div class="sticky top-24 flex flex-col gap-lg">

<section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-lg card-shadow">
<h2 class="font-headline-sm text-headline-sm text-on-background border-b border-surface-container-high pb-sm mb-md">Price Summary</h2>
<div class="flex flex-col gap-sm font-body-sm text-body-sm text-on-surface-variant">
<div class="flex justify-between items-center">
<span id="rateLabel">Vehicle Rate (<span id="dayCount"><?php echo $daysShown; ?></span> days x ₹<?php echo number_format($rate, 0); ?>)</span>
<span class="font-medium text-on-background">₹<span id="subtotalAmt"><?php echo number_format($subtotal, 2); ?></span></span>
</div>
<div class="flex justify-between items-center">
<span>Taxes &amp; Fees (18% GST)</span>
<span class="font-medium text-on-background">₹<span id="gstAmt"><?php echo number_format($gstShown, 2); ?></span></span>
</div>
<hr class="border-surface-container-high my-sm"/>
<div class="flex justify-between items-end">
<span class="font-label-md text-label-md text-on-background">Total Amount</span>
<span class="font-headline-md text-headline-md font-bold text-primary">₹<span id="totalAmt"><?php echo number_format($totalShown, 2); ?></span></span>
</div>
</div>
</section>

<section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-lg card-shadow">
<h2 class="font-headline-sm text-headline-sm text-on-background mb-md">Payment Details</h2>
<div class="flex flex-col gap-md">
<div class="flex flex-col gap-sm">
<label class="block font-label-sm text-label-sm text-on-surface-variant">Select Payment Method</label>
<div class="grid grid-cols-1 gap-sm">
<label class="flex items-center justify-between p-sm border border-outline-variant rounded-lg cursor-pointer hover:bg-surface-container-low transition-colors" id="label-upi">
<div class="flex items-center gap-sm">
<input class="w-4 h-4 text-primary focus:ring-primary border-outline-variant" name="payment_method" type="radio" value="upi" <?php echo $payment_method === 'upi' ? 'checked' : ''; ?>/>
<span class="font-body-md text-body-md text-on-background">UPI</span>
</div>
<span class="material-symbols-outlined text-secondary">account_balance_wallet</span>
</label>
<label class="flex items-center justify-between p-sm border-2 border-primary rounded-lg cursor-pointer bg-surface-container-low transition-colors" id="label-card">
<div class="flex items-center gap-sm">
<input class="w-4 h-4 text-primary focus:ring-primary border-outline-variant" name="payment_method" type="radio" value="card" <?php echo $payment_method === 'card' ? 'checked' : ''; ?>/>
<span class="font-body-md text-body-md text-on-background">Debit/Credit Card</span>
</div>
<span class="material-symbols-outlined text-secondary">credit_card</span>
</label>
<label class="flex items-center justify-between p-sm border border-outline-variant rounded-lg cursor-pointer hover:bg-surface-container-low transition-colors" id="label-cash">
<div class="flex items-center gap-sm">
<input class="w-4 h-4 text-primary focus:ring-primary border-outline-variant" name="payment_method" type="radio" value="cash" <?php echo $payment_method === 'cash' ? 'checked' : ''; ?>/>
<span class="font-body-md text-body-md text-on-background">Cash on Pickup</span>
</div>
<span class="material-symbols-outlined text-secondary">payments</span>
</label>
</div>
</div>

<div class="flex flex-col gap-md pt-md border-t border-surface-container-high" id="payment-method-details">
<div class="flex flex-col gap-md" id="fields-card">
<div>
<label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs">Name on Card</label>
<input class="block w-full px-3 py-2 border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-surface-container-lowest text-on-background font-body-md text-body-md transition-shadow" placeholder="John Doe" type="text">
</div>
<div>
<label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs">Card Number</label>
<input class="block w-full px-3 py-2 border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-surface-container-lowest text-on-background font-body-md text-body-md transition-shadow" placeholder="0000 0000 0000 0000" type="text">
</div>
<div class="grid grid-cols-2 gap-md">
<div>
<label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs">Expiry</label>
<input class="block w-full px-3 py-2 border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-surface-container-lowest text-on-background font-body-md text-body-md transition-shadow" placeholder="MM/YY" type="text">
</div>
<div>
<label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs">CVV</label>
<input class="block w-full px-3 py-2 border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-surface-container-lowest text-on-background font-body-md text-body-md transition-shadow" placeholder="123" type="password">
</div>
</div>
</div>
<div class="hidden flex flex-col gap-md" id="fields-upi">
<div>
<label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs">UPI ID</label>
<input class="block w-full px-3 py-2 border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-surface-container-lowest text-on-background font-body-md text-body-md transition-shadow" placeholder="user@upi" type="text">
</div>
</div>
<div class="hidden" id="fields-cash">
<p class="font-body-md text-body-md text-on-surface-variant p-md bg-surface-container-low rounded-lg border border-outline-variant text-center">
            Please pay at the counter during pickup.
        </p>
</div>
</div>

<div class="mt-md">
<button type="submit" class="w-full bg-primary text-on-primary font-label-md text-label-md py-3 px-4 rounded-lg hover:bg-primary-container hover:text-on-primary-container transition-colors shadow-sm flex items-center justify-center gap-sm">
<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">lock</span>
<span>Confirm &amp; Pay ₹<span id="submitTotal"><?php echo number_format($totalShown, 2); ?></span></span>
</button>
<p class="text-center font-label-sm text-label-sm text-secondary mt-sm flex items-center justify-center gap-xs">
<span class="material-symbols-outlined text-[16px]">shield</span> Secure SSL Encryption
</p>
</div>
</div>
</section>

</div>
</div>
</div>
</form>

<?php endif; ?>
</main>

<footer class="bg-surface-container-lowest dark:bg-on-background border-t border-outline-variant mt-auto">
<div class="w-full py-lg px-margin-desktop flex flex-col md:flex-row justify-between items-center max-w-max-width mx-auto gap-md">
<div class="font-label-md text-label-md font-bold text-primary">DriveEase</div>
<div class="font-body-sm text-body-sm text-on-surface-variant text-center md:text-left">
                © 2024 DriveEase Car Rentals. All rights reserved.
            </div>
</div>
</footer>

<script>
  const RATE = <?php echo json_encode($rate); ?>;

  function recalc() {
    const pickupInput = document.getElementById('pickup_date');
    const returnInput = document.getElementById('return_date');
    const pickup = new Date(pickupInput.value);
    const ret = new Date(returnInput.value);
    let days = Math.ceil((ret - pickup) / (1000 * 60 * 60 * 24));
    if (!Number.isFinite(days) || days < 1) days = 1;

    const subtotal = days * RATE;
    const gst = subtotal * 0.18;
    const total = subtotal + gst;

    document.getElementById('dayCount').textContent = days;
    document.getElementById('subtotalAmt').textContent = subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('gstAmt').textContent = gst.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('totalAmt').textContent = total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('submitTotal').textContent = total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function syncReturnMin() {
    const pickupInput = document.getElementById('pickup_date');
    const returnInput = document.getElementById('return_date');
    const next = new Date(pickupInput.value);
    next.setDate(next.getDate() + 1);
    const minReturn = next.toISOString().split('T')[0];
    returnInput.min = minReturn;
    if (returnInput.value < minReturn) returnInput.value = minReturn;
  }

  document.addEventListener('DOMContentLoaded', () => {
    recalc();

    document.getElementById('pickup_date').addEventListener('change', () => { syncReturnMin(); recalc(); });
    document.getElementById('return_date').addEventListener('change', recalc);

    const idProof = document.getElementById('id_proof');
    idProof.addEventListener('change', () => {
      const file = idProof.files[0];
      if (file && file.size > 5 * 1024 * 1024) {
        alert('ID photo must be smaller than 5MB.');
        idProof.value = '';
      }
    });

    const radioButtons = document.querySelectorAll('input[name="payment_method"]');
    const labels = { upi: document.getElementById('label-upi'), card: document.getElementById('label-card'), cash: document.getElementById('label-cash') };
    const fields = { upi: document.getElementById('fields-upi'), card: document.getElementById('fields-card'), cash: document.getElementById('fields-cash') };
    const activeClasses = ['border-2', 'border-primary', 'bg-surface-container-low'];
    const inactiveClasses = ['border', 'border-outline-variant'];

    function applyState(selectedValue) {
      Object.keys(labels).forEach(key => {
        labels[key].classList.remove(...activeClasses);
        labels[key].classList.add(...inactiveClasses);
        fields[key].classList.add('hidden');
      });
      labels[selectedValue].classList.remove(...inactiveClasses);
      labels[selectedValue].classList.add(...activeClasses);
      fields[selectedValue].classList.remove('hidden');
    }

    const checked = document.querySelector('input[name="payment_method"]:checked');
    if (checked) applyState(checked.value);

    radioButtons.forEach(radio => {
      radio.addEventListener('change', (e) => applyState(e.target.value));
    });
  });
</script>
</body></html>