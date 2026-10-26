<?php
declare(strict_types=1);

/**
 * Sends email through SMTP (PHPMailer, included in private/lib) when enabled in config,
 * otherwise through PHP mail().
 */
function send_mail(string $to, string $subject, string $html, ?string $replyTo = null): bool
{
    $fromEmail = (string)cfg('mail.from_email');
    $fromName  = (string)cfg('mail.from_name', 'Mars Pillow');
    $text = trim(html_entity_decode(strip_tags(preg_replace('/<(br|\/p|\/tr|\/h\d)>/i', "\n", $html)), ENT_QUOTES, 'UTF-8'));

    if (cfg('mail.smtp.enabled')) {
        require_once MP_PRIVATE . '/lib/PHPMailer/Exception.php';
        require_once MP_PRIVATE . '/lib/PHPMailer/PHPMailer.php';
        require_once MP_PRIVATE . '/lib/PHPMailer/SMTP.php';
        $m = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $m->isSMTP();
            $m->Host = cfg('mail.smtp.host');
            $m->Port = (int)cfg('mail.smtp.port');
            $m->SMTPAuth = true;
            $m->Username = cfg('mail.smtp.user');
            $m->Password = cfg('mail.smtp.pass');
            $m->SMTPSecure = cfg('mail.smtp.secure') === 'tls' ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $m->CharSet = 'UTF-8';
            $m->setFrom($fromEmail, $fromName);
            $m->addAddress($to);
            if ($replyTo) $m->addReplyTo($replyTo);
            $m->isHTML(true);
            $m->Subject = $subject;
            $m->Body = $html;
            $m->AltBody = $text;
            $m->send();
            return true;
        } catch (Throwable $e) {
            error_log('[mail smtp] ' . $e->getMessage());
            return false;
        }
    }

    $boundary = 'mp' . bin2hex(random_bytes(8));
    $headers = [
        'MIME-Version: 1.0',
        'From: ' . mb_encode_mimeheader($fromName) . " <$fromEmail>",
        'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
    ];
    if ($replyTo && valid_email($replyTo)) $headers[] = "Reply-To: $replyTo";
    $body = "--$boundary\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n$text\r\n"
          . "--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n$html\r\n--$boundary--";
    $ok = @mail($to, mb_encode_mimeheader($subject), $body, implode("\r\n", $headers));
    if (!$ok) error_log('[mail] mail() failed for ' . $to);
    return $ok;
}

function send_order_emails(array $order): void
{
    if (!empty($order['customer_email'])) {
        send_mail($order['customer_email'], "Your Mars Pillow order {$order['order_number']} is confirmed",
            render('emails/order-confirmation', ['order' => $order]));
    }
    $admin = (string)cfg('mail.admin_email');
    if (valid_email($admin)) {
        send_mail($admin, "New order {$order['order_number']} — " . cents($order['total_cents']),
            render('emails/admin-order', ['order' => $order]), $order['customer_email'] ?: null);
    }
}
