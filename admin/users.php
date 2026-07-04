<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>DriveEase Admin - Customer Management</title>
</head>
<body class="bg-background text-on-surface font-body-md overflow-hidden">
<!-- Shell Container -->
<div class="flex h-screen w-full">
<!-- SideNavBar -->
<?php include "sidebar.php"; ?>
<!-- Main Content Area -->
<main class="ml-64 flex-1 flex flex-col h-full bg-background relative overflow-hidden">
<!-- TopNavBar -->
<header class="h-16 flex justify-between items-center px-xl w-full bg-surface border-b border-outline-variant sticky top-0 z-20">
<div class="flex items-center gap-xl w-1/2">
<div class="relative w-full max-w-md group">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-secondary">search</span>
<input class="w-full pl-10 pr-md py-sm bg-surface-container-lowest border border-outline-variant rounded-lg font-body-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all" placeholder="Search customers by name, email or phone..." type="text"/>
</div>
</div>
<div class="flex items-center gap-lg">
<button class="p-base text-secondary hover:text-primary transition-colors active:scale-95">
<span class="material-symbols-outlined">notifications</span>
</button>
<button class="p-base text-secondary hover:text-primary transition-colors active:scale-95">
<span class="material-symbols-outlined">help_outline</span>
</button>
<div class="w-px h-6 bg-outline-variant mx-sm"></div>
<div class="flex items-center gap-sm">
<span class="font-label-md text-on-surface">Admin User</span>
<div class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-on-primary-fixed font-bold text-xs">
                            AU
                        </div>
</div>
</div>
</header>
<!-- Dashboard Content -->
<div class="flex-1 overflow-y-auto p-xl custom-scrollbar">
<!-- Page Title & Quick Actions -->
<div class="flex justify-between items-end mb-xl">
<div>
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs">Customer Directory</h2>
<p class="font-body-md text-secondary">Manage registered users, view rental history, and adjust account statuses.</p>
</div>
<div class="flex gap-md">
<button class="flex items-center gap-sm px-lg py-md border border-outline bg-surface rounded-lg font-label-md text-secondary hover:bg-surface-container-high transition-colors">
<span class="material-symbols-outlined">download</span>
                            Export CSV
                        </button>
<button class="flex items-center gap-sm px-lg py-md bg-primary text-on-primary rounded-lg font-label-md shadow-sm hover:opacity-90 transition-all">
<span class="material-symbols-outlined">person_add</span>
                            Add Customer
                        </button>
