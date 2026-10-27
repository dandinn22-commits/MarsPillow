<?php
$nights = (int)cfg('site.trial_nights'); $years = (int)cfg('site.warranty_years');
$layers = cfg('product.layers'); $heights = cfg('product.heights');
$faqPreview = array_slice(faqs(), 0, 5);
?>
<header class="hero band" id="top">
  <div class="hero-title">
    <h1 class="eyebrow scramble">Adjustable orthopedic memory foam pillow</h1>
    <p class="hero-display" aria-label="Sleep out of this world.">
      <span class="ln l1" aria-hidden="true"><span class="w">Sleep</span> <span class="w">out</span> <span class="w">of</span></span>
      <span class="ln l2" aria-hidden="true"><span class="w">this</span> <em class="w">world.</em></span>
    </p>
  </div>
  <canvas id="stage" aria-hidden="true"></canvas>
  <div class="hero-fallback" id="heroFallback" hidden></div>
  <div class="press-hint" id="pressHint" aria-hidden="true"><i></i>Press it. It's memory foam.</div>
  <div class="hero-coords tl" aria-hidden="true"><span>Mission <b>MP-01</b></span><span>Adjustable support system</span></div>
  <div class="hero-coords tr" aria-hidden="true"><span>Trial <b><?= $nights ?> nights</b></span><span>Warranty <b><?= $years ?> years</b></span></div>
  <div class="hero-foot">
    <p class="lede">The Mars Pillow is an adjustable memory foam pillow with three removable layers and a soft side and a firm side. Set the height and feel that fit your body, so your neck stays supported whether you sleep on your side, back or stomach.</p>
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
    <span>Adjust <em>·</em> Support <em>·</em> Align <em>·</em> Recover <em>·</em></span>
    <span>Adjust <em>·</em> Support <em>·</em> Align <em>·</em> Recover <em>·</em></span>
    <span>Adjust <em>·</em> Support <em>·</em> Align <em>·</em> Recover <em>·</em></span>
  </div>
</div>

<section class="band" aria-labelledby="why-h">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow scramble">Why it feels different</span>
      <h2 id="why-h">One adjustable pillow, built around your neck, your shoulders and the way you sleep.</h2>
    </div>
    <div class="benefits">
      <div class="benefit">
        <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="5" y="8" width="30" height="7" rx="2"/><rect x="5" y="17" width="30" height="3" rx="1.5"/><rect x="5" y="22" width="30" height="7" rx="2"/><path d="M20 31v5M17 34l3 3 3-3"/></svg>
        <h3>4 heights in one pillow</h3>
        <p>Three removable layers give you <?= implode(', ', array_map(fn($h) => $h['in'] . ' in', array_slice($heights, 0, -1))) ?> or <?= end($heights)['in'] ?> in. Add or remove a layer until it feels right.</p>
      </div>
      <div class="benefit">
        <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="13" cy="20" r="5"/><path d="M20 20h16M20 14h12M20 26h12"/></svg>
        <h3>Soft side, firm side</h3>
        <p>One side gently sinks, the other holds you up. Flip the pillow to switch.</p>
      </div>
      <div class="benefit">
        <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M8 30 32 6M8 30h10M8 30V20"/><path d="M14 8c6 0 10 4 10 10"/></svg>
        <h3>Square 90° edge</h3>
        <p>A straight edge sits close to your shoulder and fills the gap under your neck, instead of rolling away like a fluffy pillow.</p>
      </div>
      <div class="benefit">
        <svg viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="7" y="9" width="26" height="22" rx="5"/><path d="M7 16h26M14 9V5M26 9V5"/></svg>
        <h3>Bamboo-viscose cover</h3>
        <p>A soft, breathable cover made with viscose from bamboo. <?= todo('Unzip it and machine wash it. [TODO confirm zipper and washing]') ?></p>
      </div>
    </div>
  </div>
</section>

