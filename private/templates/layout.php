<?php
/** @var string $content  @var array $meta */
$n = csp_nonce();
$canonical = url($meta['path'] ?? '/');
$isAdmin = !empty($meta['admin']);
$navLinks = [
    ['/product/mars-pillow', 'Shop'],
    ['/#inside', 'How it works'],
    ['/about', 'Our story'],
    ['/faq', 'FAQ'],
    ['/contact', 'Contact'],
];
$cur = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$f = flash();
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($meta['title']) ?></title>
<meta name="description" content="<?= e($meta['description']) ?>">
<?php if (!empty($meta['noindex']) || $isAdmin): ?><meta name="robots" content="noindex, nofollow">
<?php else: ?><link rel="canonical" href="<?= e($canonical) ?>">
<?php endif; ?>
<meta property="og:type" content="<?= ($meta['key'] ?? '') === 'product' ? 'product' : 'website' ?>">
<meta property="og:site_name" content="Mars Pillow">
<meta property="og:title" content="<?= e($meta['title']) ?>">
<meta property="og:description" content="<?= e($meta['description']) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e(og_image()) ?>">
<meta property="og:image:width" content="1200"><meta property="og:image:height" content="630">
<meta name="twitter:card" content="summary_large_image">
<meta name="theme-color" content="#0B132B">
<link rel="icon" href="/favicon.ico" sizes="32x32">
<link rel="icon" href="/assets/img/icon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Figtree:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap">
<link rel="stylesheet" href="<?= e(asset('assets/css/site.css')) ?>">
<?php foreach ($meta['schema'] ?? [] as $s): ?>
<script type="application/ld+json" nonce="<?= e($n) ?>"><?= json_encode($s, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
<?php endforeach; ?>
<script nonce="<?= e($n) ?>">
window.MP = <?= json_encode([
    'csrf' => csrf_token(),
    'offers' => offers_for_js(),
    'tracking' => ['ga4' => (string)cfg('tracking.ga4_id'), 'pixel' => (string)cfg('tracking.meta_pixel_id')],
    'nonce' => $n,
], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?>;
</script>
</head>
<body class="<?= e($meta['body_class'] ?? ('page-' . ($meta['key'] ?? 'x'))) ?>">
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <radialGradient id="planetShade" cx="36%" cy="30%" r="78%"><stop offset="0" stop-color="#D98A5C"/><stop offset=".45" stop-color="#A2492A"/><stop offset=".8" stop-color="#5A2A3E"/><stop offset="1" stop-color="#2A1838"/></radialGradient>
    <filter id="planetTex" x="0" y="0" width="100%" height="100%"><feTurbulence type="fractalNoise" baseFrequency=".11" numOctaves="4" seed="7"/><feColorMatrix values="0 0 0 0 .32  0 0 0 0 .1  0 0 0 0 .08  0 0 0 -2.2 1.35"/></filter>
    <clipPath id="planetClip"><circle cx="32" cy="32" r="29"/></clipPath>
    <symbol id="planet" viewBox="0 0 64 64">
      <circle cx="32" cy="32" r="29" fill="url(#planetShade)"/>
      <rect width="64" height="64" clip-path="url(#planetClip)" filter="url(#planetTex)" opacity=".75"/>
      <circle cx="32" cy="32" r="29" fill="none" stroke="#F2A07A" stroke-opacity=".35" stroke-width="1"/>
    </symbol>
  </defs>
</svg>
<a class="skip" href="#main">Skip to content</a>
<canvas id="sky" aria-hidden="true"></canvas>
<div class="cursor-glow" id="glow" aria-hidden="true"></div>

<?php if (!$isAdmin): ?>
<?php if (!is_live()): ?><div class="preview-note" role="note">Test mode · no real payments</div><?php endif; ?>
<nav class="nav band" id="nav" aria-label="Main">
  <div class="wrap">
    <a class="logo" href="/" aria-label="Mars Pillow home">
      <svg aria-hidden="true"><use href="#planet"/></svg>
      <span class="lw"><em>mars</em><b>pillow.</b></span>
    </a>
    <ul class="nav-links">
      <?php foreach ($navLinks as [$href, $label]): ?>
        <li><a href="<?= e($href) ?>"<?= $cur === $href ? ' aria-current="page"' : '' ?>><?= e($label) ?></a></li>
      <?php endforeach; ?>
    </ul>
    <div class="nav-actions">
      <a class="btn btn-primary nav-cta magnetic" href="/product/mars-pillow">Shop now</a>
      <button class="cart-btn" id="cartBtn" type="button" aria-label="Open cart" aria-controls="drawer" aria-expanded="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 8h14l-1.2 11.2a2 2 0 0 1-2 1.8H8.2a2 2 0 0 1-2-1.8L5 8Z"/><path d="M9 8V6.5a3 3 0 0 1 6 0V8"/></svg>
        <span class="cart-count" id="cartCount" data-zero="true"><?= (int)cart_summary()['count'] ?></span>
      </button>
      <button class="menu-btn" id="menuBtn" type="button" aria-label="Open menu" aria-controls="mobileMenu" aria-expanded="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
      </button>
    </div>
  </div>
  <div class="mobile-menu" id="mobileMenu" hidden>
    <ul>
      <?php foreach ($navLinks as [$href, $label]): ?><li><a href="<?= e($href) ?>"><?= e($label) ?></a></li><?php endforeach; ?>
    </ul>
  </div>
  <div class="progress" id="progress"></div>
</nav>
<?php endif; ?>

<main id="main" tabindex="-1">
<?php if ($f): ?><div class="band flash-wrap"><div class="wrap"><p class="flash flash-<?= e($f['type']) ?>" role="status"><?= e($f['msg']) ?></p></div></div><?php endif; ?>
<?= $content ?>
</main>

<?php if (!$isAdmin): ?>
<footer class="band site-footer">
  <div class="wrap">
    <div class="foot-grid">
      <div class="foot-brand">
        <a class="logo" href="/" aria-label="Mars Pillow home"><svg aria-hidden="true"><use href="#planet"/></svg><span class="lw"><em>mars</em><b>pillow.</b></span></a>
        <p>A contoured memory foam pillow for back and side sleepers. <?= (int)cfg('site.trial_nights') ?>-night trial, <?= (int)cfg('site.warranty_years') ?>-year warranty, free US shipping.</p>
      </div>
      <div>
        <h2 class="foot-h">Shop</h2>
        <ul class="foot-links">
          <?php foreach (offers() as $slug => $o): ?><li><a href="/product/<?= e($slug) ?>"><?= e($o['name']) ?></a></li><?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h2 class="foot-h">Help</h2>
        <ul class="foot-links">
          <li><a href="/faq">FAQ</a></li>
          <li><a href="/shipping-policy">Shipping Policy</a></li>
          <li><a href="/returns">Returns &amp; Refunds</a></li>
          <li><a href="/contact">Contact us</a></li>
        </ul>
      </div>
      <div>
        <h2 class="foot-h">Company</h2>
        <ul class="foot-links">
          <li><a href="/about">Our story</a></li>
          <li><a href="/privacy-policy">Privacy Policy</a></li>
          <li><a href="/terms-of-service">Terms of Service</a></li>
          <li><a href="/accessibility">Accessibility</a></li>
          <li><button type="button" class="link-btn" id="cookieSettings">Cookie settings</button></li>
        </ul>
      </div>
    </div>
    <p class="disclaimer">This product is not intended to diagnose, treat, cure or prevent any disease. If you have ongoing neck or back pain, talk to your doctor.</p>
    <p class="disclaimer">© <?= date('Y') ?> <?= e(cfg('site.business_name')) ?> · <?= todo((string)cfg('site.business_address')) ?> · <a href="mailto:<?= e(cfg('site.support_email')) ?>"><?= e(cfg('site.support_email')) ?></a></p>
  </div>
</footer>

<div class="scrim" id="scrim" hidden></div>
<aside class="drawer" id="drawer" aria-label="Cart" aria-hidden="true" inert>
  <header><h2>Your cart</h2><button class="icon-btn" id="closeCart" type="button" aria-label="Close cart"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg></button></header>
  <ul class="lines" id="lines"></ul>
  <div class="drawer-foot">
    <div class="subtotal"><span>Subtotal</span><span id="subtotal">$0</span></div>
    <p class="fine">Free shipping within the US. Taxes calculated at checkout.</p>
    <a class="btn btn-primary" id="checkoutBtn" href="/checkout">Go to secure checkout</a>
    <a class="btn btn-ghost" href="/cart">View full cart</a>
  </div>
</aside>
<div class="toast" id="toast" role="status" aria-live="polite"></div>

<div class="consent" id="consent" role="dialog" aria-labelledby="consentTitle" hidden>
  <p id="consentTitle"><strong>Cookies on Mars Pillow.</strong> We use cookies to see how the site is used and to measure our ads. Essential cookies (like your cart) are always on. <a href="/privacy-policy">Privacy Policy</a></p>
  <div class="consent-actions">
    <button type="button" class="btn btn-ghost" data-consent="no">Essential only</button>
    <button type="button" class="btn btn-primary" data-consent="yes">Accept all</button>
  </div>
</div>
<?php endif; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" defer></script>
<?php if (!empty($meta['three'])): ?><script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js" defer></script><?php endif; ?>
<script src="<?= e(asset('assets/js/site.js')) ?>" defer></script>
<?php foreach ($meta['scripts'] ?? [] as $js): ?><script src="<?= e(asset($js)) ?>" defer></script>
<?php endforeach; ?>
<?php if (!empty($meta['paypal_client_id'])): ?><script src="https://www.paypal.com/sdk/js?client-id=<?= e(rawurlencode($meta['paypal_client_id'])) ?>&currency=USD&intent=capture&components=buttons&disable-funding=paylater,venmo,card" defer></script><?php endif; ?>
</body>
</html>