</div>
</div>
<!-- Metrics Bento Grid Section -->
<div class="grid grid-cols-4 gap-lg mb-xl">
<div class="bg-surface-container-lowest p-lg rounded-xl border border-outline-variant shadow-sm flex flex-col gap-sm">
<div class="flex items-center justify-between">
<span class="font-label-md text-secondary uppercase tracking-wider">Total Customers</span>
<span class="material-symbols-outlined text-primary">groups</span>
</div>
<div class="flex items-baseline gap-sm">
<span class="font-headline-lg text-headline-lg">2,845</span>
<span class="font-label-sm text-tertiary-container flex items-center">+12% <span class="material-symbols-outlined text-xs">trending_up</span></span>
</div>
</div>
<div class="bg-surface-container-lowest p-lg rounded-xl border border-outline-variant shadow-sm flex flex-col gap-sm">
<div class="flex items-center justify-between">
<span class="font-label-md text-secondary uppercase tracking-wider">Active Users</span>
<span class="material-symbols-outlined text-tertiary-fixed-dim">verified_user</span>
</div>
<div class="flex items-baseline gap-sm">
<span class="font-headline-lg text-headline-lg">2,102</span>
<span class="font-label-sm text-secondary">74% of total</span>
</div>
</div>
<div class="bg-surface-container-lowest p-lg rounded-xl border border-outline-variant shadow-sm flex flex-col gap-sm">
<div class="flex items-center justify-between">
<span class="font-label-md text-secondary uppercase tracking-wider">New This Month</span>
<span class="material-symbols-outlined text-on-primary-fixed-variant">person_add_alt</span>
</div>
<div class="flex items-baseline gap-sm">
<span class="font-headline-lg text-headline-lg">184</span>
<span class="font-label-sm text-error flex items-center">-2% <span class="material-symbols-outlined text-xs">trending_down</span></span>
</div>
</div>
<div class="bg-surface-container-lowest p-lg rounded-xl border border-outline-variant shadow-sm flex flex-col gap-sm">
<div class="flex items-center justify-between">
<span class="font-label-md text-secondary uppercase tracking-wider">Blocked Accounts</span>
<span class="material-symbols-outlined text-error">block</span>
</div>
<div class="flex items-baseline gap-sm">
<span class="font-headline-lg text-headline-lg">42</span>
<span class="font-label-sm text-secondary">Flagged for review</span>
</div>
</div>
</div>
<!-- Filters Bar -->
<div class="flex items-center justify-between bg-surface-container-low p-md rounded-lg mb-lg">
<div class="flex items-center gap-md">
<button class="px-md py-sm bg-surface-container-highest text-primary font-label-md rounded-md border border-primary-container">All Users</button>
<button class="px-md py-sm text-secondary hover:text-on-surface font-label-md transition-colors">Active</button>
<button class="px-md py-sm text-secondary hover:text-on-surface font-label-md transition-colors">Blocked</button>
<button class="px-md py-sm text-secondary hover:text-on-surface font-label-md transition-colors">Top Spenders</button>
</div>
<div class="flex items-center gap-sm">
<span class="font-label-sm text-secondary">Sort by:</span>
<select class="bg-transparent border-none font-label-md text-on-surface focus:ring-0 cursor-pointer">
<option>Joined Date (Newest)</option>
<option>Joined Date (Oldest)</option>
<option>Alphabetical A-Z</option>
<option>Most Bookings</option>
</select>
</div>
</div>
<!-- Customer Table Container -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant shadow-sm overflow-hidden">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container border-b border-outline-variant">
<th class="p-lg font-label-md text-on-surface-variant uppercase text-[11px] tracking-widest">User</th>
<th class="p-lg font-label-md text-on-surface-variant uppercase text-[11px] tracking-widest">Contact Information</th>
<th class="p-lg font-label-md text-on-surface-variant uppercase text-[11px] tracking-widest">Joined Date</th>
<th class="p-lg font-label-md text-on-surface-variant uppercase text-[11px] tracking-widest">Bookings</th>
<th class="p-lg font-label-md text-on-surface-variant uppercase text-[11px] tracking-widest">Status</th>
<th class="p-lg font-label-md text-on-surface-variant uppercase text-[11px] tracking-widest text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant">
<!-- User Row 1 -->
<tr class="hover:bg-surface-container-low transition-colors group">
<td class="p-lg">
<div class="flex items-center gap-md">
<img class="w-10 h-10 rounded-full object-cover" data-alt="A professional headshot of a middle-aged woman with glasses and a friendly smile, photographed in a minimalist studio with soft lighting. The background uses a subtle light blue gradient that aligns with the DriveEase color palette. The aesthetic is modern, clean, and high-trust, suitable for a corporate CRM interface." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBXzSype8WWjL6UUnDf51gWWCbiCaLD1u8jIwjhE4s5x7lnfm6twoi-XPXhLyWh4jAtPrsf_BwdOCgbf0YeCM2Ozm0fgm0okDcq_i5reM6ZsYJdCZjJmcIaxouA0juvZnsOzuG8lB2Oeba53ielH8jK6MS4G-yY5U97BxR4KqckuMrZNp2RV9T-SRQl1tVOViBW86zC3cUeeT0q1ZB5kbsboEuS8bGjzpawBZv374UKg04NjX-q6IuUYzUDa4p2C8ilsJfZ6Tojwuc"/>
<div>
<p class="font-label-md text-on-surface">Sarah Jenkins</p>
<p class="font-label-sm text-secondary">ID: DE-8201</p>
</div>
</div>
</td>
<td class="p-lg">
<p class="font-body-sm text-on-surface">s.jenkins@example.com</p>
<p class="font-body-sm text-secondary">+1 (555) 019-2348</p>
</td>
<td class="p-lg">
<p class="font-body-sm text-on-surface">Oct 12, 2023</p>
<p class="font-label-sm text-secondary">10:45 AM</p>
</td>
<td class="p-lg">
<div class="flex items-center gap-xs">
<span class="font-label-md text-on-surface">24</span>
<span class="material-symbols-outlined text-sm text-tertiary-fixed-dim" style="font-variation-settings: 'FILL' 1;">star</span>
</div>
</td>
<td class="p-lg">
<span class="px-md py-xs rounded-full bg-tertiary-fixed/20 text-on-tertiary-fixed-variant font-label-sm inline-flex items-center gap-xs">
<span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-fixed-variant"></span>
                                        Active
                                    </span>
