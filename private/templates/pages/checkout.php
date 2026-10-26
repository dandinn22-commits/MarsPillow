<?php /** @var array $cart  @var array $meta */ ?>
<div class="band page-pad">
  <div class="wrap narrow-wide">
    <h1 class="page-title">Secure checkout</h1>
    <div class="cart-layout">
      <section class="pay-box" aria-labelledby="pay-h">
        <h2 id="pay-h">How would you like to pay?</h2>
        <p class="fine">You'll enter your shipping address and payment details on the payment provider's secure page. We never see or store your card number.</p>

        <form method="post" action="/checkout/stripe" class="pay-option">
          <?= csrf_field() ?>
          <button type="submit" class="btn btn-primary btn-block magnetic">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2.5" y="5" width="19" height="14" rx="2.5"/><path d="M2.5 10h19"/></svg>
            Pay <?= money($cart['total']) ?> with card
          </button>
          <p class="fine">Visa, Mastercard, American Express, Discover and more, processed securely by Stripe.</p>
        </form>

        <div class="or"><span>or</span></div>

        <div class="pay-option">
          <?php if (!empty($meta['paypal_client_id'])): ?>
            <div id="paypal-buttons" aria-label="Pay with PayPal"></div>
            <p class="fine" id="paypal-msg" role="alert"></p>
          <?php else: ?>
            <p class="notice">PayPal will appear here after you add your PayPal client ID in the config file.</p>
          <?php endif; ?>
        </div>

        <ul class="pay-trust">
          <li><?= (int)cfg('site.trial_nights') ?>-night sleep trial</li>
          <li><?= (int)cfg('site.warranty_years') ?>-year warranty</li>
          <li>Free US shipping</li>
        </ul>
      </section>

      <aside class="summary order-summary">
        <h2 class="s-label">Order summary</h2>
        <ul class="sum-lines">
          <?php foreach ($cart['lines'] as $l): ?><li><span><?= e($l['name']) ?> × <?= (int)$l['qty'] ?></span><span><?= money($l['line']) ?></span></li><?php endforeach; ?>
        </ul>
        <dl class="totals">
          <div><dt>Subtotal</dt><dd><?= money($cart['subtotal']) ?></dd></div>
          <?php if ($cart['coupon']): ?><div class="disc"><dt>Discount (<?= e($cart['coupon']['code']) ?>)</dt><dd>−<?= money($cart['discount']) ?></dd></div><?php endif; ?>
          <div><dt>Shipping</dt><dd>Free</dd></div>
          <div><dt>Sales tax</dt><dd>Shown before you pay</dd></div>
          <div class="grand"><dt>Total</dt><dd><?= money($cart['total']) ?></dd></div>
        </dl>
        <a class="fine" href="/cart">← Edit cart</a>
      </aside>
    </div>
  </div>
</div>
