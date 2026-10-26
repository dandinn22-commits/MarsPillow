<?php /** @var array $order */ ob_start(); ?>
<h1 style="margin:0 0 8px;font-size:22px;color:#0B132B">New order <?= e($order['order_number']) ?> — <?= cents($order['total_cents']) ?></h1>
<p style="margin:0;line-height:1.6">Paid with <?= e(ucfirst($order['payment_method'])) ?> · <?= e($order['customer_email']) ?></p>
<?= render('emails/_items', ['order' => $order]) ?>
<p><a href="<?= e(url('/admin/order/' . $order['id'])) ?>">Open in admin</a></p>
<?php $body = ob_get_clean(); echo render('emails/_wrap', ['body' => $body, 'preheader' => 'New order']);