</td>
<td class="p-lg text-right">
<div class="flex justify-end gap-xs">
<button class="p-sm text-secondary hover:text-primary transition-colors rounded-md hover:bg-surface-container-high" title="View Details">
<span class="material-symbols-outlined">visibility</span>
</button>
<button class="p-sm text-secondary hover:text-error transition-colors rounded-md hover:bg-error-container" title="Block User">
<span class="material-symbols-outlined">block</span>
</button>
<button class="p-sm text-secondary hover:text-on-surface transition-colors rounded-md hover:bg-surface-container-high">
<span class="material-symbols-outlined">more_vert</span>
</button>
</div>
</td>
</tr>
<!-- User Row 2 -->
<tr class="hover:bg-surface-container-low transition-colors group">
<td class="p-lg">
<div class="flex items-center gap-md">
<div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center text-on-secondary-container font-bold">MH</div>
<div>
<p class="font-label-md text-on-surface">Marcus Holloway</p>
<p class="font-label-sm text-secondary">ID: DE-7942</p>
</div>
</div>
</td>
<td class="p-lg">
<p class="font-body-sm text-on-surface">marcus.h@techcorp.com</p>
<p class="font-body-sm text-secondary">+1 (555) 012-9930</p>
</td>
<td class="p-lg">
<p class="font-body-sm text-on-surface">Nov 05, 2023</p>
<p class="font-label-sm text-secondary">03:22 PM</p>
</td>
<td class="p-lg">
<span class="font-label-md text-on-surface">12</span>
</td>
<td class="p-lg">
<span class="px-md py-xs rounded-full bg-tertiary-fixed/20 text-on-tertiary-fixed-variant font-label-sm inline-flex items-center gap-xs">
<span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-fixed-variant"></span>
                                        Active
                                    </span>
