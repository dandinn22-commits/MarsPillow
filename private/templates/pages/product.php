<?php
/** @var array $selected */
$nights = (int)cfg('site.trial_nights'); $years = (int)cfg('site.warranty_years');
$p = cfg('product');
$photos = [];
foreach (['product-1.jpg' => 'Mars Pillow from the side, showing its three layers', 'product-2.jpg' => 'Mars Pillow from above on a made bed', 'product-3.jpg' => 'Close-up of the bamboo-viscose cover', 'product-4.jpg' => 'Person sleeping on their side on the Mars Pillow'] as $file => $alt) {
    $photos[] = ['src' => is_file(MP_PUBLIC . '/assets/img/' . $file) ? '/assets/img/' . $file : null, 'alt' => $alt];
}
$productFaqs = array_values(array_filter(faqs(), fn($f) => in_array($f['cat'], ['Using your orthopedic pillow', 'Care'], true)));
?>
<div class="band product-page">
  <div class="wrap">
    <nav class="crumbs" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li><li aria-current="page">Mars Pillow</li></ol></nav>

    <div class="pdp">
      <div class="gallery" id="gallery">
        <div class="g-main">
          <div class="g-slide is-active" data-slide="0" id="slide-0">
            <canvas id="pdpStage" aria-label="3D view of the Mars Pillow. Drag to turn it." role="img"></canvas>
            <div class="g-fallback" id="pdpFallback" hidden></div>
            <p class="g-tip" aria-hidden="true">Drag to turn · press to test the foam</p>
          </div>
          <?php foreach ($photos as $i => $ph): ?>
            <div class="g-slide" data-slide="<?= $i + 1 ?>" id="slide-<?= $i + 1 ?>" hidden>
              <?php if ($ph['src']): ?><img src="<?= e($ph['src']) ?>" alt="<?= e($ph['alt']) ?>" loading="lazy" width="1200" height="1200">
              <?php else: ?><div class="photo-slot"><span>Photo coming soon</span><small><?= e($ph['alt']) ?></small></div><?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="g-thumbs" role="tablist" aria-label="Product views">
          <button type="button" role="tab" aria-selected="true" aria-controls="slide-0" data-go="0" class="g-thumb"><span>3D</span></button>
          <?php foreach ($photos as $i => $ph): ?>
            <button type="button" role="tab" aria-selected="false" aria-controls="slide-<?= $i + 1 ?>" data-go="<?= $i + 1 ?>" class="g-thumb" aria-label="Photo <?= $i + 1 ?>">
              <?php if ($ph['src']): ?><img src="<?= e($ph['src']) ?>" alt="" loading="lazy" width="120" height="120"><?php else: ?><span><?= $i + 1 ?></span><?php endif; ?>
            </button>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="pdp-info">
        <span class="eyebrow">For side, back and stomach sleepers</span>
        <h1>Mars Pillow: adjustable memory foam pillow</h1>
        <p class="lede">An adjustable memory foam pillow with three removable layers, four heights, and a soft side and a firm side. Set it to your build and sleeping position to support your neck and help keep your spine aligned through the night.</p>
        <?php $rs = reviews_stats(); if ($rs['count']): ?><a class="rating-line" href="#reviews"><?= stars($rs['avg']) ?> <b><?= e(number_format($rs['avg'], 1)) ?></b> · <?= (int)$rs['count'] ?> verified review<?= $rs['count'] === 1 ? '' : 's' ?></a><?php endif; ?>
        <ul class="pdp-points">
          <li><?= $nights ?>-night sleep trial</li>
          <li><?= $years ?>-year warranty on the pillow</li>
          <li>Free US shipping in <?= todo((string)cfg('site.shipping_days')) ?> business days</li>
        </ul>
        <?= render('partials/bundle-picker', ['selected' => $selected, 'context' => 'product']) ?>
        <p class="fine">Not sure which height is for you? <a href="/find-your-fit">Take the 30-second pillow finder</a>.</p>
      </div>
    </div>

    <section class="pdp-tabs" aria-labelledby="details-h">
      <h2 id="details-h" class="sr-only">Product details</h2>
      <div class="tablist" role="tablist" aria-label="Product details">
        <button type="button" role="tab" id="tab-details" aria-controls="panel-details" aria-selected="true">Details</button>
        <button type="button" role="tab" id="tab-materials" aria-controls="panel-materials" aria-selected="false" tabindex="-1">Materials &amp; care</button>
        <button type="button" role="tab" id="tab-size" aria-controls="panel-size" aria-selected="false" tabindex="-1">Size</button>
        <button type="button" role="tab" id="tab-faq" aria-controls="panel-faq" aria-selected="false" tabindex="-1">FAQ</button>
      </div>
      <div class="tabpanel" role="tabpanel" id="panel-details" aria-labelledby="tab-details" tabindex="0">
        <h3>What's inside</h3>
        <ol class="layer-list on-all">
          <?php foreach ($p['layers'] as $i => $l): ?><li class="on"><span class="num">L/0<?= $i + 1 ?></span><b><?= e($l['name']) ?></b><span class="d"><?= todo($l['text']) ?></span></li><?php endforeach; ?>
        </ol>
        <h3>Pick your height</h3>
        <div class="table-wrap"><table class="heights">
          <caption class="sr-only">Heights you can build with the Mars Pillow layers</caption>
          <thead><tr><th scope="col">Height</th><th scope="col">Layers</th><th scope="col">Usually best for</th></tr></thead>
          <tbody><?php foreach ($p['heights'] as $h): ?><tr><th scope="row"><?= e($h['in']) ?> in <small>(<?= (int)$h['cm'] ?> cm)</small></th><td><?= e($h['how']) ?></td><td><?= e($h['for']) ?></td></tr><?php endforeach; ?></tbody>
        </table></div>
        <h3>Soft or firm</h3>
        <p>Sleep with the soft layer on top for a gentle, sinking feel. Flip the pillow so the firm layer is on top for more support. Side sleepers often start with the firm side.</p>
      </div>
      <div class="tabpanel" role="tabpanel" id="panel-materials" aria-labelledby="tab-materials" tabindex="0">
        <h3>Materials</h3><p><?= todo($p['materials']) ?></p>
        <h3>Care</h3><p><?= todo($p['care']) ?></p>
        <p class="fine">New memory foam can have a light smell for a day or two. Let it air out in a ventilated room.</p>
      </div>
      <div class="tabpanel" role="tabpanel" id="panel-size" aria-labelledby="tab-size" tabindex="0">
        <dl class="specs">
          <div><dt>Dimensions</dt><dd><?= todo($p['size']) ?></dd></div>
          <div><dt>Weight</dt><dd><?= todo($p['weight']) ?></dd></div>
          <div><dt>Heights</dt><dd><?= e(implode(' / ', array_map(fn($h) => $h['in'] . ' in', $p['heights']))) ?></dd></div>
          <div><dt>Layers</dt><dd><?= e(implode(' + ', array_map(fn($l) => $l['in'] . ' in', $p['layers']))) ?>, all removable</dd></div>
          <div><dt>Fits</dt><dd>Standard pillowcases <?= todo('[TODO confirm]') ?></dd></div>
          <div><dt>Best for</dt><dd>Side, back and stomach sleepers</dd></div>
        </dl>
      </div>
      <div class="tabpanel faq" role="tabpanel" id="panel-faq" aria-labelledby="tab-faq" tabindex="0">
        <?php foreach ($productFaqs as $f): ?><details><summary><?= e($f['q']) ?></summary><p><?= todo($f['a']) ?></p></details><?php endforeach; ?>
        <p class="fine">More questions? Read the <a href="/faq">full FAQ</a> or see how <a href="/returns">returns and the trial</a> work.</p>
      </div>
    </section>
  </div>
