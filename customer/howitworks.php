<?php
session_start();
include "../includes/header.php";
?>
<br />
<!-- Main Content -->
<main class="flex-grow">
<!-- Hero Section -->
<section class="w-full px-margin-desktop py-2xl max-w-max-width mx-auto text-center">
<h1 class="font-headline-lg text-headline-lg text-primary mb-md">Rent in 3 Easy Steps</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto">Experience seamless professional fleet management with our streamlined booking process.</p>
</section>
<!-- Steps Section (Bento Grid Style) -->
<section class="w-full px-margin-desktop py-xl max-w-max-width mx-auto">
<div class="grid grid-cols-1 md:grid-cols-3 gap-lg">
<!-- Step 1 -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm flex flex-col items-center text-center hover:bg-surface-container-low transition-colors duration-300">
<div class="w-16 h-16 rounded-full bg-primary-fixed text-primary flex items-center justify-center mb-md">
<span class="material-symbols-outlined text-headline-lg" data-icon="search">search</span>
</div>
<h3 class="font-headline-sm text-headline-sm text-primary mb-sm">1. Browse &amp; Choose</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Explore our premium fleet of vehicles tailored for professional needs.</p>
</div>
<!-- Step 2 -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm flex flex-col items-center text-center hover:bg-surface-container-low transition-colors duration-300">
<div class="w-16 h-16 rounded-full bg-primary-fixed text-primary flex items-center justify-center mb-md">
<span class="material-symbols-outlined text-headline-lg" data-icon="credit_card">credit_card</span>
</div>
<h3 class="font-headline-sm text-headline-sm text-primary mb-sm">2. Secure Booking</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Complete an easy online checkout with transparent pricing.</p>
</div>
<!-- Step 3 -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm flex flex-col items-center text-center hover:bg-surface-container-low transition-colors duration-300">
<div class="w-16 h-16 rounded-full bg-primary-fixed text-primary flex items-center justify-center mb-md">
<span class="material-symbols-outlined text-headline-lg" data-icon="directions_car">directions_car</span>
</div>
<h3 class="font-headline-sm text-headline-sm text-primary mb-sm">3. Start Your Journey</h3>
<p class="font-body-md text-body-md text-on-surface-variant">Pick up your vehicle and hit the road with confidence.</p>
</div>
</div>
</section>
<!-- Visual Anchor (Image) -->
<section class="w-full px-margin-desktop py-xl max-w-max-width mx-auto">
<div class="bg-cover bg-center w-full h-64 md:h-96 rounded-xl border border-outline-variant shadow-sm" data-alt="A modern, high-end car rental office interior. Bright, clean lighting, white surfaces with blue accents matching the DriveEase brand. A professional fleet manager is handing over car keys to a client. Sleek, corporate aesthetic." style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAQXEsY9AHKH2C1m_SF6g1E6ALSw3vWLJ4yt4g-FknGFt_b22Igm9JkOiy3tRB60U9uvE261INyIhgss6eeHj31VmnLx6-NyeL_Al7viGOAZ89TBxsrucsqK0kW03ZSYJxaAucVRADav9IAZofo4t-DhOdmESIWPETUcp3nRnx5kc8pmreYidtzER2i7lOVzqkToicZ24prkjhDzLm6bSVtJwCHQei0KgOjWDwzMIB2Xvy0EHMqx4SHX7MxJMH4ytTX7f_odcVJ1bo')"></div>
</section>
<!-- FAQ Section -->
<section class="w-full px-margin-desktop py-2xl max-w-max-width mx-auto">
<h2 class="font-headline-md text-headline-md text-primary mb-lg text-center">Frequently Asked Questions</h2>
<div class="max-w-3xl mx-auto space-y-md">
<!-- FAQ Item 1 -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-lg p-md">
<h4 class="font-headline-sm text-headline-sm text-on-surface mb-xs">What documents do I need?</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">A valid driver's license and a corporate ID or credit card for security deposit.</p>
</div>
<!-- FAQ Item 2 -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-lg p-md">
<h4 class="font-headline-sm text-headline-sm text-on-surface mb-xs">Is insurance included?</h4>
<p class="font-body-sm text-body-sm text-on-surface-variant">Basic liability insurance is included. Comprehensive coverage is available at checkout.</p>
</div>
</div>
</section>
</main>
<!-- Footer -->
<?php include "../includes/footer.php"; ?>
