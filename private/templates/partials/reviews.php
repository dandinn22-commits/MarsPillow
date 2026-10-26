<?php
/** Shows real, approved reviews. With none yet: placeholders in test mode, nothing in live mode. */
$stats = reviews_stats(); $list = reviews_approved(6);
$headingId = $headingId ?? 'rev-h';
if (!$stats['count'] && is_live()) return;
?>
<section class="band" aria-labelledby="<?= e($headingId) ?>" id="reviews">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow">Reviews</span>
      <h2 id="<?= e($headingId) ?>">From real sleepers.</h2>
      <?php if ($stats['count']): ?>
        <p class="rating-line"><?= stars($stats['avg']) ?> <b><?= e(number_format($stats['avg'], 1)) ?></b> from <?= (int)$stats['count'] ?> verified review<?= $stats['count'] === 1 ? '' : 's' ?></p>
      <?php else: ?>
        <p class="fine">Placeholder: hidden in live mode until the first real, verified review is approved.</p>
      <?php endif; ?>
    </div>
    <?php if ($list): ?>
      <div class="review-grid">
        <?php foreach ($list as $r): ?>
          <article class="review-card">
            <?= stars((float)$r['rating']) ?>
            <h3><?= e($r['title']) ?></h3>
            <p><?= nl2br(e($r['body'])) ?></p>
            <footer><b><?= e($r['name']) ?></b> · Verified buyer<?= $r['sleep_position'] ? ' · ' . e(ucfirst($r['sleep_position'])) . ' sleeper' : '' ?></footer>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="review-slots">
        <div class="slot"><b>Reviews will appear here</b>Only verified customer reviews</div>
        <div class="slot"><b>Reviews will appear here</b>Only verified customer reviews</div>
        <div class="slot"><b>Reviews will appear here</b>Only verified customer reviews</div>
      </div>
    <?php endif; ?>
  </div>
</section>
