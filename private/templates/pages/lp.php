<?php /** @var array $selected  Fast landing page for ad traffic: no 3D, one goal (add to cart). */
$nights = (int)cfg('site.trial_nights'); $years = (int)cfg('site.warranty_years');
$video = is_file(MP_PUBLIC . '/assets/video/lp.mp4');
$stats = reviews_stats();
?>
<header class="band lp-hero">
  <div class="wrap lp-grid">
    <div class="lp-copy">
      <span class="eyebrow">For side, back and stomach sleepers</span>
      <h1 class="lp-title">One height doesn't fit every neck. <em>This pillow adjusts.</em></h1>
      <?php if ($stats['count']): ?><p class="rating-line"><?= stars($stats['avg']) ?> <b><?= e(number_format($stats['avg'], 1)) ?></b> from <?= (int)$stats['count'] ?> verified review<?= $stats['count'] === 1 ? '' : 's' ?></p><?php endif; ?>
      <p class="lede">The Mars Pillow is an adjustable memory foam pillow. Three removable layers give you four heights, and a soft side and a firm side let you choose the feel. Set it once to fit your shoulders and the way you sleep.</p>
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
        <img src="/assets/img/pillow-render.webp" alt="The Mars Pillow, an adjustable memory foam pillow with three layers" width="1200" height="800" fetchpriority="high">
      <?php endif; ?>
    </div>
  </div>
</header>

<section class="band" aria-labelledby="lp-why">
  <div class="wrap">
    <div class="section-head"><h2 id="lp-why">Why sleepers switch to an adjustable pillow</h2></div>
    <div class="benefits">
      <div class="benefit"><h3>1. Your height, not ours</h3><p>A one-size pillow is too low or too high for many people. Add or remove a layer until your neck is straight.</p></div>
      <div class="benefit"><h3>2. Soft or firm</h3><p>One side gently sinks, the other holds you up. Flip the pillow to switch.</p></div>
      <div class="benefit"><h3>3. Memory foam that recovers</h3><p>It softens under your head and slowly returns to shape every morning.</p></div>
      <div class="benefit"><h3>4. Square 90° edge</h3><p>It sits close to your shoulder and fills the gap under your neck.</p></div>
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
    <p class="fine" style="margin-top:20px">Not sure which height is for you? <a href="/find-your-fit">Take the 30-second pillow finder</a>.</p>
  </div>
</section>
