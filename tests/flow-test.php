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

// ---------- New features ----------
$jar2 = tempnam(sys_get_temp_dir(), 'jar');
[, , $b] = req('GET', "$base/", null, [], $jar2);
preg_match('/"csrf":"([a-f0-9]{64})"/', $b, $m); $c2 = $m[1];
$J2 = ['Content-Type: application/json', "X-CSRF-Token: $c2"];
req('POST', "$base/api/cart", json_encode(['action' => 'add', 'offer' => 'twin-pack', 'qty' => 1]), $J2, $jar2);
[$c] = req('POST', "$base/api/coupon", json_encode(['code' => 'FAKE']), $J2, $jar2);
ok($c === 422, 'invalid discount code rejected');
[$c, , $b] = req('POST', "$base/api/coupon", json_encode(['code' => 'welcome10']), $J2, $jar2);
$cart = json_decode($b, true);
ok($c === 200 && abs($cart['discount'] - 17.9) < 0.001 && abs($cart['total'] - 161.1) < 0.001, 'WELCOME10 takes 10% off ($179 → $161.10)');
[$c, , $b] = req('POST', "$base/api/subscribe", json_encode(['email' => 'not-an-email', 'source' => 'popup']), $J2, $jar2);
ok($c === 422, 'subscribe rejects bad email');
[$c, , $b] = req('POST', "$base/api/subscribe", json_encode(['email' => 'Lead@Example.com', 'source' => 'popup']), $J2, $jar2);
ok($c === 200 && json_decode($b, true)['code'] === 'WELCOME10', 'subscribe returns the code');
$sub = $pdo->query("SELECT * FROM subscribers WHERE email='lead@example.com'")->fetch(PDO::FETCH_ASSOC);
ok($sub && str_contains((string)$sub['cart_json'], 'twin-pack'), 'subscriber saved with their cart');
// abandoned cart reminder via cron (pretend the cart is 4 hours old)
$pdo->exec("UPDATE subscribers SET cart_updated_at = '" . gmdate('Y-m-d H:i:s', time() - 4 * 3600) . "'");
exec('php ' . escapeshellarg(__DIR__ . '/../private/cron.php'), $out);
$sub = $pdo->query("SELECT * FROM subscribers WHERE email='lead@example.com'")->fetch(PDO::FETCH_ASSOC);
ok($sub['reminder_sent_at'] !== null, 'cron sends the cart reminder once (' . trim(implode(' ', $out)) . ')');
// restore link rebuilds the cart in a fresh browser
$jar3 = tempnam(sys_get_temp_dir(), 'jar');
[$c, $h] = req('GET', "$base/cart/restore/{$sub['restore_token']}", null, [], $jar3);
[, , $b] = req('GET', "$base/api/cart", null, [], $jar3);
$cart = json_decode($b, true);
ok($c === 303 && $cart['count'] === 1 && $cart['coupon']['code'] === 'WELCOME10', 'restore link brings back cart + code');
// discounted order through the Stripe webhook
[$c] = req('POST', "$base/checkout/stripe", http_build_query(['csrf' => $c2]), [], $jar2);
$oid = (int)$pdo->query("SELECT id FROM orders ORDER BY id DESC LIMIT 1")->fetchColumn();
$o = $pdo->query("SELECT * FROM orders WHERE id=$oid")->fetch(PDO::FETCH_ASSOC);
ok((int)$o['discount_cents'] === 1790 && (int)$o['total_cents'] === 16110 && $o['coupon_code'] === 'WELCOME10', 'order saved with discount');
$pdo->exec("UPDATE orders SET payment_ref='cs_test_disc' WHERE id=$oid");
$ev = json_encode(['type' => 'checkout.session.completed', 'data' => ['object' => ['id' => 'cs_test_disc', 'payment_status' => 'paid', 'amount_subtotal' => 17900, 'amount_total' => 16110, 'currency' => 'usd', 'payment_intent' => 'pi_2',
  'customer_details' => ['email' => 'lead@example.com', 'name' => 'Lee Ann'], 'shipping_details' => ['name' => 'Lee Ann', 'address' => ['line1' => '2 Oak', 'city' => 'Denver', 'state' => 'CO', 'postal_code' => '80202', 'country' => 'US']]]]]);
