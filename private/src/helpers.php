<?php
declare(strict_types=1);

/** Read a config value with dot notation: cfg('site.name') */
function cfg(string $key, $default = null)
{
    $v = $GLOBALS['MP_CONFIG'];
    foreach (explode('.', $key) as $k) {
        if (!is_array($v) || !array_key_exists($k, $v)) return $default;
        $v = $v[$k];
    }
    return $v;
}

function is_live(): bool { return cfg('mode') === 'live'; }

/** Escape for HTML output. Use for every value printed into a page. */
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

/** Absolute URL from a site path, e.g. url('/faq') */
function url(string $path = '/'): string { return rtrim((string)cfg('site.base_url'), '/') . '/' . ltrim($path, '/'); }

/** Path for assets with a cache-busting version */
function asset(string $path): string
{
    $file = MP_PUBLIC . '/' . ltrim($path, '/');
    $v = is_file($file) ? substr((string)filemtime($file), -6) : '1';
    return '/' . ltrim($path, '/') . '?v=' . $v;
}

function money(float|int $amount): string
{
    return '$' . (floor((float)$amount) == $amount ? number_format((float)$amount, 0) : number_format((float)$amount, 2));
}

/** All offers keyed by slug */
function offers(): array { return cfg('offers', []); }

function offer_by_slug(string $slug): ?array
{
    $o = offers()[$slug] ?? null;
    return $o ? $o + ['slug' => $slug] : null;
}

function offer_by_id(string $id): ?array
{
    foreach (offers() as $slug => $o) if ($o['id'] === $id) return $o + ['slug' => $slug];
    return null;
}

function savings(array $o): ?array
{
    if (empty($o['compare_at_price']) || $o['compare_at_price'] <= $o['price']) return null;
    $usd = $o['compare_at_price'] - $o['price'];
    return ['usd' => $usd, 'pct' => (int)round($usd / $o['compare_at_price'] * 100)];
}

/** Offers in a safe shape for the browser (display only — the server re-checks prices) */
function offers_for_js(): array
{
    $out = [];
    foreach (offers() as $slug => $o) {
        $out[] = [
            'id' => $o['id'], 'slug' => $slug, 'name' => $o['name'], 'price' => $o['price'],
            'compare' => $o['compare_at_price'], 'inc' => $o['includes'], 'units' => $o['units'],
            'badge' => $o['badge'], 'amazon' => $o['amazon_url'],
        ];
    }
    return $out;
}

/** Replace [TODO …] markers with a visible chip in test mode, plain text in live mode */
function todo(string $text): string
{
    $safe = e($text);
    return preg_replace('/\[TODO([^\]]*)\]/', is_live() ? '' : '<span class="todo">TODO$1</span>', $safe);
}

function redirect(string $path, int $code = 303): never
{
    header('Location: ' . (str_starts_with($path, 'http') ? $path : $path), true, $code);
    exit;
}

function json_out($data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function flash(?string $msg = null, string $type = 'ok'): ?array
{
    if ($msg !== null) { $_SESSION['flash'] = ['msg' => $msg, 'type' => $type]; return null; }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

/** Render a template from private/templates with variables, return HTML */
function render(string $template, array $vars = []): string
{
    extract($vars, EXTR_SKIP);
    ob_start();
    require MP_PRIVATE . '/templates/' . $template . '.php';
    return (string)ob_get_clean();
}

/** Render a page inside the main layout and send it */
function page(string $template, array $meta, array $vars = []): never
{
    $content = render($template, $vars + ['meta' => $meta]);
    echo render('layout', ['content' => $content, 'meta' => $meta] + $vars);
    exit;
}

function client_ip(): string { return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'; }

function log_line(string $file, string $msg): void
{
    @file_put_contents(MP_STORAGE . '/logs/' . $file, '[' . gmdate('c') . '] ' . $msg . PHP_EOL, FILE_APPEND | LOCK_EX);
}
