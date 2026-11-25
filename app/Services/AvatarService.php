<?php
/**
 * app/Services/AvatarService.php  -  validates and stores profile photos.
 * Like notes, photos keep a random name outside public/ and are served by a controller.
 */
class AvatarService
{
    /** Returns ['error' => message] or ['stored' => file name]. */
    public static function store(?array $file): array
    {
        $c = config('upload.avatars');
        $tooLarge = ['error' => 'The photo must be smaller than ' . format_bytes($c['max_size']) . '.'];
        if ($file === null || !is_string($file['name'] ?? null) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['error' => 'Choose a photo to upload.'];
        }
        if (in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) || (int) $file['size'] > $c['max_size']) {
            return $tooLarge;
        }
        if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            return ['error' => 'The upload failed. Please try again.'];
        }
        $checked = FileVerifier::check($file['tmp_name'], $file['name'], (int) $file['size']);
        if (isset($checked['error'])) {
            return ['error' => $checked['error']];
        }
        if (!in_array($checked['ext'], $c['types'], true)) {
            return ['error' => 'Choose a JPG, PNG or WEBP photo.'];
        }
        if (!is_dir($c['dir']) && !mkdir($c['dir'], 0775, true) && !is_dir($c['dir'])) {
            throw new RuntimeException('Photo folder could not be created.');
        }
        $stored = bin2hex(random_bytes(16)) . '.' . $checked['ext'];
        if (!move_uploaded_file($file['tmp_name'], self::path($stored))) {
            throw new RuntimeException('Photo could not be saved.');
        }
        return ['stored' => $stored];
    }

    public static function path(string $stored): string
    {
        return config('upload.avatars.dir') . '/' . basename($stored);
    }

    /** From the extension, which store() already matched to the real contents. */
    public static function mime(string $stored): string
    {
        return config('upload.attachments.images')[pathinfo($stored, PATHINFO_EXTENSION)] ?? 'application/octet-stream';
    }

    public static function delete(?string $stored): void
    {
        if ($stored !== null && is_file(self::path($stored))) {
            unlink(self::path($stored));
        }
    }
}
