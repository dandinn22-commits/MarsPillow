<?php /** @var string $title */ ?>
<span class="eyebrow">Legal</span>
<h1 class="page-title"><?= e($title) ?></h1>
<p class="fine">Last updated: <?= todo('[TODO date]') ?></p>
<?php if (!is_live()): ?><p class="notice">Template only. This is not legal advice. Have a US attorney review it and fill in every TODO before going live.</p><?php endif; ?>
