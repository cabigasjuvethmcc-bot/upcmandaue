<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

require_login();

require_once __DIR__ . '/../../includes/functions.php';

$pdo = get_pdo();

$stmt = $pdo->query('SELECT id, slug, title, updated_at FROM pages ORDER BY id ASC');
$pages = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';

?>

<section class="page">
    <h1>Pages</h1>
    <p>Click a page to edit its title and content.</p>

    <table class="pages-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Slug</th>
                <th>Title</th>
                <th>Last Updated</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($pages as $page): ?>
            <tr>
                <td><?= (int) $page['id'] ?></td>
                <td><?= htmlspecialchars($page['slug'], ENT_QUOTES) ?></td>
                <td><?= htmlspecialchars($page['title'], ENT_QUOTES) ?></td>
                <td><?= htmlspecialchars($page['updated_at'] ?? '', ENT_QUOTES) ?></td>
                <td>
                    <a class="btn-action" href="pages-edit.php?id=<?= (int) $page['id'] ?>">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
