<?php
declare(strict_types=1);

/**
 * Meta Conversions API: sends the purchase from the server, with hashed customer details,
 * only when the visitor accepted cookies. event_id = order number, so Meta merges it with the pixel event.
 */
function meta_capi_purchase(array $order): void
{
    $pixel = (string)cfg('tracking.meta_pixel_id'); $token = (string)cfg('tracking.meta_capi_token');
    if ($pixel === '' || $token === '' || empty($order['marketing_consent'])) return;
    $h = fn($v) => $v ? hash('sha256', strtolower(trim((string)$v))) : null;
    $name = preg_split('/\s+/', trim((string)$order['customer_name'])) ?: [];
    $user = array_filter([
        'em' => [$h($order['customer_email'])],
        'fn' => [$h($name[0] ?? '')], 'ln' => [$h(count($name) > 1 ? end($name) : '')],
        'ct' => [$h(str_replace(' ', '', (string)$order['ship_city']))], 'st' => [$h($order['ship_state'])],
        'zp' => [$h(substr((string)$order['ship_postal'], 0, 5))], 'country' => [$h($order['ship_country'])],
    ], fn($v) => $v[0] !== null);
    $event = [
        'event_name' => 'Purchase', 'event_time' => time(), 'event_id' => $order['order_number'],
        'action_source' => 'website', 'event_source_url' => url('/order/success'),
        'user_data' => $user,
        'custom_data' => [
            'currency' => 'USD', 'value' => $order['total_cents'] / 100, 'content_type' => 'product',
            'contents' => array_map(fn($i) => ['id' => $i['offer_id'], 'quantity' => (int)$i['qty'], 'item_price' => $i['unit_price_cents'] / 100], $order['items']),
        ],
    ];
    $body = ['data' => [$event], 'access_token' => $token];
    if (cfg('tracking.meta_test_event_code')) $body['test_event_code'] = cfg('tracking.meta_test_event_code');
    $ch = curl_init('https://graph.facebook.com/v19.0/' . rawurlencode($pixel) . '/events');
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'], CURLOPT_POSTFIELDS => json_encode($body)]);
    $res = curl_exec($ch); $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    if ($code >= 400 || $res === false) log_line('capi.log', "Purchase {$order['order_number']} failed: HTTP $code " . substr((string)$res, 0, 300));
}
