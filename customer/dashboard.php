<?php
session_start();

// dashboard.php is this project's home page (there is no separate index.php),
// so — like cars.php, howitworks.php, aboutus.php, and contactus.php — it must
// be viewable by everyone, logged in or not. We no longer force a redirect to
// login.php just to view it; pages that need an account (booking, profile,
// booking history) already have their own auth guards.

include "../includes/db.php";

// ---------------------------------------------------------------
// Featured Fleet: pull real, available cars from the database
// instead of showing static placeholder cards
// ---------------------------------------------------------------
$fuelBadge = [
    'Electric' => 'bg-primary-container text-on-primary-container',
    'Petrol'   => 'bg-secondary-container text-on-secondary-container',
    'Diesel'   => 'bg-on-secondary-container text-white',
    'Hybrid'   => 'bg-tertiary-container text-on-tertiary-container',
];

$featuredCars = [];
$res = $conn->query("SELECT * FROM cars WHERE status = 'Available' ORDER BY created_at DESC LIMIT 3");
if ($res) {
    $featuredCars = $res->fetch_all(MYSQLI_ASSOC);
}

include "../includes/header.php";
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
                    <form action="cars.php" method="GET" class="glass-card p-xl rounded-xl shadow-xl w-full max-w-md border border-white/20">
                        <div class="flex flex-col gap-lg">
                            <div class="space-y-sm">
                                <label class="text-label-sm text-primary uppercase tracking-wider">Pick-up Location</label>
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-md top-1/2 -translate-y-1/2 text-outline">location_on</span>
                                    <input class="w-full pl-12 pr-md py-md bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none text-body-md transition-all" placeholder="City, Airport, or Address" type="text" name="location">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-md">
                                <div class="space-y-sm">
                                    <label class="text-label-sm text-primary uppercase tracking-wider">Pick-up Date</label>
                                    <input class="w-full px-md py-md bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none text-body-md" type="date" name="pickup_date">
                                </div>
                                <div class="space-y-sm">
                                    <label class="text-label-sm text-primary uppercase tracking-wider">Drop-off Date</label>
                                    <input class="w-full px-md py-md bg-white border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none text-body-md" type="date" name="return_date">
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-primary text-on-primary py-lg rounded-lg font-headline-sm hover:bg-primary-container transition-all shadow-lg active:scale-[0.98] mt-sm">Search Vehicles</button>
                        </div>
                    </form>
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
                    <a class="text-primary font-label-md flex items-center gap-xs hover:gap-md transition-all" href="cars.php">View All Cars <span class="material-symbols-outlined">arrow_forward</span></a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-xl">
                <?php if (empty($featuredCars)): ?>
                    <div class="col-span-full text-center py-2xl text-on-surface-variant text-body-md">
                        No cars are available right now. Please check back soon.
                    </div>
                <?php endif; ?>
                <?php foreach ($featuredCars as $car):
                    $badge  = $fuelBadge[$car['fuel_type']] ?? 'bg-secondary-container text-on-secondary-container';
                    $imgSrc = $car['image'] ? "../uploads/cars/" . htmlspecialchars($car['image']) : "https://placehold.co/600x400?text=" . urlencode($car['brand']);
                ?>
                    <div class="car-card-hover group bg-white rounded-xl overflow-hidden border border-outline-variant shadow-sm hover:shadow-xl transition-all duration-300">
                        <div class="h-64 overflow-hidden relative">
                            <img class="car-image w-full h-full object-cover transition-transform duration-500" src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?>">
                            <div class="absolute top-md left-md">
                                <span class="<?php echo $badge; ?> text-label-sm px-md py-xs rounded-full"><?php echo htmlspecialchars($car['fuel_type']); ?></span>
                            </div>
                        </div>
                        <div class="p-xl">
                            <div class="flex justify-between items-start mb-md">
                                <div>
                                    <h3 class="text-headline-sm"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h3>
                                    <p class="text-body-sm text-on-surface-variant"><?php echo (int) $car['seats']; ?> Seats • <?php echo htmlspecialchars($car['transmission']); ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-headline-sm text-primary">₹<?php echo number_format($car['price_per_day'], 0); ?></p>
                                    <p class="text-label-sm text-on-surface-variant">/ day</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-lg border-t border-outline-variant pt-lg mt-md">
                                <div class="flex items-center gap-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[18px]">settings</span>
                                    <span class="text-body-sm"><?php echo $car['transmission'] === 'Automatic' ? 'Auto' : 'Manual'; ?></span>
                                </div>
                                <div class="flex items-center gap-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[18px]">local_gas_station</span>
                                    <span class="text-body-sm"><?php echo htmlspecialchars($car['fuel_type']); ?></span>
                                </div>
                                <div class="flex items-center gap-xs text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[18px]">group</span>
                                    <span class="text-body-sm"><?php echo (int) $car['seats']; ?> Seats</span>
                                </div>
                            </div>
                            <button onclick="window.location='booking.php?car_id=<?php echo (int) $car['id']; ?>'" class="w-full mt-xl py-md border border-primary text-primary font-label-md rounded-lg hover:bg-primary hover:text-on-primary transition-all">Rent Now</button>
                        </div>
                    </div>
                <?php endforeach; ?>
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

    </main>

    <?php include "../includes/footer.php"; ?>
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