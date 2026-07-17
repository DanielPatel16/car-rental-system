<?php

$base      = $base      ?? '';
$pageTitle = $pageTitle ?? 'DriveEase';
$activeNav = $activeNav ?? '';

// Small helper to print the "active" vs "inactive" nav link classes (desktop)
// The active link gets a sliding underline (::after) driven by the nav-link-active
// class + the <style> rules below, so the indicator animates in rather than
// just appearing.
function navClass($key, $activeNav) {
    return $key === $activeNav
        ? 'nav-link nav-link-active relative text-primary pb-1 text-label-md font-label-md'
        : 'nav-link relative text-on-surface-variant hover:text-primary transition-colors duration-200 pb-1 text-label-md font-label-md';
}

// Same idea, styled for the stacked mobile menu (left accent bar instead of underline)
function navClassMobile($key, $activeNav) {
    return $key === $activeNav
        ? 'mobile-nav-link mobile-nav-link-active flex items-center px-md py-sm rounded-lg bg-primary-container/10 text-primary text-label-md font-label-md border-l-2 border-primary'
        : 'mobile-nav-link flex items-center px-md py-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors duration-200 text-label-md font-label-md border-l-2 border-transparent';
}
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
    body { font-family: 'Inter', sans-serif; }

    /* ---------- Desktop nav: hover + active-state animation ---------- */
    .nav-link {
        transition: color 0.25s ease, transform 0.25s ease;
    }
    .nav-link::after {
        content: "";
        position: absolute;
        left: 50%;
        bottom: -1px;
        height: 2px;
        width: 100%;
        background: linear-gradient(90deg, theme('colors.primary'), theme('colors.tertiary'));
        border-radius: 9999px;
        transform: translateX(-50%) scaleX(0);
        transform-origin: center;
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .nav-link:hover {
        color: theme('colors.primary');
        transform: translateY(-2px);
    }
    .nav-link:hover::after,
    .nav-link-active::after {
        transform: translateX(-50%) scaleX(1);
    }
    .nav-link-active {
        transform: translateY(-1px);
        font-weight: 700;
    }

    /* Active page badge: a soft pulse the first moment the page loads, so it's unmistakable */
    @keyframes activePulse {
        0%   { background-color: theme('colors.primary-fixed'); }
        100% { background-color: transparent; }
    }
    .nav-link-active {
        border-radius: 6px;
        padding-left: 6px;
        padding-right: 6px;
        animation: activePulse 1.2s ease-out;
    }

    /* ---------- Mobile menu open/close ---------- */
    #mobile-nav-panel {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: max-height 0.35s ease, opacity 0.25s ease;
    }
    #mobile-nav-panel.open {
        max-height: 32rem;
        opacity: 1;
    }
    .hamburger-line {
        transition: transform 0.3s ease, opacity 0.3s ease, y 0.3s ease;
        transform-origin: center;
    }
    #hamburger-btn[aria-expanded="true"] .line-top { transform: translateY(6px) rotate(45deg); }
    #hamburger-btn[aria-expanded="true"] .line-mid { opacity: 0; }
    #hamburger-btn[aria-expanded="true"] .line-bot { transform: translateY(-6px) rotate(-45deg); }

    /* ---------- Account dropdown ---------- */
    #account-menu {
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

    .mobile-nav-link {
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
    }
    .mobile-nav-link:hover {
        transform: translateX(4px);
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
<body class="bg-background text-on-background min-h-screen flex flex-col">

<header id="site-header" class="fixed top-0 w-full z-50 bg-surface border-b border-outline-variant shadow-sm">
<div class="flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop max-w-max-width mx-auto h-16">

<a href="<?php echo $base; ?>index.php" class="text-headline-md font-headline-md font-bold text-primary shrink-0">DriveEase</a>

<nav class="hidden md:flex items-center gap-xl">
<a class="<?php echo navClass('home', $activeNav); ?>" href="<?php echo $base; ?>dashboard.php" <?php echo $activeNav === 'home' ? 'aria-current="page"' : ''; ?>>Home</a>
<a class="<?php echo navClass('cars', $activeNav); ?>" href="<?php echo $base; ?>cars.php" <?php echo $activeNav === 'cars' ? 'aria-current="page"' : ''; ?>>Cars</a>
<a class="<?php echo navClass('how', $activeNav); ?>" href="<?php echo $base; ?>howitworks.php" <?php echo $activeNav === 'how' ? 'aria-current="page"' : ''; ?>>How it Works</a>
<a class="<?php echo navClass('about', $activeNav); ?>" href="<?php echo $base; ?>aboutus.php" <?php echo $activeNav === 'about' ? 'aria-current="page"' : ''; ?>>About</a>
<a class="<?php echo navClass('contact', $activeNav); ?>" href="<?php echo $base; ?>contactus.php" <?php echo $activeNav === 'contact' ? 'aria-current="page"' : ''; ?>>Contact</a>
<?php if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'user'): ?>
<a class="<?php echo navClass('history', $activeNav); ?>" href="<?php echo $base; ?>booking_history.php" <?php echo $activeNav === 'history' ? 'aria-current="page"' : ''; ?>>Booking History</a>
<?php endif; ?>
</nav>

