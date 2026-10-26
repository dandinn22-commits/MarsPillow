<?php
declare(strict_types=1);

function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
}

function mp_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    session_name('mp_sid');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => is_https(),
        'httponly' => true,
        'samesite' => 'Lax', // Lax so the visitor's cart survives the redirect back from Stripe / PayPal
    ]);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_start();
    // rotate the session id every 30 minutes
    if (empty($_SESSION['_created'])) $_SESSION['_created'] = time();
    elseif (time() - $_SESSION['_created'] > 1800) { session_regenerate_id(true); $_SESSION['_created'] = time(); }
}

/** Per-request nonce for inline <script> tags */
function csp_nonce(): string
{
    static $n = null;
    return $n ??= base64_encode(random_bytes(16));
}

function mp_send_security_headers(): void
{
    $n = csp_nonce();
    $paypal = 'https://www.paypal.com https://*.paypal.com https://*.paypalobjects.com';
    $csp = [
        "default-src 'self'",
        "script-src 'self' 'nonce-$n' https://cdnjs.cloudflare.com $paypal https://www.googletagmanager.com https://connect.facebook.net",
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
        "font-src 'self' https://fonts.gstatic.com",
        "img-src 'self' data: blob: $paypal https://www.google-analytics.com https://www.googletagmanager.com https://www.facebook.com",
        "connect-src 'self' $paypal https://www.google-analytics.com https://*.google-analytics.com https://*.analytics.google.com https://www.googletagmanager.com https://www.facebook.com https://connect.facebook.net",
        "frame-src $paypal",
        "form-action 'self' https://checkout.stripe.com",
        "base-uri 'self'",
        "object-src 'none'",
        "frame-ancestors 'self'",
    ];
    header('Content-Security-Policy: ' . implode('; ', $csp));
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(self "https://www.paypal.com")');
    if (is_https()) header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// ---------- CSRF ----------
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }

function csrf_ok(): bool
{
    $sent = $_POST['csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    return is_string($sent) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $sent);
}

function require_csrf(bool $json = false): void
{
    if (csrf_ok()) return;
    if ($json) json_out(['error' => 'Your session expired. Refresh the page and try again.'], 419);
    http_response_code(419);
    exit('Your session expired. Go back, refresh the page and try again.');
}

// ---------- Rate limiting (file based, no extra database table needed) ----------
/** Returns true if the action is allowed; counts the attempt. */
function rate_limit(string $bucket, int $max, int $windowSeconds): bool
{
    $file = MP_STORAGE . '/ratelimit/' . hash('sha256', $bucket . '|' . client_ip()) . '.json';
    $now = time();
    $data = ['start' => $now, 'hits' => 0];
    $fh = @fopen($file, 'c+');
    if (!$fh) return true;
    flock($fh, LOCK_EX);
    $raw = stream_get_contents($fh);
    if ($raw) { $d = json_decode($raw, true); if (is_array($d)) $data = $d; }
    if ($now - $data['start'] > $windowSeconds) $data = ['start' => $now, 'hits' => 0];
    $data['hits']++;
    ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode($data));
    flock($fh, LOCK_UN); fclose($fh);
    return $data['hits'] <= $max;
}

// ---------- Validation ----------
function clean_text($v, int $max = 500): string
{
    $v = is_string($v) ? trim($v) : '';
    $v = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $v) ?? '';
    return mb_substr($v, 0, $max);
}

function valid_email(string $v): bool { return (bool)filter_var($v, FILTER_VALIDATE_EMAIL) && mb_strlen($v) <= 190; }

// ---------- Admin ----------
function admin_logged_in(): bool { return !empty($_SESSION['admin']) && ($_SESSION['admin_ua'] ?? '') === hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? ''); }

function require_admin(): void
{
    if (!admin_logged_in()) redirect('/admin/login');
}
