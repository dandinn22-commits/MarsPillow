<?php /** @var array $cart */ $nights = (int)cfg('site.trial_nights'); ?>
<div class="band page-pad">
  <div class="wrap narrow-wide">
    <h1 class="page-title">Your cart</h1>
    <?php if (!$cart['lines']): ?>
      <div class="empty-state">
        <svg aria-hidden="true" width="64" height="64"><use href="#planet"/></svg>
        <p class="lede">Your cart is empty. Pick a bundle to start your <?= $nights ?>-night trial.</p>
        <a class="btn btn-primary" href="/product/mars-pillow">Choose a bundle</a>
      </div>
    <?php else: ?>
      <div class="cart-layout">
        <ul class="cart-lines" id="cartPageLines">
          <?php foreach ($cart['lines'] as $l): ?>
            <li class="cart-line" data-id="<?= e($l['id']) ?>">
              <div class="thumb" aria-hidden="true"><svg viewBox="0 0 64 64"><use href="#planet"/></svg></div>
              <div class="cl-main">
                <a class="ln" href="/product/<?= e($l['slug']) ?>"><?= e($l['name']) ?></a>
                <p class="lp"><?= e(implode(' · ', $l['includes'])) ?></p>
                <p class="lp"><?= money($l['price']) ?> each</p>
              </div>
              <form method="post" action="/cart" class="cl-qty">
                <?= csrf_field() ?><input type="hidden" name="action" value="update"><input type="hidden" name="offer" value="<?= e($l['id']) ?>">
                <label class="sr-only" for="q-<?= e($l['id']) ?>">Quantity for <?= e($l['name']) ?></label>
                <input id="q-<?= e($l['id']) ?>" type="number" name="qty" min="0" max="<?= CART_MAX_QTY ?>" value="<?= (int)$l['qty'] ?>" inputmode="numeric">
                <button class="btn btn-ghost btn-sm" type="submit">Update</button>
              </form>
              <div class="cl-total">
                <strong><?= money($l['line']) ?></strong>
                <form method="post" action="/cart"><?= csrf_field() ?><input type="hidden" name="action" value="remove"><input type="hidden" name="offer" value="<?= e($l['id']) ?>"><button class="remove" type="submit">Remove<span class="sr-only"> <?= e($l['name']) ?></span></button></form>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
        <aside class="summary order-summary">
          <h2 class="s-label">Order summary</h2>
          <dl class="totals">
            <div><dt>Subtotal</dt><dd><?= money($cart['subtotal']) ?></dd></div>
            <div><dt>Shipping</dt><dd>Free (US)</dd></div>
            <div><dt>Sales tax</dt><dd>Calculated at checkout</dd></div>
            <div class="grand"><dt>Total</dt><dd><?= money($cart['total']) ?></dd></div>
          </dl>
          <a class="btn btn-primary magnetic" href="/checkout">Go to secure checkout</a>
          <p class="fine">Includes the <?= $nights ?>-night trial and <?= (int)cfg('site.warranty_years') ?>-year warranty. Pay by card or PayPal.</p>
          <a class="fine" href="/product/mars-pillow">← Keep shopping</a>
        </aside>
      </div>
    <?php endif; ?>
  </div>
</div>
