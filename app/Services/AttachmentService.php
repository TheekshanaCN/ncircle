<?php
/**
 * app/Services/AttachmentService.php  -  takes files and a GIF from a post form.
 * prepare() checks everything first. save() runs only after the post exists, so a bad file saves nothing.
 */
class AttachmentService
{
    /** Returns ['files' => [...], 'gif' => row|null] or ['error' => message]. */
    public static function prepare(?array $upload, string $gifId): array
    {
        $c = config('upload.attachments');
        $files = [];
        $total = 0;
        foreach (self::normalise($upload) as $file) {
            if ($file['error'] === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $label = FileVerifier::cleanName((string) $file['name']);
            if (in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
                return ['error' => "\"{$label}\" is larger than the server allows."];
            }
            if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
                return ['error' => "\"{$label}\" didn't upload. Please try again."];
            }
            $checked = FileVerifier::check($file['tmp_name'], (string) $file['name'], (int) $file['size']);
            if (isset($checked['error'])) {
                return ['error' => $checked['error']];
            }
            $checked['tmp_name'] = $file['tmp_name'];
            $files[] = $checked;
            $total += (int) $file['size'];
        }
        if (count($files) > $c['max_files']) {
            return ['error' => 'You can share up to ' . $c['max_files'] . ' files in one post.'];
        }
        if ($total > $c['max_total']) {
            return ['error' => 'Files in one post can add up to ' . format_bytes($c['max_total']) . '.'];
        }

        $gif = null;
        if ($gifId !== '') {
            $gif = GiphyService::find($gifId);
            if ($gif === null) {
                return ['error' => "That GIF isn't available. Please choose another."];
            }
        }
        return ['files' => $files, 'gif' => $gif];
    }

    public static function hasContent(array $media): bool
    {
        return !empty($media['files']) || !empty($media['gif']);
    }

    public static function save(string $ownerType, int $ownerId, int $userId, array $media): void
    {
        $dir = (string) config('upload.attachments.dir');
        if (!empty($media['files']) && !is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Attachment folder could not be created.');
        }
        foreach ($media['files'] ?? [] as $file) {
            // A random name outside public/, so nothing uploaded can ever be run by the server.
            $stored = bin2hex(random_bytes(16)) . '.' . $file['ext'];
            if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $stored)) {
                throw new RuntimeException('Attachment could not be saved.');
            }
            Attachment::create($ownerType, $ownerId, $userId, $file['kind'], $file['original_name'], $stored, $file['mime'], (int) $file['size'], $file['width'], $file['height'], null);
        }
        if (!empty($media['gif'])) {
            $g = $media['gif'];
            Attachment::create($ownerType, $ownerId, $userId, 'gif', mb_substr($g['title'], 0, 200), null, 'image/gif', 0, $g['width'], $g['height'], $g['id']);
        }
    }

    /** PHP gives multi-file uploads as separate lists of names, sizes and so on. */
    private static function normalise(?array $upload): array
    {
        if ($upload === null || !isset($upload['name']) || !is_array($upload['name'])) {
            return [];
        }
        $files = [];
        foreach (array_keys($upload['name']) as $i) {
            $files[] = ['name' => $upload['name'][$i], 'tmp_name' => $upload['tmp_name'][$i], 'error' => $upload['error'][$i], 'size' => $upload['size'][$i]];
        }
        return $files;
    }
}
