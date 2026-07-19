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
// Load current profile
// ---------------------------------------------------------------
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$profileErrors  = [];
$profileSuccess = null;
$pwErrors       = [];
$pwSuccess      = null;

// ---------------------------------------------------------------
// Handle "update profile" form
// ---------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_profile') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($name === '') {
        $profileErrors[] = "Full name is required.";
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $profileErrors[] = "Please enter a valid email address.";
    }
    if ($phone !== '' && !preg_match('/^[0-9+\-\s()]{7,15}$/', $phone)) {
        $profileErrors[] = "Please enter a valid phone number.";
    }

    if (empty($profileErrors)) {
        $chk = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $chk->bind_param("si", $email, $user_id);
        $chk->execute();
        if ($chk->get_result()->fetch_assoc()) {
            $profileErrors[] = "That email address is already in use by another account.";
        }
        $chk->close();
    }

    if (empty($profileErrors)) {
        $upd = $conn->prepare("UPDATE users SET user_name = ?, email = ?, mobile_number = ? WHERE id = ?");
        $upd->bind_param("sssi", $name, $email, $phone, $user_id);
        if ($upd->execute()) {
            $user['user_name']     = $name;
            $user['email']         = $email;
            $user['mobile_number'] = $phone;
            $_SESSION['user_name'] = $name;
            $profileSuccess = "Your profile has been updated successfully.";
        } else {
            $profileErrors[] = "Something went wrong while saving your profile. Please try again.";
        }
        $upd->close();
    }
}

// ---------------------------------------------------------------
// Handle "change password" form
// ---------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'change_password') {
    $current = (string) ($_POST['current_password'] ?? '');
    $new     = (string) ($_POST['new_password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    if (!password_verify($current, $user['password'] ?? '')) {
        $pwErrors[] = "Your current password is incorrect.";
    }
    if (strlen($new) < 6) {
        $pwErrors[] = "New password must be at least 6 characters long.";
    }
    if ($new !== $confirm) {
        $pwErrors[] = "New password and confirmation do not match.";
    }

    if (empty($pwErrors)) {
        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $upd->bind_param("si", $hashed, $user_id);
        if ($upd->execute()) {
            $pwSuccess = "Your password has been changed successfully.";
        } else {
            $pwErrors[] = "Something went wrong while changing your password. Please try again.";
        }
        $upd->close();
    }
}

// ---------------------------------------------------------------
// Booking stats for this customer
// ---------------------------------------------------------------
$statsStmt = $conn->prepare(
    "SELECT
        COUNT(*) AS total,
        COALESCE(SUM(booking_status = 'Completed'), 0) AS completed,
        COALESCE(SUM(booking_status = 'Confirmed'), 0) AS confirmed,
        COALESCE(SUM(booking_status = 'Pending'), 0)   AS pending,
        COALESCE(SUM(CASE WHEN booking_status IN ('Completed','Confirmed') THEN total_amount ELSE 0 END), 0) AS total_spent
     FROM bookings WHERE user_id = ?"
);
$statsStmt->bind_param("i", $user_id);
$statsStmt->execute();
$stats = $statsStmt->get_result()->fetch_assoc();
$statsStmt->close();

$memberSince = !empty($user['created_at']) ? date('F Y', strtotime($user['created_at'])) : '—';
$initial     = strtoupper(substr($user['user_name'] ?? 'U', 0, 1));

$pageTitle = 'My Profile';
include "../includes/header.php";
?>

<main class="flex-grow pt-24 max-w-max-width mx-auto w-full px-margin-mobile md:px-margin-desktop pb-2xl">

<div class="mb-lg">
<h1 class="text-headline-md font-headline-md text-on-surface">My Profile</h1>
<p class="text-body-md font-body-md text-on-surface-variant mt-xs">Manage your personal information, security, and view your rental activity.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">

<!-- Left column: identity card + stats -->
<div class="lg:col-span-4 flex flex-col gap-gutter">

<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm flex flex-col items-center text-center">
<div class="w-24 h-24 rounded-full bg-primary text-on-primary flex items-center justify-center text-headline-lg font-headline-lg mb-md">
<?php echo htmlspecialchars($initial); ?>
</div>
<h2 class="text-headline-sm font-headline-sm text-on-surface"><?php echo htmlspecialchars($user['user_name'] ?? ''); ?></h2>
<p class="text-body-sm font-body-sm text-on-surface-variant mt-xs"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
<div class="mt-md w-full pt-md border-t border-outline-variant flex flex-col gap-sm text-left">
<div class="flex items-center gap-sm text-on-surface-variant">
<span class="material-symbols-outlined text-[18px]">call</span>
<span class="text-body-sm font-body-sm"><?php echo htmlspecialchars($user['mobile_number'] ?? 'Not provided'); ?></span>
</div>
<div class="flex items-center gap-sm text-on-surface-variant">
<span class="material-symbols-outlined text-[18px]">calendar_today</span>
<span class="text-body-sm font-body-sm">Member since <?php echo htmlspecialchars($memberSince); ?></span>
</div>
</div>
<a href="booking_history.php" class="w-full mt-lg border border-primary text-primary font-label-md text-label-md py-2 rounded-lg hover:bg-surface-container-low transition-colors">View My Bookings</a>
</div>

