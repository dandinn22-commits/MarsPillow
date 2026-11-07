<?php $c = cfg('company'); $mail = e(cfg('site.support_email')); ?>
<div class="band page-pad"><div class="wrap narrow prose">
<?= render('legal/_head', ['title' => 'Shipping Policy']) ?>

<h2>Free shipping in the US</h2>
<p>Every order ships free to addresses in the 48 contiguous United States and Washington, D.C. For Alaska, Hawaii, US territories, PO boxes and APO/FPO addresses, email <a href="mailto:<?= $mail ?>"><?= $mail ?></a> before you order and we'll tell you if we can deliver and when. We don't ship outside the US yet.</p>

<h2>Processing and delivery times</h2>
<ul>
  <li>Orders are packed within <?= e($c['handling_days']) ?> business days.</li>
  <li>Delivery usually takes <?= e($c['delivery_days']) ?> business days after the order ships.</li>
  <li>Orders placed on weekends or US federal holidays are processed the next business day.</li>
</ul>

<h2>Tracking</h2>
<p>When your order ships, we email you a tracking number. We ship with USPS, UPS or FedEx, depending on your address. Your order may be packed and shipped by one of our fulfillment partners in the US.</p>

<h2>If your order is delayed</h2>
<p>If we can't ship your order within the time shown above, we'll email you with a new estimated date before that time passes. You can then choose to wait or cancel the order for a full refund. If the delay is longer than 30 days from your order date and you haven't told us you agree to wait, we'll cancel the order and refund you in full.</p>

<h2>Cancelling or changing an order</h2>
<p>You can cancel or change the shipping address of an order by emailing <a href="mailto:<?= $mail ?>"><?= $mail ?></a> within <?= (int)$c['cancel_hours'] ?> hours of placing it, as long as it hasn't shipped yet. Cancelled orders are refunded in full to the original payment method. Once an order has shipped, it can't be cancelled, but you can return it under our <a href="/returns">sleep trial</a>.</p>

<h2>Compressed packaging</h2>
<p>The Mars Pillow arrives vacuum-compressed. Unpack it, remove the plastic, and give it 24 to 48 hours to return to full shape before you judge its height. New foam can have a light smell for a day or two. Let it air out in a ventilated room.</p>

<h2>Late, lost or damaged packages</h2>
<p>If your order hasn't arrived within the delivery time above, or arrives damaged, email <a href="mailto:<?= $mail ?>"><?= $mail ?></a> with your order number, and photos if it's damaged. We'll work with the carrier and send a replacement or refund you.</p>

<h2>Sales tax</h2>
<p>Sales tax is added where required and shown before you pay.</p>
<?= render('legal/_foot') ?>
</div></div>
