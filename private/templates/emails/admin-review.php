<?php /** @var array $d */ ob_start(); ?>
<h1 style="margin:0 0 8px;font-size:20px;color:#0B132B">New review: <?= (int)$d['rating'] ?> stars</h1>
<p style="margin:0 0 6px"><b><?= e($d['title']) ?></b> — <?= e($d['name']) ?></p>
<p style="white-space:pre-wrap;line-height:1.6"><?= e($d['body']) ?></p>
<p><a href="<?= e(url('/admin#reviews')) ?>">Approve or hide it in admin</a></p>
<?php $body = ob_get_clean(); echo render('emails/_wrap', ['body' => $body, 'preheader' => 'A review is waiting for approval.']);
