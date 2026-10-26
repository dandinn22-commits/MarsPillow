<?php
declare(strict_types=1);

/**
 * Stripe Checkout (hosted payment page) using plain cURL — no Composer needed.
 * Card details are typed only on Stripe's page, never on this site.
 */
function stripe_keys(): array { return cfg('stripe.' . (is_live() ? 'live' : 'test'), []); }

function stripe_request(string $method, string $path, array $params = []): array
{
    $ch = curl_init('https://api.stripe.com/v1/' . ltrim($path, '/'));
    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD        => stripe_keys()['secret_key'] . ':',
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => ['Stripe-Version: 2024-06-20'],
    ];
    if ($method === 'POST') { $opts[CURLOPT_POST] = true; $opts[CURLOPT_POSTFIELDS] = http_build_query($params); }
    elseif ($params) { curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/' . ltrim($path, '/') . '?' . http_build_query($params)); }
    curl_setopt_array($ch, $opts);
    $body = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    if ($body === false) throw new RuntimeException('Stripe connection failed: ' . $err);
    $data = json_decode((string)$body, true) ?: [];
    if ($code >= 400) throw new RuntimeException('Stripe error: ' . ($data['error']['message'] ?? "HTTP $code"));
    return $data;
}

/** Create a Checkout Session for a pending order. Returns the Stripe URL to redirect to. */
function stripe_create_checkout(array $order): string
{
    $params = [
        'mode' => 'payment',
        'success_url' => url('/order/success') . '?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'  => url('/order/cancelled') . '?order=' . rawurlencode($order['order_number']),
        'client_reference_id' => (string)$order['id'],
        'metadata' => ['order_id' => (string)$order['id'], 'order_number' => $order['order_number']],
        'payment_intent_data' => ['metadata' => ['order_number' => $order['order_number']]],
        'shipping_address_collection' => ['allowed_countries' => ['US']],
        'phone_number_collection' => ['enabled' => 'true'],
        'billing_address_collection' => 'auto',
        // US sales tax: when you're ready, turn on Stripe Tax in your dashboard and add:
        // 'automatic_tax' => ['enabled' => 'true'],
        'line_items' => [],
    ];
    foreach ($order['items'] as $i => $it) {
        $params['line_items'][$i] = [
            'quantity' => (int)$it['qty'],
            'price_data' => [
                'currency' => 'usd',
                'unit_amount' => (int)$it['unit_price_cents'],
                'product_data' => ['name' => $it['name']],
            ],
        ];
    }
    $session = stripe_request('POST', 'checkout/sessions', $params);
    order_set_ref((int)$order['id'], $session['id']);
    return $session['url'];
}

/**
 * Verify the Stripe-Signature header (HMAC-SHA256) and return the event.
 * Throws if the signature is wrong or too old.
 */
function stripe_verify_webhook(string $payload, string $sigHeader, string $secret, int $tolerance = 300): array
{
    $parts = [];
    foreach (explode(',', $sigHeader) as $kv) {
        [$k, $v] = array_pad(explode('=', trim($kv), 2), 2, '');
        $parts[$k][] = $v;
    }
    $t = (int)($parts['t'][0] ?? 0);
    if (!$t || empty($parts['v1'])) throw new RuntimeException('Missing signature');
    if (abs(time() - $t) > $tolerance) throw new RuntimeException('Signature too old');
    $expected = hash_hmac('sha256', $t . '.' . $payload, $secret);
    $ok = false;
    foreach ($parts['v1'] as $sig) if (hash_equals($expected, $sig)) $ok = true;
    if (!$ok) throw new RuntimeException('Bad signature');
    $event = json_decode($payload, true);
    if (!is_array($event)) throw new RuntimeException('Bad JSON');
    return $event;
}

/** Handle a verified webhook event. Orders are marked paid only here. */
function stripe_handle_event(array $event): void
{
    $type = $event['type'] ?? '';
    if (!in_array($type, ['checkout.session.completed', 'checkout.session.async_payment_succeeded'], true)) return;
    $s = $event['data']['object'] ?? [];
    if (($s['payment_status'] ?? '') !== 'paid') return; // async methods: wait for async_payment_succeeded
    $order = order_by_ref((string)($s['id'] ?? ''));
    if (!$order) { $order = order_get((int)($s['client_reference_id'] ?? 0)); }
    if (!$order) { log_line('stripe.log', 'No order for session ' . ($s['id'] ?? '?')); return; }
    // compare before tax, so turning on Stripe Tax later doesn't break this check
    if ((int)($s['amount_subtotal'] ?? -1) !== (int)$order['subtotal_cents'] || strtolower((string)($s['currency'] ?? '')) !== 'usd') {
        log_line('stripe.log', "Amount mismatch for {$order['order_number']}: " . ($s['amount_subtotal'] ?? '?') . " vs {$order['subtotal_cents']}");
        return;
    }
    $ship = $s['shipping_details'] ?? ($s['collected_information']['shipping_details'] ?? []);
    $addr = $ship['address'] ?? ($s['customer_details']['address'] ?? []);
    order_mark_paid((int)$order['id'], (string)($s['payment_intent'] ?? ''), [
        'name'    => $ship['name'] ?? ($s['customer_details']['name'] ?? null),
        'email'   => $s['customer_details']['email'] ?? null,
        'line1'   => $addr['line1'] ?? null, 'line2' => $addr['line2'] ?? null,
        'city'    => $addr['city'] ?? null, 'state' => $addr['state'] ?? null,
        'postal'  => $addr['postal_code'] ?? null, 'country' => $addr['country'] ?? null,
    ]);
}
