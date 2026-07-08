<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DriveEase | Find Your Perfect Ride</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .car-card-hover:hover .car-image {
            transform: scale(1.05);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #faf8ff;
        }
    </style>

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
                        "headline-sm": ["Inter", "sans-serif"],
                        "headline-md": ["Inter", "sans-serif"],
                        "body-lg": ["Inter", "sans-serif"],
                        "label-sm": ["Inter", "sans-serif"],
                        "body-sm": ["Inter", "sans-serif"],
                        "headline-lg": ["Inter", "sans-serif"],
                        "label-md": ["Inter", "sans-serif"],
                        "headline-lg-mobile": ["Inter", "sans-serif"],
                        "body-md": ["Inter", "sans-serif"]
                    },
                    fontSize: {
                        "headline-sm": ["20px", { lineHeight: "28px", fontWeight: "600" }],
                        "headline-md": ["24px", { lineHeight: "32px", letterSpacing: "-0.01em", fontWeight: "600" }],
                        "body-lg": ["18px", { lineHeight: "28px", fontWeight: "400" }],
                        "label-sm": ["12px", { lineHeight: "16px", fontWeight: "500" }],
                        "body-sm": ["14px", { lineHeight: "20px", fontWeight: "400" }],
                        "headline-lg": ["32px", { lineHeight: "40px", letterSpacing: "-0.02em", fontWeight: "700" }],
                        "label-md": ["14px", { lineHeight: "16px", letterSpacing: "0.05em", fontWeight: "600" }],
                        "headline-lg-mobile": ["24px", { lineHeight: "32px", letterSpacing: "-0.01em", fontWeight: "700" }],
                        "body-md": ["16px", { lineHeight: "24px", fontWeight: "400" }]
                    }
                }
            }
        };
    </script>
