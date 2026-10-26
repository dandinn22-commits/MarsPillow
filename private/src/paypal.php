<?php
declare(strict_types=1);

/** PayPal Orders v2 API via cURL. The order is created and captured on the server. */
function paypal_keys(): array { return cfg('paypal.' . (is_live() ? 'live' : 'test'), []); }
function paypal_base(): string { return is_live() ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com'; }

function paypal_token(): string
{
    if (!empty($_SESSION['pp_token']) && $_SESSION['pp_token_exp'] > time() + 60) return $_SESSION['pp_token'];
    $k = paypal_keys();
    $ch = curl_init(paypal_base() . '/v1/oauth2/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_TIMEOUT => 30,
        CURLOPT_USERPWD => $k['client_id'] . ':' . $k['secret'],
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
    ]);
    $body = curl_exec($ch); $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    $d = json_decode((string)$body, true) ?: [];
    if ($code >= 400 || empty($d['access_token'])) throw new RuntimeException('PayPal login failed');
    $_SESSION['pp_token'] = $d['access_token'];
    $_SESSION['pp_token_exp'] = time() + (int)($d['expires_in'] ?? 300);
    return $d['access_token'];
}

function paypal_request(string $method, string $path, ?array $json = null): array
{
    $ch = curl_init(paypal_base() . $path);
    $headers = ['Authorization: Bearer ' . paypal_token(), 'Content-Type: application/json', 'Prefer: return=representation'];
    $opts = [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30, CURLOPT_CUSTOMREQUEST => $method, CURLOPT_HTTPHEADER => $headers];
    if ($json !== null) $opts[CURLOPT_POSTFIELDS] = json_encode($json);
    curl_setopt_array($ch, $opts);
    $body = curl_exec($ch); $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE); $err = curl_error($ch); curl_close($ch);
    if ($body === false) throw new RuntimeException('PayPal connection failed: ' . $err);
    $d = json_decode((string)$body, true) ?: [];
    if ($code >= 400) {
        log_line('paypal.log', "HTTP $code $path " . substr((string)$body, 0, 800));
        throw new RuntimeException('PayPal error: ' . ($d['details'][0]['description'] ?? $d['message'] ?? "HTTP $code"));
    }
    return $d;
}

/** Create a PayPal order for our pending order; returns PayPal's order id */
function paypal_create_order(array $order): string
{
    $items = [];
    foreach ($order['items'] as $it) {
        $items[] = [
            'name' => $it['name'], 'quantity' => (string)$it['qty'], 'category' => 'PHYSICAL_GOODS',
            'unit_amount' => ['currency_code' => 'USD', 'value' => number_format($it['unit_price_cents'] / 100, 2, '.', '')],
        ];
    }
    $total = number_format($order['total_cents'] / 100, 2, '.', '');
    $res = paypal_request('POST', '/v2/checkout/orders', [
        'intent' => 'CAPTURE',
        'purchase_units' => [[
            'reference_id' => $order['order_number'],
            'custom_id' => (string)$order['id'],
            'invoice_id' => $order['order_number'],
            'amount' => [
                'currency_code' => 'USD', 'value' => $total,
                'breakdown' => [
                    'item_total' => ['currency_code' => 'USD', 'value' => number_format($order['subtotal_cents'] / 100, 2, '.', '')],
                    'shipping'   => ['currency_code' => 'USD', 'value' => number_format($order['shipping_cents'] / 100, 2, '.', '')],
                    'discount'   => ['currency_code' => 'USD', 'value' => number_format($order['discount_cents'] / 100, 2, '.', '')],
                ],
            ],
            'items' => $items,
        ]],
        'payment_source' => ['paypal' => ['experience_context' => [
            'brand_name' => cfg('site.name'),
            'shipping_preference' => 'GET_FROM_FILE',
            'user_action' => 'PAY_NOW',
            'return_url' => url('/order/success'),
            'cancel_url' => url('/order/cancelled'),
        ]]],
    ]);
    order_set_ref((int)$order['id'], $res['id']);
    return $res['id'];
}

/** Capture an approved PayPal order and mark ours paid. Returns our order row. */
function paypal_capture(string $paypalOrderId): array
{
    $order = order_by_ref($paypalOrderId);
    if (!$order) throw new RuntimeException('Order not found');
    if ($order['status'] !== 'pending') return $order; // already captured
    $res = paypal_request('POST', '/v2/checkout/orders/' . rawurlencode($paypalOrderId) . '/capture', []);
    $pu = $res['purchase_units'][0] ?? [];
    $cap = $pu['payments']['captures'][0] ?? [];
    if (($res['status'] ?? '') !== 'COMPLETED' || ($cap['status'] ?? '') !== 'COMPLETED') {
        throw new RuntimeException('Payment was not completed');
    }
    $paidCents = (int)round(((float)($cap['amount']['value'] ?? 0)) * 100);
    if ($paidCents !== (int)$order['total_cents'] || ($cap['amount']['currency_code'] ?? '') !== 'USD') {
        log_line('paypal.log', "Amount mismatch {$order['order_number']}: $paidCents vs {$order['total_cents']}");
        throw new RuntimeException('Payment amount did not match the order');
    }
    $ship = $pu['shipping'] ?? [];
    $a = $ship['address'] ?? [];
    $payer = $res['payer'] ?? [];
    order_mark_paid((int)$order['id'], (string)($cap['id'] ?? ''), [
        'name'  => $ship['name']['full_name'] ?? trim(($payer['name']['given_name'] ?? '') . ' ' . ($payer['name']['surname'] ?? '')),
        'email' => $payer['email_address'] ?? null,
        'line1' => $a['address_line_1'] ?? null, 'line2' => $a['address_line_2'] ?? null,
        'city'  => $a['admin_area_2'] ?? null, 'state' => $a['admin_area_1'] ?? null,
        'postal' => $a['postal_code'] ?? null, 'country' => $a['country_code'] ?? null,
    ]);
    return order_get((int)$order['id']);
}
