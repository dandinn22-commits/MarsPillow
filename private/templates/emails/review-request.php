<?php /** @var array $order */ $first = trim(explode(' ', (string)$order['customer_name'])[0] ?? ''); ob_start(); ?>
<h1 style="margin:0 0 8px;font-family:Georgia,serif;font-weight:normal;font-size:28px;color:#0B132B">How are you sleeping<?= $first ? ', ' . e($first) : '' ?>?</h1>
<p style="margin:0 0 12px;line-height:1.6">You've had your Mars Pillow for a few weeks now. Would you tell other sleepers how it's going? It takes about a minute, and honest reviews, good or bad, are what help people decide.</p>
<p style="margin:0 0 16px"><a href="<?= e(url('/review/' . $order['review_token'])) ?>" style="display:inline-block;background:#E8743B;color:#0B132B;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:99px">Write my review</a></p>
<p style="margin:0;line-height:1.6;font-size:14px;color:#5B4B8A">Something not right? Just reply to this email. You're still inside your <?= (int)cfg('site.trial_nights') ?>-night trial window if it has been less than <?= (int)cfg('site.trial_nights') ?> nights.</p>
<?php $body = ob_get_clean(); echo render('emails/_wrap', ['body' => $body, 'preheader' => 'One minute to help other sleepers.']);
