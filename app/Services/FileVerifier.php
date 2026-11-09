<?php
/**
 * app/Services/FileVerifier.php  -  decides whether an uploaded file really is what it claims to be.
 *
 * Only a short list of types is accepted, and each is checked by its contents, never by its name alone.
 * This greatly reduces risk, but it is not an antivirus: it cannot spot every malicious document.
 */
class FileVerifier
{
    /** Refused anywhere in a name, so "notes.php.pdf" and "virus.exe.txt" never get through. */
    private const DANGEROUS = ['php', 'phtml', 'phar', 'exe', 'bat', 'cmd', 'com', 'scr', 'msi', 'dll', 'sh', 'js', 'jse', 'vbs', 'vbe', 'wsf', 'ps1', 'jar', 'apk', 'hta', 'html', 'htm', 'xhtml', 'svg', 'lnk', 'reg', 'cgi', 'pl', 'py'];

    /** Returns ['kind','ext','mime','width','height','size','original_name'] or ['error' => message]. */
    public static function check(string $path, string $name, int $size): array
    {
        $c = config('upload.attachments');
        $name = self::cleanName($name);
        $parts = explode('.', strtolower($name));
        $ext = count($parts) > 1 ? (string) end($parts) : '';

        if (array_intersect(array_slice($parts, 1), self::DANGEROUS)) {
            return ['error' => "\"{$name}\" looks like a program, so it can't be shared."];
        }
        if (!isset($c['images'][$ext]) && !isset($c['files'][$ext])) {
            return ['error' => "\"{$name}\" isn't allowed. Share JPG, PNG, GIF, WEBP, PDF, DOCX, PPTX, XLSX or TXT files."];
        }
        if ($size === 0) {
            return ['error' => "\"{$name}\" is empty."];
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);

        if (isset($c['images'][$ext])) {
            if ($size > $c['image_max']) {
                return ['error' => "\"{$name}\" is larger than " . format_bytes($c['image_max']) . '.'];
            }
            $info = @getimagesize($path);
            if ($mime !== $c['images'][$ext] || $info === false || $info['mime'] !== $c['images'][$ext]) {
                return ['error' => "\"{$name}\" isn't a real image."];
            }
            if ($info[0] * $info[1] > $c['max_pixels'] || $info[0] > 65000 || $info[1] > 65000) {
                return ['error' => "\"{$name}\" is too large in pixels."];
            }
            return ['kind' => 'image', 'ext' => $ext, 'mime' => $c['images'][$ext], 'width' => $info[0], 'height' => $info[1], 'size' => $size, 'original_name' => $name];
        }

        if ($size > $c['file_max']) {
            return ['error' => "\"{$name}\" is larger than " . format_bytes($c['file_max']) . '.'];
        }
        if (!in_array($mime, $c['files'][$ext], true)) {
            return ['error' => "\"{$name}\" doesn't match its file type."];
        }
        $problem = self::contentError($path, $ext);
        if ($problem !== null) {
            return ['error' => "\"{$name}\": {$problem}"];
        }
        return ['kind' => 'file', 'ext' => $ext, 'mime' => $c['files'][$ext][0], 'width' => null, 'height' => null, 'size' => $size, 'original_name' => $name];
    }

    /** Null when the contents are fine, otherwise the reason they are not. */
    public static function contentError(string $path, string $ext): ?string
    {
        return match ($ext) {
            'pdf' => self::pdfError($path),
            'docx' => self::officeError($path, 'word/document.xml'),
            'pptx' => self::officeError($path, 'ppt/presentation.xml'),
            'xlsx' => self::officeError($path, 'xl/workbook.xml'),
            'txt' => self::textError($path),
            default => 'this file type is not allowed.',
        };
    }

    private static function pdfError(string $path): ?string
    {
        $data = (string) file_get_contents($path);
        if (!str_starts_with($data, '%PDF-')) {
            return "it isn't a real PDF.";
        }
        // PDF lets names be disguised as hex, such as /J#61vaScript, so undo that before looking.
        $plain = preg_replace_callback('/#([0-9a-fA-F]{2})/', fn ($m) => chr((int) hexdec($m[1])), $data) ?? $data;
        if (preg_match('#/(JavaScript|JS|Launch|EmbeddedFile|RichMedia|XFA)\b#', $plain)) {
            return "PDFs with scripts or embedded files can't be shared.";
        }
        return null;
    }

    private static function officeError(string $path, string $requiredEntry): ?string
    {
        if (!class_exists('ZipArchive')) {
            return "this server can't check Office files yet.";
        }
        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::RDONLY) !== true) {
            return "it isn't a real Office document.";
        }
        $found = false;
        $total = 0;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $entry = strtolower((string) $stat['name']);
            $total += (int) $stat['size'];
            if ($entry === strtolower($requiredEntry)) {
                $found = true;
            }
            if (str_contains($entry, 'vbaproject') || str_contains($entry, 'activex') || preg_match('#embeddings/.*\.(bin|exe)$#', $entry)) {
                $zip->close();
                return "documents with macros or embedded programs can't be shared.";
            }
            if (str_contains($entry, '..') || str_starts_with($entry, '/')) {
                $zip->close();
                return "it isn't a real Office document.";
            }
        }
        $hasTypes = $zip->locateName('[Content_Types].xml') !== false;
        $zip->close();
        if (!$found || !$hasTypes) {
            return "it isn't a real Office document.";
        }
        if ($total > 200 * 1024 * 1024) {   // a "zip bomb" expands to far more than a real document
            return 'it expands to an unusually large size.';
        }
        return null;
    }

    private static function textError(string $path): ?string
    {
        $data = (string) file_get_contents($path);
        return str_contains($data, "\0") || !mb_check_encoding($data, 'UTF-8') ? "it isn't plain text." : null;
    }

    /** Keeps the name readable but safe to show and to send in a download header. */
    public static function cleanName(string $name): string
    {
        $name = basename(str_replace('\\', '/', $name));
        $name = (string) preg_replace('/[\x00-\x1F\x7F"<>:|?*]+/u', '', $name);
        return mb_substr(trim($name), -200) ?: 'file';
    }
}
