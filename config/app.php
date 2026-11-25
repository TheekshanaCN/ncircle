<?php
/** config/app.php  -  site-wide lists and settings */
return [
    'name'     => env_value('APP_NAME', 'NCircle'),
    'timezone' => 'Asia/Colombo',
    'student_email_domain' => 'students.nsbm.ac.lk',
    'faculties' => [
        'Computing'   => 'Faculty of Computing',
        'Business'    => 'Faculty of Business',
        'Engineering' => 'Faculty of Engineering',
        'Science'     => 'Faculty of Science',
    ],
    'years' => [1 => 'Year 1', 2 => 'Year 2', 3 => 'Year 3', 4 => 'Year 4'],
    'session_modes' => [
        'online'   => 'Online',
        'physical' => 'Physical',
        'hybrid'   => 'Hybrid',
    ],
    'help_types' => [
        'research' => 'Research',
        'project'  => 'Project',
        'tutoring' => 'Tutoring',
        'other'    => 'Other help',
    ],
    'giphy' => [
        'key'  => env_value('GIPHY_API_KEY', ''),
        'base' => env_value('GIPHY_API_BASE', 'https://api.giphy.com/v1'),
    ],
    'mail' => [
        'driver' => env_value('MAIL_DRIVER', 'log'), // log | resend | mail
        'from_address' => env_value('MAIL_FROM_ADDRESS', 'no-reply@ncircle.local'),
        'from_name'    => env_value('MAIL_FROM_NAME', 'NCircle'),
        'resend' => [
            'key'  => env_value('RESEND_API_KEY', ''),
            'base' => env_value('RESEND_API_BASE', 'https://api.resend.com'),
        ],
    ],
];
