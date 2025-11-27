<?php

declare(strict_types=1);

$dbHost = getenv('DB_HOST') !== false ? getenv('DB_HOST') : '127.0.0.1';
$dbPort = getenv('DB_PORT') !== false ? getenv('DB_PORT') : '3306';
$dbName = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'upc_mandaue';
$dbUser = getenv('DB_USER') !== false ? getenv('DB_USER') : 'root';
$dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

$config = [
    'db' => [
        'host' => $dbHost,
        'port' => $dbPort,
        'name' => $dbName,
        'user' => $dbUser,
        'pass' => $dbPass,
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'base_url' => getenv('APP_BASE_URL') !== false ? rtrim(getenv('APP_BASE_URL'), '/') : 'http://localhost/upcmandaue/public'
    ]
];

