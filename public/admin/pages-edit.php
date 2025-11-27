<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

require_login();

require_once __DIR__ . '/../../includes/functions.php';

$pdo = get_pdo();

$id = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: pages-list.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, slug, title, content FROM pages WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$page = $stmt->fetch();

if (!$page) {
    header('Location: pages-list.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? null;

    if (!verify_csrf_token($token)) {
        $errors[] = 'Invalid form token. Please try again.';
    } else {
        $title = sanitize_string($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '') {
            $errors[] = 'Title is required.';
        }

        if ($content === '') {
            $errors[] = 'Content is required.';
        }

        if (!$errors) {
            $updateStmt = $pdo->prepare('UPDATE pages SET title = :title, content = :content, updated_at = NOW() WHERE id = :id');
            $updateStmt->execute([
                'title' => $title,
                'content' => $content,
                'id' => $id,
            ]);

            $success = true;

            $stmt = $pdo->prepare('SELECT id, slug, title, content FROM pages WHERE id = :id LIMIT 1');
            $stmt->execute(['id' => $id]);
            $page = $stmt->fetch();
        }
    }
}

include __DIR__ . '/../../includes/header.php';

?>

<section class="page">
    <h1>Edit Page: <?= htmlspecialchars($page['slug'], ENT_QUOTES) ?></h1>

    <?php if ($success): ?>
        <div class="alert alert-success">Page updated successfully.</div>
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
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required value="<?= htmlspecialchars($page['title'], ENT_QUOTES) ?>">
        </div>

        <div class="form-group">
            <label for="content">Content</label>
            <textarea id="content" name="content" rows="10" required><?= htmlspecialchars($page['content'], ENT_QUOTES) ?></textarea>
        </div>

        <button type="submit" class="btn-primary">Save Changes</button>
    </form>

    <p><a href="pages-list.php">&larr; Back to Pages</a></p>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
