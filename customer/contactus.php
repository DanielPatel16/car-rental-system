<?php
session_start();
include "../includes/header.php";
?>
<!-- Main Content -->
<main class="flex-grow pt-[120px] pb-2xl px-margin-mobile md:px-margin-desktop w-full max-w-max-width mx-auto">
<!-- Page Header -->
<div class="mb-xl md:mb-2xl text-center md:text-left">
<h1 class="text-headline-lg font-headline-lg text-on-surface mb-sm">Get in Touch</h1>
<p class="text-body-lg font-body-lg text-on-surface-variant max-w-2xl">
                Whether you need to upgrade your fleet, require support, or have a general inquiry, our team is ready to assist you.
            </p>
</div>
<!-- Two Column Layout -->
<div class="grid grid-cols-1 md:grid-cols-12 gap-xl">
<!-- Left Column: Contact Form -->
<div class="md:col-span-7">
<div class="bg-surface-container-lowest shadow-sm border border-outline-variant rounded-lg p-lg">
<form action="#" class="space-y-md" method="POST">
<div>
<label class="block text-label-sm font-label-sm text-on-surface-variant mb-xs" for="name">Full Name</label>
<input class="w-full bg-surface-container-lowest border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary text-body-md font-body-md px-4 py-3 outline-none transition-shadow" id="name" name="name" placeholder="Jane Doe" type="text"/>
</div>
<div>
<label class="block text-label-sm font-label-sm text-on-surface-variant mb-xs" for="email">Professional Email</label>
<input class="w-full bg-surface-container-lowest border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary text-body-md font-body-md px-4 py-3 outline-none transition-shadow" id="email" name="email" placeholder="jane.doe@company.com" type="email"/>
</div>
<div>
<label class="block text-label-sm font-label-sm text-on-surface-variant mb-xs" for="message">Message Details</label>
<textarea class="w-full bg-surface-container-lowest border border-outline-variant rounded focus:border-primary focus:ring-1 focus:ring-primary text-body-md font-body-md px-4 py-3 outline-none transition-shadow resize-y" id="message" name="message" placeholder="How can we help optimize your fleet operations?" rows="5"></textarea>
</div>
<div class="pt-sm">
<button class="w-full md:w-auto bg-primary text-on-primary font-label-md text-label-md rounded-lg py-3 px-xl hover:bg-primary-container transition-colors shadow-sm" type="submit">
                                Send Message
                            </button>
</div>
</form>
</div>
</div>
<!-- Right Column: Office Info & Map -->
<div class="md:col-span-5 flex flex-col gap-lg">
<!-- Office Information Card -->
<div class="bg-surface-container-low border border-outline-variant rounded-lg p-lg shadow-sm">
<h2 class="text-headline-sm font-headline-sm text-on-surface mb-lg border-b border-outline-variant pb-md">Office Information</h2>
<ul class="space-y-md">
<li class="flex items-start gap-md text-body-md font-body-md text-on-surface-variant">
<span class="material-symbols-outlined text-primary mt-xs" data-icon="location_on" data-weight="fill" style="font-variation-settings: 'FILL' 1;">location_on</span>
<div>
<strong class="block text-on-surface font-semibold mb-1">Corporate Headquarters</strong>
                                450 Serra Mall<br/>
                                San Francisco, CA 94105
                            </div>
</li>
<li class="flex items-center gap-md text-body-md font-body-md text-on-surface-variant">
<span class="material-symbols-outlined text-primary" data-icon="call" data-weight="fill" style="font-variation-settings: 'FILL' 1;">call</span>
<a class="hover:text-primary transition-colors" href="tel:+15550192834">+1 (555) 019-2834</a>
</li>
<li class="flex items-center gap-md text-body-md font-body-md text-on-surface-variant">
<span class="material-symbols-outlined text-primary" data-icon="mail" data-weight="fill" style="font-variation-settings: 'FILL' 1;">mail</span>
<a class="hover:text-primary transition-colors" href="mailto:support@driveease.com">support@driveease.com</a>
</li>
</ul>
</div>
<!-- Map Placeholder -->
<div class="w-full h-64 rounded-lg overflow-hidden border border-outline-variant shadow-sm bg-surface-container relative group">
<img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" data-alt="A sleek, modern digital map interface displaying the San Francisco Bay Area. The map uses a sophisticated corporate color palette with DriveEase blue accents indicating key fleet locations. Clean lines, minimalist labels, and high-contrast roads against a light gray background, viewed from a crisp top-down perspective." data-location="San Francisco" src="https://lh3.googleusercontent.com/aida-public/AB6AXuA9uqeei8m05fMGT88ZFGqJ6-WXHlRgN4MhrDW4Yo1aoJ3ER27dqmQq6tRQFz9SzH1-oBvWVtOPmdSD6qAKSkPWrSFqWsWJHp5RSGT1GZW_wJe6ByTDb88mwrzo_q1CnZivC1RBjkho6qvSImaSRLGeS8sHC2xxhEiu4i7Q4xRtZUKgFdzX8PA3SbnPBEoKQvELceRdSLCC3wWDGtbTb__ReNTjEcuupCUd2BbuKUk1dFyxrwMEQe2LIrZv1nwZnb_8_3vQGPwjSGk"/>
<div class="absolute inset-0 ring-1 ring-inset ring-black/10 rounded-lg pointer-events-none"></div>
</div>
</div>
</div>
</main>
<!-- Footer -->
<?php include "../includes/footer.php"; ?>
