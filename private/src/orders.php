<?php
declare(strict_types=1);

function now_utc(): string { return gmdate('Y-m-d H:i:s'); }

function new_order_number(): string
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    do {
        $n = 'MP-';
        for ($i = 0; $i < 6; $i++) $n .= $chars[random_int(0, strlen($chars) - 1)];
    } while (q('SELECT 1 FROM orders WHERE order_number = ?', [$n])->fetchColumn());
    return $n;
}

/** Save a pending order from a validated cart summary. Returns the order row. */
function order_create_pending(array $cart, string $method): array
{
    if (empty($cart['lines'])) throw new RuntimeException('Cart is empty');
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $num = new_order_number();
        q('INSERT INTO orders (order_number, status, payment_method, currency, subtotal_cents, shipping_cents, discount_cents, coupon_code, total_cents, marketing_consent, created_at)
           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $num, 'pending', $method, 'USD', (int)round($cart['subtotal'] * 100), (int)round($cart['shipping'] * 100),
            (int)round(($cart['discount'] ?? 0) * 100), $cart['coupon']['code'] ?? null, (int)round($cart['total'] * 100), (($_COOKIE['mp_consent'] ?? '') === 'yes') ? 1 : 0, now_utc(),
        ]);
        $id = (int)$pdo->lastInsertId();
        foreach ($cart['lines'] as $l) {
            q('INSERT INTO order_items (order_id, offer_id, name, unit_price_cents, qty, line_total_cents) VALUES (?, ?, ?, ?, ?, ?)',
              [$id, $l['id'], $l['name'], $l['price'] * 100, $l['qty'], $l['line'] * 100]);
        }
        $pdo->commit();
    } catch (Throwable $e) { $pdo->rollBack(); throw $e; }
    return order_get($id);
}

function order_get(int $id): ?array
{
    $o = q('SELECT * FROM orders WHERE id = ?', [$id])->fetch();
    if (!$o) return null;
    $o['items'] = q('SELECT * FROM order_items WHERE order_id = ? ORDER BY id', [$id])->fetchAll();
    return $o;
}

function order_by_ref(string $ref): ?array
{
    $id = q('SELECT id FROM orders WHERE payment_ref = ?', [$ref])->fetchColumn();
    return $id ? order_get((int)$id) : null;
}

function order_set_ref(int $id, string $ref): void { q('UPDATE orders SET payment_ref = ? WHERE id = ?', [$ref, $id]); }

/**
 * Mark an order paid (safe to call twice — only the first call changes anything
 * and sends the emails). $info holds customer + shipping details from the payment provider.
 */
function order_mark_paid(int $id, string $captureId, array $info): bool
{
    $st = q("UPDATE orders SET status = 'paid', paid_at = ?, payment_capture_id = ?,
                customer_name = ?, customer_email = ?, ship_line1 = ?, ship_line2 = ?, ship_city = ?, ship_state = ?, ship_postal = ?, ship_country = ?
             WHERE id = ? AND status = 'pending'", [
        now_utc(), $captureId,
        $info['name'] ?? null, $info['email'] ?? null, $info['line1'] ?? null, $info['line2'] ?? null,
        $info['city'] ?? null, $info['state'] ?? null, $info['postal'] ?? null, $info['country'] ?? null, $id,
    ]);
    if ($st->rowCount() !== 1) return false;
    q('UPDATE orders SET review_token = ? WHERE id = ?', [bin2hex(random_bytes(20)), $id]);
    $order = order_get($id);
    if (!empty($order['customer_email'])) {
        try { q('UPDATE subscribers SET converted = 1 WHERE email = ?', [strtolower($order['customer_email'])]); } catch (Throwable $e) {}
    }
    try { meta_capi_purchase($order); } catch (Throwable $e) { error_log('[capi] ' . $e->getMessage()); }
    try {
        send_order_emails($order);
        q('UPDATE orders SET emails_sent = 1 WHERE id = ?', [$id]);
    } catch (Throwable $e) {
        error_log('[order emails] ' . $e->getMessage());
    }
    log_line('orders.log', "PAID {$order['order_number']} via {$order['payment_method']} total " . ($order['total_cents'] / 100));
    return true;
}

function order_mark_shipped(int $id, string $tracking): void
{
    $before = order_get($id);
    q("UPDATE orders SET status = 'shipped', shipped_at = ?, tracking_number = ? WHERE id = ? AND status IN ('paid','shipped')",
      [now_utc(), $tracking !== '' ? $tracking : null, $id]);
    // first time it ships (or tracking added later): tell the customer
    if ($before && !empty($before['customer_email']) && ($before['status'] === 'paid' || ($tracking !== '' && $tracking !== (string)$before['tracking_number']))) {
        send_mail($before['customer_email'], "Your Mars Pillow order {$before['order_number']} has shipped",
            render('emails/shipped', ['order' => order_get($id)]));
    }
}

function cents(int|string $c): string { return money(((int)$c) / 100); }
