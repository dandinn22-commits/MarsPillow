<?php /** @var array $s  @var array $items */ $code = (string)cfg('popup_code'); ob_start(); ?>
<h1 style="margin:0 0 8px;font-family:Georgia,serif;font-weight:normal;font-size:28px;color:#0B132B">Your cart is still waiting.</h1>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:12px 0">
<?php foreach ($items as $i): ?><tr><td style="padding:8px 0;border-bottom:1px solid #E2DEF2"><?= e($i['name']) ?> × <?= (int)$i['qty'] ?></td><td align="right" style="padding:8px 0;border-bottom:1px solid #E2DEF2"><?= money($i['price'] * $i['qty']) ?></td></tr><?php endforeach; ?>
</table>
<p style="margin:0 0 12px;line-height:1.6">Every order ships free in the US, and you get <?= (int)cfg('site.trial_nights') ?> nights to decide.<?php if ($code): ?> Your code <b><?= e($code) ?></b> is added automatically when you go back.<?php endif; ?></p>
<p style="margin:0 0 16px"><a href="<?= e(url('/cart/restore/' . $s['restore_token'])) ?>" style="display:inline-block;background:#E8743B;color:#0B132B;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:99px">Return to my cart</a></p>
<p style="margin:0;font-size:12px;color:#8F84BF"><a style="color:#8F84BF" href="<?= e(url('/unsubscribe/' . $s['restore_token'])) ?>">Unsubscribe</a></p>
<?php $body = ob_get_clean(); echo render('emails/_wrap', ['body' => $body, 'preheader' => 'Your Mars Pillow is still in your cart.']);
