<?php /** @var string $code  @var array $s  @var ?string $quiz */ ob_start();
$res = $quiz ? quiz_result(array_combine(['pos', 'build', 'who'], array_pad(explode('-', $quiz), 3, ''))) : null; ?>
<h1 style="margin:0 0 8px;font-family:Georgia,serif;font-weight:normal;font-size:28px;color:#0B132B">Here's your code: <?= e($code) ?></h1>
<p style="margin:0 0 12px;line-height:1.6"><?= e(coupon_find($code)['label'] ?? '') ?>. Enter it in your cart, or use the button below and we'll add it for you.</p>
<?php if ($res): ?><p style="margin:0 0 12px;line-height:1.6"><b>Your pillow finder result:</b> set it to <?= e($res['height']['in']) ?> in, <?= e($res['feel']) ?> side up. <?= e($res['why']) ?></p><?php endif; ?>
<p style="margin:0 0 16px"><a href="<?= e(url('/product/mars-pillow') . '?code=' . rawurlencode($code)) ?>" style="display:inline-block;background:#E8743B;color:#0B132B;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:99px">Shop Mars Pillow</a></p>
<p style="margin:0;font-size:12px;color:#8F84BF"><a style="color:#8F84BF" href="<?= e(url('/unsubscribe/' . $s['restore_token'])) ?>">Unsubscribe</a></p>
<?php $body = ob_get_clean(); echo render('emails/_wrap', ['body' => $body, 'preheader' => 'Your discount code is inside.']);
