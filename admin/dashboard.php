<!DOCTYPE html><html class="light" lang="en"><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>DriveEase | Admin Dashboard</title>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC; /* Level 0 Background */
        }
        /* Custom scrollbar for data density */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
        
        .chart-bar { transition: height 1s ease-out; }
    </style>
</head>
<body class="text-on-background">
<!-- Sidebar Container -->
<?php include "sidebar.php"; ?>
<!-- Main Canvas -->
<main class="lg:pl-64 min-h-screen">
<!-- Top Bar -->
<header class="h-16 bg-surface dark:bg-surface-container border-b border-outline-variant dark:border-outline flex items-center justify-between px-4 sm:px-margin-desktop sticky top-0 z-30 shadow-sm gap-2">
<h2 class="text-base sm:text-headline-sm font-headline-sm text-primary truncate pl-12 lg:pl-0">Dashboard Overview</h2>
<div class="flex items-center gap-2 sm:gap-lg shrink-0">
<div class="relative group hidden md:block">
<input class="bg-surface-container-low border border-outline-variant rounded-full px-md py-xs pl-10 text-body-sm focus:ring-2 focus:ring-primary focus:outline-none w-48 lg:w-64 transition-all group-focus-within:w-64 lg:group-focus-within:w-80" placeholder="Search for bookings, cars..." type="text">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant opacity-60">search</span>
</div>
<button class="p-1 rounded-full hover:bg-surface-container-high transition-colors md:hidden">
<span class="material-symbols-outlined text-primary">search</span>
</button>
<button class="relative p-xs rounded-full hover:bg-surface-container-high transition-colors">
<span class="material-symbols-outlined text-primary">notifications</span>
<span class="absolute top-1 right-1 w-2 h-2 bg-error rounded-full"></span>
</button>
<div class="flex items-center gap-sm cursor-pointer border-l border-outline-variant pl-2 sm:pl-lg">
<div class="text-right hidden sm:block">
<p class="text-label-md font-label-md text-on-surface">Alex Rivera</p>
<p class="text-[10px] text-on-surface-variant font-medium">FLEET MANAGER</p>
</div>
<img class="w-10 h-10 rounded-full border border-primary object-cover" data-alt="A professional business portrait of a fleet manager in a clean, modern corporate setting. He is smiling slightly, wearing a sharp navy blazer, with a blurred high-tech car dealership background featuring bright blue lighting and sleek glass surfaces. The photo is high-resolution with soft, even lighting." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAyZSIPQdHpZiKEAFST_m34UWaoaZtE8dkVKWkLogHzUl-jigYWqKOQjtPlhUqs7r5JwJ_JvQAaKIFkp0yvFzEbtC3jr2UWtx_5HbuKwAUKcRt3lSZcpiHnhplgNb1YYCEvX69DDd2_n21cmHc2lWkFWL7M1AfhEiQGSkqZXYkOmaYKQXUuQD6kBShiZImjwQVR8qUctyqz8LUKPHHSU6rESuBIl7Qs0lIb-D9-tkxTGygcxpdm6WtG3UpQ6u7U9j1GboNCIqT7XAw">
</div>
</div>
</header>
<div class="p-4 sm:p-6 lg:p-margin-desktop max-w-max-width mx-auto pb-24 lg:pb-margin-desktop">
<!-- Quick Stats Bento Grid -->
<section class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-gutter mb-6 sm:mb-xl">
<!-- Total Revenue -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-3 sm:p-lg shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)] group hover:border-primary transition-all">
<div class="flex justify-between items-start mb-sm">
<div class="p-sm bg-secondary-container rounded-lg text-primary">
<span class="material-symbols-outlined">payments</span>
</div>
<span class="text-tertiary font-label-sm flex items-center gap-xs">
<span class="material-symbols-outlined text-[14px]">trending_up</span> +12.5%
                        </span>
