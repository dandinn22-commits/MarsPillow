<?php /** @var array $order */ ?>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;margin:16px 0">
<?php foreach ($order['items'] as $i): ?>
<tr><td style="padding:10px 0;border-bottom:1px solid #E2DEF2"><?= e($i['name']) ?> × <?= (int)$i['qty'] ?></td><td align="right" style="padding:10px 0;border-bottom:1px solid #E2DEF2"><?= cents($i['line_total_cents']) ?></td></tr>
<?php endforeach; ?>
<tr><td style="padding:10px 0">Shipping</td><td align="right">Free</td></tr>
<tr><td style="padding:10px 0;font-weight:bold">Total</td><td align="right" style="font-weight:bold"><?= cents($order['total_cents']) ?></td></tr>
</table>
<?php if ($order['ship_line1']): ?>
<p style="margin:0 0 4px;font-size:13px;color:#5B4B8A;text-transform:uppercase;letter-spacing:1px">Shipping to</p>
<p style="margin:0 0 16px;line-height:1.5"><?= e($order['customer_name']) ?><br><?= e($order['ship_line1']) ?><?= $order['ship_line2'] ? '<br>' . e($order['ship_line2']) : '' ?><br><?= e($order['ship_city']) ?>, <?= e($order['ship_state']) ?> <?= e($order['ship_postal']) ?></p>
<?php endif; ?>
