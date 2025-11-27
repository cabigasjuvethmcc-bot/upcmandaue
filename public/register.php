<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? null;

    if (!verify_csrf_token($token)) {
        $errors[] = 'Invalid form token. Please try again.';
    } else {
        $name = sanitize_string($_POST['name'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ?: '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if ($name === '') {
            $errors[] = 'Name is required.';
        }

        if ($email === '') {
            $errors[] = 'Valid email is required.';
        }

        if ($password === '' || strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match.';
        }

        if (!$errors) {
            if (!register_user($name, $email, $password)) {
                $errors[] = 'Could not register. Email may already be in use.';
            } else {
                $success = true;
            }
        }
    }
}

include __DIR__ . '/../includes/header.php';

?>

<section class="page">
    <h1>Register</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">Registration successful. You can now log in.</div>
    <?php endif; ?>

    <?php if ($errors): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" class="contact-form" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="password_confirm">Confirm Password</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>

        <button type="submit" class="btn-primary">Register</button>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