</div>
<p class="text-label-sm font-label-sm text-on-surface-variant mb-xs">TOTAL REVENUE</p>
<h3 class="text-lg sm:text-headline-md font-headline-md text-on-surface">₹128,430.00</h3>
<p class="text-[11px] text-on-surface-variant mt-xs">vs last 30 days</p>
</div>
<!-- Active Rentals -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-3 sm:p-lg shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)] group hover:border-primary transition-all">
<div class="flex justify-between items-start mb-sm">
<div class="p-sm bg-primary-container rounded-lg text-on-primary">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">directions_car</span>
</div>
<span class="text-tertiary font-label-sm flex items-center gap-xs">
<span class="material-symbols-outlined text-[14px]">trending_up</span> +4
                        </span>
</div>
<p class="text-label-sm font-label-sm text-on-surface-variant mb-xs">ACTIVE RENTALS</p>
<h3 class="text-headline-md font-headline-md text-on-surface">42</h3>
<p class="text-[11px] text-on-surface-variant mt-xs">84% occupancy rate</p>
</div>
<!-- Pending Bookings -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-3 sm:p-lg shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)] group hover:border-primary transition-all">
<div class="flex justify-between items-start mb-sm">
<div class="p-sm bg-tertiary-fixed rounded-lg text-on-tertiary-fixed-variant">
<span class="material-symbols-outlined">pending_actions</span>
</div>
<span class="text-error font-label-sm flex items-center gap-xs">
<span class="material-symbols-outlined text-[14px]">warning</span> Action Req.
                        </span>
</div>
<p class="text-label-sm font-label-sm text-on-surface-variant mb-xs">PENDING BOOKINGS</p>
<h3 class="text-headline-md font-headline-md text-on-surface">15</h3>
<p class="text-[11px] text-on-surface-variant mt-xs">Average response: 12m</p>
</div>
<!-- Total Customers -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-3 sm:p-lg shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)] group hover:border-primary transition-all">
<div class="flex justify-between items-start mb-sm">
<div class="p-sm bg-surface-container-highest rounded-lg text-secondary">
<span class="material-symbols-outlined">group</span>
</div>
<span class="text-tertiary font-label-sm flex items-center gap-xs">
<span class="material-symbols-outlined text-[14px]">trending_up</span> +1.2k
                        </span>
