<?php
// End-to-end checks against the local server (test mode, SQLite).
$base = 'http://127.0.0.1:8080'; $jar = tempnam(sys_get_temp_dir(), 'jar'); $fails = 0;
function req($method, $url, $body = null, $headers = [], $jar = null) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_CUSTOMREQUEST => $method, CURLOPT_HEADER => true,
        CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar, CURLOPT_HTTPHEADER => $headers]);
    if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    $r = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); $hs = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    return [$code, substr($r, 0, $hs), substr($r, $hs)];
}
function ok($cond, $msg) { global $fails; echo ($cond ? "PASS " : "FAIL ") . $msg . "\n"; if (!$cond) $fails++; }
[$c, $h, $b] = req('GET', "$base/", null, [], $jar);
preg_match('/"csrf":"([a-f0-9]{64})"/', $b, $m); $csrf = $m[1] ?? '';
ok($csrf !== '', 'CSRF token present');
ok(str_contains($h, 'Content-Security-Policy') && str_contains($h, 'X-Frame-Options'), 'security headers sent');
ok(str_contains($b, 'application/ld+json') && str_contains($b, '"@type":"Product"'), 'Product JSON-LD on home');
$J = ['Content-Type: application/json', "X-CSRF-Token: $csrf"];
[$c] = req('POST', "$base/api/cart", json_encode(['action' => 'add', 'offer' => 'twin-pack', 'qty' => 1]), ['Content-Type: application/json', 'X-CSRF-Token: bad'], $jar);
ok($c === 419, 'cart API rejects bad CSRF');
[$c, , $b] = req('POST', "$base/api/cart", json_encode(['action' => 'add', 'offer' => 'twin-pack', 'qty' => 2, 'price' => 1]), $J, $jar);
$cart = json_decode($b, true);
ok($c === 200 && $cart['subtotal'] === 358 && $cart['count'] === 2, 'add 2 twin packs = $358 (browser price ignored)');
[$c, , $b] = req('POST', "$base/api/cart", json_encode(['action' => 'add', 'offer' => 'single', 'qty' => 50]), $J, $jar);
$cart = json_decode($b, true);
ok($cart['lines'][1]['qty'] === 10, 'quantity capped at 10');
[$c, , $b] = req('POST', "$base/api/cart", json_encode(['action' => 'update', 'offer' => 'single', 'qty' => 0]), $J, $jar);
ok(json_decode($b, true)['count'] === 2, 'remove line');
[$c, , $b] = req('POST', "$base/api/cart", json_encode(['action' => 'add', 'offer' => 'fake']), $J, $jar);
ok($c === 422, 'unknown product rejected');
[$c, , $b] = req('GET', "$base/checkout", null, [], $jar);
ok($c === 200 && str_contains($b, 'Pay $358 with card'), 'checkout shows server total');
// Stripe: no network here, so the create call fails gracefully and redirects back with a message
[$c, $h] = req('POST', "$base/checkout/stripe", http_build_query(['csrf' => $csrf]), [], $jar);
ok($c === 303 && str_contains($h, 'Location: /checkout'), 'Stripe failure handled gracefully');
// Simulate a paid order + signed webhook
$pdo = new PDO('sqlite:/tmp/claude-0/mp.sqlite');
$id = (int)$pdo->query("SELECT id FROM orders ORDER BY id DESC LIMIT 1")->fetchColumn();
ok($id > 0, 'pending order saved before redirect');
$pdo->exec("UPDATE orders SET payment_ref='cs_test_123' WHERE id=$id");
$event = json_encode(['type' => 'checkout.session.completed', 'data' => ['object' => [
  'id' => 'cs_test_123', 'payment_status' => 'paid', 'amount_subtotal' => 35800, 'amount_total' => 35800, 'currency' => 'usd', 'payment_intent' => 'pi_123',
  'customer_details' => ['email' => 'buyer@example.com', 'name' => 'Jane Doe'],
  'shipping_details' => ['name' => 'Jane Doe', 'address' => ['line1' => '1 Main St', 'city' => 'Austin', 'state' => 'TX', 'postal_code' => '73301', 'country' => 'US']],
]]]);
$t = time(); $sig = hash_hmac('sha256', "$t.$event", 'whsec_testsecret');
[$c] = req('POST', "$base/webhook/stripe", $event, ['Content-Type: application/json', "Stripe-Signature: t=$t,v1=bad"]);
ok($c === 400, 'webhook with wrong signature rejected');
[$c] = req('POST', "$base/webhook/stripe", $event, ['Content-Type: application/json', "Stripe-Signature: t=$t,v1=$sig"]);
$o = $pdo->query("SELECT * FROM orders WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
ok($c === 200 && $o['status'] === 'paid' && $o['customer_email'] === 'buyer@example.com' && $o['ship_city'] === 'Austin', 'signed webhook marks order paid with address');
[$c] = req('POST', "$base/webhook/stripe", $event, ['Content-Type: application/json', "Stripe-Signature: t=$t,v1=$sig"]);
ok($c === 200, 'repeat webhook is harmless');
[$c, , $b] = req('GET', "$base/order/success?session_id=cs_test_123", null, [], $jar);
ok(str_contains($b, 'Your order is confirmed') && str_contains($b, 'purchase-data'), 'success page shows paid order');
[, , $b] = req('GET', "$base/api/cart", null, [], $jar);
ok(json_decode($b, true)['count'] === 0, 'cart cleared after payment');
// Contact form
[$c, $h] = req('POST', "$base/contact", http_build_query(['csrf' => $csrf, 'name' => '', 'email' => 'nope', 'message' => 'hi']), [], $jar);
ok($c === 200, 'contact validation re-shows form');
[$c, $h] = req('POST', "$base/contact", http_build_query(['csrf' => $csrf, 'name' => 'Sam', 'email' => 'sam@example.com', 'subject' => 'My order', 'message' => 'Hello, where is my pillow?']), [], $jar);
$n = (int)$pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
ok($c === 303 && $n === 1, 'contact message saved');
[$c] = req('POST', "$base/contact", http_build_query(['csrf' => $csrf, 'name' => 'Bot', 'email' => 'b@x.com', 'message' => 'buy cheap stuff now', 'website' => 'spam.com']), [], $jar);
ok((int)$pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn() === 1, 'honeypot drops bots');
// Admin
[$c, $h] = req('GET', "$base/admin", null, [], $jar);
ok($c === 303, 'admin requires login');
[$c, , $b] = req('GET', "$base/admin/login", null, [], $jar);
[$c, $h] = req('POST', "$base/admin/login", http_build_query(['csrf' => $csrf, 'password' => 'wrong']), [], $jar);
ok($c === 200, 'wrong admin password rejected');
[$c, $h] = req('POST', "$base/admin/login", http_build_query(['csrf' => $csrf, 'password' => 'TestPassword123']), [], $jar);
ok($c === 303, 'admin login works');
[$c, , $b] = req('GET', "$base/admin", null, [], $jar);
preg_match('/"csrf":"([a-f0-9]{64})"/', $b, $m); $csrf2 = $m[1] ?? '';
ok($c === 200 && str_contains($b, 'Jane Doe') && str_contains($b, 'Hello, where is my pillow?'), 'admin lists order and message');
[$c] = req('POST', "$base/admin/order/$id/ship", http_build_query(['csrf' => $csrf2, 'tracking' => '1Z999']), [], $jar);
$o = $pdo->query("SELECT status, tracking_number FROM orders WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
ok($o['status'] === 'shipped' && $o['tracking_number'] === '1Z999', 'mark as shipped');
[$c, , $b] = req('GET', "$base/<script>", null, [], $jar);
ok($c === 404 && !str_contains($b, '<script>alert'), '404 page safe');
echo $fails ? "\n$fails FAILED\n" : "\nALL PASSED\n";
exit($fails ? 1 : 0);
