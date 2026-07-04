<!DOCTYPE html><html class="light" lang="en"><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>DriveEase Admin - Bookings Management</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "surface-container-low": "#f2f3ff",
                    "surface-dim": "#d2d9f4",
                    "primary-container": "#1e40af",
                    "on-background": "#131b2e",
                    "on-surface-variant": "#444653",
                    "on-primary": "#ffffff",
                    "tertiary-fixed-dim": "#4edea3",
                    "on-tertiary-container": "#3fd298",
                    "secondary": "#516072",
                    "surface-container": "#eaedff",
                    "on-secondary-container": "#556477",
                    "on-error": "#ffffff",
                    "tertiary-fixed": "#6ffbbe",
                    "surface-container-high": "#e2e7ff",
                    "secondary-container": "#d2e1f7",
                    "outline-variant": "#c4c5d5",
                    "on-primary-container": "#a8b8ff",
                    "surface-container-highest": "#dae2fd",
                    "surface": "#faf8ff",
                    "on-tertiary-fixed": "#002113",
                    "surface-tint": "#3755c3",
                    "on-primary-fixed-variant": "#173bab",
                    "on-tertiary": "#ffffff",
                    "primary-fixed-dim": "#b8c4ff",
                    "on-surface": "#131b2e",
                    "primary": "#00288e",
                    "secondary-fixed": "#d4e4fa",
                    "on-error-container": "#93000a",
                    "primary-fixed": "#dde1ff",
                    "on-primary-fixed": "#001453",
                    "secondary-fixed-dim": "#b9c8de",
                    "on-secondary-fixed": "#0d1c2d",
                    "error-container": "#ffdad6",
                    "on-tertiary-fixed-variant": "#005236",
                    "surface-container-lowest": "#ffffff",
                    "inverse-on-surface": "#eef0ff",
                    "inverse-surface": "#283044",
                    "inverse-primary": "#b8c4ff",
                    "tertiary": "#003d27",
                    "surface-variant": "#dae2fd",
                    "error": "#ba1a1a",
                    "background": "#faf8ff",
                    "tertiary-container": "#00563a",
                    "on-secondary": "#ffffff",
                    "surface-bright": "#faf8ff",
                    "on-secondary-fixed-variant": "#39485a",
                    "outline": "#757684"
            },
            "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
            },
            "spacing": {
                    "margin-desktop": "32px",
                    "lg": "24px",
                    "gutter": "24px",
                    "max-width": "1440px",
                    "xl": "32px",
                    "sm": "8px",
                    "md": "16px",
                    "margin-mobile": "16px",
                    "xs": "4px",
                    "base": "4px",
                    "2xl": "48px"
            },
            "fontFamily": {
                    "label-sm": ["Inter"],
                    "body-sm": ["Inter"],
                    "label-md": ["Inter"],
                    "body-lg": ["Inter"],
                    "body-md": ["Inter"],
                    "headline-sm": ["Inter"],
                    "headline-md": ["Inter"],
                    "headline-lg-mobile": ["Inter"],
                    "headline-lg": ["Inter"]
            },
            "fontSize": {
                    "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "500"}],
                    "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                    "label-md": ["14px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                    "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                    "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                    "headline-sm": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                    "headline-md": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                    "headline-lg-mobile": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "700"}],
                    "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700"}]
            }
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
        }
        body { font-family: 'Inter', sans-serif; }
        .booking-row:hover { background-color: rgba(210, 225, 247, 0.3); }
        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 1);
        }
    </style>
