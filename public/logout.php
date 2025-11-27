<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

logout();

global $config;
$baseUrl = $config['app']['base_url'] ?? '';
header('Location: ' . $baseUrl . '/index.php');
exit;
