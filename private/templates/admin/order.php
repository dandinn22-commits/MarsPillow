<?php /** @var array $order */ $o = $order; ?>
<?= render('admin/_bar') ?>
<div class="band page-pad admin"><div class="wrap narrow-wide">
  <p><a href="/admin">← All orders</a></p>
  <h1 class="page-title">Order <?= e($o['order_number']) ?> <span class="status s-<?= e($o['status']) ?>"><?= e($o['status']) ?></span></h1>
  <div class="cart-layout">
    <div>
      <div class="table-wrap"><table class="admin-table">
        <thead><tr><th scope="col">Item</th><th scope="col">Qty</th><th scope="col">Price</th><th scope="col">Total</th></tr></thead>
        <tbody>
          <?php foreach ($o['items'] as $i): ?><tr><td><?= e($i['name']) ?></td><td><?= (int)$i['qty'] ?></td><td><?= cents($i['unit_price_cents']) ?></td><td><?= cents($i['line_total_cents']) ?></td></tr><?php endforeach; ?>
          <tr><th scope="row" colspan="3">Total</th><td><b><?= cents($o['total_cents']) ?></b></td></tr>
        </tbody>
      </table></div>
      <dl class="specs" style="margin-top:24px">
        <div><dt>Created</dt><dd><?= e($o['created_at']) ?> UTC</dd></div>
        <div><dt>Paid</dt><dd><?= e($o['paid_at'] ?: '—') ?></dd></div>
        <div><dt>Shipped</dt><dd><?= e($o['shipped_at'] ?: '—') ?></dd></div>
        <div><dt>Payment</dt><dd><?= e(ucfirst($o['payment_method'])) ?> · <?= e($o['payment_capture_id'] ?: $o['payment_ref'] ?: '—') ?></dd></div>
        <div><dt>Emails sent</dt><dd><?= $o['emails_sent'] ? 'Yes' : 'No' ?></dd></div>
      </dl>
    </div>
    <aside class="summary">
      <h2 class="s-label">Ship to</h2>
      <p><?= e($o['customer_name'] ?: '—') ?><br><?= e($o['ship_line1']) ?><?= $o['ship_line2'] ? '<br>' . e($o['ship_line2']) : '' ?><br><?= e($o['ship_city']) ?> <?= e($o['ship_state']) ?> <?= e($o['ship_postal']) ?><br><?= e($o['ship_country']) ?></p>
      <p><a href="mailto:<?= e($o['customer_email']) ?>"><?= e($o['customer_email']) ?></a></p>
      <?php if (in_array($o['status'], ['paid', 'shipped'], true)): ?>
      <form method="post" action="/admin/order/<?= (int)$o['id'] ?>/ship">
        <?= csrf_field() ?>
        <div class="field"><label for="tracking">Tracking number</label><input id="tracking" name="tracking" value="<?= e($o['tracking_number']) ?>"></div>
        <button class="btn btn-primary" type="submit"><?= $o['status'] === 'shipped' ? 'Update tracking' : 'Mark as shipped' ?></button>
      </form>
      <?php endif; ?>
    </aside>
  </div>
</div></div>