</div>
<p class="text-label-sm font-label-sm text-on-surface-variant mb-xs">TOTAL CUSTOMERS</p>
<h3 class="text-headline-md font-headline-md text-on-surface">2,840</h3>
<p class="text-[11px] text-on-surface-variant mt-xs">Lifetime members</p>
</div>
</section>
<!-- Main Charts & Tables Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-gutter">
<!-- Revenue Growth Chart & Fleet Status (Left/Middle Column span) -->
<div class="lg:col-span-2 space-y-gutter">
<!-- Revenue Growth Chart Card -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)]">
<div class="flex justify-between items-center mb-lg">
<div>
<h4 class="text-headline-sm font-headline-sm text-on-surface">Revenue Growth</h4>
<p class="text-body-sm font-body-sm text-on-surface-variant">Monthly revenue overview (Current Year)</p>
</div>
<select class="bg-surface-container-low border border-outline-variant rounded-lg text-label-md font-label-md px-md py-xs focus:ring-primary focus:outline-none">
<option>Last 6 Months</option>
<option>Last Year</option>
</select>
</div>
<!-- Visual Mockup of Chart -->
<div class="h-48 sm:h-64 w-full flex items-end gap-md px-md pt-lg relative overflow-x-auto">
<!-- Y-Axis Labels -->
<div class="absolute left-0 h-full flex flex-col justify-between text-[10px] text-on-surface-variant font-bold">
<span class="">100k</span>
<span class="">75k</span>
<span class="">50k</span>
<span class="">25k</span>
<span class="">0</span>
</div>
<!-- Grid Lines -->
<div class="absolute inset-0 flex flex-col justify-between py-xs pointer-events-none ml-8">
<div class="border-t border-outline-variant border-dashed w-full h-0 opacity-40"></div>
<div class="border-t border-outline-variant border-dashed w-full h-0 opacity-40"></div>
<div class="border-t border-outline-variant border-dashed w-full h-0 opacity-40"></div>
<div class="border-t border-outline-variant border-dashed w-full h-0 opacity-40"></div>
<div class="border-t border-outline-variant w-full h-0"></div>
</div>
<!-- Bars -->
<div class="flex-grow ml-8 h-full flex items-end justify-between z-10">
<div class="chart-bar w-6 sm:w-12 bg-secondary-container rounded-t-lg hover:bg-primary transition-colors cursor-pointer" style="height: 45%;"></div>
<div class="chart-bar w-6 sm:w-12 bg-secondary-container rounded-t-lg hover:bg-primary transition-colors cursor-pointer" style="height: 60%;"></div>
<div class="chart-bar w-6 sm:w-12 bg-secondary-container rounded-t-lg hover:bg-primary transition-colors cursor-pointer" style="height: 55%;"></div>
<div class="chart-bar w-6 sm:w-12 bg-secondary-container rounded-t-lg hover:bg-primary transition-colors cursor-pointer" style="height: 85%;"></div>
<div class="chart-bar w-6 sm:w-12 bg-primary rounded-t-lg transition-colors cursor-pointer" style="height: 95%;"></div>
<div class="chart-bar w-6 sm:w-12 bg-secondary-container rounded-t-lg hover:bg-primary transition-colors cursor-pointer" style="height: 75%;"></div>
</div>
</div>
<!-- X-Axis Labels -->
<div class="flex justify-between ml-16 mt-sm text-[10px] text-on-surface-variant font-bold">
<span class="">JAN</span><span class="">FEB</span><span class="">MAR</span><span class="">APR</span><span class="">MAY</span><span class="">JUN</span>
</div>
</div>
<!-- Recent Bookings Table Card -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)] overflow-hidden">
<div class="p-lg border-b border-outline-variant flex justify-between items-center">
<h4 class="text-headline-sm font-headline-sm text-on-surface">Recent Bookings</h4>
<button class="text-primary font-label-md hover:underline">View All</button>
</div>
<div class="overflow-x-auto -mx-1 px-1">
<table class="w-full min-w-[640px] text-left">
<thead class="bg-surface-container-low text-label-sm font-label-sm text-on-surface-variant uppercase tracking-wider">
<tr>
<th class="px-3 sm:px-lg py-2 sm:py-md">Customer</th>
<th class="px-3 sm:px-lg py-2 sm:py-md">Vehicle</th>
<th class="px-3 sm:px-lg py-2 sm:py-md">Date Range</th>
<th class="px-3 sm:px-lg py-2 sm:py-md">Amount</th>
<th class="px-3 sm:px-lg py-2 sm:py-md">Status</th>
<th class="px-3 sm:px-lg py-2 sm:py-md text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/30 text-body-sm">
<!-- Row 1 -->
<tr class="hover:bg-primary-container/5 transition-colors cursor-default">
<td class="px-3 sm:px-lg py-2 sm:py-md">
<div class="flex items-center gap-sm">
<div class="w-8 h-8 rounded-full bg-secondary-container text-primary flex items-center justify-center font-bold text-xs">JS</div>
<span class="font-medium">James Smith</span>
</div>
</td>
<td class="px-3 sm:px-lg py-2 sm:py-md">Tesla Model 3</td>
<td class="px-3 sm:px-lg py-2 sm:py-md">Oct 24 - Oct 27</td>
<td class="px-3 sm:px-lg py-2 sm:py-md font-semibold">₹540.00</td>
<td class="px-3 sm:px-lg py-2 sm:py-md">
<span class="px-sm py-1 bg-tertiary-fixed text-on-tertiary-fixed-variant rounded-full text-[10px] font-bold">CONFIRMED</span>
</td>
<td class="px-3 sm:px-lg py-2 sm:py-md text-right">
<div class="flex justify-end gap-xs">
<button class="p-1 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">check_circle</span></button>
<button class="p-1 hover:text-error transition-colors"><span class="material-symbols-outlined text-[20px]">cancel</span></button>
</div>
</td>
</tr>
<!-- Row 2 -->
<tr class="hover:bg-primary-container/5 transition-colors cursor-default">
<td class="px-3 sm:px-lg py-2 sm:py-md">
<div class="flex items-center gap-sm">
<div class="w-8 h-8 rounded-full bg-surface-container-highest text-on-surface-variant flex items-center justify-center font-bold text-xs">EM</div>
<span class="font-medium">Elena Martinez</span>
</div>
</td>
<td class="px-3 sm:px-lg py-2 sm:py-md">BMW X5</td>
<td class="px-3 sm:px-lg py-2 sm:py-md">Oct 25 - Oct 29</td>
<td class="px-3 sm:px-lg py-2 sm:py-md font-semibold">₹890.00</td>
<td class="px-3 sm:px-lg py-2 sm:py-md">
<span class="px-sm py-1 bg-secondary-container text-on-secondary-container rounded-full text-[10px] font-bold">PENDING</span>
</td>
<td class="px-3 sm:px-lg py-2 sm:py-md text-right">
<div class="flex justify-end gap-xs">
<button class="p-1 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">check_circle</span></button>
<button class="p-1 hover:text-error transition-colors"><span class="material-symbols-outlined text-[20px]">cancel</span></button>
</div>
</td>
</tr>
<!-- Row 3 -->
<tr class="hover:bg-primary-container/5 transition-colors cursor-default">
<td class="px-3 sm:px-lg py-2 sm:py-md">
<div class="flex items-center gap-sm">
<div class="w-8 h-8 rounded-full bg-primary-fixed text-on-primary-fixed flex items-center justify-center font-bold text-xs">RH</div>
<span class="font-medium">Robert Huang</span>
</div>
</td>
<td class="px-3 sm:px-lg py-2 sm:py-md">Mercedes C-Class</td>
<td class="px-3 sm:px-lg py-2 sm:py-md">Oct 26 - Oct 26</td>
<td class="px-3 sm:px-lg py-2 sm:py-md font-semibold">₹150.00</td>
<td class="px-3 sm:px-lg py-2 sm:py-md">
<span class="px-sm py-1 bg-tertiary-fixed text-on-tertiary-fixed-variant rounded-full text-[10px] font-bold">CONFIRMED</span>
</td>
<td class="px-3 sm:px-lg py-2 sm:py-md text-right">
<div class="flex justify-end gap-xs">
<button class="p-1 hover:text-primary transition-colors"><span class="material-symbols-outlined text-[20px]">check_circle</span></button>
<button class="p-1 hover:text-error transition-colors"><span class="material-symbols-outlined text-[20px]">cancel</span></button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
<!-- Right Sidebar Content -->
<div class="space-y-gutter">
<!-- Fleet Status Summary -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)]">
<h4 class="text-headline-sm font-headline-sm text-on-surface mb-md">Fleet Status</h4>
<div class="relative h-36 w-36 sm:h-48 sm:w-48 mx-auto mb-lg">
<!-- SVG Donut Chart Mockup -->
<svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
<circle class="stroke-surface-container-high" cx="18" cy="18" fill="none" r="16" stroke-width="3"></circle>
<circle class="stroke-primary" cx="18" cy="18" fill="none" r="16" stroke-dasharray="75, 100" stroke-width="3"></circle>
<circle class="stroke-tertiary-fixed-dim" cx="18" cy="18" fill="none" r="16" stroke-dasharray="25, 100" stroke-dashoffset="-75" stroke-width="3"></circle>
</svg>
<div class="absolute inset-0 flex flex-col items-center justify-center">
<span class="text-headline-md font-headline-md text-on-surface">150</span>
<span class="text-[10px] font-bold text-on-surface-variant uppercase">Total Fleet</span>
</div>
</div>
<div class="space-y-sm">
<div class="flex items-center justify-between text-body-sm">
<div class="flex items-center gap-sm">
<span class="w-3 h-3 rounded-full bg-primary"></span>
<span class="">Rented</span>
</div>
<span class="font-bold">112</span>
</div>
<div class="flex items-center justify-between text-body-sm">
<div class="flex items-center gap-sm">
<span class="w-3 h-3 rounded-full bg-tertiary-fixed-dim"></span>
<span class="">Available</span>
</div>
<span class="font-bold">28</span>
</div>
<div class="flex items-center justify-between text-body-sm">
<div class="flex items-center gap-sm">
<span class="w-3 h-3 rounded-full bg-error"></span>
<span class="">Maintenance</span>
</div>
<span class="font-bold">10</span>
</div>
</div>
<button class="w-full mt-lg py-sm border border-primary text-primary font-label-md rounded-lg hover:bg-primary-container/5 transition-colors">
                            Manage Fleet Details
                        </button>
