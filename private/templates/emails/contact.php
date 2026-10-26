<?php /** @var array $m */ ob_start(); ?>
<h1 style="margin:0 0 12px;font-size:20px;color:#0B132B">New message: <?= e($m['subject']) ?></h1>
<p style="margin:0 0 4px"><b>From:</b> <?= e($m['name']) ?> &lt;<?= e($m['email']) ?>&gt;</p>
<p style="white-space:pre-wrap;line-height:1.6;margin:16px 0 0"><?= e($m['message']) ?></p>
<?php $body = ob_get_clean(); echo render('emails/_wrap', ['body' => $body, 'preheader' => 'Contact form message']);
