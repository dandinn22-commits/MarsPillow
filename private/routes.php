<?php
declare(strict_types=1);

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$path = '/' . trim($path, '/');

function not_found(): never
{
    http_response_code(404);
    page('pages/404', page_meta('404'));
}

// A link with ?code=WELCOME10 adds the discount to the visitor's cart
if ($method === 'GET' && !empty($_GET['code']) && is_string($_GET['code'])) cart_apply_coupon($_GET['code']);

// ---------------- Pages ----------------
$static = [
    '/'                 => ['pages/home', 'home'],
    '/about'            => ['pages/about', 'about'],
    '/faq'              => ['pages/faq', 'faq'],
    '/privacy-policy'   => ['legal/privacy', 'privacy'],
    '/terms-of-service' => ['legal/terms', 'terms'],
    '/shipping-policy'  => ['legal/shipping', 'shipping'],
    '/returns'          => ['legal/returns', 'returns'],
    '/accessibility'    => ['legal/accessibility', 'accessibility'],
];

if ($method === 'GET' && isset($static[$path])) {
    [$tpl, $key] = $static[$path];
    $meta = page_meta($key);
    if ($key === 'home') { $meta['schema'] = [schema_organization(), schema_website(), schema_product()]; $meta['scripts'] = ['assets/js/pillow3d.js', 'assets/js/home.js']; $meta['three'] = true; $meta['body_class'] = 'home'; }
    if ($key === 'faq') { $meta['schema'] = [schema_faq(faqs()), schema_breadcrumbs([['Home', '/'], ['FAQ', '/faq']])]; }
    page($tpl, $meta);
}

// ---------------- Product ----------------
if ($method === 'GET' && $path === '/product') redirect('/product/mars-pillow', 301);
if ($method === 'GET' && preg_match('#^/product/([a-z0-9-]+)$#', $path, $m)) {
    $offer = offer_by_slug($m[1]);
    if (!$offer) not_found();
    $meta = page_meta('product'); // canonical stays /product/mars-pillow for every bundle (same page, preselected)
    $meta['schema'] = [schema_product(), schema_breadcrumbs([['Home', '/'], ['Mars Pillow', '/product/mars-pillow']])];
    $meta['scripts'] = ['assets/js/pillow3d.js', 'assets/js/product.js'];
    $meta['three'] = true;
    page('pages/product', $meta, ['selected' => $offer]);
}

// ---------------- Cart ----------------
if ($path === '/cart') {
    if ($method === 'POST') { // works without JavaScript
        require_csrf();
        $id = (string)($_POST['offer'] ?? '');
        match ($_POST['action'] ?? '') {
            'add'    => cart_add($id, (int)($_POST['qty'] ?? 1)),
            'update' => cart_set($id, (int)($_POST['qty'] ?? 0)),
            'remove' => cart_set($id, 0),
            'coupon' => cart_apply_coupon((string)($_POST['code'] ?? '')) ? flash('Discount applied.') : flash('That code is not valid.', 'error'),
            'uncoupon' => cart_remove_coupon(),
            default  => null,
        };
        subscriber_sync_cart();
        redirect('/cart');
    }
    page('pages/cart', page_meta('cart'), ['cart' => cart_summary()]);
}

if ($path === '/api/cart') {
    if ($method === 'POST') {
        require_csrf(true);
        $in = json_decode(file_get_contents('php://input') ?: '[]', true) ?: [];
        $id = (string)($in['offer'] ?? '');
        if (!offer_by_id($id) && ($in['action'] ?? '') !== 'clear') json_out(['error' => 'Unknown product'], 422);
        match ($in['action'] ?? '') {
            'add'    => cart_add($id, (int)($in['qty'] ?? 1)),
            'update' => cart_set($id, (int)($in['qty'] ?? 0)),
            'remove' => cart_set($id, 0),
            default  => json_out(['error' => 'Unknown action'], 422),
        };
        subscriber_sync_cart();
    }
    json_out(cart_summary());
}

