<?php /** @var array $errors  @var array $old */
$v = fn($k) => e($old[$k] ?? '');
$err = function ($k) use ($errors) { return isset($errors[$k]) ? '<p class="field-error" id="err-' . $k . '">' . e($errors[$k]) . '</p>' : ''; };
$inv = fn($k) => isset($errors[$k]) ? ' aria-invalid="true" aria-describedby="err-' . $k . '"' : '';
?>
<div class="band page-pad">
  <div class="wrap contact-layout">
    <div>
      <nav class="crumbs" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li><li aria-current="page">Contact</li></ol></nav>
      <span class="eyebrow">Contact</span>
      <h1 class="page-title">Contact us</h1>
      <p class="lede">Questions about your pillow, an order, or the <?= (int)cfg('site.trial_nights') ?>-night trial? Send us a message and we'll reply within one business day.</p>
      <h2 class="h-small">Other ways to contact us</h2>
      <ul class="contact-points">
        <li><b>Email</b> <a href="mailto:<?= e(cfg('site.support_email')) ?>"><?= e(cfg('site.support_email')) ?></a></li>
        <li><b>Orders</b> Include your order number (it starts with MP-)</li>
        <li><b>Quick answers</b> <a href="/faq">Read the FAQ</a> · <a href="/shipping-policy">Shipping</a> · <a href="/returns">Returns</a></li>
      </ul>
    </div>
    <form class="form-card" method="post" action="/contact" novalidate>
      <?= csrf_field() ?>
      <?php if (!empty($errors['form'])): ?><p class="flash flash-error" role="alert"><?= e($errors['form']) ?></p><?php endif; ?>
      <?php if ($errors && empty($errors['form'])): ?><p class="flash flash-error" role="alert">Please fix the highlighted fields.</p><?php endif; ?>
      <div class="field">
        <label for="c-name">Your name</label>
        <input id="c-name" name="name" autocomplete="name" required maxlength="120" value="<?= $v('name') ?>"<?= $inv('name') ?>>
        <?= $err('name') ?>
      </div>
      <div class="field">
        <label for="c-email">Email</label>
        <input id="c-email" name="email" type="email" autocomplete="email" required maxlength="190" value="<?= $v('email') ?>"<?= $inv('email') ?>>
        <?= $err('email') ?>
      </div>
      <div class="field">
        <label for="c-subject">Subject <span class="opt">(optional)</span></label>
        <select id="c-subject" name="subject">
          <?php foreach (['General question', 'My order', 'Returns and the trial', 'Warranty', 'Wholesale or press'] as $s): ?>
            <option<?= ($old['subject'] ?? '') === $s ? ' selected' : '' ?>><?= e($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label for="c-message">Message</label>
        <textarea id="c-message" name="message" rows="6" required maxlength="5000"<?= $inv('message') ?>><?= $v('message') ?></textarea>
        <?= $err('message') ?>
      </div>
      <div class="hp" aria-hidden="true">
        <label for="c-website">Leave this field empty</label>
        <input id="c-website" name="website" tabindex="-1" autocomplete="off">
      </div>
      <button class="btn btn-primary magnetic" type="submit">Send message</button>
      <p class="fine">We use your details only to answer you. See our <a href="/privacy-policy">Privacy Policy</a>.</p>
    </form>
  </div>
</div>