<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm">
<h3 class="text-label-md font-label-md text-on-surface-variant mb-md">Rental Summary</h3>
<div class="grid grid-cols-2 gap-md">
<div class="bg-surface-container-low rounded-lg p-md text-center">
<p class="text-headline-sm font-headline-sm text-primary"><?php echo (int) $stats['total']; ?></p>
<p class="text-label-sm font-label-sm text-on-surface-variant mt-xs">Total Bookings</p>
</div>
<div class="bg-surface-container-low rounded-lg p-md text-center">
<p class="text-headline-sm font-headline-sm text-tertiary"><?php echo (int) $stats['completed']; ?></p>
<p class="text-label-sm font-label-sm text-on-surface-variant mt-xs">Completed</p>
</div>
<div class="bg-surface-container-low rounded-lg p-md text-center">
<p class="text-headline-sm font-headline-sm text-secondary"><?php echo (int) $stats['confirmed'] + (int) $stats['pending']; ?></p>
<p class="text-label-sm font-label-sm text-on-surface-variant mt-xs">Active</p>
</div>
<div class="bg-surface-container-low rounded-lg p-md text-center">
<p class="text-headline-sm font-headline-sm text-on-surface">₹<?php echo number_format((float) $stats['total_spent'], 0); ?></p>
<p class="text-label-sm font-label-sm text-on-surface-variant mt-xs">Total Spent</p>
</div>
</div>
</div>

</div>

<!-- Right column: editable forms -->
<div class="lg:col-span-8 flex flex-col gap-gutter">

<!-- Personal Information -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm">
<h3 class="text-headline-sm font-headline-sm text-on-surface mb-lg pb-md border-b border-outline-variant">Personal Information</h3>

<?php if ($profileSuccess): ?>
<div class="mb-md bg-tertiary-fixed/30 border border-tertiary text-on-tertiary-fixed-variant rounded-lg p-md text-body-sm font-body-sm">
<?php echo htmlspecialchars($profileSuccess); ?>
</div>
<?php endif; ?>
<?php if ($profileErrors): ?>
<div class="mb-md bg-error-container border border-error text-on-error-container rounded-lg p-md">
<ul class="list-disc list-inside text-body-sm font-body-sm">
<?php foreach ($profileErrors as $e): ?>
<li><?php echo htmlspecialchars($e); ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<form method="POST" class="space-y-md">
<input type="hidden" name="action" value="update_profile">
<div class="grid grid-cols-1 md:grid-cols-2 gap-md">
<div>
<label class="block text-label-sm font-label-sm text-on-surface-variant mb-xs" for="name">Full Name</label>
<input class="w-full bg-surface-container-lowest border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary text-body-md font-body-md px-4 py-3 outline-none transition-shadow" id="name" name="name" type="text" value="<?php echo htmlspecialchars($user['user_name'] ?? ''); ?>" required>
</div>
<div>
<label class="block text-label-sm font-label-sm text-on-surface-variant mb-xs" for="phone">Phone Number</label>
<input class="w-full bg-surface-container-lowest border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary text-body-md font-body-md px-4 py-3 outline-none transition-shadow" id="phone" name="phone" type="text" placeholder="+91 98765 43210" value="<?php echo htmlspecialchars($user['mobile_number'] ?? ''); ?>">
</div>
</div>
<div>
<label class="block text-label-sm font-label-sm text-on-surface-variant mb-xs" for="email">Email Address</label>
<input class="w-full bg-surface-container-lowest border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary text-body-md font-body-md px-4 py-3 outline-none transition-shadow" id="email" name="email" type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
</div>
<div class="pt-sm">
<button class="bg-primary text-on-primary font-label-md text-label-md rounded-lg py-3 px-xl hover:bg-primary-container transition-colors shadow-sm" type="submit">Save Changes</button>
</div>
</form>
</div>

<!-- Security -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm">
<h3 class="text-headline-sm font-headline-sm text-on-surface mb-lg pb-md border-b border-outline-variant">Security</h3>

<?php if ($pwSuccess): ?>
<div class="mb-md bg-tertiary-fixed/30 border border-tertiary text-on-tertiary-fixed-variant rounded-lg p-md text-body-sm font-body-sm">
<?php echo htmlspecialchars($pwSuccess); ?>
</div>
<?php endif; ?>
<?php if ($pwErrors): ?>
<div class="mb-md bg-error-container border border-error text-on-error-container rounded-lg p-md">
<ul class="list-disc list-inside text-body-sm font-body-sm">
<?php foreach ($pwErrors as $e): ?>
<li><?php echo htmlspecialchars($e); ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<form method="POST" class="space-y-md">
<input type="hidden" name="action" value="change_password">
<div>
<label class="block text-label-sm font-label-sm text-on-surface-variant mb-xs" for="current_password">Current Password</label>
<input class="w-full bg-surface-container-lowest border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary text-body-md font-body-md px-4 py-3 outline-none transition-shadow" id="current_password" name="current_password" type="password" required>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-md">
<div>
<label class="block text-label-sm font-label-sm text-on-surface-variant mb-xs" for="new_password">New Password</label>
<input class="w-full bg-surface-container-lowest border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary text-body-md font-body-md px-4 py-3 outline-none transition-shadow" id="new_password" name="new_password" type="password" minlength="6" required>
</div>
<div>
<label class="block text-label-sm font-label-sm text-on-surface-variant mb-xs" for="confirm_password">Confirm New Password</label>
<input class="w-full bg-surface-container-lowest border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary text-body-md font-body-md px-4 py-3 outline-none transition-shadow" id="confirm_password" name="confirm_password" type="password" minlength="6" required>
</div>
</div>
<div class="pt-sm">
<button class="border border-primary text-primary font-label-md text-label-md rounded-lg py-3 px-xl hover:bg-surface-container-low transition-colors" type="submit">Update Password</button>
</div>
</form>
</div>

</div>
</div>
</main>

<?php include "../includes/footer.php"; ?>