<?php
/**
 * app/Services/UploadService.php  -  validates and stores note files.
 * Files keep a random name outside public/, so nothing uploaded can ever be executed by the server.
 */
class UploadService
{
    /** Returns ['error' => message] or the stored file's details. */
    public static function store(?array $file): array
    {
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['error' => 'Choose a file to upload.'];
        }
        $maxLabel = format_bytes((int) config('upload.max_size'));
        if (in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            return ['error' => "The file is larger than the server allows (limit {$maxLabel})."];
        }
        if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
            return ['error' => 'The upload failed. Please try again.'];
        }
        if ((int) $file['size'] > (int) config('upload.max_size')) {
            return ['error' => "The file is larger than {$maxLabel}."];
        }
        if ((int) $file['size'] === 0) {
            return ['error' => 'That file is empty.'];
        }

        $types = config('upload.types');
        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!isset($types[$ext]) || !in_array($mime, $types[$ext], true) || !self::contentMatches($file['tmp_name'], $ext)) {
            return ['error' => 'Only real PDF, DOCX or PPTX files can be uploaded.'];
        }

        $dir = (string) config('upload.dir');
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Upload folder could not be created.');
        }
        $stored = bin2hex(random_bytes(16)) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $stored)) {
            throw new RuntimeException('Uploaded file could not be saved.');
        }
        return [
            'file_path'     => $stored,
            'original_name' => basename((string) $file['name']),
            'file_type'     => $ext,
            'file_size'     => (int) $file['size'],
        ];
    }

    /** The same content checks as attachments: real format, no scripts or macros inside. */
    private static function contentMatches(string $path, string $ext): bool
    {
        return FileVerifier::contentError($path, $ext) === null;
    }

    /** Full path on disk. Seed notes ('seed/x.pdf') ship with the repo. */
    public static function path(string $stored): string
    {
        if (str_starts_with($stored, 'seed/')) {
            return config('upload.seed_dir') . '/' . basename($stored);
        }
        return config('upload.dir') . '/' . basename($stored);
    }

    public static function delete(string $stored): void
    {
        if (!str_starts_with($stored, 'seed/') && is_file(self::path($stored))) {
            unlink(self::path($stored));
        }
    }
}
