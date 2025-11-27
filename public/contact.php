<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

$page = get_page_by_slug('contact');

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? null;

    if (!verify_csrf_token($token)) {
        $errors[] = 'Invalid form token. Please try again.';
    } else {
        $name = sanitize_string($_POST['name'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ?: '';
        $subject = sanitize_string($_POST['subject'] ?? '');
        $message = sanitize_string($_POST['message'] ?? '');

        if ($name === '') {
            $errors[] = 'Name is required.';
        }

        if ($email === '') {
            $errors[] = 'Valid email is required.';
        }

        if ($subject === '') {
            $errors[] = 'Subject is required.';
        }

        if ($message === '') {
            $errors[] = 'Message is required.';
        }

        if (!$errors) {
            create_contact_message($name, $email, $subject, $message);
            $success = true;
        }
    }
}

include __DIR__ . '/../includes/header.php';

?>

<section class="page">
    <h1><?= htmlspecialchars($page['title'] ?? 'Contact', ENT_QUOTES) ?></h1>
    <div class="page-content">
        <?= $page !== null ? nl2br(htmlspecialchars($page['content'], ENT_QUOTES)) : 'Please reach out using the form below.'; ?>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">Thank you for your message. We will get back to you soon.</div>
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
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" required>
        </div>

        <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn-primary">Send Message</button>
    </form>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
