<?php
/**
 * includes/footer.php
 * Closes </main> is NOT here on purpose — each page closes its own
 * <main> right before including this file, since page layouts differ
 * (sidebar + grid on cars.php, single column on booking_history.php, etc).
 * $base must already be set the same way it was for header.php.
 */
$base = $base ?? '';
?>
<footer class="w-full mt-auto bg-surface-container-highest border-t border-outline-variant">
<div class="w-full py-xl px-margin-desktop grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 items-start max-w-max-width mx-auto gap-xl">
<div class="space-y-md">
<div class="text-headline-sm font-headline-sm font-bold text-on-surface">DriveEase</div>
<p class="text-body-sm text-on-surface-variant max-w-xs">Premium car rental solutions for business and leisure. Experience the road like never before with our elite fleet.</p>
</div>
<div class="space-y-md">
<h4 class="text-label-md font-label-md text-on-surface">Quick Links</h4>
<div class="flex flex-col gap-sm">
<a class="text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" href="<?php echo $base; ?>customer/cars.php">Our Fleet</a>
<a class="text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" href="<?php echo $base; ?>howitworks.php">How it Works</a>
<a class="text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" href="<?php echo $base; ?>aboutus.php">About Us</a>
<a class="text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" href="<?php echo $base; ?>contactus.php">Contact</a>
</div>
</div>
<div class="space-y-md">
<h4 class="text-label-md font-label-md text-on-surface">Legal</h4>
<div class="flex flex-col gap-sm">
<a class="text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" href="#">Privacy Policy</a>
<a class="text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" href="#">Terms of Service</a>
<a class="text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" href="#">Cookie Policy</a>
<a class="text-body-sm text-on-surface-variant hover:text-on-surface transition-colors" href="#">Refund Policy</a>
</div>
</div>
<div class="space-y-md">
<h4 class="text-label-md font-label-md text-on-surface">Subscribe</h4>
<p class="text-body-sm text-on-surface-variant">Stay updated with our newest arrivals and offers.</p>
<div class="flex gap-xs">
<input class="bg-surface border border-outline-variant rounded-lg px-md py-2 text-body-sm flex-grow focus:outline-none focus:ring-1 focus:ring-primary" placeholder="Email address" type="email"/>
<button class="bg-primary text-on-primary px-md py-2 rounded-lg font-label-md hover:bg-primary-container transition-colors">Join</button>
</div>
</div>
</div>
<div class="w-full border-t border-outline-variant py-lg px-margin-desktop max-w-max-width mx-auto flex flex-col md:flex-row justify-between items-center gap-md">
<span class="text-label-sm font-label-sm text-on-surface-variant">© 2024 DriveEase Car Rental Systems. All rights reserved.</span>
<div class="flex gap-lg">
<a class="material-symbols-outlined text-on-surface-variant hover:text-primary" href="#">facebook</a>
<a class="material-symbols-outlined text-on-surface-variant hover:text-primary" href="#">language</a>
<a class="material-symbols-outlined text-on-surface-variant hover:text-primary" href="#">public</a>
</div>
</div>
</footer>
</body>
</html>
