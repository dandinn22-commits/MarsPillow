<div class="band strip">
  <div class="wrap">
    <div class="item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 14.5A8 8 0 1 1 9.5 4a6.5 6.5 0 0 0 10.5 10.5Z"/></svg><?= (int)cfg('site.trial_nights') ?>-night sleep trial</div>
    <div class="item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3 4 6v6c0 4.5 3.4 8.3 8 9 4.6-.7 8-4.5 8-9V6l-8-3Z"/></svg><?= (int)cfg('site.warranty_years') ?>-year warranty on the pillow</div>
    <div class="item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 7h11v9H3zM14 10h4l3 3v3h-7z"/></svg><span>Free US shipping, <?= todo((string)cfg('site.shipping_days')) ?> business days</span></div>
    <div class="item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>Secure checkout: card or PayPal</div>
  </div>
</div>
