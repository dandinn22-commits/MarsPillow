<?php $nights = (int)cfg('site.trial_nights'); ?>
<div class="band page-pad">
  <div class="wrap narrow prose">
    <span class="eyebrow">Our story</span>
    <h1 class="page-title">Why we designed a better neck pillow</h1>
    <p class="lede">Mars Pillow started with one question: why do so many pillows leave a gap under the neck? We set out to build a neck pillow that fills that gap, for back sleepers and side sleepers alike. <?= todo('[TODO add your own founder story: who you are and why you started]') ?></p>

    <h2>The problem with flat pillows</h2>
    <p>Most pillows are the same height from edge to edge. When you lie down, your head sinks in and your neck is left without support. Over a night, that can mean waking up stiff.</p>

    <h2>How we designed the Mars neck pillow</h2>
    <p>We started with the shape. The Mars Pillow has a raised neck roll on one edge, a lower roll on the other, and a gentle dip for your head. Then we chose slow-recovery memory foam, so the pillow softens where your head rests and stays firm where your neck needs it. See <a href="/#inside">what's inside each layer</a>.</p>
    <ul>
      <li>Two contour heights, one for back sleepers and one for side sleepers.</li>
      <li>A breathable cover you can zip off and wash.</li>
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
