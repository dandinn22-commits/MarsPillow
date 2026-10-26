<?php
declare(strict_types=1);

define('MP_PRIVATE', __DIR__);
define('MP_STORAGE', __DIR__ . '/storage');

$configFile = __DIR__ . '/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('Setup needed: copy private/config.example.php to private/config.php and fill in your settings.');
}
$GLOBALS['MP_CONFIG'] = require $configFile;

foreach (['logs', 'ratelimit'] as $d) {
    if (!is_dir(MP_STORAGE . "/$d")) @mkdir(MP_STORAGE . "/$d", 0750, true);
}

// ---- Errors: never shown to visitors in live mode ----
$live = ($GLOBALS['MP_CONFIG']['mode'] ?? 'test') === 'live';
error_reporting(E_ALL);
ini_set('display_errors', $live ? '0' : '1');
ini_set('log_errors', '1');
ini_set('error_log', MP_STORAGE . '/logs/php-error.log');

require __DIR__ . '/src/helpers.php';
require __DIR__ . '/src/db.php';
require __DIR__ . '/src/security.php';
require __DIR__ . '/src/cart.php';
require __DIR__ . '/src/orders.php';
require __DIR__ . '/src/mailer.php';
require __DIR__ . '/src/stripe.php';
require __DIR__ . '/src/paypal.php';
require __DIR__ . '/src/seo.php';
require __DIR__ . '/src/reviews.php';
require __DIR__ . '/src/subscribers.php';
require __DIR__ . '/src/capi.php';

set_exception_handler(function (Throwable $e) use ($live) {
    error_log('[uncaught] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    if (!headers_sent()) http_response_code(500);
    if ($live) {
        echo '<!doctype html><meta charset="utf-8"><title>Something went wrong</title><body style="font-family:system-ui;background:#0B132B;color:#F5F3FF;padding:40px"><h1>Something went wrong.</h1><p>Please try again in a minute. If it keeps happening, email us.</p></body>';
    } else {
        echo '<pre style="white-space:pre-wrap">' . e((string)$e) . '</pre>';
    }
});

if (PHP_SAPI !== 'cli') {
    mp_start_session();
    mp_send_security_headers();
}
