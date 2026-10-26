<?php /** @var ?string $hash */ ?>
<?= render('admin/_bar') ?>
<div class="band page-pad"><div class="wrap narrow">
  <form class="form-card" method="post" action="/admin/make-hash">
    <h1 class="page-title">Create admin password</h1>
    <p class="fine">Works only in test mode. Choose a strong password (at least 10 characters), then copy the result into <code>admin.password_hash</code> in config.php.</p>
    <?= csrf_field() ?>
    <div class="field"><label for="pw">New password</label><input id="pw" name="password" type="password" minlength="10" required autocomplete="new-password"></div>
    <button class="btn btn-primary" type="submit">Create hash</button>
    <?php if ($hash): ?><div class="field"><label for="hash">Paste this into config.php</label><textarea id="hash" rows="3" readonly><?= e($hash) ?></textarea></div><?php endif; ?>
  </form>
</div></div>