</td>
<td class="p-lg text-right">
<div class="flex justify-end gap-xs">
<button class="p-sm text-secondary hover:text-primary transition-colors rounded-md hover:bg-surface-container-high">
<span class="material-symbols-outlined">visibility</span>
</button>
<button class="p-sm text-secondary hover:text-error transition-colors rounded-md hover:bg-error-container">
<span class="material-symbols-outlined">block</span>
</button>
<button class="p-sm text-secondary hover:text-on-surface transition-colors rounded-md hover:bg-surface-container-high">
<span class="material-symbols-outlined">more_vert</span>
</button>
</div>
</td>
</tr>
<!-- User Row 3 (Blocked) -->
<tr class="hover:bg-surface-container-low transition-colors group">
<td class="p-lg">
<div class="flex items-center gap-md">
<img class="w-10 h-10 rounded-full object-cover grayscale opacity-80" data-alt="A clean, minimalist portrait of a man with short dark hair wearing a navy blue polo shirt. He has a neutral expression. The background is a soft, out-of-focus city skyline at dusk, using muted tones of blue and gray that complement the DriveEase interface. High-end, professional corporate photography style." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBKTdETOQmUwRlrn3xRB_Dhc1sVs5PGwEhvkjEX3N-97FxkgA7ew942YlrLD8GSVC6LrTzBkkMUE7B2KCtkF35As2BiOo3zI52KozEyA-emC6wCW5v0tGfJeOxy9rdazZN50rHJRzLwb-_UsjyhMxkPopECTkCRLHw_Y0hVRXUbAb1W-zetstzGOwuUb8rUojbf6_zZLbAIoxRCdxKgFVnLXBDANk70iR8hUbI9YpK8W9O1c_UX6LXSpzQakTkqJneesHy8Aer7nLc"/>
<div>
<p class="font-label-md text-on-surface">David Chen</p>
<p class="font-label-sm text-secondary">ID: DE-5521</p>
</div>
</div>
</td>
<td class="p-lg">
<p class="font-body-sm text-on-surface">d.chen_88@outlook.com</p>
<p class="font-body-sm text-secondary">+1 (555) 018-4421</p>
</td>
<td class="p-lg">
<p class="font-body-sm text-on-surface">Aug 22, 2023</p>
<p class="font-label-sm text-secondary">09:12 AM</p>
</td>
<td class="p-lg">
<span class="font-label-md text-on-surface">2</span>
</td>
<td class="p-lg">
<span class="px-md py-xs rounded-full bg-error-container text-on-error-container font-label-sm inline-flex items-center gap-xs">
<span class="w-1.5 h-1.5 rounded-full bg-on-error-container"></span>
                                        Blocked
                                    </span>
</td>
<td class="p-lg text-right">
<div class="flex justify-end gap-xs">
<button class="p-sm text-secondary hover:text-primary transition-colors rounded-md hover:bg-surface-container-high">
<span class="material-symbols-outlined">visibility</span>
</button>
<button class="p-sm text-tertiary hover:text-primary-fixed-dim transition-colors rounded-md hover:bg-surface-container-high" title="Unblock User">
<span class="material-symbols-outlined">check_circle</span>
</button>
<button class="p-sm text-secondary hover:text-on-surface transition-colors rounded-md hover:bg-surface-container-high">
<span class="material-symbols-outlined">more_vert</span>
</button>
</div>
</td>
</tr>
<!-- User Row 4 -->
<tr class="hover:bg-surface-container-low transition-colors group">
<td class="p-lg">
<div class="flex items-center gap-md">
<img class="w-10 h-10 rounded-full object-cover" data-alt="A bright, professional close-up of a young woman with curly hair wearing a modern yellow sweater. She looks confident and approachable. The lighting is natural and crisp, creating a high-trust atmosphere. The background is a clean, modern coworking space with plants and glass walls, reflecting the high-performance SaaS brand style of DriveEase." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCXrXxyrZVE0ABNVisH-g9kGhF5HwbMi4W3SqAVodM92dao-7hciyv9eBiFX-1jC6TQhAxEeDv2qysQzFzHdqcYAMPNXcl6oowxHEyxJXpqz339HUVpqBeFfUp1Zai5MUYmuKfV5gtkI9mzEJcRA7h8lciboB_i98yPEHc5s5bw5EP88zJaOtQCD61YDdKPWrvwgweMl5BYedsYgmZPEg7t1MfwWtDFxrEugKQOt0H8HWBZkeHZX95PwwcQ-ayUwf-bal9yJiLbJzQ"/>
<div>
<p class="font-label-md text-on-surface">Elena Rodriguez</p>
<p class="font-label-sm text-secondary">ID: DE-9012</p>
</div>
</div>
</td>
<td class="p-lg">
<p class="font-body-sm text-on-surface">elena.rod@global.net</p>
<p class="font-body-sm text-secondary">+1 (555) 014-7732</p>
</td>
<td class="p-lg">
<p class="font-body-sm text-on-surface">Jan 14, 2024</p>
<p class="font-label-sm text-secondary">11:58 AM</p>
</td>
<td class="p-lg">
<span class="font-label-md text-on-surface">45</span>
</td>
<td class="p-lg">
<span class="px-md py-xs rounded-full bg-tertiary-fixed/20 text-on-tertiary-fixed-variant font-label-sm inline-flex items-center gap-xs">
<span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-fixed-variant"></span>
                                        Active
                                    </span>
