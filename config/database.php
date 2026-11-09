<?php
/** config/database.php  -  values come from .env */
return [
    'host' => env_value('DB_HOST', '127.0.0.1'),
    'port' => env_value('DB_PORT', '3306'),
    'name' => env_value('DB_NAME', 'ncircle'),
    'user' => env_value('DB_USER', 'root'),
    'pass' => env_value('DB_PASS', ''),
];
