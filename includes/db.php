<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function get_pdo(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    global $config;

    $host = $config['db']['host'];
    $port = $config['db']['port'];
    $name = $config['db']['name'];
    $charset = $config['db']['charset'];
    $user = $config['db']['user'];
    $pass = $config['db']['pass'];

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);

    return $pdo;
}

