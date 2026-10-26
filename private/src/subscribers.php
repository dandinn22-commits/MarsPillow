<?php
declare(strict_types=1);

/** Email capture (popup + quiz) and abandoned-cart reminders. */
function subscriber_add(string $email, string $source, ?string $quiz = null): array
{
    $email = strtolower(trim($email));
    $row = q('SELECT * FROM subscribers WHERE email = ?', [$email])->fetch();
    if (!$row) {
        q('INSERT INTO subscribers (email, source, quiz_result, restore_token, created_at) VALUES (?, ?, ?, ?, ?)',
          [$email, $source, $quiz, bin2hex(random_bytes(20)), now_utc()]);
        $row = q('SELECT * FROM subscribers WHERE email = ?', [$email])->fetch();
        $row['is_new'] = true;
    } else {
        if ($quiz) q('UPDATE subscribers SET quiz_result = ? WHERE id = ?', [$quiz, $row['id']]);
        if ($row['unsubscribed']) q('UPDATE subscribers SET unsubscribed = 0 WHERE id = ?', [$row['id']]);
        $row['is_new'] = false;
    }
    $_SESSION['sub_email'] = $email;
    subscriber_sync_cart();
    return $row;
}

/** Save this visitor's cart next to their email, so a reminder can bring it back. */
function subscriber_sync_cart(): void
{
    if (empty($_SESSION['sub_email'])) return;
    try {
        $cart = cart_raw();
        q('UPDATE subscribers SET cart_json = ?, cart_updated_at = ?, reminder_sent_at = NULL WHERE email = ? AND converted = 0',
          [$cart ? json_encode($cart) : null, $cart ? now_utc() : null, $_SESSION['sub_email']]);
    } catch (Throwable $e) { error_log('[subscriber cart] ' . $e->getMessage()); }
}

function subscriber_by_token(string $token): ?array
{
    if (!preg_match('/^[a-f0-9]{40}$/', $token)) return null;
    return q('SELECT * FROM subscribers WHERE restore_token = ?', [$token])->fetch() ?: null;
}

/** Cron: one reminder for carts left for a few hours (and not older than 7 days). */
function cron_cart_reminders(): int
{
    $hours = (int)cfg('automations.cart_reminder_hours', 3);
    $rows = q('SELECT * FROM subscribers WHERE cart_json IS NOT NULL AND converted = 0 AND unsubscribed = 0 AND reminder_sent_at IS NULL AND cart_updated_at <= ? AND cart_updated_at >= ?',
              [gmdate('Y-m-d H:i:s', time() - $hours * 3600), gmdate('Y-m-d H:i:s', time() - 7 * 86400)])->fetchAll();
    $n = 0;
    foreach ($rows as $s) {
        $items = [];
        foreach ((json_decode((string)$s['cart_json'], true) ?: []) as $id => $qty) { $o = offer_by_id((string)$id); if ($o) $items[] = ['name' => $o['name'], 'qty' => (int)$qty, 'price' => $o['price']]; }
        if (!$items) continue;
        if (send_mail($s['email'], 'You left something in your cart', render('emails/cart-reminder', ['s' => $s, 'items' => $items]))) $n++;
        q('UPDATE subscribers SET reminder_sent_at = ? WHERE id = ?', [now_utc(), $s['id']]);
    }
    return $n;
}
