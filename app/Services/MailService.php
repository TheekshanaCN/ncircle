<?php
/**
 * app/Services/MailService.php  -  sends account emails.
 * MAIL_DRIVER=log writes them to storage/logs/mail.log (local default).
 * MAIL_DRIVER=resend sends them through the Resend API, using RESEND_API_KEY.
 * MAIL_DRIVER=mail uses PHP's mail(), which needs a mail server on the VPS.
 */
class MailService
{
    // Attached inside the email, because email apps cannot load images from localhost or show SVG.
    private const LOGO_FILE = '/public/assets/img/logo-email.png';
    private const LOGO_CID = 'ncircle-logo';
    private const LOGO_HEIGHT = 48;

    /**
     * An email with one button, using views/emails/action.php.
     * $data: name, title, intro, button, link, note. The plain-text version is built from the same data.
     */
    public static function sendAction(string $to, string $subject, array $data): bool
    {
        $text = "Hi {$data['name']},\n\n{$data['intro']}\n{$data['link']}\n\n{$data['note']}";
        return self::send($to, $subject, $text, self::renderAction($data));
    }

    /** $logo is a cid: reference in real emails. */
    public static function renderAction(array $data, string $logo = 'cid:' . self::LOGO_CID): string
    {
        [$width, $height] = getimagesize(BASE_PATH . self::LOGO_FILE);
        $logoWidth = (int) round($width * self::LOGO_HEIGHT / $height);
        extract($data, EXTR_SKIP);
        ob_start();
        include BASE_PATH . '/views/emails/action.php';
        return (string) ob_get_clean();
    }

    /** False means the email was not handed over, so callers should not claim it was sent. */
    public static function send(string $to, string $subject, string $body, string $html = ''): bool
    {
        switch (config('app.mail.driver')) {
            case 'resend':
                return self::sendWithResend($to, $subject, $body, $html);
            case 'mail':
                $headers = 'From: ' . self::from() . "\r\nContent-Type: text/plain; charset=UTF-8";
                return mail($to, $subject, $body, $headers);
            case 'log':
                return self::writeToLog($to, $subject, $body);
            default:
                error_log('MailService: unknown MAIL_DRIVER, email not sent.');
                return false;
        }
    }

    public static function isLogDriver(): bool
    {
        return config('app.mail.driver') === 'log';
    }

    private static function from(): string
    {
        return config('app.mail.from_name') . ' <' . config('app.mail.from_address') . '>';
    }

    private static function sendWithResend(string $to, string $subject, string $body, string $html): bool
    {
        $key = (string) config('app.mail.resend.key');
        if ($key === '') {
            error_log('MailService: RESEND_API_KEY is not set, email not sent.');
            return false;
        }
        $payload = ['from' => self::from(), 'to' => [$to], 'subject' => $subject, 'text' => $body];
        if ($html !== '') {
            $payload['html'] = $html;
            $payload['attachments'] = [[
                'filename' => 'ncircle.png',
                'content' => base64_encode((string) file_get_contents(BASE_PATH . self::LOGO_FILE)),
                'content_id' => self::LOGO_CID,
            ]];
        }
        $ch = curl_init(rtrim((string) config('app.mail.resend.base'), '/') . '/emails');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_INVALID_UTF8_SUBSTITUTE),
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $key, 'Content-Type: application/json'],
            CURLOPT_USERAGENT => 'php-curl',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => false,
        ]);
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        if ($response === false || $status < 200 || $status >= 300) {
            // Never log the body of the email: it holds a live sign-in link.
            error_log("MailService: Resend failed (HTTP {$status}) " . ($error !== '' ? $error : (string) $response));
            return false;
        }
        return true;
    }

    private static function writeToLog(string $to, string $subject, string $body): bool
    {
        $dir = BASE_PATH . '/storage/logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $entry = sprintf("[%s] To: %s\nSubject: %s\n%s\n\n", date('c'), $to, $subject, $body);
        return file_put_contents($dir . '/mail.log', $entry, FILE_APPEND | LOCK_EX) !== false;
    }
}