</head>
<body class="bg-background text-on-surface font-['Inter']">
    <header class="fixed top-0 w-full z-50 bg-surface border-b border-outline-variant shadow-sm h-16">
        <nav class="flex justify-between items-center w-full px-margin-desktop max-w-max-width mx-auto h-full">
            <div class="flex items-center gap-xl">
                <span class="text-headline-md font-bold text-primary">DriveEase</span>
                <div class="hidden md:flex items-center gap-lg">
                    <a class="text-primary border-b-2 border-primary pb-1 text-label-md hover:text-primary transition-colors duration-200" href="index.php">Home</a>
                    <a class="text-on-surface-variant text-label-md hover:text-primary transition-colors duration-200" href="customer/cars.php">Cars</a>
                    <a class="text-on-surface-variant text-label-md hover:text-primary transition-colors duration-200" href="#">How it Works</a>
                    <a class="text-on-surface-variant text-label-md hover:text-primary transition-colors duration-200" href="#">About</a>
                    <a class="text-on-surface-variant text-label-md hover:text-primary transition-colors duration-200" href="#">Contact</a>
                    <a class="text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="customer/booking_history.php">Booking History</a>
                </div>
            </div>

            <div class="flex items-center gap-md">
                <?php if (isset($_SESSION['user_id'])): ?>
                <button class="material-symbols-outlined text-primary p-xs hover:bg-surface-container-high rounded-full transition-colors">notifications</button>
                <div class="relative group">
                    <button class="flex items-center gap-sm cursor-pointer">
                        <div class="w-8 h-8 rounded-full overflow-hidden border border-outline-variant">
                            <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDSp_pP4RqWjO23JNAuHvbCrwg8U6h0Mox0kMLpmwuHyJVLOjvG2-_IA2ONo9DVugKUOlTF-IvRvhItfu_nWTU1H020e7TewvTZY5azSw3TYCdxvBBGXG0gxg9FPFBbnMHuVQgnsBR-NmytHJ6OdvLG0ns7EpjdpSI_BKAIhtVjZXGuDlkIHh6pmaxwce2jzjgf-a4pob2M-sM_Z5RXoc_Zh42kPtVodMEhbJ3QxiPNatc49cRLHQeqpGUpR8p-6jUz7PgtRP9KMWw" alt="Profile">
                        </div>
                        <span class="hidden lg:inline text-label-md font-label-md text-on-surface"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Account'); ?></span>
                        <span class="material-symbols-outlined text-on-surface-variant text-[18px]">expand_more</span>
                    </button>
                    <div class="absolute right-0 mt-2 w-44 bg-surface border border-outline-variant rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                        <a href="admin/dashboard.php" class="block px-md py-sm text-body-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors">Admin Dashboard</a>
                        <?php else: ?>
                        <a href="customer/profile.php" class="block px-md py-sm text-body-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-colors">My Profile</a>
                        <?php endif; ?>
                        <a href="logout.php" class="block px-md py-sm text-body-sm text-error hover:bg-error-container transition-colors">Logout</a>
                    </div>
                </div>
                <?php else: ?>
                <a href="login.php" class="text-on-surface-variant hover:text-primary transition-colors duration-200 text-label-md font-label-md px-md py-xs">Login</a>
                <a href="register.php" class="bg-primary text-on-primary px-lg py-xs rounded-lg font-label-md text-label-md hover:bg-primary-container transition-colors duration-200">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="pt-16">
        <section class="relative h-[870px] min-h-[600px] flex items-center overflow-hidden">
            <div class="absolute inset-0 z-0">
                <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBTHdjneGjmA9diaR0POjwkgjJ2XBpSJtAwVshsu3tLI9WamUZXopVeKyfDLq01klb0cmjKrrtSyQvuLqry3ErKmUVjEgof0kJK_ptBJhSut7UsuTevdW46Ixpt96WnIclHlPlpLlu1ZqjNDUfa8pXCzs5mob8CDNLjzKcSj7Rkzdq1YzEWldRXRL8tuc2nEBb8eF4LWnZCwLMiGqcnEKjXMHjBw99hJDhV9IIGLUhzER2j6dydu_vprrrRLaloJ5d-dOvn731jhrY" alt="Luxury car on coastal road">
                <div class="absolute inset-0 bg-gradient-to-r from-background/90 via-background/40 to-transparent"></div>
            </div>

            <div class="relative z-10 px-margin-desktop max-w-max-width mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-xl">
                <div class="lg:col-span-6 flex flex-col justify-center">
                    <h1 class="text-headline-lg text-on-surface mb-md max-w-md md:text-6xl md:leading-tight">Find Your Perfect Ride</h1>
                    <p class="text-body-lg text-on-surface-variant mb-xl max-w-md">Premium vehicles for every journey. Book in seconds and experience the future of car rental with DriveEase.</p>
                </div>

                <div class="lg:col-span-6 flex items-center justify-end">
                    <div class="glass-card p-xl rounded-xl shadow-xl w-full max-w-md border border-white/20">
                        <div class="flex flex-col gap-lg">
                            <div class="space-y-sm">
                                <label class="text-label-sm text-primary uppercase tracking-wider">Pick-up Location</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline">location_on</span>
                                    <input class="w-full pl-12 pr-md py-md bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none text-body-md transition-all" placeholder="City, Airport, or Address" type="text">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-md">
                                <div class="space-y-sm">
                                    <label class="text-label-sm text-primary uppercase tracking-wider">Pick-up Date</label>
                                    <input class="w-full px-md py-md bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none text-body-md" type="date">
                                </div>
                                <div class="space-y-sm">
                                    <label class="text-label-sm text-primary uppercase tracking-wider">Drop-off Date</label>
                                    <input class="w-full px-md py-md bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none text-body-md" type="date">
                                </div>
                            </div>

                            <button class="w-full bg-primary text-on-primary py-lg rounded-lg font-headline-sm hover:bg-primary-container transition-all shadow-lg active:scale-[0.98] mt-sm">Search Vehicles</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-2xl px-margin-desktop max-w-max-width mx-auto">
            <div class="text-center mb-2xl">
                <h2 class="text-headline-lg text-on-surface">Experience Excellence</h2>
                <div class="h-1.5 w-16 bg-primary mx-auto mt-md rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-xl">
                <div class="group p-xl bg-surface-container-low rounded-xl border border-outline-variant hover:border-primary transition-all duration-300">
                    <div class="w-16 h-16 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center mb-lg group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl">bolt</span>
                    </div>
                    <h3 class="text-headline-sm mb-md">Easy Booking</h3>
                    <p class="text-body-md text-on-surface-variant leading-relaxed">Our streamlined process lets you secure your dream car in under two minutes, with instant confirmation.</p>
                </div>

                <div class="group p-xl bg-surface-container-low rounded-xl border border-outline-variant hover:border-primary transition-all duration-300">
                    <div class="w-16 h-16 bg-secondary-container text-on-secondary-container rounded-full flex items-center justify-center mb-lg group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl">directions_car</span>
                    </div>
                    <h3 class="text-headline-sm mb-md">Wide Fleet</h3>
                    <p class="text-body-md text-on-surface-variant leading-relaxed">From eco-friendly electrics to premium SUVs, our diverse fleet is meticulously maintained for your safety.</p>
                </div>

                <div class="group p-xl bg-surface-container-low rounded-xl border border-outline-variant hover:border-primary transition-all duration-300">
                    <div class="w-16 h-16 bg-tertiary-container text-on-tertiary-container rounded-full flex items-center justify-center mb-lg group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-3xl">support_agent</span>
                    </div>
                    <h3 class="text-headline-sm mb-md">24/7 Support</h3>
                    <p class="text-body-md text-on-surface-variant leading-relaxed">Our dedicated assistance team is always available to ensure your journey is smooth and worry-free.</p>
                </div>
            </div>
        </section>

        <section class="py-2xl bg-surface-container">
            <div class="px-margin-desktop max-w-max-width mx-auto">
                <div class="flex justify-between items-end mb-2xl">
                    <div>
                        <h2 class="text-headline-lg text-on-surface">Featured Fleet</h2>
                        <p class="text-body-md text-on-surface-variant">Top-rated vehicles ready for your next adventure.</p>
                    </div>
                    <a class="text-primary font-label-md flex items-center gap-xs hover:gap-md transition-all" href="#">View All Cars <span class="material-symbols-outlined">arrow_forward</span></a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-xl">
                    <div class="car-card-hover group bg-white rounded-xl overflow-hidden border border-outline-variant shadow-sm hover:shadow-xl transition-all duration-300">
                        <div class="h-64 overflow-hidden relative">
                            <img class="car-image w-full h-full object-cover transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBpkCJpRc77tyNvGYoBru2vUyIpIBotZ7SyvXtgSJ0PYl2vTTJCq09qSIPMcIl73goR8fjqJvo_wCs2B9tLv3_YUfO3D4bCZVyZfw4RIC6SMrOXXjl6LufxiOUk7uPOfUAr3PawSaObTd-aO_0XL7u_IId4LfyzSGUYC7KxQ1XZ9PxDWa0F4Bw2laTgrSCEp7QXhD8OYm9x0DrySBZiNCUJ-c43xmtfN6JkuWRWF4OnbR3PW6jurhRYJLQaWVIvUieOc5HDqlGCGZk" alt="Tesla Model S">
                            <div class="absolute top-md left-md">
                                <span class="bg-primary-container text-on-primary-container text-label-sm px-md py-xs rounded-full">Electric</span>
                            </div>
                        </div>
                        <div class="p-xl">
                            <div class="flex justify-between items-start mb-md">
                                <div>
                                    <h3 class="text-headline-sm">Tesla Model S</h3>
                                    <p class="text-body-sm text-on-surface-variant">Luxury Sedan • 5 Seats</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-headline-sm text-primary">₹120</p>
                                    <p class="text-label-sm text-on-surface-variant">/ day</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-lg border-t border-outline-variant pt-lg mt-md">
                                <div class="flex items-center gap-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[18px]">settings</span>
                                    <span class="text-body-sm">Auto</span>
                                </div>
                                <div class="flex items-center gap-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[18px]">bolt</span>
                                    <span class="text-body-sm">400mi</span>
                                </div>
                                <div class="flex items-center gap-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[18px]">verified</span>
                                    <span class="text-body-sm">Top Rated</span>
                                </div>
                            </div>
                            <button class="w-full mt-xl py-md border border-primary text-primary font-label-md rounded-lg hover:bg-primary hover:text-on-primary transition-all">Rent Now</button>
                        </div>
                    </div>

                    <div class="car-card-hover group bg-white rounded-xl overflow-hidden border border-outline-variant shadow-sm hover:shadow-xl transition-all duration-300">
                        <div class="h-64 overflow-hidden relative">
                            <img class="car-image w-full h-full object-cover transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBQVdzntCz3nO6jbakuOj1owSH5NOZRQNdLzRMX0JlCWnvSjYdY_EOMbVYyjNCpvcPAUz9T7fCKfJeUw-_ZMd9UcLs92LlRc7L4mO6J1Z9H_ZQfgBiOgwmx_gjJeA00K2Kct96FuFybZ7yYA4ka_SIaovk6k90UdMsAl54xa8NT3MslQOxVurvrZxVDnhQS5fMwwi8W6L9ONX-xbQaJvLxPskXkh2BrnmkrAtvvR8p4ue7aVkMzRrSjeks5ofFROdVEeGmu2GopnUw" alt="Range Rover Sport">
                            <div class="absolute top-md left-md">
                                <span class="bg-secondary-container text-on-secondary-container text-label-sm px-md py-xs rounded-full">SUV</span>
                            </div>
                        </div>
                        <div class="p-xl">
                            <div class="flex justify-between items-start mb-md">
                                <div>
                                    <h3 class="text-headline-sm">Range Rover Sport</h3>
                                    <p class="text-body-sm text-on-surface-variant">Full-size SUV • 7 Seats</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-headline-sm text-primary">₹180</p>
                                    <p class="text-label-sm text-on-surface-variant">/ day</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-lg border-t border-outline-variant pt-lg mt-md">
                                <div class="flex items-center gap-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[18px]">settings</span>
                                    <span class="text-body-sm">Auto</span>
                                </div>
                                <div class="flex items-center gap-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[18px]">local_gas_station</span>
                                    <span class="text-body-sm">Hybrid</span>
                                </div>
                                <div class="flex items-center gap-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[18px]">group</span>
                                    <span class="text-body-sm">Large Group</span>
                                </div>
                            </div>
                            <button class="w-full mt-xl py-md border border-primary text-primary font-label-md rounded-lg hover:bg-primary hover:text-on-primary transition-all">Rent Now</button>
                        </div>
                    </div>

                    <div class="car-card-hover group bg-white rounded-xl overflow-hidden border border-outline-variant shadow-sm hover:shadow-xl transition-all duration-300">
                        <div class="h-64 overflow-hidden relative">
                            <img class="car-image w-full h-full object-cover transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAkoYsmUV8GLzJQueys_whD1hO_HQDibnNNqaiCq69IMkLrtih2hNL3HPxsSEbkYXnLo21BSwPcuvgPwhCqbRWqC9Ki13JP8K7UsD0Y1BhXEHC6zIhLK0IT6SuR-P4jlc6bp1NtWvRZ8l4RvOrv5oT0gADeBRel7CsZqvJYqT48_cXh6QWVUxprRnNlP-75zmnpeAZtgchg0V7ZLGRQ86lk2Spv-xUPOhpfDq_0VVTdGVmrvrP2hfXn6sL0CQeoRSqrs0XwH7Wfyj0" alt="BMW M4 Competition">
                            <div class="absolute top-md left-md">
                                <span class="bg-error-container text-on-error-container text-label-sm px-md py-xs rounded-full">Performance</span>
                            </div>
                        </div>
                        <div class="p-xl">
                            <div class="flex justify-between items-start mb-md">
                                <div>
                                    <h3 class="text-headline-sm">BMW M4 Competition</h3>
                                    <p class="text-body-sm text-on-surface-variant">Sport Coupe • 4 Seats</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-headline-sm text-primary">₹210</p>
                                    <p class="text-label-sm text-on-surface-variant">/ day</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-lg border-t border-outline-variant pt-lg mt-md">
                                <div class="flex items-center gap-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[18px]">settings</span>
                                    <span class="text-body-sm">Paddles</span>
                                </div>
                                <div class="flex items-center gap-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[18px]">speed</span>
                                    <span class="text-body-sm">High HP</span>
                                </div>
                                <div class="flex items-center gap-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[18px]">stars</span>
                                    <span class="text-body-sm">Premium</span>
                                </div>
                            </div>
                            <button class="w-full mt-xl py-md border border-primary text-primary font-label-md rounded-lg hover:bg-primary hover:text-on-primary transition-all">Rent Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-2xl px-margin-desktop max-w-max-width mx-auto">
            <div class="text-center mb-2xl">
                <h2 class="text-headline-lg text-on-surface">How It Works</h2>
                <p class="text-body-md text-on-surface-variant mt-sm">Three simple steps to hit the road.</p>
            </div>

            <div class="relative">
                <div class="hidden md:block absolute top-1/2 left-0 w-full h-0.5 bg-outline-variant -translate-y-1/2 z-0"></div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2xl relative z-10">
                    <div class="flex flex-col items-center text-center bg-background px-md">
                        <div class="w-16 h-16 bg-primary text-on-primary rounded-full flex items-center justify-center text-headline-sm font-bold mb-lg shadow-lg">1</div>
                        <h4 class="text-headline-sm mb-md">Choose Your Car</h4>
                        <p class="text-body-md text-on-surface-variant max-w-xs">Browse our extensive collection of premium vehicles and find the one that fits your style and needs.</p>
                    </div>

                    <div class="flex flex-col items-center text-center bg-background px-md">
                        <div class="w-16 h-16 bg-primary text-on-primary rounded-full flex items-center justify-center text-headline-sm font-bold mb-lg shadow-lg">2</div>
                        <h4 class="text-headline-sm mb-md">Book &amp; Confirm</h4>
                        <p class="text-body-md text-on-surface-variant max-w-xs">Enter your details, select any extras, and confirm your booking with our secure payment gateway.</p>
                    </div>

                    <div class="flex flex-col items-center text-center bg-background px-md">
                        <div class="w-16 h-16 bg-primary text-on-primary rounded-full flex items-center justify-center text-headline-sm font-bold mb-lg shadow-lg">3</div>
                        <h4 class="text-headline-sm mb-md">Drive Away</h4>
                        <p class="text-body-md text-on-surface-variant max-w-xs">Pick up your car from your chosen location or have it delivered to your doorstep. It's that simple!</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-2xl bg-primary text-on-primary overflow-hidden relative">
            <div class="absolute inset-0 opacity-10">
                <svg height="100%" viewBox="0 0 100 100" width="100%" preserveAspectRatio="none">
                    <defs>
                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"></path>
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#grid)"></rect>
                </svg>
            </div>

            <div class="px-margin-desktop max-w-max-width mx-auto text-center relative z-10">
                <h2 class="text-headline-lg mb-lg">Ready for a Premium Journey?</h2>
                <p class="text-body-lg mb-xl max-w-2xl mx-auto opacity-90">Join thousands of happy drivers who trust DriveEase for their daily commutes and long adventures.</p>
                <div class="flex flex-col sm:flex-row gap-md justify-center">
                    <button class="bg-white text-primary px-2xl py-lg rounded-lg font-headline-sm hover:bg-surface-container transition-all">Start Your Search</button>
                    <button class="bg-transparent border-2 border-white text-on-primary px-2xl py-lg rounded-lg font-headline-sm hover:bg-white/10 transition-all">Download App</button>
                </div>
            </div>
        </section>
    </main>

    <footer class="w-full mt-auto bg-surface-container-highest border-t border-outline-variant">
        <div class="w-full py-xl px-margin-desktop grid grid-cols-1 md:grid-cols-2 items-center max-w-max-width mx-auto">
            <div class="mb-xl md:mb-0">
                <span class="text-headline-sm font-bold text-on-surface block mb-md">DriveEase</span>
                <p class="text-body-sm text-on-surface-variant max-w-xs">Your trusted partner for modern, reliable car rental solutions worldwide.</p>
                <p class="text-body-sm text-on-surface-variant mt-xl">© 2024 DriveEase Car Rental Systems. All rights reserved.</p>
            </div>
            <div class="grid grid-cols-2 gap-xl md:justify-items-end">
                <div class="flex flex-col gap-sm">
                    <h4 class="text-label-md text-on-surface">Quick Links</h4>
                    <a class="text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="#">Home</a>
                    <a class="text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="#">Cars</a>
                    <a class="text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="#">About</a>
                </div>
                <div class="flex flex-col gap-sm">
                    <h4 class="text-label-md text-on-surface">Support</h4>
                    <a class="text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="#">Help Center</a>
                    <a class="text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="#">Privacy</a>
                    <a class="text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="#">Terms</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