<div class="flex items-center gap-sm md:gap-md">
<?php if (isset($_SESSION['user_id'])): ?>
    <div class="relative">
        <button id="account-btn" aria-haspopup="true" aria-expanded="false" class="flex items-center gap-xs px-sm py-1 rounded-full hover:bg-surface-container-high active:scale-95 transition-all duration-150">
            <span class="w-8 h-8 rounded-full bg-primary-fixed text-primary flex items-center justify-center font-label-md text-label-md">
                <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
            </span>
            <span class="hidden lg:inline text-label-md font-label-md text-on-surface"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Account'); ?></span>
            <span class="material-symbols-outlined hidden md:inline text-outline text-sm">expand_more</span>
        </button>
        <div id="account-menu" class="absolute right-0 mt-2 w-48 bg-surface-container-lowest border border-outline-variant rounded-lg shadow-lg py-xs">
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <a href="<?php echo $base; ?>admin/dashboard.php" class="block px-md py-sm text-body-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors">Admin Dashboard</a>
            <?php else: ?>
            <a href="<?php echo $base; ?>profile.php" class="block px-md py-sm text-body-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors">My Profile</a>
            <a href="<?php echo $base; ?>booking_history.php" class="block px-md py-sm text-body-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors">My Bookings</a>
            <?php endif; ?>
            <a href="<?php echo $base; ?>../logout.php" class="block px-md py-sm text-body-sm text-error hover:bg-error-container transition-colors">Logout</a>
        </div>
    </div>
<?php else: ?>
    <a href="<?php echo $base; ?>login.php" class="hidden sm:inline-block text-on-surface-variant hover:text-primary transition-colors duration-200 text-label-md font-label-md px-md py-xs">Login</a>
    <a href="<?php echo $base; ?>register.php" class="bg-primary text-on-primary px-md md:px-lg py-xs rounded-lg font-label-md text-label-md hover:bg-primary-container active:scale-95 transition-all duration-200">Register</a>
<?php endif; ?>

<!-- Hamburger: shown on mobile & tablet, hidden once the full nav fits (md and up) -->
<button id="hamburger-btn" aria-expanded="false" aria-controls="mobile-nav-panel" aria-label="Toggle navigation menu" class="md:hidden flex flex-col justify-center items-center w-9 h-9 rounded-lg hover:bg-surface-container-high transition-colors">
    <span class="hamburger-line line-top block w-5 h-0.5 bg-on-surface rounded-full mb-1"></span>
    <span class="hamburger-line line-mid block w-5 h-0.5 bg-on-surface rounded-full mb-1"></span>
    <span class="hamburger-line line-bot block w-5 h-0.5 bg-on-surface rounded-full"></span>
</button>
</div>

</div>

<!-- Mobile / tablet nav panel -->
<nav id="mobile-nav-panel" class="md:hidden bg-surface border-t border-outline-variant px-margin-mobile">
<div class="flex flex-col gap-xs py-md">
<a class="<?php echo navClassMobile('home', $activeNav); ?>" href="<?php echo $base; ?>dashboard.php" <?php echo $activeNav === 'home' ? 'aria-current="page"' : ''; ?>>Home</a>
<a class="<?php echo navClassMobile('cars', $activeNav); ?>" href="<?php echo $base; ?>cars.php" <?php echo $activeNav === 'cars' ? 'aria-current="page"' : ''; ?>>Cars</a>
<a class="<?php echo navClassMobile('how', $activeNav); ?>" href="<?php echo $base; ?>howitworks.php" <?php echo $activeNav === 'how' ? 'aria-current="page"' : ''; ?>>How it Works</a>
<a class="<?php echo navClassMobile('about', $activeNav); ?>" href="<?php echo $base; ?>aboutus.php" <?php echo $activeNav === 'about' ? 'aria-current="page"' : ''; ?>>About</a>
<a class="<?php echo navClassMobile('contact', $activeNav); ?>" href="<?php echo $base; ?>contactus.php" <?php echo $activeNav === 'contact' ? 'aria-current="page"' : ''; ?>>Contact</a>
<?php if (isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') === 'user'): ?>
<a class="<?php echo navClassMobile('history', $activeNav); ?>" href="<?php echo $base; ?>booking_history.php" <?php echo $activeNav === 'history' ? 'aria-current="page"' : ''; ?>>Booking History</a>
<?php endif; ?>
<?php if (!isset($_SESSION['user_id'])): ?>
<a href="<?php echo $base; ?>login.php" class="sm:hidden flex items-center px-md py-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors text-label-md font-label-md border-l-2 border-transparent">Login</a>
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