</head>
<body class="bg-background text-on-surface min-h-screen">
<!-- SideNavBar Anchor -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container-lowest dark:bg-on-background border-r border-outline-variant dark:border-outline shadow-sm dark:shadow-none z-50">
<div class="flex flex-col h-full p-md space-y-sm">
<div class="py-md px-sm">
<h1 class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed">DriveEase Admin</h1>
<p class="font-label-sm text-label-sm text-secondary">Fleet Management</p>
</div>
<nav class="flex-grow space-y-xs pt-md">
<a class="flex items-center gap-md px-md py-sm text-secondary dark:text-secondary-fixed-dim hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant rounded-lg transition-colors group" href="dashboard.php">
<span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
<span class="font-label-md text-label-md">Dashboard</span>
</a>
<a class="flex items-center gap-md px-md py-sm text-secondary dark:text-secondary-fixed-dim hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant rounded-lg transition-colors group" href="cars.php">
<span class="material-symbols-outlined" data-icon="directions_car">directions_car</span>
<span class="font-label-md text-label-md">Fleet</span>
</a>
<!-- ACTIVE TAB: Bookings -->
<a class="flex items-center gap-md px-md py-sm text-primary dark:text-primary-fixed-dim font-bold bg-secondary-container dark:bg-primary-container rounded-lg transition-all duration-200" href="bookings.php">
<span class="material-symbols-outlined" data-icon="calendar_month">calendar_month</span>
<span class="font-label-md text-label-md">Bookings</span>
</a>
<a class="flex items-center gap-md px-md py-sm text-secondary dark:text-secondary-fixed-dim hover:bg-surface-container-high dark:hover:bg-on-secondary-fixed-variant rounded-lg transition-colors group" href="users.php">
<span class="material-symbols-outlined" data-icon="group">group</span>
<span class="font-label-md text-label-md">Customers</span>
</a>
</nav>
<div class="mt-auto pt-md border-t border-outline-variant">
<button class="w-full bg-primary text-on-primary py-sm rounded-lg font-label-md text-label-md hover:opacity-90 transition-all flex items-center justify-center gap-sm">
<span class="material-symbols-outlined" data-icon="add">add</span>
                    New Booking
                </button>
</div>
</div>
</aside>
<!-- TopNavBar Anchor -->
<header class="fixed top-0 right-0 w-[calc(100%-16rem)] h-16 bg-surface dark:bg-on-background border-b border-outline-variant dark:border-outline z-40">
<div class="flex justify-between items-center px-xl h-full w-full">
<div class="flex items-center gap-md w-1/3">
<div class="relative w-full max-w-md">
<span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-secondary" data-icon="search">search</span>
<input class="w-full pl-10 pr-md py-xs bg-surface-container-low border border-outline-variant rounded-lg focus:outline-none focus:border-primary font-body-sm text-body-sm" placeholder="Search bookings, customers, or VIN..." type="text">
</div>
</div>
<div class="flex items-center gap-lg">
<button class="relative text-secondary hover:text-primary transition-colors">
<span class="material-symbols-outlined" data-icon="notifications">notifications</span>
<span class="absolute top-0 right-0 w-2 h-2 bg-error rounded-full"></span>
</button>
<button class="text-secondary hover:text-primary transition-colors">
<span class="material-symbols-outlined" data-icon="help_outline">help_outline</span>
</button>
<div class="flex items-center gap-sm pl-md border-l border-outline-variant">
<div class="text-right">
<p class="font-label-md text-label-md text-on-surface">Alex Manager</p>
<p class="font-label-sm text-label-sm text-secondary">Admin Access</p>
</div>
<img class="w-10 h-10 rounded-full border border-primary object-cover" data-alt="A professional headshot of a corporate fleet manager in a clean, modern office setting. The lighting is bright and professional, following a light-mode aesthetic with soft blues and whites. The individual is wearing professional attire and looking confidently toward the camera." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBeoe_DdMdzOnWC20h3U8FPDUKfQHCIQP_dyioEThtyuVHd1sOv6tPaUf6yuodwJy_hQE43PEE_C2u4whjt65-F3aNf8wmYVLeBbDbdoRumRJsBW0eMxt-w1melyuiDQJdag_o_8UjgbCvKwpMGIo_f2yzf1CaEOAWeMt8Pv2vkKCTk3EP6dbFGQXlaCFE95RqzSIk0AWScNL8gdkLaOOw-SHabO6AQ5k1FE4A_5OR53CQOjAP-jiOgMCPTggQFYlr9SZOCcdhsj0s">
</div>
</div>
</div>
</header>
<!-- Main Content Canvas -->
<main class="ml-64 pt-16 p-xl">
<div class="max-w-[1440px] mx-auto">
<!-- Header Section -->
<div class="flex justify-between items-end mb-xl">
<div>
<h2 class="font-headline-lg text-headline-lg text-on-surface">Bookings Management</h2>
<p class="font-body-md text-body-md text-secondary mt-xs">Monitor and manage all active, pending, and historical rental transactions.</p>
</div>
<div class="flex gap-md">
<button class="flex items-center gap-sm px-md py-sm bg-surface-container-lowest border border-outline-variant rounded-lg font-label-md text-label-md text-secondary hover:bg-surface-container transition-colors">
<span class="material-symbols-outlined" data-icon="download">download</span>
                        Export CSV
                    </button>
