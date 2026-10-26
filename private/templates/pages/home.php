<?php
$nights = (int)cfg('site.trial_nights'); $years = (int)cfg('site.warranty_years');
$layers = cfg('product.layers');
$faqPreview = array_slice(faqs(), 0, 5);
?>
<header class="hero band" id="top">
  <div class="hero-title">
    <h1 class="eyebrow scramble">Orthopedic memory foam pillow</h1>
    <p class="hero-display" aria-label="Sleep out of this world.">
      <span class="ln l1" aria-hidden="true"><span class="w">Sleep</span> <span class="w">out</span> <span class="w">of</span></span>
      <span class="ln l2" aria-hidden="true"><span class="w">this</span> <em class="w">world.</em></span>
    </p>
  </div>
  <canvas id="stage" aria-hidden="true"></canvas>
  <div class="hero-fallback" id="heroFallback" hidden></div>
  <div class="press-hint" id="pressHint" aria-hidden="true"><i></i>Press it. It's memory foam.</div>
  <div class="hero-coords tl" aria-hidden="true"><span>Mission <b>MP-01</b></span><span>Neck support system</span></div>
  <div class="hero-coords tr" aria-hidden="true"><span>Trial <b><?= $nights ?> nights</b></span><span>Warranty <b><?= $years ?> years</b></span></div>
  <div class="hero-foot">
    <p class="lede">The Mars Pillow is an orthopedic memory foam pillow with a contoured shape, designed to support your neck and help keep your spine aligned, whether you sleep on your back or your side.</p>
    <div class="hero-actions">
      <a class="btn btn-primary magnetic" href="#bundles">Start your <?= $nights ?>-night trial
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </a>
      <a class="btn btn-ghost" href="#inside">See what's inside</a>
    </div>
    <div class="hero-meta">
      <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 7h11v9H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="17.5" r="1.6"/><circle cx="17" cy="17.5" r="1.6"/></svg>Free US shipping</span>
      <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M20 14.5A8 8 0 1 1 9.5 4a6.5 6.5 0 0 0 10.5 10.5Z"/></svg><?= $nights ?>-night trial</span>
      <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"/></svg><?= $years ?>-year warranty</span>
    </div>
  </div>
</header>

<div class="marquee band" aria-hidden="true">
  <div class="track" id="marquee">
    <span>Support <em>·</em> Align <em>·</em> Recover <em>·</em> Repeat <em>·</em></span>
    <span>Support <em>·</em> Align <em>·</em> Recover <em>·</em> Repeat <em>·</em></span>
    <span>Support <em>·</em> Align <em>·</em> Recover <em>·</em> Repeat <em>·</em></span>
  </div>
</div>

<section class="band" aria-labelledby="why-h">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow scramble">Why it feels different</span>
      <h2 id="why-h">An orthopedic pillow shaped around the curve of your neck.</h2>
    </div>
    <div class="benefits">
      <div class="benefit">
        <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M4 26c4-10 9-12 13-8s8 6 13-2 6 2 6 10"/><path d="M4 32h32"/></svg>
        <h3>Contoured support</h3>
        <p>A raised neck roll and a gentle dip for your head help hold your neck in a natural position.</p>
      </div>
      <div class="benefit">
        <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="13" cy="20" r="5"/><path d="M20 20h16M20 14h12M20 26h12"/></svg>
        <h3>Back and side sleepers</h3>
        <p>Two contour heights, one on each long edge. Turn the pillow to match how you sleep.</p>
      </div>
      <div class="benefit">
        <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M20 4v32M8 10l24 20M32 10 8 30"/><circle cx="20" cy="20" r="4"/></svg>
        <h3>Sleeps cool</h3>
        <p><?= todo('A breathable cooling layer sits between you and the foam. [TODO confirm]') ?></p>
      </div>
      <div class="benefit">
        <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="7" y="9" width="26" height="22" rx="5"/><path d="M7 16h26M14 9V5M26 9V5"/></svg>
        <h3>Washable cover</h3>
        <p>Unzip the cover and machine wash it. The foam core stays inside, fresh and in shape.</p>
      </div>
    </div>
  </div>
</section>

<section class="band inside" id="inside" aria-labelledby="inside-h">
  <div class="wrap inside-stage" id="insideStage">
    <div>
      <span class="eyebrow scramble">Inside the Mars Pillow</span>
      <h2 id="inside-h" style="margin-top:18px">Four layers, one job: <em>hold your neck.</em></h2>
      <ol class="layer-list" id="layerList">
        <?php foreach ($layers as $i => $l): ?>
          <li><span class="num">L/0<?= $i + 1 ?></span><b><?= e($l['name']) ?></b><span class="d"><?= todo($l['text']) ?></span></li>
        <?php endforeach; ?>
      </ol>
    </div>
    <div class="exploded" id="exploded" aria-hidden="true">
      <canvas id="exCanvas"></canvas>
      <svg class="callouts" id="callouts"></svg>
      <div class="fallback" id="exFallback" hidden></div>
    </div>
  </div>
</section>

