<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

ensure_session_started();

$baseUrl = $config['app']['base_url'];

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPC Mandaue</title>
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="logo" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/index.php">UPC Mandaue</a>

        <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">
            <span class="nav-toggle-bar"></span>
            <span class="nav-toggle-bar"></span>
            <span class="nav-toggle-bar"></span>
        </button>

        <nav class="site-nav" aria-label="Main navigation">
            <ul>
                <li><a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/index.php">Home</a></li>
                <li><a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/about.php">About</a></li>
                <li><a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/contact.php">Contact</a></li>
                <?php if (is_logged_in()): ?>
                    <li><a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/admin/dashboard.php">Dashboard</a></li>
                    <li><a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main class="site-main container">
