<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_admin_or_editor();
require_once __DIR__ . '/../../includes/functions.php';

$pdo = get_pdo();

$memberId = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int) $_GET['id'] : 0;

if ($memberId <= 0) {
    header('Location: members-list.php');
    exit;
}

$stmt = $pdo->prepare('SELECT id, full_name FROM members WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $memberId]);
$member = $stmt->fetch();

if (!$member) {
    header('Location: members-list.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? null;

    if (!verify_csrf_token($token)) {
        $errors[] = 'Invalid form token. Please try again.';
    } else {
        if (isset($_POST['add_relationship'])) {
            $relatedId = isset($_POST['related_member_id']) && ctype_digit($_POST['related_member_id']) ? (int) $_POST['related_member_id'] : 0;
            $relationshipType = $_POST['relationship_type'] ?? '';

            if ($relatedId <= 0) {
                $errors[] = 'Please choose a related member.';
            }

            $validTypes = ['parent', 'child', 'spouse', 'sibling'];
            if (!in_array($relationshipType, $validTypes, true)) {
                $errors[] = 'Invalid relationship type.';
            }

            if ($relatedId === $memberId) {
                $errors[] = 'A member cannot be related to themselves.';
            }

            if (!$errors) {
                $stmt = $pdo->prepare('INSERT INTO member_relationships (member_id, related_member_id, relationship_type) VALUES (:member_id, :related_member_id, :relationship_type)');
                $stmt->execute([
                    'member_id' => $memberId,
                    'related_member_id' => $relatedId,
                    'relationship_type' => $relationshipType,
                ]);

                header('Location: member-relationships.php?id=' . $memberId);
                exit;
            }
        } elseif (isset($_POST['delete_relationship']) && isset($_POST['relationship_id']) && ctype_digit($_POST['relationship_id'])) {
            $relId = (int) $_POST['relationship_id'];
            $stmt = $pdo->prepare('DELETE FROM member_relationships WHERE id = :id AND member_id = :member_id');
            $stmt->execute([
                'id' => $relId,
                'member_id' => $memberId,
            ]);

            header('Location: member-relationships.php?id=' . $memberId);
            exit;
        }
    }
}

$stmt = $pdo->prepare('SELECT mr.id, mr.relationship_type, m.full_name AS related_name FROM member_relationships mr JOIN members m ON mr.related_member_id = m.id WHERE mr.member_id = :member_id ORDER BY mr.id ASC');
$stmt->execute(['member_id' => $memberId]);
$relationships = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT id, full_name FROM members WHERE id <> :id ORDER BY full_name ASC');
$stmt->execute(['id' => $memberId]);
$allMembers = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';

?>

<section class="page">
    <h1>Family Relationships for <?= htmlspecialchars($member['full_name'], ENT_QUOTES) ?></h1>

    <?php if ($errors): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <h2>Existing Relationships</h2>

    <table class="pages-table">
        <thead>
            <tr>
                <th>Related Member</th>
                <th>Relationship</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($relationships as $rel): ?>
            <tr>
                <td><?= htmlspecialchars($rel['related_name'], ENT_QUOTES) ?></td>
                <td><?= htmlspecialchars($rel['relationship_type'], ENT_QUOTES) ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">
                        <input type="hidden" name="relationship_id" value="<?= (int) $rel['id'] ?>">
                        <button type="submit" name="delete_relationship" class="btn-action">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Add Relationship</h2>

    <form method="post" class="contact-form" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">

        <div class="form-group">
            <label for="related_member_id">Related Member</label>
            <select id="related_member_id" name="related_member_id" required>
                <option value="">-- Select Member --</option>
                <?php foreach ($allMembers as $m): ?>
                    <option value="<?= (int) $m['id'] ?>"><?= htmlspecialchars($m['full_name'], ENT_QUOTES) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="relationship_type">Relationship Type</label>
            <select id="relationship_type" name="relationship_type" required>
                <option value="">-- Select Type --</option>
                <option value="parent">Parent</option>
                <option value="child">Child</option>
                <option value="spouse">Spouse</option>
                <option value="sibling">Sibling</option>
            </select>
        </div>

        <button type="submit" name="add_relationship" class="btn-primary">Add Relationship</button>
    </form>

    <p><a href="members-list.php">&larr; Back to Members</a></p>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
