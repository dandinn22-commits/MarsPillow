<?php /** @var array $a  @var ?array $result */
$nights = (int)cfg('site.trial_nights');
$q = [
  'pos' => ['How do you usually sleep?', ['side' => 'On my side', 'back' => 'On my back', 'stomach' => 'On my stomach', 'both' => 'I switch during the night']],
  'build' => ['How would you describe your shoulders?', ['narrow' => 'Narrow or petite', 'average' => 'Average', 'broad' => 'Broad']],
  'who' => ['Who is the pillow for?', ['me' => 'Just me', 'two' => 'Me and my partner']],
];
?>
<div class="band page-pad">
  <div class="wrap narrow">
    <nav class="crumbs" aria-label="Breadcrumb"><ol><li><a href="/">Home</a></li><li aria-current="page">Pillow finder</li></ol></nav>
    <span class="eyebrow">Pillow finder</span>
    <h1 class="page-title">Which pillow height fits you?</h1>
    <p class="lede">Answer three quick questions. We'll tell you how to set up your Mars Pillow, which height and which side, and which bundle makes sense for you.</p>

    <?php if (!$result): ?>
    <form class="quiz form-card" method="get" action="/find-your-fit" id="quiz">
      <?php $i = 0; foreach ($q as $name => [$label, $opts]): $i++; ?>
        <fieldset class="quiz-step" data-step="<?= $i ?>">
          <legend><span class="q-num"><?= $i ?>/3</span> <?= e($label) ?></legend>
          <div class="q-opts">
            <?php foreach ($opts as $val => $text): ?>
              <label class="q-opt"><input type="radio" name="<?= e($name) ?>" value="<?= e($val) ?>" required<?= ($a[$name] ?? '') === $val ? ' checked' : '' ?>><span><?= e($text) ?></span></label>
            <?php endforeach; ?>
          </div>
        </fieldset>
      <?php endforeach; ?>
      <button class="btn btn-primary" type="submit">See my result</button>
    </form>
    <?php else: $o = $result['offer']; ?>
    <section class="quiz-result form-card" aria-labelledby="res-h" data-result="<?= e($result['key']) ?>">
      <span class="eyebrow">Your result</span>
      <h2 id="res-h">Set it to <em><?= e($result['height']['in']) ?> in</em>, <?= e($result['feel']) ?> side up.</h2>
      <p class="fine">Setup: <?= e($result['height']['how']) ?> (<?= (int)$result['height']['cm'] ?> cm).</p>
      <p><?= e($result['why']) ?></p>
      <?php if ($result['note']): ?><p class="fine"><?= e($result['note']) ?></p><?php endif; ?>
      <div class="res-offer">
        <div><span class="s-label">Recommended</span><b><?= e($o['name']) ?></b><span class="fine"><?= e(implode(' · ', $o['includes'])) ?></span></div>
        <strong><?= money($o['price']) ?></strong>
      </div>
      <div class="hero-actions">
        <a class="btn btn-primary magnetic" href="/product/<?= e($o['slug']) ?>">Start your <?= $nights ?>-night trial</a>
        <a class="btn btn-ghost" href="/find-your-fit">Start over</a>
      </div>
      <form class="inline-sub" data-subscribe="quiz" data-quiz="<?= e($result['key']) ?>" novalidate>
        <label for="qz-email">Email me my result and a <?= e(strtolower(coupon_find((string)cfg('popup_code'))['label'] ?? 'discount')) ?></label>
        <div class="inline-row"><input id="qz-email" type="email" name="email" autocomplete="email" placeholder="you@example.com" required><button class="btn btn-ghost" type="submit">Send</button></div>
        <p class="fine sub-msg" role="status"></p>
      </form>
    </section>
    <?php endif; ?>
    <section class="prose quiz-info" aria-labelledby="how-h">
      <h2 id="how-h">How the pillow heights work</h2>
      <p>The <a href="/product/mars-pillow">Mars Pillow</a> has three removable memory foam layers. Stack them to get <?= e(implode(', ', array_map(fn($h) => $h['in'] . ' in', cfg('product.heights')))) ?>. Stomach sleepers usually need the least height, back sleepers a medium height, and side sleepers the most, to fill the space between the shoulder and the head. One side of the pillow is soft and the other is firm, so you can also change how it feels.</p>
      <p>Your body gets used to a new pillow over a few nights, which is why you have <?= $nights ?> nights to decide. See how <a href="/returns">the trial works</a> or read the <a href="/faq">FAQ</a>.</p>
    </section>
    <p class="fine" style="margin-top:20px">This is a general guide, not medical advice. If you have ongoing neck pain, talk to your doctor.</p>
  </div>
</div>