// ---------------- Checkout ----------------
if ($path === '/checkout' && $method === 'GET') {
    $cart = cart_summary();
    if (!$cart['lines']) redirect('/cart');
    $meta = page_meta('checkout');
    $meta['scripts'] = ['assets/js/checkout.js'];
    $pp = paypal_keys()['client_id'] ?? '';
    $meta['paypal_client_id'] = (str_contains($pp, 'TODO') || $pp === '') ? '' : $pp;
    page('pages/checkout', $meta, ['cart' => $cart]);
}

if ($path === '/checkout/stripe' && $method === 'POST') {
    require_csrf();
    $cart = cart_summary();
    if (!$cart['lines']) redirect('/cart');
    if (!rate_limit('checkout', 20, 600)) { flash('Too many attempts. Please wait a few minutes and try again.', 'error'); redirect('/checkout'); }
    try {
        $order = order_create_pending($cart, 'stripe');
        $_SESSION['last_order'] = $order['id'];
        redirect(stripe_create_checkout($order));
    } catch (Throwable $e) {
        error_log('[stripe checkout] ' . $e->getMessage());
        flash('We could not open the card payment page. Please try again, or pay with PayPal.', 'error');
        redirect('/checkout');
    }
}

if ($path === '/api/paypal/create-order' && $method === 'POST') {
    require_csrf(true);
    $cart = cart_summary();
    if (!$cart['lines']) json_out(['error' => 'Your cart is empty.'], 422);
    if (!rate_limit('checkout', 20, 600)) json_out(['error' => 'Too many attempts. Please wait a few minutes.'], 429);
    try {
        $order = order_create_pending($cart, 'paypal');
        $_SESSION['last_order'] = $order['id'];
        json_out(['id' => paypal_create_order($order)]);
    } catch (Throwable $e) {
        error_log('[paypal create] ' . $e->getMessage());
        json_out(['error' => 'PayPal is not available right now. Please try again or pay by card.'], 502);
    }
}

if ($path === '/api/paypal/capture-order' && $method === 'POST') {
    require_csrf(true);
    $in = json_decode(file_get_contents('php://input') ?: '[]', true) ?: [];
    $ppId = preg_replace('/[^A-Z0-9]/', '', strtoupper((string)($in['orderID'] ?? '')));
    try {
        $order = paypal_capture($ppId);
        cart_clear();
        $_SESSION['last_order'] = $order['id'];
        json_out(['ok' => true, 'redirect' => '/order/success?order=' . rawurlencode($order['order_number'])]);
    } catch (Throwable $e) {
        error_log('[paypal capture] ' . $e->getMessage());
        json_out(['error' => 'We could not complete the PayPal payment. You have not been charged. Please try again.'], 502);
    }
}

// Stripe calls this URL after a payment. Set it in Stripe Dashboard > Developers > Webhooks.
if ($path === '/webhook/stripe' && $method === 'POST') {
    $payload = file_get_contents('php://input') ?: '';
    try {
        $event = stripe_verify_webhook($payload, $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '', (string)(stripe_keys()['webhook_secret'] ?? ''));
        stripe_handle_event($event);
        json_out(['received' => true]);
    } catch (Throwable $e) {
        log_line('stripe.log', 'Webhook rejected: ' . $e->getMessage());
        json_out(['error' => 'Invalid'], 400);
    }
}

if ($path === '/order/success' && $method === 'GET') {
    $order = null;
    if (!empty($_GET['session_id'])) $order = order_by_ref(preg_replace('/[^A-Za-z0-9_]/', '', (string)$_GET['session_id']));
    // only show an order that this visitor's session created
    if ($order && (int)($_SESSION['last_order'] ?? 0) !== (int)$order['id']) $order = null;
    if (!$order && !empty($_SESSION['last_order'])) $order = order_get((int)$_SESSION['last_order']);
    if ($order && in_array($order['status'], ['paid', 'shipped'], true)) cart_clear();
    $meta = page_meta('success');
    $meta['scripts'] = ['assets/js/success.js'];
    page('pages/success', $meta, ['order' => $order]);
}

if ($path === '/order/cancelled' && $method === 'GET') {
    page('pages/cancelled', page_meta('cancelled'));
}

