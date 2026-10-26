<header class="admin-bar band"><div class="wrap">
  <a class="logo" href="/admin"><svg aria-hidden="true"><use href="#planet"/></svg><span class="lw"><em>mars</em><b>admin</b></span></a>
  <?php if (admin_logged_in()): ?>
  <form method="post" action="/admin/logout"><?= csrf_field() ?><button class="btn btn-ghost btn-sm" type="submit">Log out</button></form>
  <?php endif; ?>
</div></header>