<span class="text-label-md text-on-surface dark:text-inverse-on-surface uppercase mb-sm">Company</span>
<a class="text-on-surface-variant dark:text-surface-variant text-body-sm hover:text-on-surface transition-colors" href="#">Privacy Policy</a>
<a class="text-on-surface-variant dark:text-surface-variant text-body-sm hover:text-on-surface transition-colors" href="#">Terms of Service</a>
</div>
<div class="flex flex-col gap-sm">
<span class="text-label-md text-on-surface dark:text-inverse-on-surface uppercase mb-sm">Support</span>
<a class="text-on-surface-variant dark:text-surface-variant text-body-sm hover:text-on-surface transition-colors" href="#">Cookie Policy</a>
<a class="text-on-surface-variant dark:text-surface-variant text-body-sm hover:text-on-surface transition-colors" href="#">Contact Support</a>
</div>
</div>
</div>
</footer>
<script>
        // Simple scroll interaction for Header
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.classList.add('shadow-md');
                header.classList.add('bg-white/90');
                header.classList.add('backdrop-blur-md');
            } else {
                header.classList.remove('shadow-md');
                header.classList.remove('bg-white/90');
                header.classList.remove('backdrop-blur-md');
            }
        });

        // Booking widget date logic (simple check)
        const dateInputs = document.querySelectorAll('input[type="date"]');
        const today = new Date().toISOString().split('T')[0];
        dateInputs.forEach(input => {
            input.min = today;
            input.value = today;
        });
    </script>
</body></html>