// ---------------- Contact ----------------
if ($path === '/contact') {
    $errors = []; $old = [];
    if ($method === 'POST') {
        require_csrf();
        $old = ['name' => clean_text($_POST['name'] ?? '', 120), 'email' => clean_text($_POST['email'] ?? '', 190),
                'subject' => clean_text($_POST['subject'] ?? '', 190), 'message' => clean_text($_POST['message'] ?? '', 5000)];
        $honeypot = trim((string)($_POST['website'] ?? ''));
        if ($honeypot !== '') { flash('Thanks! Your message was sent.'); redirect('/contact'); } // silently drop bots
        if (!rate_limit('contact', 5, 3600)) $errors['form'] = 'You have sent several messages in the last hour. Please try again later or email us directly.';
        if ($old['name'] === '') $errors['name'] = 'Enter your name.';
        if (!valid_email($old['email'])) $errors['email'] = 'Enter a valid email address, like name@example.com.';
        if (mb_strlen($old['message']) < 10) $errors['message'] = 'Write a message of at least 10 characters.';
        if (!$errors) {
            if ($old['subject'] === '') $old['subject'] = 'General question';
            try {
                q('INSERT INTO contact_messages (name, email, subject, message, ip, created_at) VALUES (?, ?, ?, ?, ?, ?)',
                  [$old['name'], $old['email'], $old['subject'], $old['message'], client_ip(), now_utc()]);
            } catch (Throwable $e) { error_log('[contact db] ' . $e->getMessage()); }
            $html = render('emails/contact', ['m' => $old]);
            send_mail((string)cfg('mail.admin_email'), 'Contact form: ' . $old['subject'], $html, $old['email']);
            flash('Thanks, ' . $old['name'] . '! Your message was sent. We reply within one business day.');
            redirect('/contact');
        }
    }
    $meta = page_meta('contact');
    $meta['schema'] = [schema_breadcrumbs([['Home', '/'], ['Contact', '/contact']])];
    page('pages/contact', $meta, ['errors' => $errors, 'old' => $old]);
}

// ---------------- Discount codes ----------------
if ($path === '/api/coupon' && $method === 'POST') {
    require_csrf(true);
    $in = json_decode(file_get_contents('php://input') ?: '[]', true) ?: [];
    if (!empty($in['remove'])) { cart_remove_coupon(); json_out(cart_summary()); }
    if (!rate_limit('coupon', 20, 600)) json_out(['error' => 'Too many tries. Please wait a few minutes.'], 429);
    if (!cart_apply_coupon((string)($in['code'] ?? ''))) json_out(['error' => 'That code is not valid.'], 422);
    json_out(cart_summary());
}

// ---------------- Email capture (popup + quiz) ----------------
if ($path === '/api/subscribe' && $method === 'POST') {
    require_csrf(true);
    $in = json_decode(file_get_contents('php://input') ?: '[]', true) ?: [];
    $email = clean_text($in['email'] ?? '', 190);
    if (!empty($in['website'])) json_out(['ok' => true]); // honeypot
    if (!valid_email($email)) json_out(['error' => 'Enter a valid email address, like name@example.com.'], 422);
    if (!rate_limit('subscribe', 6, 3600)) json_out(['error' => 'Too many tries. Please try again later.'], 429);
    $source = in_array($in['source'] ?? '', ['popup', 'quiz'], true) ? $in['source'] : 'popup';
    $quiz = $source === 'quiz' ? clean_text($in['quiz'] ?? '', 60) : null;
    $sub = subscriber_add($email, $source, $quiz);
    $code = (string)cfg('popup_code');
    if ($sub['is_new']) send_mail($email, 'Your Mars Pillow code: ' . $code, render('emails/welcome-code', ['code' => $code, 's' => $sub, 'quiz' => $quiz]));
    json_out(['ok' => true, 'code' => $code, 'label' => coupon_find($code)['label'] ?? '']);
}

if (preg_match('#^/cart/restore/([a-f0-9]{40})$#', $path, $m) && $method === 'GET') {
    $sub = subscriber_by_token($m[1]);
    if ($sub && $sub['cart_json']) {
        foreach ((json_decode((string)$sub['cart_json'], true) ?: []) as $id => $qty) cart_set((string)$id, (int)$qty);
        $_SESSION['sub_email'] = $sub['email'];
        if (cfg('popup_code')) cart_apply_coupon((string)cfg('popup_code'));
    }
    redirect('/cart');
}

