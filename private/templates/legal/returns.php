<?php
$c = cfg('company'); $ret = e($c['returns_email']); $mail = e(cfg('site.support_email'));
$n = (int)cfg('site.trial_nights'); $w = (int)cfg('site.warranty_years'); $min = (int)$c['min_nights'];
?>
<div class="band page-pad"><div class="wrap narrow prose">
<?= render('legal/_head', ['title' => 'Returns, Refunds & Warranty']) ?>

<h2>Your <?= $n ?>-night sleep trial</h2>
<p>A new pillow takes a few nights to get used to, so we ask you to sleep on your Mars Pillow for at least <?= $min ?> nights and try a few heights and both sides. If it still isn't right for you within <?= $n ?> nights of delivery, you can return it for a refund of the price you paid for it.</p>
<p>The sleep trial applies to purchases made on <b>marspillows.com</b>. Purchases made on Amazon or in another store follow that store's return policy.</p>

<h2>How to return a pillow</h2>
<ol>
  <li>Email <a href="mailto:<?= $ret ?>"><?= $ret ?></a> with your order number (it starts with MP-) and a sentence about why it didn't work for you.</li>
  <li>We reply within 2 business days with a return authorization number and the address to send the pillow to. Please don't send a return without it.</li>
  <li>Pack the pillow, all its layers and the cover. It does not need to be in its original box or compressed.</li>
  <li>Ship it with a trackable service. <b>Return shipping is paid by you</b>, and the original shipping is free, so nothing is deducted for it.</li>
  <li>When the return arrives and we check that it is complete, we refund your original payment method within <?= (int)$c['refund_days'] ?> business days. Your bank may take a few more days to show it.</li>
</ol>

<h2>Return rules</h2>
<ul>
  <li><b>One trial return per household.</b> The sleep trial is meant to let you try the Mars Pillow once.</li>
  <li><b>Bundles:</b> you can return part of a bundle. Your refund is what you paid for the bundle minus the regular single-item price of the items you keep.</li>
  <li><b>Extra covers</b> can be returned unused, in their original packaging, within 30 days of delivery.</li>
  <li><b>Discounts:</b> if you used a discount code, the refund is the amount you actually paid.</li>
  <li>Returns that are incomplete, heavily soiled or damaged by misuse may be refused or partly refunded. We'll tell you before we decide.</li>
</ul>

<h2>Damaged, defective or wrong item</h2>
<p>If your order arrives damaged, defective or isn't what you ordered, email <a href="mailto:<?= $ret ?>"><?= $ret ?></a> within <?= (int)$c['damage_days'] ?> days of delivery with your order number and photos. We'll send a replacement or refund you in full, and in this case <b>we cover all shipping costs</b>.</p>

<h2 id="warranty"><?= $w ?>-year limited warranty</h2>
<p><?= e($c['legal_name']) ?> warrants to the original purchaser that the Mars Pillow will be free from defects in materials and workmanship under normal home use for <?= $w ?> years from the date of delivery.</p>
<h3>What this warranty covers</h3>
<ul>
  <li>Foam that permanently loses its shape, such as a dent deeper than 1 inch (2.5 cm) that does not recover within 24 hours without weight on it.</li>
  <li>Foam that cracks or crumbles.</li>
  <li>A zipper or seam on the original cover that fails under normal use.</li>
</ul>
<h3>What it does not cover</h3>
<ul>
  <li>Normal softening of the foam over time, or changes in how firm the pillow feels to you.</li>
  <li>Stains, burns, cuts, pet damage, or damage from washing or drying the foam layers.</li>
  <li>Pillows bought used, from an unauthorized seller, or used for commercial purposes.</li>
</ul>
<h3>Who is covered</h3>
<p>The original purchaser who bought from marspillows.com or from the Mars Pillow store on Amazon. Proof of purchase (your order number or Amazon order confirmation) is required. The warranty is not transferable.</p>
<h3>How to make a claim and what we will do</h3>
<p>Email <a href="mailto:<?= $ret ?>"><?= $ret ?></a> with your order number, a short description of the problem and photos. If the claim is approved, we will, at our choice, replace the defective layer or cover, or replace the pillow, and we pay the shipping of the replacement to you. If we ask you to send the defective item back, we provide the shipping label. If the exact item is no longer available, we replace it with one of equal or greater value.</p>
<h3>Legal</h3>
<p>Any implied warranties, including the implied warranties of merchantability and fitness for a particular purpose, are limited to the <?= $w ?>-year duration of this written warranty. We are not liable for incidental or consequential damages. Some states do not allow limits on how long an implied warranty lasts, or the exclusion of incidental or consequential damages, so these limits may not apply to you. This warranty gives you specific legal rights, and you may also have other rights, which vary from state to state.</p>

<h2>Questions</h2>
<p>Returns and warranty: <a href="mailto:<?= $ret ?>"><?= $ret ?></a>. Anything else: <a href="mailto:<?= $mail ?>"><?= $mail ?></a>.</p>
<?= render('legal/_foot') ?>
</div></div>