<section class="band" id="spine" aria-labelledby="spine-h">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow scramble">Spine alignment</span>
      <h2 id="spine-h">A straighter line from head to shoulders.</h2>
      <p class="lede">A flat pillow can let your head tip and your neck bend. A contoured shape fills the gap under your neck so it rests closer to a neutral line. Many sleepers find that more comfortable.</p>
      <p class="fine">Want to read more about sleep and neck pain? See the <a href="https://www.sleepfoundation.org/neck-pain" target="_blank" rel="noopener">Sleep Foundation's guide to neck pain</a>.</p>
    </div>
    <div class="spine-grid">
      <article class="spine-card">
        <header><h3>Regular pillow</h3><span class="pill bad">Neck bent</span></header>
        <svg viewBox="0 0 360 190" id="spineBad" role="img" aria-label="Side view: on a flat pillow the neck curves upward"></svg>
        <p>Your head sinks or tilts up, and your neck bends to follow it.</p>
      </article>
      <article class="spine-card good">
        <header><h3>Mars Pillow</h3><span class="pill good">Aligned</span></header>
        <svg viewBox="0 0 360 190" id="spineGood" role="img" aria-label="Side view: on the Mars Pillow the neck rests in a straight line"></svg>
        <p>The neck roll supports the gap, so your neck rests in a straighter line.</p>
      </article>
    </div>
  </div>
</section>

<section class="band" id="nights" aria-labelledby="nights-h">
  <div class="wrap nights-layout">
    <div class="section-head" style="margin:0">
      <span class="eyebrow scramble"><?= $nights ?>-night trial</span>
      <h2 id="nights-h">Two full moon cycles to <em>decide.</em></h2>
      <p class="lede">Your neck needs a few nights to get used to a new shape. Sleep on it for <?= $nights ?> nights. If it isn't right for you, <a href="/returns">send it back</a>.</p>
      <div class="night-count" aria-hidden="true"><span id="nightNum"><?= $nights ?></span><small>Night <span id="nightOf"><?= $nights ?></span> of <?= $nights ?></small></div>
    </div>
    <div class="moons" id="moons" data-nights="<?= $nights ?>" role="img" aria-label="Calendar of <?= $nights ?> nights shown as moon phases"></div>
  </div>
</section>

<section class="band bundles-bg" id="bundles" aria-labelledby="bundles-h">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow scramble">Bundles</span>
      <h2 id="bundles-h">Pick your mission.</h2>
      <p class="lede">Every bundle ships free within the US and comes with the <?= $nights ?>-night trial. The more you bundle, the more you save.</p>
    </div>
    <?= render('partials/bundle-picker', ['selected' => offer_by_slug((string)cfg('default_offer')), 'context' => 'home']) ?>
  </div>
</section>

<section class="band" aria-labelledby="cmp-h">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow scramble">Side by side</span>
      <h2 id="cmp-h">Mars Pillow vs. a regular pillow.</h2>
    </div>
    <div class="table-wrap">
      <table>
        <caption class="sr-only">Comparison of the Mars Pillow and a regular pillow</caption>
        <thead><tr><th scope="col">Feature</th><th scope="col" class="us">Mars Pillow</th><th scope="col">Regular pillow</th></tr></thead>
        <tbody>
          <tr><th scope="row">Shape</th><td class="us">Contoured, two heights</td><td>Flat or rounded</td></tr>
          <tr><th scope="row">Neck support</th><td class="us"><span class="yes">Built-in neck roll</span></td><td><span class="no">None</span></td></tr>
          <tr><th scope="row">Fill</th><td class="us">Memory foam core</td><td>Fiber or feathers that flatten</td></tr>
          <tr><th scope="row">Cooling layer</th><td class="us"><?= todo('Yes [TODO]') ?></td><td><span class="no">Usually not</span></td></tr>
          <tr><th scope="row">Washable cover</th><td class="us"><span class="yes">Zip-off, machine washable</span></td><td>Varies</td></tr>
          <tr><th scope="row">Trial</th><td class="us"><span class="yes"><?= $nights ?> nights</span></td><td><span class="no">Rarely</span></td></tr>
          <tr><th scope="row">Warranty</th><td class="us"><span class="yes"><?= $years ?> years</span></td><td><span class="no">Varies</span></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php if (!is_live()): ?>
<section class="band" aria-labelledby="rev-h" style="padding-top:0">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow">Reviews</span>
      <h2 id="rev-h">From real sleepers.</h2>
      <p class="fine">Placeholder: this section is hidden in live mode until you add real, verified customer reviews.</p>
    </div>
    <div class="review-slots">
      <div class="slot"><b>Reviews will appear here</b>Only verified customer reviews</div>
      <div class="slot"><b>Reviews will appear here</b>Only verified customer reviews</div>
      <div class="slot"><b>Reviews will appear here</b>Only verified customer reviews</div>
    </div>
  </div>
</section>
<?php endif; ?>

<?= render('partials/trust-strip') ?>

<section class="band faq" id="faq" aria-labelledby="faq-h">
  <div class="wrap faq-grid">
    <div class="section-head" style="margin:0">
      <span class="eyebrow scramble">FAQ</span>
      <h2 id="faq-h">Questions about the pillow, answered.</h2>
      <p class="lede">More answers on our <a href="/faq">full FAQ page</a>, or <a href="/contact">send us a message</a>.</p>
    </div>
    <div>
      <?php foreach ($faqPreview as $f): ?>
        <details><summary><?= e($f['q']) ?></summary><p><?= todo($f['a']) ?></p></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="band final" aria-labelledby="final-h">
  <div class="planet" aria-hidden="true"></div>
  <div class="wrap">
    <span class="eyebrow scramble">Ready for liftoff</span>
    <h2 id="final-h">Try the Mars orthopedic pillow for <em><?= $nights ?> nights.</em></h2>
    <p class="lede">Start with one pillow, or bring one home for everyone in the bed. Free shipping, and <?= $nights ?> nights to decide.</p>
    <a class="btn btn-primary magnetic" href="/product/mars-pillow">Start your <?= $nights ?>-night trial</a>
  </div>
</section>