</div>

<section class="band" aria-labelledby="why-pdp-h">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow">Why it works</span>
      <h2 id="why-pdp-h">Why an adjustable memory foam pillow feels different</h2>
      <p class="lede">A regular pillow comes in one height, so it is too low for some people and too high for others, and your neck bends to make up the difference. The Mars Pillow lets you build the height that fits your shoulders and the way you sleep. Memory foam slowly softens under pressure and then returns to shape, so the support stays where you need it.</p>
    </div>
    <div class="benefits">
      <div class="benefit"><h3>4 heights</h3><p>Add or remove a layer to change the height.</p></div>
      <div class="benefit"><h3>Soft side, firm side</h3><p>Flip the pillow to change how it feels.</p></div>
      <div class="benefit"><h3>Square 90° edge</h3><p>Sits close to your shoulder and fills the gap under your neck.</p></div>
      <div class="benefit"><h3><?= $nights ?> nights to decide</h3><p>Not right for you? <a href="/returns">Send it back</a>.</p></div>
    </div>
  </div>
</section>

<?= render('partials/compare-brand') ?>
<?= render('partials/reviews', ['headingId' => 'pdp-rev']) ?>
<?= render('partials/trust-strip') ?>

<div class="sticky-atc" id="stickyAtc" aria-hidden="true">
  <div><b data-sticky="name"><?= e($selected['name']) ?></b><span data-sticky="price"><?= money($selected['price']) ?></span></div>
  <button type="button" class="btn btn-primary" data-sticky="add" tabindex="-1">Add to cart</button>
</div>
