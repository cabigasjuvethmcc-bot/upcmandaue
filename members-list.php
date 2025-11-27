<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_once __DIR__ . '/../../includes/functions.php';

$pdo = get_pdo();

$stmt = $pdo->query('SELECT id, full_name, birthday, water_baptism_date, holy_ghost_baptism_date FROM members ORDER BY full_name ASC');
$members = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';

?>

<section class="page">
    <h1>Members</h1>
    <p>Manage member personal information and relationships.</p>

    <p><a class="btn-primary" href="members-edit.php">Add New Member</a></p>

    <table class="pages-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Birthday</th>
                <th>Water Baptism</th>
                <th>Holy Ghost Baptism</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($members as $member): ?>
            <tr>
                <td><?= (int) $member['id'] ?></td>
                <td><?= htmlspecialchars($member['full_name'], ENT_QUOTES) ?></td>
                <td><?= htmlspecialchars($member['birthday'] ?? '', ENT_QUOTES) ?></td>
                <td><?= htmlspecialchars($member['water_baptism_date'] ?? '', ENT_QUOTES) ?></td>
                <td><?= htmlspecialchars($member['holy_ghost_baptism_date'] ?? '', ENT_QUOTES) ?></td>
                <td>
                    <a class="btn-action" href="members-edit.php?id=<?= (int) $member['id'] ?>">Edit</a>
                    <a class="btn-action" href="member-relationships.php?id=<?= (int) $member['id'] ?>">Family</a>
                    <a class="btn-action" href="bible-study-group-members.php?member_id=<?= (int) $member['id'] ?>">Groups</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
