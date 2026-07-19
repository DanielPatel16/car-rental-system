<?php

$base      = $base      ?? '';
$pageTitle = $pageTitle ?? 'DriveEase';
$activeNav = $activeNav ?? '';

// Auto-detect which nav item should be "active" from the current filename,
// so a page doesn't break the highlight just because it forgot to set
// $activeNav manually before including this file. A page can still set
// $activeNav itself beforehand to override this.
if ($activeNav === '') {
    $__current_file = basename($_SERVER['PHP_SELF']);
    $__auto_nav_map = [
        'dashboard.php'       => 'home',
        'index.php'           => 'home',
        'cars.php'            => 'cars',
        'howitworks.php'      => 'how',
        'aboutus.php'         => 'about',
        'contactus.php'       => 'contact',
        'booking_history.php' => 'history',
    ];
    $activeNav = $__auto_nav_map[$__current_file] ?? '';
}

// Small helper to print the "active" vs "inactive" nav link classes (desktop)
// The active link gets a sliding underline (::after) driven by the nav-link-active
// class + the <style> rules below, so the indicator animates in rather than
// just appearing.
function navClass($key, $activeNav) {
    return $key === $activeNav
        ? 'nav-link nav-link-active'
        : 'nav-link';
}

// Same idea, styled for the stacked mobile menu (left accent bar instead of underline)
function navClassMobile($key, $activeNav) {
    return $key === $activeNav
        ? 'mobile-nav-link mobile-nav-link-active'
        : 'mobile-nav-link';
}

