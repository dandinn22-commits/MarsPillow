<?php $mail = e(cfg('site.support_email')); $n = (int)cfg('site.trial_nights'); $w = (int)cfg('site.warranty_years'); ?>
<div class="band page-pad"><div class="wrap narrow prose">
<?= render('legal/_head', ['title' => 'Returns & Refunds']) ?>
<h2>Your <?= $n ?>-night sleep trial</h2>
<p>A new pillow takes a few nights to get used to. Sleep on your Mars Pillow for at least <?= todo('[TODO e.g. 14]') ?> nights. If it isn't right for you within <?= $n ?> nights of delivery, you can return it for a refund.</p>
<h2>How to start a return</h2>
<ol>
  <li>Email <a href="mailto:<?= $mail ?>"><?= $mail ?></a> with your order number (it starts with MP-).</li>
  <li>We reply with return instructions <?= todo('[TODO: prepaid label / donation option / customer pays return shipping]') ?>.</li>
  <li>Once we receive the return <?= todo('[TODO or confirm donation]') ?>, we refund your original payment method within <?= todo('[TODO 5–10]') ?> business days.</li>
</ol>
<h2>What's covered</h2>
<ul>
  <li>Limit: <?= todo('[TODO e.g. one trial return per household]') ?>.</li>
  <li>Extra covers can be returned unused and in original packaging within <?= todo('[TODO 30]') ?> days.</li>
  <li>Bundles: you can return part of a bundle; the refund is the bundle price minus the price of the items you keep. <?= todo('[TODO confirm]') ?></li>
  <li>Items bought from Amazon or another store follow that store's return policy.</li>
</ul>
<h2><?= $w ?>-year limited warranty</h2>
<p>The Mars Pillow is covered for <?= $w ?> years from delivery against defects in materials and workmanship, such as foam that permanently loses its shape <?= todo('[TODO define, e.g. a dent deeper than 1 inch]') ?> or a broken zipper. The warranty doesn't cover normal wear, stains, misuse or damage from washing the foam core. To make a claim, email us with your order number and photos. We'll repair or replace the item.</p>
<h2>Damaged or wrong item</h2>
<p>If something arrives damaged or isn't what you ordered, email us within <?= todo('[TODO 7]') ?> days of delivery and we'll make it right at no cost to you.</p>
<?= render('legal/_foot') ?>
</div></div>
