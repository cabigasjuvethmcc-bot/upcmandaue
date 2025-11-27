<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function current_user_id(): ?int
{
    ensure_session_started();

    if (isset($_SESSION['user_id']) && is_int($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    }

    if (isset($_SESSION['user_id']) && ctype_digit((string) $_SESSION['user_id'])) {
        return (int) $_SESSION['user_id'];
    }

    return null;
}

function is_logged_in(): bool
{
    return current_user_id() !== null;
}

function require_login(): void
{
    global $config;

    if (!is_logged_in()) {
        $baseUrl = $config['app']['base_url'];
        header('Location: ' . $baseUrl . '/login.php');
        exit;
    }
}

function find_user_by_email(string $email): ?array
{
    $pdo = get_pdo();

    $stmt = $pdo->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);

    $user = $stmt->fetch();

    if ($user === false) {
        return null;
    }

    return $user;
}

function register_user(string $name, string $email, string $password): bool
{
    $pdo = get_pdo();

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)');

    try {
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => $hash,
        ]);
    } catch (PDOException $e) {
        return false;
    }

    return true;
}

function attempt_login(string $email, string $password): bool
{
    $user = find_user_by_email($email);

    if ($user === null) {
        return false;
    }

    if (!password_verify($password, $user['password_hash'])) {
        return false;
    }

    ensure_session_started();

    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];

    return true;
}

function logout(): void
{
    ensure_session_started();

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], isset($params['secure']) && $params['secure'], isset($params['httponly']) && $params['httponly']);
    }

    session_destroy();
}