// Home always points to dashboard.php — this project has no separate public
// index.php, dashboard.php IS the home page. (See the auth-guard fix in
// dashboard.php itself: it must not force a login redirect, since Home needs
// to be reachable from public pages like cars.php/howitworks.php/aboutus.php/contactus.php
// even for guests who aren't logged in.)
$homeHref = $base . 'dashboard.php';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>DriveEase | <?php echo htmlspecialchars($pageTitle); ?></title>
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
          }
        }
      }
    </script>
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        vertical-align: middle;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: #faf8ff;
        color: #131b2e;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* ---------- Header shell ---------- */
    #site-header {
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 50;
        background-color: #faf8ff;
        border-bottom: 1px solid #c4c5d5;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .dj-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        padding: 0 16px;
        max-width: 1440px;
        margin: 0 auto;
        height: 64px;
    }

    @media (min-width: 768px) {
        .dj-header-row {
            padding: 0 32px;
        }
    }

    .dj-brand {
        font-size: 24px;
        line-height: 32px;
        letter-spacing: -0.01em;
        font-weight: 700;
        color: #00288e;
        text-decoration: none;
        flex-shrink: 0;
    }

    .dj-nav {
        display: none;
        align-items: center;
        gap: 32px;
    }

    @media (min-width: 768px) {
        .dj-nav {
            display: flex;
        }
    }

    .dj-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    @media (min-width: 768px) {
        .dj-actions {
            gap: 16px;
        }
    }

    /* ---------- Desktop nav: hover + active-state animation ---------- */
    .nav-link {
        position: relative;
        display: inline-block;
        color: #444653;
        font-size: 14px;
        line-height: 16px;
        letter-spacing: 0.05em;
        font-weight: 600;
        padding-bottom: 4px;
        text-decoration: none;
        transition: color 0.25s ease, transform 0.25s ease;
    }
    .nav-link::after {
        content: "";
        position: absolute;
        left: 50%;
        bottom: -1px;
        height: 2px;
        width: 100%;
        background: linear-gradient(90deg, #00288e, #003d27);
        border-radius: 9999px;
        transform: translateX(-50%) scaleX(0);
        transform-origin: center;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .nav-link:hover {
        color: #00288e;
        transform: translateY(-2px);
    }
    .nav-link:hover::after,
    .nav-link-active::after {
        transform: translateX(-50%) scaleX(1);
    }
    .nav-link-active {
        color: #00288e;
        transform: translateY(-1px);
        font-weight: 700;
    }

    /* Active page badge: a soft pulse the first moment the page loads, so it's unmistakable */
    @keyframes activePulse {
        0%   { background-color: #dde1ff; }
        100% { background-color: transparent; }
    }
    .nav-link-active {
        border-radius: 6px;
        padding-left: 6px;
        padding-right: 6px;
        animation: activePulse 1.2s ease-out;
    }

    /* ---------- Account button + avatar ---------- */
    #account-btn {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 9999px;
        background: none;
        border: none;
        cursor: pointer;
        transition: background-color 0.15s ease, transform 0.15s ease;
    }
    #account-btn:hover {
        background-color: #e2e7ff;
    }
    #account-btn:active {
        transform: scale(0.95);
    }

    .dj-avatar {
        width: 32px;
        height: 32px;
        border-radius: 9999px;
        background-color: #dde1ff;
        color: #00288e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.05em;
    }

    .dj-account-name {
        display: none;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.05em;
        color: #131b2e;
    }
    @media (min-width: 1024px) {
        .dj-account-name {
            display: inline;
        }
    }

    .dj-account-chevron {
        display: none;
        color: #757684;
        font-size: 16px;
    }
    @media (min-width: 768px) {
        .dj-account-chevron {
            display: inline;
        }
    }

    /* ---------- Account dropdown ---------- */
    #account-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 8px);
        width: 192px;
        background-color: #ffffff;
        border: 1px solid #c4c5d5;
        border-radius: 8px;
        box-shadow: 0px 10px 15px -3px rgba(0, 40, 142, 0.12);
        padding: 4px 0;
        opacity: 0;
        transform: translateY(-6px);
        pointer-events: none;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }
    #account-menu.open {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    .dj-account-link {
        display: block;
        padding: 8px 16px;
        font-size: 14px;
        color: #444653;
        text-decoration: none;
        transition: background-color 0.2s ease, color 0.2s ease;
    }
    .dj-account-link:hover {
        background-color: #e2e7ff;
        color: #00288e;
    }

    .dj-account-link-danger {
        color: #ba1a1a;
    }
    .dj-account-link-danger:hover {
        background-color: #ffdad6;
        color: #ba1a1a;
    }

    /* ---------- Guest links ---------- */
    .dj-link-login {
        display: none;
        color: #444653;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.05em;
        padding: 4px 16px;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    @media (min-width: 640px) {
        .dj-link-login {
            display: inline-block;
        }
    }
    .dj-link-login:hover {
        color: #00288e;
    }

    .dj-btn-register {
        background-color: #00288e;
        color: #ffffff;
        padding: 4px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-decoration: none;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }
    @media (min-width: 768px) {
        .dj-btn-register {
            padding: 4px 24px;
        }
    }
    .dj-btn-register:hover {
        background-color: #1e40af;
    }
    .dj-btn-register:active {
        transform: scale(0.95);
    }

    /* ---------- Mobile menu open/close ---------- */
    #hamburger-btn {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: none;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    #hamburger-btn:hover {
        background-color: #e2e7ff;
    }
    @media (min-width: 768px) {
        #hamburger-btn {
            display: none;
        }
    }

    #mobile-nav-panel {
        background-color: #faf8ff;
        border-top: 1px solid #c4c5d5;
        padding: 0 16px;
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: max-height 0.35s ease, opacity 0.25s ease;
    }
    #mobile-nav-panel.open {
        max-height: 32rem;
        opacity: 1;
    }
    @media (min-width: 768px) {
        #mobile-nav-panel {
            display: none;
        }
    }

    .dj-mobile-nav-inner {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding: 16px 0;
    }

    .dj-mobile-login-only {
        display: flex;
    }
    @media (min-width: 640px) {
        .dj-mobile-login-only {
            display: none;
        }
    }

    .hamburger-line {
        display: block;
        width: 20px;
        height: 2px;
        background-color: #131b2e;
        border-radius: 9999px;
        margin-bottom: 4px;
        transition: transform 0.3s ease, opacity 0.3s ease, y 0.3s ease;
        transform-origin: center;
    }
    .line-bot {
        margin-bottom: 0;
    }
    #hamburger-btn[aria-expanded="true"] .line-top { transform: translateY(6px) rotate(45deg); }
    #hamburger-btn[aria-expanded="true"] .line-mid { opacity: 0; }
    #hamburger-btn[aria-expanded="true"] .line-bot { transform: translateY(-6px) rotate(-45deg); }

    /* ---------- Mobile nav links (left accent bar) ---------- */
    .mobile-nav-link {
        display: flex;
        align-items: center;
        padding: 8px 16px;
        border-radius: 8px;
        color: #444653;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-decoration: none;
        border-left: 2px solid transparent;
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
    }
    .mobile-nav-link:hover {
        background-color: #e2e7ff;
        color: #00288e;
        transform: translateX(4px);
    }
    .mobile-nav-link-active {
        background-color: rgba(30, 64, 175, 0.1);
        color: #00288e;
        border-left-color: #00288e;
    }

    @media (prefers-reduced-motion: reduce) {
        .nav-link, .nav-link::after, .mobile-nav-link, #mobile-nav-panel, .hamburger-line, #account-menu {
            animation: none !important;
            transition: none !important;
        }
    }