<section class="band inside" id="inside" aria-labelledby="inside-h">
  <div class="wrap inside-stage" id="insideStage">
    <div>
      <span class="eyebrow scramble">Inside the Mars Pillow</span>
      <h2 id="inside-h" style="margin-top:18px">Three layers. <em>Your height.</em></h2>
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
      <h2 id="spine-h">The right height keeps your neck straight.</h2>
      <p class="lede">If a pillow is too low or too high, your head tips and your neck bends. The right height depends on how you sleep and how broad your shoulders are, which is why the Mars Pillow lets you change it. Many sleepers find a pillow that fits their build more comfortable.</p>
      <p class="fine">Want to read more about sleep and neck pain? See the <a href="https://www.sleepfoundation.org/neck-pain" target="_blank" rel="noopener">Sleep Foundation's guide to neck pain</a>.</p>
    </div>
    <div class="spine-grid">
      <article class="spine-card">
        <header><h3>One-size pillow</h3><span class="pill bad">Neck bent</span></header>
        <svg viewBox="0 0 360 190" id="spineBad" role="img" aria-label="Side view: on a pillow that is too low the neck bends down toward the bed"></svg>
        <p>Too low for your shoulders, so your head drops and your neck bends.</p>
      </article>
      <article class="spine-card good">
        <header><h3>Mars Pillow</h3><span class="pill good">Aligned</span></header>
        <svg viewBox="0 0 360 190" id="spineGood" role="img" aria-label="Side view: on the Mars Pillow set to the right height the neck rests in a straight line"></svg>
        <p>Set to your height, the pillow fills the gap so your neck rests in a straighter line.</p>
      </article>
    </div>
  </div>
</section>

<section class="band" id="builder" aria-labelledby="builder-h">
  <div class="wrap builder">
    <div class="section-head" style="margin:0">
      <span class="eyebrow scramble">Build your height</span>
      <h2 id="builder-h">Four heights. <em>Pick yours.</em></h2>
      <p class="lede">Unzip the cover and stack the layers. Choose how you sleep to see the setup most people start with.</p>
      <div class="b-opts" role="radiogroup" aria-label="Pillow height">
        <?php foreach ($heights as $i => $h): ?>
          <button type="button" role="radio" class="b-opt" data-h="<?= $i ?>" data-in="<?= e($h['in']) ?>" data-how="<?= e($h['how']) ?>" aria-checked="<?= $i === 3 ? 'true' : 'false' ?>">
            <b><?= e($h['in']) ?> in</b><span><?= e($h['for']) ?></span>
          </button>
        <?php endforeach; ?>
      </div>
      <p class="fine">Not sure? <a href="/find-your-fit">Take the 30-second pillow finder</a>.</p>
    </div>
    <div class="b-stage" id="bStage" data-h="3" aria-live="polite">
      <div class="b-ruler" aria-hidden="true"><span id="bIn"><?= e(end($heights)['in']) ?></span><small>in</small></div>
      <div class="b-stack" aria-hidden="true">
        <div class="b-layer soft"><span>Soft layer · 2.4 in</span></div>
        <div class="b-layer fine"><span>Fine-tune · 0.8 in</span></div>
        <div class="b-layer firm"><span>Firm layer · 2.4 in</span></div>
      </div>
      <p class="b-how" id="bHow"><?= e(end($heights)['how']) ?></p>
    </div>
  </div>
</section>

<section class="band" id="nights" aria-labelledby="nights-h">
  <div class="wrap nights-layout">
    <div class="section-head" style="margin:0">
      <span class="eyebrow scramble"><?= $nights ?>-night trial</span>
      <h2 id="nights-h">Two full moon cycles to <em>decide.</em></h2>
      <p class="lede">Your neck needs a few nights to get used to a new pillow. Try different heights and both sides for <?= $nights ?> nights. If it isn't right for you, <a href="/returns">send it back</a>.</p>
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
      <p class="lede">Every bundle ships free within the US and comes with the <?= $nights ?>-night trial. The more you bundle, the more you save. Not sure which height you need? <a href="/find-your-fit">Take the 30-second pillow finder</a>.</p>
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
          <tr><th scope="row">Height</th><td class="us"><span class="yes">4 heights, you choose</span></td><td><span class="no">One height</span></td></tr>
          <tr><th scope="row">Feel</th><td class="us"><span class="yes">Soft side and firm side</span></td><td>One feel</td></tr>
          <tr><th scope="row">Fill</th><td class="us">Three memory foam layers</td><td>Fiber or feathers that flatten</td></tr>
                    <tr><th scope="row">Cover</th><td class="us"><span class="yes">Bamboo-viscose blend</span></td><td>Varies</td></tr>
          <tr><th scope="row">Trial</th><td class="us"><span class="yes"><?= $nights ?> nights</span></td><td><span class="no">Rarely</span></td></tr>
          <tr><th scope="row">Warranty</th><td class="us"><span class="yes"><?= $years ?> years</span></td><td><span class="no">Varies</span></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?= render('partials/reviews') ?>

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
    <h2 id="final-h">Find your height in <em><?= $nights ?> nights.</em></h2>
    <p class="lede">Start with one pillow, or bring one home for everyone in the bed. Free shipping, and <?= $nights ?> nights to decide.</p>
    <a class="btn btn-primary magnetic" href="/product/mars-pillow">Start your <?= $nights ?>-night trial</a>
  </div>
</section>
