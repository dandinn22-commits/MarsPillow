<?php /** @var array $order  @var array $errors  @var array $old  @var bool $done */
$err = fn($k) => isset($errors[$k]) ? '<p class="field-error" id="err-' . $k . '">' . e($errors[$k]) . '</p>' : '';
$inv = fn($k) => isset($errors[$k]) ? ' aria-invalid="true" aria-describedby="err-' . $k . '"' : '';
?>
<div class="band page-pad"><div class="wrap narrow">
  <?php if ($done): ?>
    <div class="center-text">
      <svg class="big-planet" aria-hidden="true" width="96" height="96"><use href="#planet"/></svg>
      <h1 class="page-title">Thank you for your review!</h1>
      <p class="lede">We read every review. Once it's checked, it will appear on our site.</p>
      <a class="btn btn-primary" href="/">Back to home</a>
    </div>
  <?php else: ?>
  <form class="form-card" method="post" novalidate>
    <span class="eyebrow">Order <?= e($order['order_number']) ?></span>
    <h1 class="page-title">How is your Mars Pillow?</h1>
    <p class="fine">Honest reviews help other sleepers decide. Good or bad, we publish real experiences.</p>
    <?= csrf_field() ?>
    <?php if ($errors): ?><p class="flash flash-error" role="alert">Please fix the highlighted fields.</p><?php endif; ?>
    <fieldset class="field rating-field"<?= $inv('rating') ?>>
      <legend>Your rating</legend>
      <div class="rating-input">
        <?php for ($i = 5; $i >= 1; $i--): ?><input type="radio" id="r<?= $i ?>" name="rating" value="<?= $i ?>"<?= (int)($old['rating'] ?? 0) === $i ? ' checked' : '' ?>><label for="r<?= $i ?>" title="<?= $i ?> stars"><span class="sr-only"><?= $i ?> star<?= $i > 1 ? 's' : '' ?></span>★</label><?php endfor; ?>
      </div>
      <?= $err('rating') ?>
    </fieldset>
    <div class="field"><label for="rv-title">Title</label><input id="rv-title" name="title" maxlength="120" value="<?= e($old['title'] ?? '') ?>"<?= $inv('title') ?>><?= $err('title') ?></div>
    <div class="field"><label for="rv-body">Your review</label><textarea id="rv-body" name="body" rows="6" maxlength="3000"<?= $inv('body') ?>><?= e($old['body'] ?? '') ?></textarea><?= $err('body') ?></div>
    <div class="field"><label for="rv-pos">How do you sleep? <span class="opt">(optional)</span></label>
      <select id="rv-pos" name="sleep_position"><option value="">Choose</option><option value="back">On my back</option><option value="side">On my side</option><option value="both">Both</option></select></div>
    <div class="field"><label for="rv-name">Name to show</label><input id="rv-name" name="name" maxlength="80" placeholder="Jane D." value="<?= e($old['name'] ?? '') ?>"<?= $inv('name') ?>><?= $err('name') ?></div>
    <button class="btn btn-primary" type="submit">Send my review</button>
  </form>
  <?php endif; ?>
</div></div>
