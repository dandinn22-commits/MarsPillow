<?php
$c = cfg('company'); $biz = e($c['legal_name']); $mail = e(cfg('site.support_email'));
$n = (int)cfg('site.trial_nights'); $w = (int)cfg('site.warranty_years');
?>
<div class="band page-pad"><div class="wrap narrow prose">
<?= render('legal/_head', ['title' => 'Terms of Service']) ?>
<p>These Terms of Service apply when you use marspillows.com or buy from <?= $biz ?>, a <?= e($c['state']) ?> limited liability company ("Mars Pillow", "we", "us"). By placing an order you agree to these terms. Please read the section on dispute resolution carefully: it affects how disputes are resolved.</p>

<h2>Eligibility and orders</h2>
<p>You must be at least 18 years old, or have a parent's or guardian's permission, to buy. We ship only to addresses in the United States. An order is accepted when we send a confirmation email. We may cancel an order and refund you in full if a product is unavailable, a price or description is listed in error, or we suspect fraud.</p>

<h2>Prices and payment</h2>
<p>Prices are in US dollars. Shipping within the contiguous US is free. Sales tax is added where required and shown before you pay. Payments are processed by Stripe or PayPal. Crossed-out "compare at" prices on bundles show the total of the same items bought separately at their regular prices.</p>

<h2>Discount codes and promotions</h2>
<p>Discount codes can be used once per customer, one code per order, and can't be combined with other offers unless we say so. They have no cash value and can't be applied to past orders. We may end a promotion at any time, but an order already placed keeps its discount.</p>

<h2>Product information</h2>
<p>We try to describe our products and show their colors as accurately as possible. Small differences in color, measurements (within about ½ inch) and firmness can occur between production batches and screens. The Mars Pillow is imported.</p>

<h2>Sleep trial, returns, shipping and warranty</h2>
<p>Your purchase includes a <?= $n ?>-night sleep trial and a <?= $w ?>-year limited warranty, described on our <a href="/returns">Returns, Refunds &amp; Warranty</a> page. Delivery, delays and cancellations are described in our <a href="/shipping-policy">Shipping Policy</a>. Both pages are part of these terms.</p>

<h2>Health disclaimer</h2>
<p>Our products are designed for comfort and support. They are not medical devices and are not intended to diagnose, treat, cure or prevent any disease or condition. If you have neck, back or sleep problems, talk to your doctor.</p>

<h2>Reviews and content you send us</h2>
<p>If you send us a review, photo or message, you confirm it is your own honest experience and give us a non-exclusive, royalty-free license to show it on our website and marketing, with the name you enter in the review form. We don't pay for reviews and we don't edit their meaning. We may decline to publish content that is offensive, off-topic or not about a verified purchase.</p>

<h2>Website content</h2>
<p>All text, images, logos and designs on this site belong to us or our licensors. Mars Pillow is a registered trademark of <?= $biz ?>. You may not copy or reuse our content without permission.</p>

<h2>Limitation of liability</h2>
<p>To the fullest extent allowed by law, our total liability for any claim related to a product is limited to the amount you paid for it. We are not liable for indirect, incidental or consequential damages. Some states do not allow these limits, so they may not apply to you. Nothing in these terms limits rights you have under consumer protection laws that can't be waived.</p>

<h2 id="disputes">Dispute resolution</h2>
<p><b>Talk to us first.</b> Most problems can be solved by email. Before starting any claim, you agree to email <a href="mailto:<?= $mail ?>"><?= $mail ?></a> with a description of the problem and give us 30 days to try to resolve it.</p>
<p><b>Arbitration.</b> If we can't resolve it, you and we agree that any dispute about these terms or our products will be settled by binding individual arbitration administered by the American Arbitration Association under its Consumer Arbitration Rules, instead of in court. The arbitration may take place by video or phone, or in the county where you live. Either of us may instead bring an individual claim in small claims court.</p>
<p><b>No class actions.</b> Claims may be brought only on an individual basis, not as a plaintiff or class member in any class or representative proceeding.</p>
<p><b>Opting out.</b> You can opt out of this arbitration agreement within 30 days of your first purchase by emailing <a href="mailto:<?= $mail ?>"><?= $mail ?></a> with your name, order number and the words "arbitration opt-out".</p>

<h2>Governing law</h2>
<p>These terms are governed by the laws of the State of <?= e($c['state']) ?> and applicable US federal law, without regard to conflict-of-law rules.</p>

<h2>Changes to these terms</h2>
<p>We may update these terms. The version in effect when you place an order applies to that order.</p>

<h2>Contact</h2>
<p><?= $biz ?>, <?= e($c['address']) ?>. Email: <a href="mailto:<?= $mail ?>"><?= $mail ?></a></p>
<?= render('legal/_foot') ?>
</div></div>
