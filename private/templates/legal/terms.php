<?php $biz = e(cfg('site.business_name')); $mail = e(cfg('site.support_email')); $n = (int)cfg('site.trial_nights'); $w = (int)cfg('site.warranty_years'); ?>
<div class="band page-pad"><div class="wrap narrow prose">
<?= render('legal/_head', ['title' => 'Terms of Service']) ?>
<p>These Terms of Service apply when you use this website or buy from <?= $biz ?> ("we", "us"). By placing an order you agree to these terms.</p>
<h2>Eligibility and orders</h2>
<p>You must be at least 18 years old, or have a parent's permission, to buy. We ship only to addresses in the United States. An order is accepted when we send a confirmation email. We may cancel an order and refund you in full if a product is unavailable, a price is listed in error, or we suspect fraud.</p>
<h2>Prices and payment</h2>
<p>Prices are in US dollars. Shipping within the US is free. Sales tax is added where required and shown before you pay. Payments are processed by Stripe or PayPal.</p>
<h2>Sleep trial, returns and warranty</h2>
<p>Your purchase includes a <?= $n ?>-night sleep trial and a <?= $w ?>-year limited warranty, described in our <a href="/returns">Returns &amp; Refunds</a> page, which is part of these terms.</p>
<h2>Health disclaimer</h2>
<p>Our products are designed for comfort and support. They are not medical devices and are not intended to diagnose, treat, cure or prevent any disease. If you have neck, back or sleep problems, talk to your doctor.</p>
<h2>Website content</h2>
<p>All text, images, logos and designs on this site belong to us or our licensors. You may not copy or reuse them without permission.</p>
<h2>Limitation of liability</h2>
<p>To the fullest extent allowed by law, our total liability for any claim related to a product is limited to the amount you paid for it. We are not liable for indirect or consequential damages. Some states do not allow these limits, so they may not apply to you.</p>
<h2>Governing law for these terms</h2>
<p>These terms are governed by the laws of the State of <?= todo('[TODO state where your LLC is registered]') ?>, without regard to conflict-of-law rules. <?= todo('[TODO dispute resolution / arbitration clause, if your attorney recommends one]') ?></p>
<h2>Contact</h2>
<p><?= $biz ?>, <?= todo((string)cfg('site.business_address')) ?>. Email: <a href="mailto:<?= $mail ?>"><?= $mail ?></a></p>
<?= render('legal/_foot') ?>
</div></div>
