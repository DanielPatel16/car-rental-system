<!DOCTYPE html>

<html class="scroll-smooth" lang="en" style=""><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>DriveEase - Complete Your Booking</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
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
                    "label-sm": [
                            "Inter"
                    ],
                    "headline-sm": [
                            "Inter"
                    ],
                    "headline-lg-mobile": [
                            "Inter"
                    ],
                    "body-sm": [
                            "Inter"
                    ],
                    "body-md": [
                            "Inter"
                    ],
                    "headline-lg": [
                            "Inter"
                    ],
                    "label-md": [
                            "Inter"
                    ],
                    "body-lg": [
                            "Inter"
                    ],
                    "headline-md": [
                            "Inter"
                    ]
            },
            "fontSize": {
                    "label-sm": [
                            "12px",
                            {
                                    "lineHeight": "16px",
                                    "fontWeight": "500"
                            }
                    ],
                    "headline-sm": [
                            "20px",
                            {
                                    "lineHeight": "28px",
                                    "fontWeight": "600"
                            }
                    ],
                    "headline-lg-mobile": [
                            "24px",
                            {
                                    "lineHeight": "32px",
                                    "letterSpacing": "-0.01em",
                                    "fontWeight": "700"
                            }
                    ],
                    "body-sm": [
                            "14px",
                            {
                                    "lineHeight": "20px",
                                    "fontWeight": "400"
                            }
                    ],
                    "body-md": [
                            "16px",
                            {
                                    "lineHeight": "24px",
                                    "fontWeight": "400"
                            }
                    ],
                    "headline-lg": [
                            "32px",
                            {
                                    "lineHeight": "40px",
                                    "letterSpacing": "-0.02em",
                                    "fontWeight": "700"
                            }
                    ],
                    "label-md": [
                            "14px",
                            {
                                    "lineHeight": "16px",
                                    "letterSpacing": "0.05em",
                                    "fontWeight": "600"
                            }
                    ],
                    "body-lg": [
                            "18px",
                            {
                                    "lineHeight": "28px",
                                    "fontWeight": "400"
                            }
                    ],
                    "headline-md": [
                            "24px",
                            {
                                    "lineHeight": "32px",
                                    "letterSpacing": "-0.01em",
                                    "fontWeight": "600"
                            }
                    ]
            }
    },
        },
      }
    </script>
