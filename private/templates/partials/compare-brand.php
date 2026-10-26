<?php $c = cfg('comparison'); if (!$c || empty($c['rows'])) return; ?>
<section class="band" aria-labelledby="brand-h">
  <div class="wrap">
    <div class="section-head">
      <span class="eyebrow">Compare</span>
      <h2 id="brand-h">Mars Pillow vs. <?= e($c['brand']) ?></h2>
      <p class="lede">The same idea, a contoured memory foam pillow for back and side sleepers. Here is how the details compare.</p>
    </div>
    <div class="table-wrap">
      <table>
        <caption class="sr-only">Mars Pillow compared with <?= e($c['brand']) ?></caption>
        <thead><tr><th scope="col">Detail</th><th scope="col" class="us">Mars Pillow</th><th scope="col"><?= e($c['brand']) ?></th></tr></thead>
        <tbody>
          <?php foreach ($c['rows'] as [$k, $us, $them]): ?><tr><th scope="row"><?= e($k) ?></th><td class="us"><?= e($us) ?></td><td><?= e($them) ?></td></tr><?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <p class="fine compare-note"><?= e($c['brand']) ?> is a trademark of its owner. Mars Pillow is not affiliated with it. Details checked on <?= e(date('F j, Y', strtotime($c['checked_on']))) ?> from <a href="<?= e($c['source_url']) ?>" target="_blank" rel="noopener">a published review</a>. Prices and terms change, so check before you buy.</p>
  </div>
</section>