<button class="flex items-center gap-sm px-md py-sm bg-primary text-on-primary rounded-lg font-label-md text-label-md hover:opacity-90 transition-all shadow-sm">
<span class="material-symbols-outlined" data-icon="add">add</span>
                        Create Manual Booking
                    </button>
</div>
</div>
<!-- Bento Grid Stats (Contextual) -->
<div class="grid grid-cols-4 gap-lg mb-xl">
<div class="glass-panel p-lg rounded-xl">
<p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Total Bookings</p>
<div class="flex items-baseline gap-sm mt-xs">
<h3 class="font-headline-md text-headline-md text-primary">1,284</h3>
<span class="font-label-sm text-label-sm text-tertiary-container flex items-center"><span class="material-symbols-outlined text-[16px]" data-icon="trending_up">trending_up</span> 12%</span>
</div>
</div>
<div class="glass-panel p-lg rounded-xl">
<p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Active Rentals</p>
<div class="flex items-baseline gap-sm mt-xs">
<h3 class="font-headline-md text-headline-md text-primary">84</h3>
<span class="font-label-sm text-label-sm text-secondary">Currently on road</span>
</div>
</div>
<div class="glass-panel p-lg rounded-xl">
<p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Pending Approvals</p>
<div class="flex items-baseline gap-sm mt-xs">
<h3 class="font-headline-md text-headline-md text-error">12</h3>
<span class="font-label-sm text-label-sm text-error">Requires action</span>
</div>
</div>
<div class="glass-panel p-lg rounded-xl">
<p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Revenue (MTD)</p>
<div class="flex items-baseline gap-sm mt-xs">
<h3 class="font-headline-md text-headline-md text-primary">₹42,500</h3>
<span class="font-label-sm text-label-sm text-tertiary-container flex items-center"><span class="material-symbols-outlined text-[16px]" data-icon="trending_up">trending_up</span> 8.4%</span>
</div>
</div>
</div>
<!-- Main Table Container -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
<!-- Filters & Controls -->
<div class="p-lg border-b border-outline-variant flex flex-wrap items-center justify-between gap-md bg-surface-bright">
<div class="flex items-center gap-md">
<button class="px-md py-sm rounded-full bg-primary-fixed text-on-primary-fixed font-label-md text-label-md">All Bookings</button>
<button class="px-md py-sm rounded-full hover:bg-surface-container-high font-label-md text-label-md text-secondary transition-colors">Pending</button>
<button class="px-md py-sm rounded-full hover:bg-surface-container-high font-label-md text-label-md text-secondary transition-colors">Confirmed</button>
<button class="px-md py-sm rounded-full hover:bg-surface-container-high font-label-md text-label-md text-secondary transition-colors">Completed</button>
<button class="px-md py-sm rounded-full hover:bg-surface-container-high font-label-md text-label-md text-secondary transition-colors">Cancelled</button>
</div>
<div class="flex items-center gap-sm">
<span class="font-body-sm text-body-sm text-secondary">Sort by:</span>
<select class="bg-transparent border-none font-label-md text-label-md text-on-surface focus:ring-0 cursor-pointer">
<option>Latest Created</option>
<option>Pick-up Date</option>
<option>Amount (High-Low)</option>
</select>
</div>
</div>
<!-- Table Content -->
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container text-on-surface-variant">
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Booking ID</th>
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Customer</th>
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Vehicle</th>
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Dates</th>
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Total Amount</th>
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Status</th>
<th class="px-lg py-md font-label-md text-label-md uppercase tracking-wider">Action</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant">
<!-- Row 1 -->
<tr class="booking-row transition-colors cursor-pointer">
<td class="px-lg py-lg font-label-md text-label-md text-primary">#BK-9482</td>
<td class="px-lg py-lg">
<div class="flex items-center gap-md">
<div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center text-primary font-bold text-xs">JS</div>
<div>
<p class="font-label-md text-label-md">John Schmidt</p>
<p class="font-body-sm text-body-sm text-secondary">john.s@example.com</p>
</div>
</div>
</td>
<td class="px-lg py-lg">
<div class="flex items-center gap-sm">
<span class="material-symbols-outlined text-secondary" data-icon="directions_car">directions_car</span>
<div>
<p class="font-label-md text-label-md">Tesla Model 3</p>
<p class="font-body-sm text-body-sm text-secondary">Blue • 2023</p>
</div>
</div>
</td>
<td class="px-lg py-lg">
<p class="font-label-sm text-label-sm">Oct 12 - Oct 15</p>
<p class="font-body-sm text-body-sm text-secondary">3 Days</p>
</td>
<td class="px-lg py-lg font-label-md text-label-md">₹450.00</td>
<td class="px-lg py-lg">
<span class="px-sm py-xs rounded-full bg-tertiary-container/10 text-tertiary-container font-label-sm text-label-sm border border-tertiary-container/20">Confirmed</span>
</td>
<td class="px-lg py-lg">
<button class="material-symbols-outlined text-secondary hover:text-primary transition-colors" data-icon="more_vert">more_vert</button>
</td>
</tr>
<!-- Row 2 -->
<tr class="booking-row transition-colors cursor-pointer">
<td class="px-lg py-lg font-label-md text-label-md text-primary">#BK-9481</td>
<td class="px-lg py-lg">
<div class="flex items-center gap-md">
<div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center text-primary font-bold text-xs">ML</div>
<div>
<p class="font-label-md text-label-md">Maria Lopez</p>
<p class="font-body-sm text-body-sm text-secondary">m.lopez@cloud.com</p>
</div>
</div>
</td>
<td class="px-lg py-lg">
<div class="flex items-center gap-sm">
<span class="material-symbols-outlined text-secondary" data-icon="directions_car">directions_car</span>
<div>
<p class="font-label-md text-label-md">BMW X5</p>
<p class="font-body-sm text-body-sm text-secondary">White • 2024</p>
</div>
</div>
</td>
<td class="px-lg py-lg">
<p class="font-label-sm text-label-sm">Oct 10 - Oct 11</p>
<p class="font-body-sm text-body-sm text-secondary">1 Day</p>
</td>
<td class="px-lg py-lg font-label-md text-label-md">₹210.00</td>
<td class="px-lg py-lg">
<span class="px-sm py-xs rounded-full bg-surface-container-highest text-secondary font-label-sm text-label-sm border border-outline-variant">Completed</span>
</td>
<td class="px-lg py-lg">
<button class="material-symbols-outlined text-secondary hover:text-primary transition-colors" data-icon="more_vert">more_vert</button>
</td>
</tr>
<!-- Row 3 -->
<tr class="booking-row transition-colors cursor-pointer">
<td class="px-lg py-lg font-label-md text-label-md text-primary">#BK-9480</td>
<td class="px-lg py-lg">
<div class="flex items-center gap-md">
<div class="w-8 h-8 rounded-full bg-error-container/20 flex items-center justify-center text-error font-bold text-xs">RA</div>
<div>
<p class="font-label-md text-label-md">Robert Aris</p>
<p class="font-body-sm text-body-sm text-secondary">raris@web.net</p>
</div>
</div>
</td>
<td class="px-lg py-lg">
<div class="flex items-center gap-sm">
<span class="material-symbols-outlined text-secondary" data-icon="directions_car">directions_car</span>
<div>
<p class="font-label-md text-label-md">Audi A4</p>
<p class="font-body-sm text-body-sm text-secondary">Grey • 2022</p>
</div>
</div>
</td>
<td class="px-lg py-lg">
<p class="font-label-sm text-label-sm">Oct 15 - Oct 20</p>
<p class="font-body-sm text-body-sm text-secondary">5 Days</p>
</td>
<td class="px-lg py-lg font-label-md text-label-md">₹625.00</td>
<td class="px-lg py-lg">
<span class="px-sm py-xs rounded-full bg-error-container/10 text-error font-label-sm text-label-sm border border-error-container/20">Pending</span>
</td>
<td class="px-lg py-lg">
<button class="material-symbols-outlined text-secondary hover:text-primary transition-colors" data-icon="more_vert">more_vert</button>
</td>
</tr>
<!-- Row 4 -->
<tr class="booking-row transition-colors cursor-pointer">
<td class="px-lg py-lg font-label-md text-label-md text-primary">#BK-9479</td>
<td class="px-lg py-lg">
<div class="flex items-center gap-md">
<div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center text-primary font-bold text-xs">EW</div>
<div>
<p class="font-label-md text-label-md">Emma Wilson</p>
<p class="font-body-sm text-body-sm text-secondary">emw@design.io</p>
</div>
</div>
</td>
<td class="px-lg py-lg">
<div class="flex items-center gap-sm">
<span class="material-symbols-outlined text-secondary" data-icon="directions_car">directions_car</span>
<div>
<p class="font-label-md text-label-md">Jeep Wrangler</p>
<p class="font-body-sm text-body-sm text-secondary">Black • 2023</p>
</div>
</div>
</td>
<td class="px-lg py-lg">
<p class="font-label-sm text-label-sm">Oct 18 - Oct 22</p>
<p class="font-body-sm text-body-sm text-secondary">4 Days</p>
</td>
<td class="px-lg py-lg font-label-md text-label-md">₹560.00</td>
<td class="px-lg py-lg">
<span class="px-sm py-xs rounded-full bg-surface-container-high text-secondary font-label-sm text-label-sm border border-outline-variant opacity-50 line-through">Cancelled</span>
</td>
<td class="px-lg py-lg">
<button class="material-symbols-outlined text-secondary hover:text-primary transition-colors" data-icon="more_vert">more_vert</button>
</td>
</tr>
<!-- Row 5 -->
<tr class="booking-row transition-colors cursor-pointer">
<td class="px-lg py-lg font-label-md text-label-md text-primary">#BK-9478</td>
<td class="px-lg py-lg">
<div class="flex items-center gap-md">
<div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center text-primary font-bold text-xs">KB</div>
<div>
<p class="font-label-md text-label-md">Kevin Brown</p>
<p class="font-body-sm text-body-sm text-secondary">kbrown@mail.com</p>
</div>
</div>
</td>
<td class="px-lg py-lg">
<div class="flex items-center gap-sm">
<span class="material-symbols-outlined text-secondary" data-icon="directions_car">directions_car</span>
<div>
<p class="font-label-md text-label-md">Toyota RAV4</p>
<p class="font-body-sm text-body-sm text-secondary">Red • 2022</p>
</div>
</div>
</td>
<td class="px-lg py-lg">
<p class="font-label-sm text-label-sm">Oct 20 - Oct 25</p>
<p class="font-body-sm text-body-sm text-secondary">5 Days</p>
</td>
<td class="px-lg py-lg font-label-md text-label-md">₹375.00</td>
<td class="px-lg py-lg">
<span class="px-sm py-xs rounded-full bg-tertiary-container/10 text-tertiary-container font-label-sm text-label-sm border border-tertiary-container/20">Confirmed</span>
</td>
<td class="px-lg py-lg">
<button class="material-symbols-outlined text-secondary hover:text-primary transition-colors" data-icon="more_vert">more_vert</button>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Pagination Footer -->
<div class="p-lg border-t border-outline-variant flex items-center justify-between bg-surface-bright">
<p class="font-body-sm text-body-sm text-secondary">Showing 1 to 5 of 1,284 entries</p>
<div class="flex items-center gap-xs">
<button class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-surface-container-high transition-colors disabled:opacity-50" disabled="">
<span class="material-symbols-outlined" data-icon="chevron_left">chevron_left</span>
</button>
<button class="w-10 h-10 flex items-center justify-center rounded-lg bg-primary text-on-primary font-label-md text-label-md">1</button>
<button class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-surface-container-high transition-colors font-label-md text-label-md">2</button>
<button class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-surface-container-high transition-colors font-label-md text-label-md">3</button>
<span class="px-xs text-secondary">...</span>
<button class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-surface-container-high transition-colors font-label-md text-label-md">257</button>
<button class="w-10 h-10 flex items-center justify-center rounded-lg border border-outline-variant hover:bg-surface-container-high transition-colors">
<span class="material-symbols-outlined" data-icon="chevron_right">chevron_right</span>
</button>
</div>
</div>
</div>
</div>
</main>
<!-- Micro-interaction Scripts -->
<script>
        // Simple row click animation simulation
        document.querySelectorAll('.booking-row').forEach(row => {
            row.addEventListener('click', () => {
                row.classList.add('scale-[0.99]');
                setTimeout(() => row.classList.remove('scale-[0.99]'), 100);
            });
        });

        // Hover feedback for filter buttons
        const filterBtns = document.querySelectorAll('.px-md.py-sm.rounded-full');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => {
                    b.classList.remove('bg-primary-fixed', 'text-on-primary-fixed');
                    b.classList.add('text-secondary', 'hover:bg-surface-container-high');
                });
                btn.classList.add('bg-primary-fixed', 'text-on-primary-fixed');
                btn.classList.remove('text-secondary', 'hover:bg-surface-container-high');
            });
        });
    </script>




</body></html>