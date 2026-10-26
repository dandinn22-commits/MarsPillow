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
            default  => null,
        };
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

// ---------------- SEO files ----------------
if ($path === '/sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    $urls = ['/', '/product/mars-pillow', '/faq', '/about', '/contact', '/shipping-policy', '/returns', '/privacy-policy', '/terms-of-service', '/accessibility'];
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