</td>
<td class="p-lg text-right">
<div class="flex justify-end gap-xs">
<button class="p-sm text-secondary hover:text-primary transition-colors rounded-md hover:bg-surface-container-high">
<span class="material-symbols-outlined">visibility</span>
</button>
<button class="p-sm text-secondary hover:text-error transition-colors rounded-md hover:bg-error-container">
<span class="material-symbols-outlined">block</span>
</button>
<button class="p-sm text-secondary hover:text-on-surface transition-colors rounded-md hover:bg-surface-container-high">
<span class="material-symbols-outlined">more_vert</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<!-- Pagination -->
<div class="flex items-center justify-between mt-xl pb-xl">
<p class="font-body-sm text-secondary">Showing <span class="font-bold text-on-surface">1-10</span> of <span class="font-bold text-on-surface">2,845</span> customers</p>
<div class="flex items-center gap-sm">
<button class="p-md border border-outline-variant rounded-lg text-secondary hover:bg-surface-container-high transition-colors disabled:opacity-30" disabled="">
<span class="material-symbols-outlined">chevron_left</span>
</button>
<button class="w-10 h-10 flex items-center justify-center bg-primary text-on-primary rounded-lg font-label-md">1</button>
<button class="w-10 h-10 flex items-center justify-center text-secondary hover:bg-surface-container-high rounded-lg font-label-md transition-colors">2</button>
<button class="w-10 h-10 flex items-center justify-center text-secondary hover:bg-surface-container-high rounded-lg font-label-md transition-colors">3</button>
<span class="text-secondary px-sm">...</span>
<button class="w-10 h-10 flex items-center justify-center text-secondary hover:bg-surface-container-high rounded-lg font-label-md transition-colors">285</button>
<button class="p-md border border-outline-variant rounded-lg text-secondary hover:bg-surface-container-high transition-colors">
<span class="material-symbols-outlined">chevron_right</span>
</button>
</div>
</div>
</div>
<!-- Footer Detail Pane (Hidden by default, triggered by JS) -->
<div class="absolute bottom-0 left-0 right-0 h-1/2 bg-surface border-t border-outline translate-y-full transition-transform duration-300 z-40 flex flex-col shadow-2xl" id="detail-panel">
<div class="p-lg flex justify-between items-center border-b border-outline-variant">
<h3 class="font-headline-sm text-on-surface">Customer Profile Details</h3>
<button class="p-sm hover:bg-surface-container-high rounded-full transition-colors" onclick="togglePanel()">
<span class="material-symbols-outlined">close</span>
</button>
</div>
<div class="flex-1 p-xl grid grid-cols-3 gap-xl overflow-y-auto custom-scrollbar">
<div class="space-y-lg">
<div class="flex items-center gap-lg">
<div class="w-24 h-24 rounded-xl bg-surface-container-highest border-2 border-primary-fixed-dim overflow-hidden">
<img class="w-full h-full object-cover" data-alt="Close up portrait for a profile detail view of a business customer. Clean lighting, high resolution, professional corporate aesthetic with light blue brand colors." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDBYB8svITZDvepFkz38qFC2_SGvobYQRIhb0PFmX98EKjmibOZzINWx7ZuMeYJDqOEV4z3twbLNrZMy1bTijMHm872zdJJDBEJMwt3p0CvHamEp0-ysenGF_K5gbV1hFeag-7_jvW3KiXtc9GfjC12vqG21YjjoO53EBVWFWzkKhB5Ec1L1DDB0yQVsg5jl2bEPq2G7WHI9VxHE71b2nzom78AJMSbIhn3_hYK98hKFmnxQYpANv7YsK4EPcazGngyY7_IDkl54zs"/>
</div>
<div>
<h4 class="font-headline-sm text-on-surface">Sarah Jenkins</h4>
<p class="text-secondary font-label-md">Verified Member since 2023</p>
<span class="inline-block mt-sm px-md py-xs bg-tertiary-fixed/20 text-on-tertiary-fixed-variant rounded-full font-label-sm">Premium Tier</span>
</div>
</div>
<div class="bg-surface-container-low p-md rounded-lg space-y-sm">
<p class="font-label-sm text-secondary uppercase">Personal Information</p>
<p class="font-body-sm text-on-surface flex justify-between"><span>Email:</span> <span class="font-bold">s.jenkins@example.com</span></p>
<p class="font-body-sm text-on-surface flex justify-between"><span>Phone:</span> <span class="font-bold">+1 (555) 019-2348</span></p>
<p class="font-body-sm text-on-surface flex justify-between"><span>Location:</span> <span class="font-bold">New York, NY</span></p>
</div>
</div>
<div class="space-y-lg">
<p class="font-label-md text-secondary uppercase tracking-widest border-b border-outline-variant pb-xs">Recent Activity</p>
<div class="space-y-md">
<div class="flex gap-md">
<span class="material-symbols-outlined text-primary">directions_car</span>
<div>
<p class="font-label-md text-on-surface">Tesla Model 3 Rental</p>
<p class="font-label-sm text-secondary">Completed • Jan 12 - Jan 15</p>
</div>
</div>
<div class="flex gap-md">
<span class="material-symbols-outlined text-primary">directions_car</span>
<div>
<p class="font-label-md text-on-surface">BMW X5 Rental</p>
<p class="font-label-sm text-secondary">Completed • Dec 20 - Dec 24</p>
</div>
</div>
</div>
<div class="bg-secondary-fixed/30 p-md rounded-lg">
<p class="font-label-md text-on-secondary-fixed mb-xs">Total Lifetime Spend</p>
<p class="font-headline-md text-primary">$4,280.50</p>
</div>
</div>
<div class="space-y-lg">
<p class="font-label-md text-secondary uppercase tracking-widest border-b border-outline-variant pb-xs">Account Settings</p>
<div class="space-y-sm">
<button class="w-full flex items-center justify-between p-md border border-outline-variant rounded-lg hover:bg-surface-container-high transition-colors">
<span class="flex items-center gap-sm font-label-md text-on-surface"><span class="material-symbols-outlined text-secondary">edit</span> Edit Profile</span>
<span class="material-symbols-outlined text-sm text-outline">chevron_right</span>
</button>
<button class="w-full flex items-center justify-between p-md border border-outline-variant rounded-lg hover:bg-surface-container-high transition-colors">
<span class="flex items-center gap-sm font-label-md text-on-surface"><span class="material-symbols-outlined text-secondary">history</span> View All Bookings</span>
<span class="material-symbols-outlined text-sm text-outline">chevron_right</span>
</button>
<button class="w-full flex items-center justify-between p-md border border-error-container rounded-lg bg-error-container/10 hover:bg-error-container transition-colors group">
<span class="flex items-center gap-sm font-label-md text-error"><span class="material-symbols-outlined">block</span> Block Account</span>
<span class="material-symbols-outlined text-sm text-error">chevron_right</span>
</button>
</div>
</div>
</div>
</div>
</main>
</div>
<!-- Micro-interactions Script -->
<script>
        function togglePanel() {
            const panel = document.getElementById('detail-panel');
            panel.classList.toggle('translate-y-full');
        }

        // Add event listeners to "View Details" buttons
        document.querySelectorAll('button[title="View Details"]').forEach(btn => {
            btn.addEventListener('click', () => {
                togglePanel();
            });
        });

        // Search Bar Focus Effect
        const searchInput = document.querySelector('input[type="text"]');
        searchInput.addEventListener('focus', () => {
            searchInput.parentElement.classList.add('ring-2', 'ring-primary/20');
        });
        searchInput.addEventListener('blur', () => {
            searchInput.parentElement.classList.remove('ring-2', 'ring-primary/20');
        });
    </script>
</body></html>
