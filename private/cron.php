<?php
/**
 * Scheduled task: review-request emails and abandoned-cart reminders.
 * Hostinger: hPanel > Advanced > Cron Jobs > Custom > every 15 minutes:
 *   /usr/bin/php /home/<your user>/domains/<your domain>/private/cron.php
 */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('MP_PUBLIC', dirname(__DIR__) . '/public_html');
require __DIR__ . '/bootstrap.php';
$lock = fopen(MP_STORAGE . '/cron.lock', 'c');
if (!flock($lock, LOCK_EX | LOCK_NB)) exit(0); // already running
$r = cron_review_requests();
$c = cron_cart_reminders();
log_line('cron.log', "review requests sent: $r, cart reminders sent: $c");
echo "review requests: $r, cart reminders: $c\n";
