<?php
declare(strict_types=1);

/** Verified reviews. A review can only be written through the private link a paying customer gets by email. */
function reviews_approved(int $limit = 12): array
{
    try { return q("SELECT name, rating, title, body, sleep_position, created_at FROM reviews WHERE status = 'approved' ORDER BY id DESC LIMIT " . (int)$limit)->fetchAll(); }
    catch (Throwable $e) { return []; }
}

function reviews_stats(): array
{
    try {
        $r = q("SELECT COUNT(*) c, AVG(rating) a FROM reviews WHERE status = 'approved'")->fetch();
        return ['count' => (int)$r['c'], 'avg' => $r['c'] ? round((float)$r['a'], 1) : 0];
    } catch (Throwable $e) { return ['count' => 0, 'avg' => 0]; }
}

function review_order_by_token(string $token): ?array
{
    if (!preg_match('/^[a-f0-9]{40}$/', $token)) return null;
    $id = q("SELECT id FROM orders WHERE review_token = ? AND status IN ('paid','shipped')", [$token])->fetchColumn();
    return $id ? order_get((int)$id) : null;
}

function review_exists(int $orderId): bool { return (bool)q('SELECT 1 FROM reviews WHERE order_id = ?', [$orderId])->fetchColumn(); }

function review_save(int $orderId, array $d): void
{
    q('INSERT INTO reviews (order_id, name, rating, title, body, sleep_position, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
      [$orderId, $d['name'], $d['rating'], $d['title'], $d['body'], $d['sleep_position'] ?: null, 'pending', now_utc()]);
    $admin = (string)cfg('mail.admin_email');
    if (valid_email($admin)) send_mail($admin, "New {$d['rating']}-star review waiting for approval", render('emails/admin-review', ['d' => $d]));
}

function stars(float $rating): string
{
    $full = (int)round($rating);
    return '<span class="stars" aria-label="' . e(number_format($rating, 1)) . ' out of 5 stars"><span aria-hidden="true">'
        . str_repeat('★', $full) . '<span class="off">' . str_repeat('★', 5 - $full) . '</span></span></span>';
}

/** Cron: ask for a review N days after an order shipped. */
function cron_review_requests(): int
{
    $days = (int)cfg('automations.review_request_days', 21);
    $cut = gmdate('Y-m-d H:i:s', time() - $days * 86400);
    $rows = q("SELECT id FROM orders WHERE status = 'shipped' AND shipped_at <= ? AND review_requested_at IS NULL AND review_token IS NOT NULL AND customer_email IS NOT NULL", [$cut])->fetchAll();
    $n = 0;
    foreach ($rows as $r) {
        $o = order_get((int)$r['id']);
        if (review_exists((int)$o['id'])) { q('UPDATE orders SET review_requested_at = ? WHERE id = ?', [now_utc(), $o['id']]); continue; }
        if (send_mail($o['customer_email'], 'How is your Mars Pillow working for you?', render('emails/review-request', ['order' => $o]))) $n++;
        q('UPDATE orders SET review_requested_at = ? WHERE id = ?', [now_utc(), $o['id']]);
    }
    return $n;
}
