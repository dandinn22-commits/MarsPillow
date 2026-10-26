<?php /** @var array $orders  @var string $status  @var array $counts  @var array $messages */
$tabs = ['' => 'All paid', 'paid' => 'To ship', 'shipped' => 'Shipped', 'pending' => 'Unpaid (abandoned)'];
?>
<?= render('admin/_bar') ?>
<div class="band page-pad admin"><div class="wrap">
  <h1 class="page-title">Orders</h1>
  <nav class="admin-tabs" aria-label="Filter orders">
    <?php foreach ($tabs as $k => $label): ?>
      <a href="/admin<?= $k ? '?status=' . e($k) : '' ?>"<?= $status === $k ? ' aria-current="page"' : '' ?>><?= e($label) ?><?php if ($k && isset($counts[$k])): ?> <span class="count"><?= (int)$counts[$k] ?></span><?php endif; ?></a>
    <?php endforeach; ?>
  </nav>
  <div class="table-wrap">
    <table class="admin-table">
      <thead><tr><th scope="col">Order</th><th scope="col">Date (UTC)</th><th scope="col">Status</th><th scope="col">Customer</th><th scope="col">Items</th><th scope="col">Total</th><th scope="col">Paid with</th></tr></thead>
      <tbody>
      <?php if (!$orders): ?><tr><td colspan="7">No orders yet.</td></tr><?php endif; ?>
      <?php foreach ($orders as $o): ?>
        <tr>
          <th scope="row"><a href="/admin/order/<?= (int)$o['id'] ?>"><?= e($o['order_number']) ?></a></th>
          <td><?= e(substr((string)$o['created_at'], 0, 16)) ?></td>
          <td><span class="status s-<?= e($o['status']) ?>"><?= e($o['status']) ?></span></td>
          <td><?= e($o['customer_name'] ?: '—') ?><br><small><?= e($o['customer_email'] ?: '') ?></small></td>
          <td><?php foreach ($o['items'] as $i): ?><?= e($i['name']) ?> × <?= (int)$i['qty'] ?><br><?php endforeach; ?></td>
          <td><?= cents($o['total_cents']) ?></td>
          <td><?= e(ucfirst($o['payment_method'])) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <h2 class="h-small" style="margin-top:48px">Latest contact messages</h2>
  <div class="table-wrap">
    <table class="admin-table">
      <thead><tr><th scope="col">Date (UTC)</th><th scope="col">From</th><th scope="col">Subject</th><th scope="col">Message</th></tr></thead>
      <tbody>
      <?php if (!$messages): ?><tr><td colspan="4">No messages yet.</td></tr><?php endif; ?>
      <?php foreach ($messages as $m): ?>
        <tr><td><?= e(substr((string)$m['created_at'], 0, 16)) ?></td><td><?= e($m['name']) ?><br><a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a></td><td><?= e($m['subject']) ?></td><td class="msg"><?= nl2br(e($m['message'])) ?></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div></div>
