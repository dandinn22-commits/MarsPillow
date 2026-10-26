<?php
declare(strict_types=1);

/**
 * The cart lives in the server session: [offer_id => quantity].
 * Prices are never stored here — they are read from config every time.
 */
const CART_MAX_QTY = 10;

function cart_raw(): array { return $_SESSION['cart'] ?? []; }

function cart_set(string $offerId, int $qty): void
{
    if (!offer_by_id($offerId)) return;
    $cart = cart_raw();
    $qty = max(0, min(CART_MAX_QTY, $qty));
    if ($qty === 0) unset($cart[$offerId]); else $cart[$offerId] = $qty;
    $_SESSION['cart'] = $cart;
}

function cart_add(string $offerId, int $qty = 1): void
{
    cart_set($offerId, (cart_raw()[$offerId] ?? 0) + max(1, $qty));
}

function cart_clear(): void { unset($_SESSION['cart']); }

/** Validated cart with server prices */
function cart_summary(): array
{
    $lines = []; $subtotal = 0; $count = 0;
    foreach (cart_raw() as $id => $qty) {
        $o = offer_by_id((string)$id);
        $qty = (int)$qty;
        if (!$o || $qty < 1) continue;
        $qty = min($qty, CART_MAX_QTY);
        $line = $o['price'] * $qty;
        $lines[] = [
            'id' => $o['id'], 'slug' => $o['slug'], 'name' => $o['name'], 'price' => $o['price'],
            'qty' => $qty, 'line' => $line, 'includes' => $o['includes'],
        ];
        $subtotal += $line; $count += $qty;
    }
    return ['lines' => $lines, 'subtotal' => $subtotal, 'shipping' => 0, 'total' => $subtotal, 'count' => $count, 'currency' => 'USD'];
}