<style>
        body { background-color: #F8FAFC; } /* Level 0 Background per design system */
        .card-shadow { box-shadow: 0px 4px 6px -1px rgba(0, 40, 142, 0.05); } /* Tinted ambient shadow */
    </style>
</head>
<body class="bg-background text-on-background font-body-md min-h-screen flex flex-col antialiased">
<!-- TopAppBar Semantic Shell -->
<header class="bg-surface-bright dark:bg-on-background border-b border-outline-variant sticky top-0 z-50">
<div class="flex justify-between items-center w-full px-margin-desktop max-w-max-width mx-auto h-16">
<div class="flex items-center gap-xl">
<a class="font-headline-md text-headline-md font-bold text-primary dark:text-primary-fixed" href="#">DriveEase</a>
<!-- Desktop Nav -->
<nav class="hidden md:flex gap-lg h-16 items-center">
<a class="text-on-surface-variant dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed transition-colors font-body-md text-body-md cursor-pointer active:scale-95 transition-transform flex items-center h-full" href="#">Fleet</a>
<!-- My Bookings is Active based on semantic intent of this booking page flow context -->
<a class="text-primary dark:text-primary-fixed border-b-2 border-primary dark:border-primary-fixed pb-1 hover:text-primary dark:hover:text-primary-fixed transition-colors font-body-md text-body-md cursor-pointer active:scale-95 transition-transform flex items-center h-[calc(100%-2px)] mt-[2px]" href="#">My Bookings</a>
<a class="text-on-surface-variant dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed transition-colors font-body-md text-body-md cursor-pointer active:scale-95 transition-transform flex items-center h-full" href="#">Support</a>
</nav>
</div>
<div class="flex items-center gap-md">
<button class="text-on-surface-variant hover:text-primary transition-colors flex items-center justify-center cursor-pointer active:scale-95 transition-transform">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0;">notifications</span>
</button>
<button class="text-on-surface-variant hover:text-primary transition-colors flex items-center justify-center cursor-pointer active:scale-95 transition-transform">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 0;">help</span>
</button>
<div class="w-8 h-8 rounded-full overflow-hidden border border-outline-variant ml-sm flex-shrink-0 cursor-pointer active:scale-95 transition-transform">
<img alt="Customer profile avatar" class="w-full h-full object-cover" data-alt="A professional headshot of a person looking forward, well-lit in a modern office environment, maintaining a clean and trustworthy aesthetic appropriate for enterprise software user avatars." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDfJUyxnY71hJHw7HiMOxJeNXL1nhsYYRDbTW8GZTaIDmbEFTxoKjPyx53Z631Y2psgXMsFMeoJIF5AfLpx0g7ge3MEQ_yS0b9YETGUtZLNhc_f6ToE2N8WsrSTXXkSNWvFc55twd23Ao0-QYfdU0rqbBDQESOmeErUFrU7iyjfxV74n4_tMyjawAkJ3mUTE_nxciLOBJAbHSWTqc3yVic6Bd7lfAGvxZ-d-8qfHPcpAn5caUgeTFaLovsr578HNL6lnMJ1cyT8YmQ"/>
</div>
</div>
</div>
</header>
<!-- Main Content Canvas -->
<main class="flex-grow w-full px-margin-mobile md:px-margin-desktop py-xl max-w-max-width mx-auto">
<div class="mb-lg">
<h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-on-background mb-xs">Complete Your Booking</h1>
<p class="font-body-md text-body-md text-on-surface-variant">Review your details and complete secure payment.</p>
</div>
<div class="flex flex-col lg:flex-row gap-gutter">
<!-- Left Column: Details & Extras -->
<div class="w-full lg:w-2/3 flex flex-col gap-lg">
<!-- Vehicle Details Card -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-lg card-shadow">
<h2 class="font-headline-sm text-headline-sm text-on-background border-b border-surface-container-high pb-sm mb-md">Selected Vehicle</h2>
<div class="flex flex-col md:flex-row gap-lg items-start">
<div class="w-full md:w-1/3 aspect-[4/3] rounded bg-surface-container overflow-hidden">
<img alt="Selected luxury car" class="w-full h-full object-cover" data-alt="A sleek, modern luxury sedan parked in a well-lit, clean studio environment. The car is painted in a deep metallic blue, reflecting crisp studio lights. High quality, professional automotive photography style." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCrg84DzeVh-5wI7zcQesOHHHAtNfbkGE9K1Q1F2ZbsjrHKVfUMTA-5ts2lm_u2xWkMGaFn9CiA6HpFE9tU7THQxz05uiy0abo-fKTQaAFghQGAlb6QoPVGH2sJwVV6t72UUjr28X16XbChsMk-Dm7S4Y-ZWkQ16NRjLRZ6XB3QJEu7dlY8kSaJElDNcj4f0pkTX-S95xSlPTDWQsArCmBADHfc1Oxzp0xjIprb3cjGfGb-b2C0EdhTNM8oyPAfq6aEDxVPCDF94CI"/>
</div>
<div class="w-full md:w-2/3 flex flex-col gap-sm">
<div class="flex justify-between items-start">
<div>
<h3 class="font-headline-md text-headline-md text-on-background">Mercedes-Benz E-Class</h3>
<p class="font-body-sm text-body-sm text-secondary">Or similar premium sedan</p>
</div>
<span class="bg-surface-container-low text-primary px-sm py-xs rounded-full font-label-sm text-label-sm flex items-center gap-xs border border-primary-fixed-dim">
<span class="material-symbols-outlined text-[16px]">verified</span> Premium
                                </span>
</div>
<div class="grid grid-cols-2 md:grid-cols-4 gap-sm mt-sm">
<div class="flex items-center gap-xs text-on-surface-variant">
<span class="material-symbols-outlined text-[20px]">airline_seat_recline_normal</span>
<span class="font-body-sm text-body-sm">5 Seats</span>
</div>
<div class="flex items-center gap-xs text-on-surface-variant">
<span class="material-symbols-outlined text-[20px]">work</span>
<span class="font-body-sm text-body-sm">3 Bags</span>
</div>
<div class="flex items-center gap-xs text-on-surface-variant">
<span class="material-symbols-outlined text-[20px]">settings</span>
<span class="font-body-sm text-body-sm">Auto</span>
</div>
<div class="flex items-center gap-xs text-on-surface-variant">
<span class="material-symbols-outlined text-[20px]">ac_unit</span>
<span class="font-body-sm text-body-sm">A/C</span>
</div>
</div>
</div>
</div>
</section>
<!-- Rental Details Card -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-lg card-shadow">
<h2 class="font-headline-sm text-headline-sm text-on-background border-b border-surface-container-high pb-sm mb-md">Trip Details</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-lg relative">
<!-- Connecting Line for Desktop -->
<div class="hidden md:block absolute left-1/2 top-4 bottom-4 w-px bg-surface-container-high transform -translate-x-1/2"></div>
<div class="flex flex-col gap-sm">
<div class="flex items-center gap-sm text-primary">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">flight_land</span>
<h4 class="font-label-md text-label-md">Pick-up</h4>
</div>
<div>
<p class="font-body-lg text-body-lg text-on-background font-medium">New Delhi Airport (DEL)</p>
<p class="font-body-sm text-body-sm text-secondary">Terminal 3 Arrivals</p>
</div>
<div class="bg-surface-container-low p-sm rounded border border-surface-container-high mt-xs flex items-center gap-sm">
<span class="material-symbols-outlined text-secondary text-[20px]">calendar_today</span>
<div>
<p class="font-body-sm text-body-sm text-on-background font-medium">Oct 15, 2024</p>
<p class="font-label-sm text-label-sm text-secondary">10:00 AM</p>
</div>
</div>
</div>
<div class="flex flex-col gap-sm">
<div class="flex items-center gap-sm text-primary">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">flight_takeoff</span>
<h4 class="font-label-md text-label-md">Drop-off</h4>
</div>
<div>
<p class="font-body-lg text-body-lg text-on-background font-medium">New Delhi Airport (DEL)</p>
<p class="font-body-sm text-body-sm text-secondary">Terminal 3 Departures</p>
</div>
<div class="bg-surface-container-low p-sm rounded border border-surface-container-high mt-xs flex items-center gap-sm">
<span class="material-symbols-outlined text-secondary text-[20px]">calendar_today</span>
<div>
<p class="font-body-sm text-body-sm text-on-background font-medium">Oct 20, 2024</p>
<p class="font-label-sm text-label-sm text-secondary">10:00 AM</p>
</div>
</div>
</div>
</div>
</section>
<!-- Extras & Insurance Card -->
</div>
<!-- Right Column: Sticky Summary & Payment -->
<div class="w-full lg:w-1/3">
<div class="sticky top-24 flex flex-col gap-lg">
<!-- Summary Card -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-lg card-shadow">
<h2 class="font-headline-sm text-headline-sm text-on-background border-b border-surface-container-high pb-sm mb-md">Price Summary</h2>
<div class="flex flex-col gap-sm font-body-sm text-body-sm text-on-surface-variant">
<div class="flex justify-between items-center">
<span class="">Vehicle Rate (5 days x ₹4,000)</span>
<span class="font-medium text-on-background">₹20,000</span>
</div>
<div class="flex justify-between items-center">
<span class="">Taxes &amp; Fees (18% GST)</span>
<span class="font-medium text-on-background">₹3,600</span>
</div>
<div class="flex justify-between items-center text-secondary">
<span class="">Extras</span>
<span class="">₹0</span>
</div>
<hr class="border-surface-container-high my-sm"/>
<div class="flex justify-between items-end">
<span class="font-label-md text-label-md text-on-background">Total Amount</span>
<span class="font-headline-md text-headline-md font-bold text-primary">₹23,600</span>
</div>
</div>
</section>
<!-- Payment Form Card -->
<section class="bg-surface-container-lowest border border-outline-variant rounded-lg p-lg card-shadow">
<h2 class="font-headline-sm text-headline-sm text-on-background mb-md">Payment Details</h2>
<div class="flex flex-col gap-md">
<!-- Payment Method Selection -->
<div class="flex flex-col gap-sm">
<label class="block font-label-sm text-label-sm text-on-surface-variant">Select Payment Method</label>
<div class="grid grid-cols-1 gap-sm">
<!-- UPI Option -->
<label class="flex items-center justify-between p-sm border border-outline-variant rounded-lg cursor-pointer hover:bg-surface-container-low transition-colors" id="label-upi">
<div class="flex items-center gap-sm">
<input class="w-4 h-4 text-primary focus:ring-primary border-outline-variant" name="payment_method" type="radio" value="upi"/>
<span class="font-body-md text-body-md text-on-background">UPI</span>
</div>
<span class="material-symbols-outlined text-secondary">account_balance_wallet</span>
</label>
<!-- Card Option (Selected by default for this UI state) -->
<label class="flex items-center justify-between p-sm border-2 border-primary rounded-lg cursor-pointer bg-surface-container-low transition-colors" id="label-card">
<div class="flex items-center gap-sm">
<input checked="" class="w-4 h-4 text-primary focus:ring-primary border-outline-variant" name="payment_method" type="radio" value="card"/>
<span class="font-body-md text-body-md text-on-background">Debit/Credit Card</span>
</div>
<span class="material-symbols-outlined text-secondary">credit_card</span>
</label>
<!-- Cash Option -->
<label class="flex items-center justify-between p-sm border border-outline-variant rounded-lg cursor-pointer hover:bg-surface-container-low transition-colors" id="label-cash">
<div class="flex items-center gap-sm">
<input class="w-4 h-4 text-primary focus:ring-primary border-outline-variant" name="payment_method" type="radio" value="cash"/>
<span class="font-body-md text-body-md text-on-background">Cash on Pickup</span>
</div>
<span class="material-symbols-outlined text-secondary">payments</span>
</label>
</div>
</div>
<!-- Dynamic Content Area (Showing Card fields as default) -->
<div class="flex flex-col gap-md pt-md border-t border-surface-container-high" id="payment-method-details">
<!-- Card Details (Visible) -->
<div class="flex flex-col gap-md" id="fields-card">
<div>
<label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs">Name on Card</label>
<div class="relative">
<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
<span class="material-symbols-outlined text-secondary text-[20px]">person</span>
</div>
<input class="block w-full pl-10 pr-3 py-2 border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-surface-container-lowest text-on-background font-body-md text-body-md transition-shadow" placeholder="John Doe" type="text"/>
</div>
</div>
<div>
<label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs">Card Number</label>
<div class="relative">
<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
<span class="material-symbols-outlined text-secondary text-[20px]">credit_card</span>
</div>
<input class="block w-full pl-10 pr-3 py-2 border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-surface-container-lowest text-on-background font-body-md text-body-md transition-shadow" placeholder="0000 0000 0000 0000" type="text"/>
</div>
</div>
<div class="grid grid-cols-2 gap-md">
<div>
<label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs">Expiry</label>
<input class="block w-full px-3 py-2 border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-surface-container-lowest text-on-background font-body-md text-body-md transition-shadow" placeholder="MM/YY" type="text"/>
</div>
<div>
<label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs">CVV</label>
<input class="block w-full px-3 py-2 border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-surface-container-lowest text-on-background font-body-md text-body-md transition-shadow" placeholder="123" type="password"/>
</div>
</div>
</div>
<!-- UPI Details (Hidden) -->
<div class="hidden flex flex-col gap-md" id="fields-upi">
<div>
<label class="block font-label-sm text-label-sm text-on-surface-variant mb-xs">UPI ID</label>
<div class="relative">
<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
<span class="material-symbols-outlined text-secondary text-[20px]">alternate_email</span>
</div>
<input class="block w-full pl-10 pr-3 py-2 border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none bg-surface-container-lowest text-on-background font-body-md text-body-md transition-shadow" placeholder="user@upi" type="text"/>
</div>
</div>
</div>
<!-- Cash Details (Hidden) -->
<div class="hidden" id="fields-cash">
<p class="font-body-md text-body-md text-on-surface-variant p-md bg-surface-container-low rounded-lg border border-outline-variant text-center">
            Please pay at the counter during pickup.
        </p>
</div>
</div>
<!-- Action Button -->
<div class="mt-md">
<button class="w-full bg-primary text-on-primary font-label-md text-label-md py-3 px-4 rounded-lg hover:bg-primary-container hover:text-on-primary-container transition-colors shadow-sm flex items-center justify-center gap-sm" type="button">
<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">lock</span>
      Confirm &amp; Pay ₹23,600
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
</main>
<!-- Footer Semantic Shell -->
<footer class="bg-surface-container-lowest dark:bg-on-background border-t border-outline-variant mt-auto">
<div class="w-full py-lg px-margin-desktop flex flex-col md:flex-row justify-between items-center max-w-max-width mx-auto gap-md">
<div class="font-label-md text-label-md font-bold text-primary">DriveEase</div>
<div class="font-body-sm text-body-sm text-on-surface-variant text-center md:text-left">
                © 2024 DriveEase Car Rentals. All rights reserved.
            </div>
<nav class="flex gap-lg items-center">
<a class="text-on-surface-variant dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed transition-opacity hover:opacity-80 font-body-sm text-body-sm" href="#">Terms of Service</a>
<a class="text-on-surface-variant dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed transition-opacity hover:opacity-80 font-body-sm text-body-sm" href="#">Privacy Policy</a>
<a class="text-on-surface-variant dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed transition-opacity hover:opacity-80 font-body-sm text-body-sm" href="#">Contact Us</a>
</nav>
</div>
</footer>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const radioButtons = document.querySelectorAll('input[name="payment_method"]');
    const labels = {
      upi: document.getElementById('label-upi'),
      card: document.getElementById('label-card'),
      cash: document.getElementById('label-cash')
    };
    const fields = {
      upi: document.getElementById('fields-upi'),
      card: document.getElementById('fields-card'),
      cash: document.getElementById('fields-cash')
    };

    const activeClasses = ['border-2', 'border-primary', 'bg-surface-container-low'];
    const inactiveClasses = ['border', 'border-outline-variant'];

    radioButtons.forEach(radio => {
      radio.addEventListener('change', (e) => {
        const selectedValue = e.target.value;

        // Reset all labels and fields
        Object.keys(labels).forEach(key => {
          labels[key].classList.remove(...activeClasses);
          labels[key].classList.add(...inactiveClasses);
          fields[key].classList.add('hidden');
        });

        // Set active label and field
        labels[selectedValue].classList.remove(...inactiveClasses);
        labels[selectedValue].classList.add(...activeClasses);
        fields[selectedValue].classList.remove('hidden');
      });
    });
  });
</script>
</body></html>