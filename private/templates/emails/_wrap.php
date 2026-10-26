<?php /** @var string $body  @var string $preheader */ ?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>Mars Pillow</title></head>
<body style="margin:0;background:#0B132B;font-family:Helvetica,Arial,sans-serif;color:#1C2541">
<span style="display:none;max-height:0;overflow:hidden"><?= e($preheader ?? '') ?></span>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#0B132B;padding:28px 12px"><tr><td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px">
<tr><td style="padding:8px 4px 20px;font-family:Georgia,serif;font-size:28px"><span style="color:#E8743B;font-style:italic">mars</span> <b style="color:#F5F3FF;font-family:Helvetica,Arial,sans-serif;font-size:21px">pillow.</b></td></tr>
<tr><td style="background:#F5F3FF;border-radius:16px;padding:28px">
<?= $body ?>
</td></tr>
<tr><td style="padding:18px 4px;color:#A9A3C9;font-size:12px;line-height:1.5">
<?= e(cfg('site.business_name')) ?> · <?= e(preg_replace('/\[TODO[^\]]*\]/', '', (string)cfg('site.business_address'))) ?><br>
Questions? Reply to this email or write to <a style="color:#F2A07A" href="mailto:<?= e(cfg('site.support_email')) ?>"><?= e(cfg('site.support_email')) ?></a>.
</td></tr></table></td></tr></table></body></html>
