<?php
/** @var array $selected  @var string $context 'home'|'product' */
$nights = (int)cfg('site.trial_nights'); $years = (int)cfg('site.warranty_years');
$s = savings($selected);
?>
<form class="bundles-layout picker" method="post" action="/cart" data-picker data-context="<?= e($context) ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="add">
  <fieldset class="bundle-grid">
    <legend class="sr-only">Choose a bundle</legend>
    <?php foreach (offers() as $slug => $o): $sv = savings($o); ?>
      <label class="bundle" data-id="<?= e($o['id']) ?>">
        <input type="radio" name="offer" value="<?= e($o['id']) ?>" class="sr-only"<?= $o['id'] === $selected['id'] ? ' checked' : '' ?>>
        <?php if ($o['badge']): ?><span class="badge<?= $o['badge'] === 'Best Value' ? ' alt' : '' ?>"><?= e($o['badge']) ?></span><?php endif; ?>
        <span class="b-name"><?= e($o['name']) ?></span>
        <span class="b-inc"><?= e(implode(' · ', $o['includes'])) ?></span>
        <span class="b-price"><strong><?= money($o['price']) ?></strong><?php if ($sv): ?><s><span class="sr-only">Regular price </span><?= money($o['compare_at_price']) ?></s><span class="b-save">Save <?= money($sv['usd']) ?></span><?php endif; ?></span>
        <span class="units" aria-hidden="true"><?php foreach ($o['units'] as $u): ?><i class="<?= $u === 'c' ? 'cv' : '' ?>" style="width:<?= $u === 'p' ? 34 : 18 ?>px"></i><?php endforeach; ?></span>
      </label>
    <?php endforeach; ?>
  </fieldset>
  <aside class="summary" aria-live="polite">
    <span class="s-label">Your selection</span>
    <div class="s-name" data-s="name"><?= e($selected['name']) ?></div>
    <div class="s-price"><strong data-s="price"><?= money($selected['price']) ?></strong><s data-s="compare"><?= $s ? money($selected['compare_at_price']) : '' ?></s></div>
    <span class="save-chip" data-s="save"<?= $s ? '' : ' hidden' ?>><?= $s ? 'You save ' . money($s['usd']) . ' (' . $s['pct'] . '%)' : '' ?></span>
    <ul data-s="inc">
      <?php foreach ($selected['includes'] as $inc): ?><li><?= e($inc) ?></li><?php endforeach; ?>
      <li><?= $nights ?>-night trial, <?= $years ?>-year warranty</li>
      <li>Free US shipping</li>
    </ul>
    <?php if ($context === 'product'): ?>
      <div class="qty-row">
        <label for="qty">Quantity</label>
        <div class="qty qty-lg"><button type="button" data-q="-1" aria-label="Decrease quantity">−</button><input id="qty" name="qty" type="number" inputmode="numeric" min="1" max="<?= CART_MAX_QTY ?>" value="1"><button type="button" data-q="1" aria-label="Increase quantity">+</button></div>
      </div>
    <?php else: ?>
      <input type="hidden" name="qty" value="1">
    <?php endif; ?>
    <button class="btn btn-primary magnetic" type="submit" data-s="add">Add to cart · <?= money($selected['price']) ?></button>
    <a class="btn btn-ghost amazon-btn" data-s="amazon" href="<?= e($selected['amazon_url'] ?: '#') ?>" target="_blank" rel="noopener"<?= $selected['amazon_url'] ? '' : ' hidden' ?>>Also available on Amazon</a>
    <p class="fine">Secure checkout with card or PayPal. Prices in USD. <?php if ($context === 'home'): ?><a href="/product/mars-pillow">See full product details</a><?php endif; ?></p>
  </aside>
</form>
