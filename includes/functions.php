<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function ensure_session_started(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function csrf_token(): string
{
    ensure_session_started();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool
{
    ensure_session_started();

    if (empty($_SESSION['csrf_token']) || $token === null) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

function sanitize_string(string $value): string
{
    return trim(filter_var($value, FILTER_SANITIZE_STRING));
}

function get_page_by_slug(string $slug): ?array
{
    $pdo = get_pdo();

    $stmt = $pdo->prepare('SELECT title, content FROM pages WHERE slug = :slug LIMIT 1');
    $stmt->execute(['slug' => $slug]);

    $page = $stmt->fetch();

    if ($page === false) {
        return null;
    }

    return $page;
}

function create_contact_message(string $name, string $email, string $subject, string $message): void
{
    $pdo = get_pdo();

    $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)');
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
    ]);
}

