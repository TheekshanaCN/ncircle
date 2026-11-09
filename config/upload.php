<?php
/** config/upload.php  -  note uploads. Files live outside public/ and are served by a controller. */
return [
    'max_size' => 10 * 1024 * 1024,
    'dir'      => BASE_PATH . '/storage/uploads/resources',
    'seed_dir' => BASE_PATH . '/database/seed-files',
    // Images and files on forum posts and group walls. Only these types are ever accepted.
    'attachments' => [
        'dir'        => BASE_PATH . '/storage/uploads/attachments',
        'max_files'  => 4,
        'max_total'  => 20 * 1024 * 1024,
        'image_max'  => 5 * 1024 * 1024,
        'file_max'   => 10 * 1024 * 1024,
        'max_pixels' => 40000000,
        'images' => ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'],
        'files'  => [
            'pdf'  => ['application/pdf'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream'],
            'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip', 'application/octet-stream'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/octet-stream'],
            'txt'  => ['text/plain'],
        ],
    ],
    // extension => accepted MIME types. Office files can be detected as plain zip.
    'types' => [
        'pdf'  => ['application/pdf'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip', 'application/octet-stream'],
    ],
];
