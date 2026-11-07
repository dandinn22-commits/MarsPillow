<?php $c = cfg('company'); $biz = e($c['legal_name']); $mail = e(cfg('site.support_email')); ?>
<div class="band page-pad"><div class="wrap narrow prose">
<?= render('legal/_head', ['title' => 'Privacy Policy']) ?>
<p>This Privacy Policy explains how <?= $biz ?> ("Mars Pillow", "we", "us") collects, uses and shares information when you visit marspillows.com or buy from us.</p>

<h2>Information we collect</h2>
<ul>
  <li><b>Order information:</b> your name, email, phone number, shipping and billing address, and the items you buy.</li>
  <li><b>Payment information:</b> payments are handled by Stripe and PayPal. We do not receive or store your full card number.</li>
  <li><b>Email sign-ups:</b> if you enter your email to get a discount code, we keep your email and, if you leave items in your cart, what was in it.</li>
  <li><b>Reviews and messages:</b> reviews you write, and what you send us through the contact form or by email.</li>
  <li><b>Usage information:</b> if you accept cookies, we use Google Analytics, Google Ads and the Meta Pixel to understand how visitors use the site and to measure our ads.</li>
</ul>

<h2>How we use your information</h2>
<ul>
  <li>To process, ship and support your order, including returns and warranty claims.</li>
  <li>To send order emails: confirmation, shipping, and a request to review your purchase.</li>
  <li>To send marketing emails if you signed up, such as your discount code or a reminder about items left in your cart.</li>
  <li>To answer your questions and improve our website.</li>
  <li>If you accepted cookies, to measure and improve our advertising.</li>
  <li>To prevent fraud and meet our legal obligations.</li>
</ul>

<h2>Marketing emails</h2>
<p>Every marketing email has an unsubscribe link. Click it and we'll stop sending marketing emails right away. We'll still send emails about orders you place.</p>

<h2>Who we share it with</h2>
<ul>
  <li><b>Service providers</b> that help us run the store: payment processors (Stripe, PayPal), fulfillment partners that store and ship our products (which may include Amazon Multi-Channel Fulfillment), shipping carriers, our web host, and our email provider (Google Workspace).</li>
  <li><b>Advertising partners,</b> only if you accepted cookies: Google and Meta. When you buy, we may also send them a confirmation of the purchase (order number and value) together with your email address in a hashed (scrambled) form, so they can measure which ads work. This is done only if you accepted cookies.</li>
  <li><b>Legal reasons:</b> if required by law, or to protect our rights or prevent fraud.</li>
</ul>
<p>We do not sell your personal information for money.</p>

<h2 id="choices">Your privacy choices</h2>
<p>Under some state laws, such as California's, using advertising cookies to show you ads can count as "sharing" personal information. You can opt out at any time:</p>
<ul>
  <li>Click <b>Cookie settings</b> at the bottom of any page and choose "Essential only".</li>
  <li>Use a browser or extension that sends the <b>Global Privacy Control</b> signal. We treat it as an opt-out and don't load advertising or analytics cookies.</li>
</ul>

<h2>Cookies</h2>
<p>Essential cookies keep your cart and checkout working. Analytics and advertising cookies load only after you choose "Accept all".</p>

<h2>Your privacy rights</h2>
<p>Depending on your state (for example California, Colorado, Connecticut, Texas, Virginia and others), you may have the right to know what personal information we have about you, to access, correct or delete it, to get a copy of it, and to opt out of targeted advertising. To make a request, email <a href="mailto:<?= $mail ?>"><?= $mail ?></a>. We may ask you to confirm your identity, and we reply within 45 days. If we deny your request, you can appeal by replying to our answer. We will not discriminate against you for using these rights.</p>

<h2>How long we keep information</h2>
<p>We keep order records for 7 years, for accounting, tax and warranty purposes. We keep contact messages for up to 2 years, and email sign-ups until you unsubscribe or ask us to delete them.</p>

<h2>Where your information is processed</h2>
<p>We are based in Israel and our service providers are mostly in the United States, so your information may be processed in both countries. We protect it the same way wherever it is processed.</p>

<h2>Children</h2>
<p>Our store is not directed to children under 13, and we do not knowingly collect their information.</p>

<h2>Security</h2>
<p>We use HTTPS, and payments are handled on Stripe's and PayPal's secure pages. No method of transmission is 100% secure, but we work to protect your information.</p>

<h2>Changes</h2>
<p>We may update this policy. The date at the top shows the latest version.</p>

<h2>Contact</h2>
<p><?= $biz ?>, <?= e($c['address']) ?>. Email: <a href="mailto:<?= $mail ?>"><?= $mail ?></a></p>
<?= render('legal/_foot') ?>
</div></div>
