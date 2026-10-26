<?php $biz = e(cfg('site.business_name')); $mail = e(cfg('site.support_email')); ?>
<div class="band page-pad"><div class="wrap narrow prose">
<?= render('legal/_head', ['title' => 'Privacy Policy']) ?>
<p>This Privacy Policy explains how <?= $biz ?> ("Mars Pillow", "we", "us") collects, uses and shares information when you visit our website or buy from us.</p>
<h2>Information we collect</h2>
<ul>
  <li><b>Order information:</b> your name, email, phone number, shipping and billing address, and the items you buy.</li>
  <li><b>Payment information:</b> payments are handled by Stripe and PayPal. We do not receive or store your full card number.</li>
  <li><b>Messages:</b> what you send us through the contact form or by email.</li>
  <li><b>Usage information:</b> if you accept cookies, we use Google Analytics and the Meta Pixel to understand how visitors use the site and to measure ads.</li>
</ul>
<h2>How we use your information</h2>
<ul>
  <li>To process, ship and support your order, including returns and warranty claims.</li>
  <li>To answer your questions.</li>
  <li>To improve our website and, if you agreed to cookies, measure our advertising.</li>
  <li>To prevent fraud and meet our legal obligations.</li>
</ul>
<h2>Who we share it with</h2>
<p>We share information only with service providers that help us run the store: payment processors (Stripe, PayPal), shipping carriers, our web host and email provider, and, if you accepted cookies, Google and Meta. <?= todo('[TODO list your 3PL / fulfillment partner]') ?> We do not sell your personal information.</p>
<h2>Cookies</h2>
<p>Essential cookies keep your cart and checkout working. Analytics and advertising cookies load only after you choose "Accept all". You can change your choice at any time with the "Cookie settings" link at the bottom of every page.</p>
<h2>Your privacy rights</h2>
<p>Depending on your state (for example California, Colorado, Connecticut, Virginia and others), you may have the right to access, correct, delete or get a copy of your personal information, and to opt out of targeted advertising. To make a request, email <a href="mailto:<?= $mail ?>"><?= $mail ?></a>. We will not discriminate against you for using these rights.</p>
<h2>How long we keep information</h2>
<p>We keep order records for as long as needed for accounting, tax and warranty purposes <?= todo('[TODO e.g. 7 years]') ?>, and contact messages for up to <?= todo('[TODO 2 years]') ?>.</p>
<h2>Children</h2>
<p>Our store is not directed to children under 13, and we do not knowingly collect their information.</p>
<h2>Security</h2>
<p>We use HTTPS, and payments are handled on Stripe's and PayPal's secure pages. No method of transmission is 100% secure, but we work to protect your information.</p>
<h2>Changes</h2>
<p>We may update this policy. The date at the top shows the latest version.</p>
<h2>Contact</h2>
<p><?= $biz ?>, <?= todo((string)cfg('site.business_address')) ?>. Email: <a href="mailto:<?= $mail ?>"><?= $mail ?></a></p>
<?= render('legal/_foot') ?>
</div></div>
