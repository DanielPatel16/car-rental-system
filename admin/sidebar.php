<?php
// admin/includes/sidebar.php
// Shared sidebar for all admin pages.
// Usage: include "includes/sidebar.php";  (from any file inside /admin)
// Styling lives in admin/assets/sidebar.css (linked once in your page <head>).

$current_page = basename($_SERVER['PHP_SELF']);

$nav_items = [
    ['label' => 'Dashboard',   'icon' => 'dashboard',      'href' => 'dashboard.php'],
    ['label' => 'Fleet',       'icon' => 'directions_car', 'href' => 'cars.php'],
    ['label' => 'Bookings',    'icon' => 'calendar_today', 'href' => 'bookings.php'],
    ['label' => 'Customers',   'icon' => 'group',          'href' => 'users.php'],
    ['label' => 'Reports',     'icon' => 'insights',       'href' => 'reports.php'],
    // ['label' => 'Maintenance', 'icon' => 'build',          'href' => '#'],
    // ['label' => 'Settings',    'icon' => 'settings',       'href' => '#'],
];
?>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
<!-- Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<!-- Tailwind CSS (loaded once here; every page that includes this sidebar gets it automatically) -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
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
                borderRadius: {
                    DEFAULT: "0.25rem",
                    lg: "0.5rem",
                    xl: "0.75rem",
                    full: "9999px"
                },
                spacing: {
                    xs: "4px",
                    xl: "32px",
                    base: "4px",
                    "margin-mobile": "16px",
                    "margin-desktop": "32px",
                    md: "16px",
                    gutter: "24px",
                    lg: "24px",
                    sm: "8px",
                    "max-width": "1440px",
                    "2xl": "48px"
                },
                fontFamily: {
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
                fontSize: {
                    "headline-sm": ["20px", {lineHeight: "28px", fontWeight: "600"}],
                    "headline-md": ["24px", {lineHeight: "32px", letterSpacing: "-0.01em", fontWeight: "600"}],
                    "body-lg": ["18px", {lineHeight: "28px", fontWeight: "400"}],
                    "label-sm": ["12px", {lineHeight: "16px", fontWeight: "500"}],
                    "body-sm": ["14px", {lineHeight: "20px", fontWeight: "400"}],
                    "headline-lg": ["32px", {lineHeight: "40px", letterSpacing: "-0.02em", fontWeight: "700"}],
                    "label-md": ["14px", {lineHeight: "16px", letterSpacing: "0.05em", fontWeight: "600"}],
                    "headline-lg-mobile": ["24px", {lineHeight: "32px", letterSpacing: "-0.01em", fontWeight: "700"}],
                    "body-md": ["16px", {lineHeight: "24px", fontWeight: "400"}]
                }
            }
        }
    }
