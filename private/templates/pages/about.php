<?php $nights = (int)cfg('site.trial_nights'); ?>
<div class="band page-pad">
  <div class="wrap narrow prose">
    <span class="eyebrow">Our story</span>
    <h1 class="page-title">Why we designed an adjustable pillow</h1>
    <p class="lede">Mars Pillow started with one question: why do pillows come in one height when people come in every shape? We set out to build a pillow you can adjust to your own neck and shoulders, whether you sleep on your side, back or stomach. <?= todo('[TODO add your own founder story: who you are and why you started]') ?></p>

    <h2>The problem with one-size pillows</h2>
    <p>A side sleeper with broad shoulders needs a lot more height than a stomach sleeper. A one-size pillow is too low for one of them and too high for the other, and the neck bends to make up the difference. Over a night, that can mean waking up stiff.</p>

    <h2>How we designed an adjustable Mars Pillow</h2>
    <p>We started with height. The Mars Pillow is built from three memory foam layers you can remove, so you can set it to four heights. One side is soft and one is firm, so you also choose how it feels. And we gave it a square 90° edge that sits close to your shoulder instead of rolling away. See <a href="/#inside">what's inside each layer</a>.</p>
    <ul>
      <li>Four heights from three removable layers, for side, back and stomach sleepers.</li>
      <li>A soft side and a firm side. Flip the pillow to switch.</li>
      <li>A breathable cover made with viscose from bamboo.</li>
      <li>A <?= $nights ?>-night trial, because a new pillow takes a few nights to get used to. <a href="/returns">How the trial works</a>.</li>
    </ul>

    <h2>Why "Mars"?</h2>
    <p>Sleep should feel like leaving the day behind. We liked the idea of a pillow that takes you somewhere calm and quiet, far from the noise. So we named it after the planet that has fascinated people for centuries.</p>

    <h2>Our promise</h2>
    <p>We only use real customer reviews, and we don't make medical claims. We answer common questions in our <a href="/faq">FAQ</a>, and we stand behind every pillow with a <?= (int)cfg('site.warranty_years') ?>-year warranty. If you have a question, <a href="/contact">write to us</a> and a real person will answer.</p>

    <div class="cta-box">
      <h2>Try it for <?= $nights ?> nights</h2>
      <p>Free US shipping, and <?= $nights ?> nights to decide.</p>
      <a class="btn btn-primary magnetic" href="/product/mars-pillow">Start your <?= $nights ?>-night trial</a>
    </div>
  </div>
</div>
