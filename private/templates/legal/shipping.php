<?php $mail = e(cfg('site.support_email')); ?>
<div class="band page-pad"><div class="wrap narrow prose">
<?= render('legal/_head', ['title' => 'Shipping Policy']) ?>
<h2>Free shipping in the US</h2>
<p>Every order ships free to addresses in the 50 United States <?= todo('[TODO confirm: Alaska, Hawaii, PO boxes, APO/FPO]') ?>. We don't ship outside the US yet.</p>
<h2>Processing and delivery times</h2>
<ul>
  <li>Orders are packed within <?= todo('[TODO 1–2]') ?> business days.</li>
  <li>Delivery takes <?= todo((string)cfg('site.shipping_days')) ?> business days after the order ships.</li>
  <li>Orders placed on weekends or US holidays are processed the next business day.</li>
</ul>
<h2>Tracking</h2>
<p>When your order ships, we email you a tracking number. Carrier: <?= todo('[TODO USPS / UPS / FedEx]') ?>.</p>
<h2>Compressed packaging</h2>
<p>The Mars Pillow may arrive compressed. Unpack it and give it up to <?= todo('[TODO 24–48]') ?> hours to return to full shape.</p>
<h2>Late, lost or damaged packages</h2>
<p>If your order hasn't arrived within the time above, or arrives damaged, email <a href="mailto:<?= $mail ?>"><?= $mail ?></a> with your order number and photos if it's damaged. We'll work with the carrier and send a replacement or refund.</p>
<h2>Wrong address</h2>
<p>Check your shipping address at checkout. If you spot a mistake, email us right away. We can change it only before the order ships.</p>
<?= render('legal/_foot') ?>
</div></div>