</div>
<!-- Maintenance Alerts -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)]">
<h4 class="text-headline-sm font-headline-sm text-on-surface mb-md">Maintenance Alerts</h4>
<div class="space-y-md">
<div class="flex gap-md">
<div class="text-error mt-1">
<span class="material-symbols-outlined">warning</span>
</div>
<div>
<p class="text-label-md font-label-md text-on-surface">Oil Change Required</p>
<p class="text-body-sm text-on-surface-variant">Toyota Camry (Plate: AB-1234)</p>
<p class="text-[11px] text-error font-bold mt-xs">URGENT</p>
</div>
</div>
<div class="flex gap-md border-t border-outline-variant/30 pt-md">
<div class="text-secondary mt-1">
<span class="material-symbols-outlined">build</span>
</div>
<div>
<p class="text-label-md font-label-md text-on-surface">Scheduled Service</p>
<p class="text-body-sm text-on-surface-variant">Ford Mustang (Plate: GT-5000)</p>
<p class="text-[11px] text-on-surface-variant font-bold mt-xs">IN 2 DAYS</p>
</div>
</div>
</div>
</div>
<!-- Upcoming Inspections Map/Location Mockup -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)]">
<div class="h-32 w-full" data-location="Chicago">
<div class="w-full h-full bg-surface-container-high flex items-center justify-center text-on-surface-variant/40">
<span class="material-symbols-outlined text-[48px]">map</span>
</div>
</div>
<div class="p-md">
<p class="text-label-sm font-label-sm text-on-surface-variant">ACTIVE RETURN HUB</p>
<p class="text-body-md font-bold text-on-surface">Chicago O'Hare Terminal 1</p>
<p class="text-body-sm text-on-surface-variant">4 Returns expected next hour</p>
</div>
</div>
</div>
</div>
</div>
<!-- Footer -->
<footer class="w-full mt-auto bg-surface-container-highest dark:bg-inverse-surface border-t border-outline-variant dark:border-outline">
<div class="w-full py-8 sm:py-xl px-4 sm:px-margin-desktop grid grid-cols-1 md:grid-cols-2 gap-4 items-center max-w-max-width mx-auto text-center md:text-left">
<div>
<h5 class="text-headline-sm font-headline-sm font-bold text-on-surface dark:text-inverse-on-surface">DriveEase</h5>
<p class="text-body-sm font-body-sm text-on-surface-variant dark:text-surface-variant">© 2024 DriveEase Car Rental Systems. All rights reserved.</p>
</div>
<div class="flex flex-wrap md:justify-end gap-lg mt-md md:mt-0">
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-on-surface transition-colors" href="#">Privacy Policy</a>
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-on-surface transition-colors" href="#">Terms of Service</a>
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-on-surface transition-colors" href="#">Contact Support</a>
</div>
</div>
</footer>
</main>
<!-- Mobile Nav Bar (only visible on small screens) -->
<nav class="fixed bottom-0 left-0 right-0 h-16 bg-surface border-t border-outline-variant flex lg:hidden items-center justify-around z-50">
<a class="flex flex-col items-center text-primary" href="#">
<span class="material-symbols-outlined">dashboard</span>
<span class="text-[10px] font-bold">Dashboard</span>
</a>
<a class="flex flex-col items-center text-on-surface-variant" href="#">
<span class="material-symbols-outlined">directions_car</span>
<span class="text-[10px] font-bold">Fleet</span>
</a>
<a class="flex flex-col items-center text-on-surface-variant" href="#">
<span class="material-symbols-outlined">calendar_today</span>
<span class="text-[10px] font-bold">Bookings</span>
</a>
<a class="flex flex-col items-center text-on-surface-variant" href="#">
<span class="material-symbols-outlined">person</span>
<span class="text-[10px] font-bold">Profile</span>
</a>
</nav>
<script>
        // Simple animation trigger for chart bars
        window.addEventListener('DOMContentLoaded', () => {
            const bars = document.querySelectorAll('.chart-bar');
            bars.forEach(bar => {
                const targetHeight = bar.style.height;
                bar.style.height = '0%';
                setTimeout(() => {
                    bar.style.height = targetHeight;
                }, 200);
            });
        });
    </script>


</body></html>