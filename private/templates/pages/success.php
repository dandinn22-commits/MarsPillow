<?php /** @var ?array $order */
$paid = $order && in_array($order['status'], ['paid', 'shipped'], true);
$purchase = $paid ? [
    'id' => $order['order_number'], 'value' => $order['total_cents'] / 100, 'currency' => 'USD', 'email' => (string)$order['customer_email'],
    'items' => array_map(fn($i) => ['id' => $i['offer_id'], 'name' => $i['name'], 'price' => $i['unit_price_cents'] / 100, 'qty' => (int)$i['qty']], $order['items']),
] : null;
?>
<div class="band page-pad">
  <div class="wrap narrow center-text">
    <svg class="big-planet" aria-hidden="true" width="96" height="96"><use href="#planet"/></svg>
    <?php if ($paid): ?>
      <h1 class="page-title">Thank you! Your order is confirmed.</h1>
      <p class="lede">Order <strong><?= e($order['order_number']) ?></strong>. We sent a confirmation to <strong><?= e($order['customer_email'] ?: 'your email') ?></strong>.</p>
      <div class="summary order-summary left">
        <ul class="sum-lines">
          <?php foreach ($order['items'] as $i): ?><li><span><?= e($i['name']) ?> × <?= (int)$i['qty'] ?></span><span><?= cents($i['line_total_cents']) ?></span></li><?php endforeach; ?>
        </ul>
        <dl class="totals"><div class="grand"><dt>Total paid</dt><dd><?= cents($order['total_cents']) ?></dd></div></dl>
      </div>
      <h2 class="h-small">What happens next</h2>
      <ol class="steps">
        <li>We pack your order and email you a tracking number.</li>
        <li>It arrives in <?= todo((string)cfg('site.shipping_days')) ?> business days.</li>
        <li>Your <?= (int)cfg('site.trial_nights') ?>-night trial starts the night it arrives.</li>
      </ol>
      <a class="btn btn-primary" href="/faq">Read tips for your first nights</a>
      <?php if ($purchase): ?><script type="application/json" id="purchase-data"><?= json_encode($purchase, JSON_HEX_TAG) ?></script><?php endif; ?>
    <?php elseif ($order && $order['status'] === 'pending'): ?>
      <h1 class="page-title">We're confirming your payment…</h1>
      <p class="lede" id="pending-msg" data-pending="1">This usually takes a few seconds. This page refreshes on its own. Please don't pay again.</p>
      <p class="fine">Order <?= e($order['order_number']) ?>. If this page doesn't update within a minute, <a href="/contact">contact us</a> and include your order number.</p>
    <?php else: ?>
      <h1 class="page-title">Thanks for visiting</h1>
      <p class="lede">We couldn't find a recent order in this browser. If you just paid, check your email for the confirmation.</p>
      <a class="btn btn-primary" href="/">Back to home</a>
    <?php endif; ?>
  </div>
</div>
