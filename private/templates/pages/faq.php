<?php
$groups = [];
foreach (faqs() as $f) $groups[$f['cat']][] = $f;
$nights = (int)cfg('site.trial_nights');
?>
<div class="band page-pad faq">
  <div class="wrap narrow">
    <nav class="crumbs" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li><li aria-current="page">FAQ</li></ol></nav>
    <span class="eyebrow">Help center</span>
    <h1 class="page-title">Orthopedic pillow FAQ</h1>
    <p class="lede">Answers to common questions about the Mars Pillow orthopedic pillow: how to use it, how to care for it, and how the trial, warranty and shipping work. Can't find your answer? <a href="/contact">Contact us</a>.</p>
    <?php foreach ($groups as $cat => $items): ?>
      <section class="faq-group" aria-labelledby="g-<?= e(md5($cat)) ?>">
        <h2 id="g-<?= e(md5($cat)) ?>"><?= e($cat) ?></h2>
        <?php foreach ($items as $f): ?><details><summary><?= e($f['q']) ?></summary><p><?= todo($f['a']) ?></p></details><?php endforeach; ?>
      </section>
    <?php endforeach; ?>
    <div class="cta-box">
      <h2>Still deciding?</h2>
      <p>Sleep on it for <?= $nights ?> nights. If it isn't right for you, <a href="/returns">send it back</a>.</p>
      <a class="btn btn-primary magnetic" href="/product/mars-pillow">Start your <?= $nights ?>-night trial</a>
    </div>
  </div>
</div>
