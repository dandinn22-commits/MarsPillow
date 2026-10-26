<?php /** @var ?string $error */ ?>
<?= render('admin/_bar') ?>
<div class="band page-pad"><div class="wrap narrow">
  <form class="form-card" method="post" action="/admin/login">
    <h1 class="page-title">Admin login</h1>
    <?= csrf_field() ?>
    <?php if ($error): ?><p class="flash flash-error" role="alert"><?= e($error) ?></p><?php endif; ?>
    <?php if (str_contains((string)cfg('admin.password_hash'), 'TODO')): ?><p class="notice">No admin password set yet. <?= is_live() ? 'Add a password hash in config.php.' : 'Create one at <a href="/admin/make-hash">/admin/make-hash</a>.' ?></p><?php endif; ?>
    <div class="field"><label for="pw">Password</label><input id="pw" name="password" type="password" autocomplete="current-password" required autofocus></div>
    <button class="btn btn-primary" type="submit">Log in</button>
  </form>
</div></div>
