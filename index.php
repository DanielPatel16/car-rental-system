<?php
session_start();

include "includes/header.php";

?>


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

    <?php include "includes/footer.php"; ?>
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