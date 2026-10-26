<?php
// Local development only: php -S localhost:8080 -t public_html tests/dev-router.php
$f = __DIR__ . '/../public_html' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($_SERVER['REQUEST_URI'] !== '/' && is_file($f)) return false;
require __DIR__ . '/../public_html/index.php';
