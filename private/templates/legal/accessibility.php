<?php $mail = e(cfg('site.support_email')); ?>
<div class="band page-pad"><div class="wrap narrow prose">
<?= render('legal/_head', ['title' => 'Accessibility Statement']) ?>
<p>We want everyone to be able to shop at Mars Pillow, including people who use assistive technology.</p>
<h2>Our accessibility goal</h2>
<p>We aim to meet the Web Content Accessibility Guidelines (WCAG) 2.1, level AA.</p>
<h2>What we do</h2>
<ul>
  <li>The whole site works with a keyboard, with a visible focus outline.</li>
  <li>Images have text alternatives, and form fields have labels.</li>
  <li>Text and buttons use strong color contrast.</li>
  <li>If your device asks for reduced motion, we turn off animations.</li>
  <li>Card payments happen on Stripe's page and PayPal's page, which follow their own accessibility standards.</li>
</ul>
<h2>Known limits</h2>
<p>The interactive 3D pillow is decorative. All of its information is also available as text on the page.</p>
<h2>Tell us about a barrier</h2>
<p>If something on the site is hard to use, email <a href="mailto:<?= $mail ?>"><?= $mail ?></a>. Tell us the page and what happened. We aim to reply within <?= todo('[TODO 2]') ?> business days, and we can help you place your order by email.</p>
<?= render('legal/_foot') ?>
</div></div>
