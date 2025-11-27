<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_admin_or_editor();
require_once __DIR__ . '/../../includes/functions.php';

$pdo = get_pdo();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? null;

    if (!verify_csrf_token($token)) {
        $errors[] = 'Invalid form token. Please try again.';
    } else {
        $name = sanitize_string($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $leaderId = isset($_POST['leader_member_id']) && ctype_digit($_POST['leader_member_id']) ? (int) $_POST['leader_member_id'] : null;

        if ($name === '') {
            $errors[] = 'Group name is required.';
        }

        if (!$errors) {
            $stmt = $pdo->prepare('INSERT INTO bible_study_groups (name, description, leader_member_id) VALUES (:name, :description, :leader_member_id)');
            $stmt->execute([
                'name' => $name,
                'description' => $description !== '' ? $description : null,
                'leader_member_id' => $leaderId,
            ]);

            header('Location: bible-study-groups.php');
            exit;
        }
    }
}

$stmt = $pdo->query('SELECT id, full_name FROM members ORDER BY full_name ASC');
$members = $stmt->fetchAll();

$stmt = $pdo->query('SELECT g.id, g.name, g.description, m.full_name AS leader_name FROM bible_study_groups g LEFT JOIN members m ON g.leader_member_id = m.id ORDER BY g.name ASC');
$groups = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';

?>

<section class="page">
    <h1>Bible Study Groups</h1>

    <?php if ($errors): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <h2>Create New Group</h2>

    <form method="post" class="contact-form" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">

        <div class="form-group">
            <label for="name">Group Name</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label for="leader_member_id">Leader (optional)</label>
            <select id="leader_member_id" name="leader_member_id">
                <option value="">-- Select Leader --</option>
                <?php foreach ($members as $m): ?>
                    <option value="<?= (int) $m['id'] ?>"><?= htmlspecialchars($m['full_name'], ENT_QUOTES) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn-primary">Create Group</button>
    </form>

    <h2>Existing Groups</h2>

    <table class="pages-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Leader</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($groups as $group): ?>
            <tr>
                <td><?= htmlspecialchars($group['name'], ENT_QUOTES) ?></td>
                <td><?= htmlspecialchars($group['leader_name'] ?? '', ENT_QUOTES) ?></td>
                <td><?= htmlspecialchars($group['description'] ?? '', ENT_QUOTES) ?></td>
                <td>
                    <a class="btn-action" href="bible-study-group-members.php?group_id=<?= (int) $group['id'] ?>">Members</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="dashboard.php">&larr; Back to Dashboard</a></p>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
