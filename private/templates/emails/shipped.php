<?php /** @var array $order */ ob_start(); ?>
<h1 style="margin:0 0 8px;font-family:Georgia,serif;font-weight:normal;font-size:28px;color:#0B132B">Your Mars Pillow is on its way.</h1>
<p style="margin:0 0 12px;line-height:1.6">Order <b><?= e($order['order_number']) ?></b> has shipped.</p>
<?php if ($order['tracking_number']): ?><p style="margin:0 0 12px;line-height:1.6">Tracking number: <b><?= e($order['tracking_number']) ?></b></p><?php endif; ?>
<p style="margin:0 0 12px;line-height:1.6">When it arrives, unpack it and give it a few hours to return to full shape. Your <?= (int)cfg('site.trial_nights') ?>-night trial starts the first night you sleep on it.</p>
<?php $body = ob_get_clean(); echo render('emails/_wrap', ['body' => $body, 'preheader' => 'Your order has shipped.']);