</style>
<?php if (isset($extraHead)) echo $extraHead; // let a page inject a couple of extra <style>/<link> lines if it truly needs them ?>
</head>
<body>

<header id="site-header">
<div class="dj-header-row">

<a href="<?php echo $base; ?>index.php" class="dj-brand">DriveEase</a>

<nav class="dj-nav">
<a class="<?php echo navClass('home', $activeNav); ?>" href="<?php echo $homeHref; ?>" <?php echo $activeNav === 'home' ? 'aria-current="page"' : ''; ?>>Home</a>
<a class="<?php echo navClass('cars', $activeNav); ?>" href="<?php echo $base; ?>cars.php" <?php echo $activeNav === 'cars' ? 'aria-current="page"' : ''; ?>>Cars</a>
<a class="<?php echo navClass('how', $activeNav); ?>" href="<?php echo $base; ?>howitworks.php" <?php echo $activeNav === 'how' ? 'aria-current="page"' : ''; ?>>How it Works</a>
<a class="<?php echo navClass('about', $activeNav); ?>" href="<?php echo $base; ?>aboutus.php" <?php echo $activeNav === 'about' ? 'aria-current="page"' : ''; ?>>About</a>
<a class="<?php echo navClass('contact', $activeNav); ?>" href="<?php echo $base; ?>contactus.php" <?php echo $activeNav === 'contact' ? 'aria-current="page"' : ''; ?>>Contact</a>
<?php if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'user'): ?>
<a class="<?php echo navClass('history', $activeNav); ?>" href="<?php echo $base; ?>booking_history.php" <?php echo $activeNav === 'history' ? 'aria-current="page"' : ''; ?>>Booking History</a>
<?php endif; ?>
</nav>

<div class="dj-actions">
<?php if (isset($_SESSION['user_id'])): ?>
    <div class="relative">
        <button id="account-btn" aria-haspopup="true" aria-expanded="false">
            <span class="dj-avatar">
                <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
            </span>
            <span class="dj-account-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Account'); ?></span>
            <span class="material-symbols-outlined dj-account-chevron">expand_more</span>
        </button>
        <div id="account-menu">
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <a href="<?php echo $base; ?>admin/dashboard.php" class="dj-account-link">Admin Dashboard</a>
            <?php else: ?>
            <a href="<?php echo $base; ?>profile.php" class="dj-account-link">My Profile</a>
            <a href="<?php echo $base; ?>booking_history.php" class="dj-account-link">My Bookings</a>
            <?php endif; ?>
            <a href="<?php echo $base; ?>../logout.php" class="dj-account-link dj-account-link-danger">Logout</a>
        </div>
    </div>