$t = time(); $sig = hash_hmac('sha256', "$t.$ev", 'whsec_testsecret');
req('POST', "$base/webhook/stripe", $ev, ['Content-Type: application/json', "Stripe-Signature: t=$t,v1=$sig"]);
$o = $pdo->query("SELECT * FROM orders WHERE id=$oid")->fetch(PDO::FETCH_ASSOC);
ok($o['status'] === 'paid' && strlen((string)$o['review_token']) === 40, 'discounted order paid, review link created');
ok((int)$pdo->query("SELECT converted FROM subscribers WHERE email='lead@example.com'")->fetchColumn() === 1, 'subscriber marked as buyer (no more reminders)');
// review request after shipping
$pdo->exec("UPDATE orders SET status='shipped', shipped_at='" . gmdate('Y-m-d H:i:s', time() - 22 * 86400) . "' WHERE id=$oid");
$out = []; exec('php ' . escapeshellarg(__DIR__ . '/../private/cron.php'), $out);
ok($pdo->query("SELECT review_requested_at FROM orders WHERE id=$oid")->fetchColumn() !== null, 'cron sends review request 21+ days after shipping');
// write a review
$tok = $o['review_token'];
[$c, , $b] = req('GET', "$base/review/$tok", null, [], $jar2);
ok($c === 200 && str_contains($b, 'How is your Mars Pillow?'), 'review form opens from the email link');
[$c, , $b] = req('POST', "$base/review/$tok", http_build_query(['csrf' => $c2, 'rating' => 9, 'title' => '', 'body' => 'short', 'name' => '']), [], $jar2);
ok(str_contains($b, 'Please fix the highlighted fields'), 'review validation');
[$c, , $b] = req('POST', "$base/review/$tok", http_build_query(['csrf' => $c2, 'rating' => 5, 'title' => 'Finally no stiff neck', 'body' => 'Took about a week to get used to it, now I sleep through the night.', 'name' => 'Lee A.', 'sleep_position' => 'side']), [], $jar2);
ok(str_contains($b, 'Thank you for your review'), 'review submitted');
[, , $b] = req('GET', "$base/product/mars-pillow");
ok(!str_contains($b, 'Finally no stiff neck') && !str_contains($b, 'aggregateRating'), 'pending review not shown yet');
$rid = (int)$pdo->query("SELECT id FROM reviews LIMIT 1")->fetchColumn();
[$c] = req('POST', "$base/admin/review/$rid/approve", http_build_query(['csrf' => $csrf2]), [], $jar);
[, , $b] = req('GET', "$base/product/mars-pillow");
ok(str_contains($b, 'Finally no stiff neck') && str_contains($b, '"aggregateRating"'), 'approved review shows on product page + rating in Google data');
// quiz + landing page + feed
[, , $b] = req('GET', "$base/find-your-fit?pos=side&build=broad&who=two");
ok(str_contains($b, 'higher edge') && str_contains($b, 'Mars Twin Pack'), 'quiz: side + partner → higher edge, Twin Pack');
[, , $b] = req('GET', "$base/find-your-fit?pos=back&build=average&who=me");
ok(str_contains($b, 'lower edge') && str_contains($b, 'Mars Sleep Pack'), 'quiz: back + solo → lower edge, Sleep Pack');
[, , $b] = req('GET', "$base/lp/neck-pillow");
ok(str_contains($b, 'noindex') && !str_contains($b, '["three"') && str_contains($b, 'TEMPUR-Neck'), 'landing page: noindex, no 3D, brand comparison');
[, , $b] = req('GET', "$base/feed.xml");
ok(substr_count($b, '<item>') === 4 && str_contains($b, '<g:price>179.00 USD</g:price>'), 'Merchant Center feed has 4 bundles with prices');

echo $fails ? "\n$fails FAILED\n" : "\nALL PASSED\n";
exit($fails ? 1 : 0);
