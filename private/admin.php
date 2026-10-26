<?php
declare(strict_types=1);
/** Admin routes. Included from routes.php for any /admin path. */

header('X-Robots-Tag: noindex, nofollow');

function admin_page(string $tpl, array $vars = []): never
{
    $meta = page_meta('admin');
    $meta['admin'] = true;
    page($tpl, $meta, $vars);
}

// One-time helper to create the password hash (test mode only)
if ($path === '/admin/make-hash') {
    if (is_live()) not_found();
    $hash = null;
    if ($method === 'POST') { require_csrf(); $pw = (string)($_POST['password'] ?? ''); if (strlen($pw) >= 10) $hash = password_hash($pw, PASSWORD_DEFAULT); }
    admin_page('admin/make-hash', ['hash' => $hash]);
}

if ($path === '/admin/login') {
    $error = null;
    if ($method === 'POST') {
        require_csrf();
        if (!rate_limit('admin-login', 8, 900)) {
            $error = 'Too many login attempts. Wait 15 minutes and try again.';
        } else {
            $hash = (string)cfg('admin.password_hash');
            if ($hash !== '' && !str_contains($hash, 'TODO') && password_verify((string)($_POST['password'] ?? ''), $hash)) {
                session_regenerate_id(true);
                $_SESSION['admin'] = true;
                $_SESSION['admin_ua'] = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
                $_SESSION['csrf'] = bin2hex(random_bytes(32));
                redirect('/admin');
            }
            usleep(400000);
            $error = 'That password is not correct.';
        }
    }
    admin_page('admin/login', ['error' => $error]);
}

if ($path === '/admin/logout' && $method === 'POST') {
    require_csrf();
    unset($_SESSION['admin'], $_SESSION['admin_ua']);
    session_regenerate_id(true);
    redirect('/admin/login');
}

require_admin();

if ($path === '/admin' && $method === 'GET') {
    $status = in_array($_GET['status'] ?? '', ['pending', 'paid', 'shipped', 'cancelled', 'refunded'], true) ? $_GET['status'] : '';
    $sql = 'SELECT * FROM orders' . ($status ? ' WHERE status = ?' : " WHERE status <> 'pending'") . ' ORDER BY id DESC LIMIT 200';
    $orders = q($sql, $status ? [$status] : [])->fetchAll();
    foreach ($orders as &$o) $o['items'] = q('SELECT name, qty FROM order_items WHERE order_id = ?', [$o['id']])->fetchAll();
    unset($o);
    $counts = [];
    foreach (q('SELECT status, COUNT(*) c FROM orders GROUP BY status')->fetchAll() as $r) $counts[$r['status']] = (int)$r['c'];
    $messages = q('SELECT * FROM contact_messages ORDER BY id DESC LIMIT 20')->fetchAll();
    admin_page('admin/orders', ['orders' => $orders, 'status' => $status, 'counts' => $counts, 'messages' => $messages]);
}

if (preg_match('#^/admin/order/(\d+)$#', $path, $m) && $method === 'GET') {
    $order = order_get((int)$m[1]);
    if (!$order) not_found();
    admin_page('admin/order', ['order' => $order]);
}

if (preg_match('#^/admin/order/(\d+)/ship$#', $path, $m) && $method === 'POST') {
    require_csrf();
    order_mark_shipped((int)$m[1], clean_text($_POST['tracking'] ?? '', 120));
    flash('Order marked as shipped.');
    redirect('/admin/order/' . (int)$m[1]);
}

not_found();
