<?php /** @var array $selected  Fast landing page for ad traffic: no 3D, one goal (add to cart). */
$nights = (int)cfg('site.trial_nights'); $years = (int)cfg('site.warranty_years');
$video = is_file(MP_PUBLIC . '/assets/video/lp.mp4');
$stats = reviews_stats();
?>
<header class="band lp-hero">
  <div class="wrap lp-grid">
    <div class="lp-copy">
      <span class="eyebrow">For side and back sleepers</span>
      <h1 class="lp-title">Your pillow leaves a gap under your neck. <em>This one fills it.</em></h1>
      <?php if ($stats['count']): ?><p class="rating-line"><?= stars($stats['avg']) ?> <b><?= e(number_format($stats['avg'], 1)) ?></b> from <?= (int)$stats['count'] ?> verified review<?= $stats['count'] === 1 ? '' : 's' ?></p><?php endif; ?>
      <p class="lede">The Mars Pillow is a contoured memory foam pillow with two heights: a lower edge for back sleepers and a higher edge that fills the space between your shoulder and your head.</p>
      <ul class="lp-points">
        <li><b><?= $nights ?> nights</b> to decide, or send it back</li>
        <li><b><?= $years ?>-year</b> warranty</li>
        <li><b>Free</b> US shipping</li>
      </ul>
      <a class="btn btn-primary btn-lg" href="#lp-buy">Start your <?= $nights ?>-night trial</a>
    </div>
    <div class="lp-media">
      <?php if ($video): ?>
        <video src="/assets/video/lp.mp4" autoplay muted loop playsinline preload="metadata" poster="/assets/img/pillow-render.webp" aria-label="Video of a hand pressing the Mars Pillow"></video>
      <?php else: ?>
        <img src="/assets/img/pillow-render.webp" alt="The Mars Pillow, a contoured memory foam pillow with a higher and a lower edge" width="1200" height="800" fetchpriority="high">
      <?php endif; ?>
    </div>
  </div>
</header>

<section class="band" aria-labelledby="lp-why">
  <div class="wrap">
    <div class="section-head"><h2 id="lp-why">Why sleepers switch to a contour pillow</h2></div>
    <div class="benefits">
      <div class="benefit"><h3>1. No more neck gap</h3><p>A flat pillow lets your head sink and your neck bend. The neck roll fills that space.</p></div>
      <div class="benefit"><h3>2. Two heights</h3><p>Lower edge for back sleeping, higher edge for side sleeping. Turn it to switch.</p></div>
      <div class="benefit"><h3>3. Memory foam that recovers</h3><p>It softens under your head and slowly returns to shape every morning.</p></div>
      <div class="benefit"><h3>4. Washable cover</h3><p>Zip it off and machine wash it. The foam stays fresh inside.</p></div>
    </div>
  </div>
</section>

<?= render('partials/compare-brand') ?>
<?= render('partials/reviews', ['headingId' => 'lp-rev']) ?>

<section class="band bundles-bg" id="lp-buy" aria-labelledby="lp-buy-h">
  <div class="wrap">
    <div class="section-head"><h2 id="lp-buy-h">Choose your bundle</h2><p class="lede">Every bundle ships free and includes the <?= $nights ?>-night trial.</p></div>
    <?= render('partials/bundle-picker', ['selected' => $selected, 'context' => 'home']) ?>
  </div>
</section>

<?= render('partials/trust-strip') ?>

<section class="band faq" aria-labelledby="lp-faq">
  <div class="wrap narrow">
    <h2 id="lp-faq">Before you order</h2>
    <?php foreach (array_slice(faqs(), 0, 6) as $f): ?><details><summary><?= e($f['q']) ?></summary><p><?= todo($f['a']) ?></p></details><?php endforeach; ?>
    <p class="fine" style="margin-top:20px">Not sure which edge is for you? <a href="/find-your-fit">Take the 30-second pillow finder</a>.</p>
  </div>
</section>