</script>
<style>
    /* Font-family + Material Symbols fill are already guaranteed by the
       Tailwind config + font links loaded above in this same file. */
    .sidebar {
        font-family: 'Inter', sans-serif;
    }

    .sidebar .material-symbols-outlined {
        font-family: 'Material Symbols Outlined';
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }

    .sidebar .sidebar-link.active .material-symbols-outlined {
        font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }

    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        height: 100%;
        width: 16rem;
        background-color: #f2f3ff;
        border-right: 1px solid #c4c5d5;
        display: flex;
        flex-direction: column;
        padding: 24px 0;
        z-index: 50;
    }

    .sidebar-brand {
        padding: 0 16px;
        margin-bottom: 32px;
    }

    .sidebar-brand h1 {
        font-size: 24px;
        font-weight: 700;
        color: #00288e;
        margin: 0;
    }

    .sidebar-brand p {
        font-size: 14px;
        color: #516072;
        margin: 4px 0 0;
    }

    .sidebar-nav {
        flex-grow: 1;
        overflow-y: auto;
        padding: 0 8px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 8px 16px;
        border-radius: 8px;
        color: #516072;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    .sidebar-link:hover {
        background-color: #e2e7ff;
    }

    .sidebar-link.active {
        background-color: #d2e1f7;
        color: #00288e;
        font-weight: 700;
    }

    .sidebar-link .material-symbols-outlined {
        font-size: 20px;
    }

    .sidebar-footer {
        margin-top: auto;
        padding: 16px 8px 0;
        border-top: 1px solid #c4c5d5;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .sidebar-cta {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        background-color: #00288e;
        color: #ffffff;
        border-radius: 8px;
        padding: 8px 16px;
        margin-bottom: 16px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: opacity 0.15s ease;
    }

    .sidebar-cta:hover {
        opacity: 0.9;
    }

    .sidebar-logout {
        color: #ba1a1a;
    }

    .sidebar-logout:hover {
        background-color: #ffdad6;
    }

    /* ---------- Mobile toggle button ---------- */
    .sidebar-toggle {
        display: none;
        position: fixed;
        top: 12px;
        left: 12px;
        z-index: 60;
        background-color: #00288e;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        width: 40px;
        height: 40px;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .sidebar-toggle .material-symbols-outlined {
        font-family: 'Material Symbols Outlined';
        font-size: 22px;
    }

    /* ---------- Overlay behind mobile sidebar ---------- */
    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background-color: rgba(19, 27, 46, 0.5);
        z-index: 40;
    }

    .sidebar-overlay.open {
        display: block;
    }

    /* ---------- Tablet: narrower, icon-friendly sidebar ---------- */
    @media (max-width: 1024px) {
        .sidebar {
            width: 5rem;
        }

        .sidebar-brand p,
        .sidebar-link span:not(.material-symbols-outlined),
        .sidebar-cta {
            display: none;
        }

        .sidebar-link,
        .sidebar-cta {
            justify-content: center;
            padding: 12px;
        }

        .sidebar-cta {
            display: flex;
        }
    }

    /* ---------- Mobile: off-canvas sidebar ---------- */
    @media (max-width: 640px) {
        .sidebar-toggle {
            display: flex;
        }

        .sidebar {
            width: 16rem;
            transform: translateX(-100%);
            transition: transform 0.25s ease;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .sidebar-brand p,
        .sidebar-link span:not(.material-symbols-outlined) {
            display: inline;
        }

        .sidebar-cta {
            display: flex;
        }

        .sidebar-link,
        .sidebar-cta {
            justify-content: flex-start;
            padding: 8px 16px;
        }
    }
</style>

<!-- Hamburger button, visible on mobile only -->
<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle menu">
    <span class="material-symbols-outlined">menu</span>
</button>

<!-- Overlay, closes sidebar when tapped -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <h1>DriveEase</h1>
        <p>Fleet Management</p>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($nav_items as $item):
            $isActive = ($current_page === $item['href']);
        ?>
        <a href="<?php echo htmlspecialchars($item['href']); ?>"
           class="sidebar-link<?php echo $isActive ? ' active' : ''; ?>">
            <span class="material-symbols-outlined"><?php echo $item['icon']; ?></span>
            <span><?php echo htmlspecialchars($item['label']); ?></span>
        </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <a href="cars.php" class="sidebar-cta">
            <span class="material-symbols-outlined">add</span>
            Add New Vehicle
        </a>
        <a href="#" class="sidebar-link">
            <span class="material-symbols-outlined">help</span>
            <span>Support</span>
        </a>
        <a href="../logout.php" class="sidebar-link sidebar-logout">
            <span class="material-symbols-outlined">logout</span>
            <span>Logout</span>
        </a>
    </div>
</aside>

<script>
    (function () {
        var sidebar = document.getElementById('sidebar');
        var toggle = document.getElementById('sidebarToggle');
        var overlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('open');
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        }

        toggle.addEventListener('click', function () {
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        overlay.addEventListener('click', closeSidebar);

        // Close automatically if the viewport is resized back to desktop/tablet
        window.addEventListener('resize', function () {
            if (window.innerWidth > 640) {
                closeSidebar();
            }
        });
    })();
</script>