if (preg_match('#^/unsubscribe/([a-f0-9]{40})$#', $path, $m)) {
    $sub = subscriber_by_token($m[1]);
    if ($sub && $method === 'POST') { require_csrf(); q('UPDATE subscribers SET unsubscribed = 1 WHERE id = ?', [$sub['id']]); flash('You have been unsubscribed. You will not get any more emails from us.'); redirect('/'); }
    page('pages/unsubscribe', page_meta('review') + ['noindex' => true], ['sub' => $sub]);
}

// ---------------- Fit quiz ----------------
if ($path === '/find-your-fit' && $method === 'GET') {
    $answers = ['pos' => $_GET['pos'] ?? '', 'build' => $_GET['build'] ?? '', 'who' => $_GET['who'] ?? ''];
    $result = quiz_result($answers);
    $meta = page_meta('quiz');
    $meta['schema'] = [schema_breadcrumbs([['Home', '/'], ['Pillow finder', '/find-your-fit']])];
    $meta['scripts'] = ['assets/js/quiz.js'];
    page('pages/quiz', $meta, ['a' => $answers, 'result' => $result]);
}

// ---------------- Ad landing page (fast, no 3D) ----------------
if ($path === '/lp/neck-pillow' && $method === 'GET') {
    $meta = page_meta('lp');
    $meta['body_class'] = 'page-lp';
    page('pages/lp', $meta, ['selected' => offer_by_slug((string)cfg('default_offer'))]);
}

// ---------------- Verified reviews ----------------
if (preg_match('#^/review/([a-f0-9]{40})$#', $path, $m)) {
    $order = review_order_by_token($m[1]);
    if (!$order) not_found();
    $errors = []; $old = []; $done = review_exists((int)$order['id']);
    if ($method === 'POST' && !$done) {
        require_csrf();
        $old = ['name' => clean_text($_POST['name'] ?? '', 80), 'rating' => (int)($_POST['rating'] ?? 0), 'title' => clean_text($_POST['title'] ?? '', 120),
                'body' => clean_text($_POST['body'] ?? '', 3000), 'sleep_position' => in_array($_POST['sleep_position'] ?? '', ['back', 'side', 'both'], true) ? $_POST['sleep_position'] : ''];
        if ($old['name'] === '') $errors['name'] = 'Enter the name to show with your review, like "Jane D."';
        if ($old['rating'] < 1 || $old['rating'] > 5) $errors['rating'] = 'Choose from 1 to 5 stars.';
        if ($old['title'] === '') $errors['title'] = 'Add a short title.';
        if (mb_strlen($old['body']) < 20) $errors['body'] = 'Write at least 20 characters about your experience.';
        if (!$errors) { review_save((int)$order['id'], $old); $done = true; }
    }
    page('pages/review', page_meta('review'), ['order' => $order, 'errors' => $errors, 'old' => $old, 'done' => $done]);
}

// ---------------- Google Merchant Center product feed ----------------
if ($path === '/feed.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    echo render('feed');
    exit;
}

// ---------------- SEO files ----------------
if ($path === '/sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    $urls = ['/', '/product/mars-pillow', '/find-your-fit', '/faq', '/about', '/contact', '/shipping-policy', '/returns', '/privacy-policy', '/terms-of-service', '/accessibility'];
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ($urls as $u) echo '  <url><loc>' . e(url($u)) . '</loc><changefreq>' . ($u === '/' || str_starts_with($u, '/product') ? 'weekly' : 'monthly') . '</changefreq><priority>' . ($u === '/' ? '1.0' : (str_starts_with($u, '/product') ? '0.9' : '0.5')) . "</priority></url>\n";
    echo '</urlset>';
    exit;
}
if ($path === '/robots.txt') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "User-agent: *\nDisallow: /admin\nDisallow: /cart\nDisallow: /checkout\nDisallow: /order/\nDisallow: /api/\n\nSitemap: " . url('/sitemap.xml') . "\n";
    exit;
}

// ---------------- Admin ----------------
if (str_starts_with($path, '/admin')) {
    require MP_PRIVATE . '/admin.php';
}

not_found();