<?php else: ?>
    <a href="<?php echo $base; ?>login.php" class="dj-link-login">Login</a>
    <a href="<?php echo $base; ?>register.php" class="dj-btn-register">Register</a>
<?php endif; ?>

<!-- Hamburger: shown on mobile & tablet, hidden once the full nav fits (md and up) -->
<button id="hamburger-btn" aria-expanded="false" aria-controls="mobile-nav-panel" aria-label="Toggle navigation menu">
    <span class="hamburger-line line-top"></span>
    <span class="hamburger-line line-mid"></span>
    <span class="hamburger-line line-bot"></span>
</button>
</div>

</div>

<!-- Mobile / tablet nav panel -->
<nav id="mobile-nav-panel">
<div class="dj-mobile-nav-inner">
<a class="<?php echo navClassMobile('home', $activeNav); ?>" href="<?php echo $homeHref; ?>" <?php echo $activeNav === 'home' ? 'aria-current="page"' : ''; ?>>Home</a>
<a class="<?php echo navClassMobile('cars', $activeNav); ?>" href="<?php echo $base; ?>cars.php" <?php echo $activeNav === 'cars' ? 'aria-current="page"' : ''; ?>>Cars</a>
<a class="<?php echo navClassMobile('how', $activeNav); ?>" href="<?php echo $base; ?>howitworks.php" <?php echo $activeNav === 'how' ? 'aria-current="page"' : ''; ?>>How it Works</a>
<a class="<?php echo navClassMobile('about', $activeNav); ?>" href="<?php echo $base; ?>aboutus.php" <?php echo $activeNav === 'about' ? 'aria-current="page"' : ''; ?>>About</a>
<a class="<?php echo navClassMobile('contact', $activeNav); ?>" href="<?php echo $base; ?>contactus.php" <?php echo $activeNav === 'contact' ? 'aria-current="page"' : ''; ?>>Contact</a>
<?php if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'user'): ?>
<a class="<?php echo navClassMobile('history', $activeNav); ?>" href="<?php echo $base; ?>booking_history.php" <?php echo $activeNav === 'history' ? 'aria-current="page"' : ''; ?>>Booking History</a>
<?php endif; ?>
<?php if (!isset($_SESSION['user_id'])): ?>
<a href="<?php echo $base; ?>login.php" class="mobile-nav-link dj-mobile-login-only">Login</a>
<?php endif; ?>
</div>
</nav>
</header>

<script>
(function () {
    var hamburger   = document.getElementById('hamburger-btn');
    var mobilePanel = document.getElementById('mobile-nav-panel');
    var accountBtn  = document.getElementById('account-btn');
    var accountMenu = document.getElementById('account-menu');

    function closeMobileMenu() {
        if (!hamburger || !mobilePanel) return;
        hamburger.setAttribute('aria-expanded', 'false');
        mobilePanel.classList.remove('open');
    }

    if (hamburger && mobilePanel) {
        hamburger.addEventListener('click', function () {
            var isOpen = hamburger.getAttribute('aria-expanded') === 'true';
            hamburger.setAttribute('aria-expanded', String(!isOpen));
            mobilePanel.classList.toggle('open', !isOpen);
            if (!isOpen && accountMenu) { accountMenu.classList.remove('open'); accountBtn && accountBtn.setAttribute('aria-expanded', 'false'); }
        });
        // Close the mobile menu automatically once the viewport is wide enough for the full nav
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 768) closeMobileMenu();
        });
    }

    if (accountBtn && accountMenu) {
        accountBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var isOpen = accountMenu.classList.toggle('open');
            accountBtn.setAttribute('aria-expanded', String(isOpen));
            if (isOpen) closeMobileMenu();
        });
        document.addEventListener('click', function (e) {
            if (!accountMenu.contains(e.target) && !accountBtn.contains(e.target)) {
                accountMenu.classList.remove('open');
                accountBtn.setAttribute('aria-expanded', 'false');
            }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                accountMenu.classList.remove('open');
                accountBtn.setAttribute('aria-expanded', 'false');
                closeMobileMenu();
            }
        });
    }
})();
</script>