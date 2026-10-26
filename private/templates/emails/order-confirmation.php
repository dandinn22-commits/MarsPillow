<?php /** @var array $order */
$first = trim(explode(' ', (string)$order['customer_name'])[0] ?? '');
ob_start(); ?>
<h1 style="margin:0 0 8px;font-family:Georgia,serif;font-weight:normal;font-size:28px;color:#0B132B">Thanks<?= $first ? ', ' . e($first) : '' ?>! Your order is confirmed.</h1>
<p style="margin:0 0 4px;line-height:1.6">Order <b><?= e($order['order_number']) ?></b></p>
<p style="margin:0;line-height:1.6">We're getting it ready. You'll get another email with a tracking number when it ships. Delivery usually takes <?= e(trim(preg_replace('/\[TODO\]?\s*/', '', (string)cfg('site.shipping_days')), '] ')) ?> business days.</p>
<?= render('emails/_items', ['order' => $order]) ?>
<p style="margin:0 0 12px;line-height:1.6"><b>Your <?= (int)cfg('site.trial_nights') ?>-night trial</b> starts the night your pillow arrives. Give your neck a few nights to get used to the new shape. Back sleepers usually like the lower edge, side sleepers the higher one.</p>
<p style="margin:0"><a href="<?= e(url('/faq')) ?>" style="display:inline-block;background:#E8743B;color:#0B132B;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:99px">Read tips for your first nights</a></p>
<?php $body = ob_get_clean(); echo render('emails/_wrap', ['body' => $body, 'preheader' => 'Order ' . $order['order_number'] . ' is confirmed.']);
