<?php /** @var ?array $sub */ ?>
<div class="band page-pad"><div class="wrap narrow center-text">
  <h1 class="page-title">Unsubscribe</h1>
  <?php if ($sub && !$sub['unsubscribed']): ?>
    <p class="lede">Stop all marketing emails to <b><?= e($sub['email']) ?></b>? Order emails will still arrive.</p>
    <form method="post"><?= csrf_field() ?><button class="btn btn-primary" type="submit">Unsubscribe</button></form>
  <?php else: ?>
    <p class="lede">This email is already unsubscribed, or the link has expired.</p>
  <?php endif; ?>
</div></div>
