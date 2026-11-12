<?php
/**
 * app/Services/MailService.php  -  sends account emails.
 * MAIL_DRIVER=log writes them to storage/logs/mail.log (local default).
 * MAIL_DRIVER=mail uses PHP's mail(), which needs a mail server on the VPS.
 */
class MailService
{
    public static function send(string $to, string $subject, string $body): bool
    {
        if (config('app.mail.driver') === 'mail') {
            $headers = 'From: ' . config('app.mail.from') . "\r\nContent-Type: text/plain; charset=UTF-8";
            return mail($to, $subject, $body, $headers);
        }
        $dir = BASE_PATH . '/storage/logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $entry = sprintf("[%s] To: %s\nSubject: %s\n%s\n\n", date('c'), $to, $subject, $body);
        return file_put_contents($dir . '/mail.log', $entry, FILE_APPEND | LOCK_EX) !== false;
    }

    public static function isLogDriver(): bool
    {
        return config('app.mail.driver') !== 'mail';
    }
}
