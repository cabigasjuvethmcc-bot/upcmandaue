<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_admin_or_editor();
require_once __DIR__ . '/../../includes/functions.php';

$pdo = get_pdo();

$groupId = isset($_GET['group_id']) && ctype_digit($_GET['group_id']) ? (int) $_GET['group_id'] : 0;
$memberIdFromQuery = isset($_GET['member_id']) && ctype_digit($_GET['member_id']) ? (int) $_GET['member_id'] : 0;

if ($groupId <= 0 && $memberIdFromQuery <= 0) {
    header('Location: members-list.php');
    exit;
}

$group = null;
$memberContext = null;

if ($groupId > 0) {
    $stmt = $pdo->prepare('SELECT id, name FROM bible_study_groups WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $groupId]);
    $group = $stmt->fetch();
}

if ($memberIdFromQuery > 0) {
    $stmt = $pdo->prepare('SELECT id, full_name FROM members WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $memberIdFromQuery]);
    $memberContext = $stmt->fetch();
}

if (!$group && !$memberContext) {
    header('Location: members-list.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? null;

    if (!verify_csrf_token($token)) {
        $errors[] = 'Invalid form token. Please try again.';
    } else {
        if (isset($_POST['add_member'])) {
            $targetGroupId = isset($_POST['group_id']) && ctype_digit($_POST['group_id']) ? (int) $_POST['group_id'] : 0;
            $memberId = isset($_POST['member_id']) && ctype_digit($_POST['member_id']) ? (int) $_POST['member_id'] : 0;
            $joinedAt = trim($_POST['joined_at'] ?? '');

            if ($targetGroupId <= 0 || $memberId <= 0) {
                $errors[] = 'Please choose both group and member.';
            }

            if ($joinedAt === '') {
                $errors[] = 'Joined date is required.';
            }

            if (!$errors) {
                $stmt = $pdo->prepare('INSERT INTO bible_study_group_members (group_id, member_id, joined_at) VALUES (:group_id, :member_id, :joined_at) ON DUPLICATE KEY UPDATE joined_at = VALUES(joined_at)');
                $stmt->execute([
                    'group_id' => $targetGroupId,
                    'member_id' => $memberId,
                    'joined_at' => $joinedAt,
                ]);

                header('Location: bible-study-group-members.php?group_id=' . $targetGroupId);
                exit;
            }
        } elseif (isset($_POST['remove_member']) && isset($_POST['membership_id']) && ctype_digit($_POST['membership_id'])) {
            $membershipId = (int) $_POST['membership_id'];
            $stmt = $pdo->prepare('DELETE FROM bible_study_group_members WHERE id = :id');
            $stmt->execute(['id' => $membershipId]);

            $redirectGroupId = $groupId > 0 ? $groupId : (isset($_POST['current_group_id']) && ctype_digit($_POST['current_group_id']) ? (int) $_POST['current_group_id'] : 0);
            if ($redirectGroupId > 0) {
                header('Location: bible-study-group-members.php?group_id=' . $redirectGroupId);
            } else {
                header('Location: members-list.php');
            }
            exit;
        }
    }
}

$stmt = $pdo->query('SELECT id, name FROM bible_study_groups ORDER BY name ASC');
$groups = $stmt->fetchAll();

$stmt = $pdo->query('SELECT id, full_name FROM members ORDER BY full_name ASC');
$members = $stmt->fetchAll();

$memberships = [];

if ($group) {
    $stmt = $pdo->prepare('SELECT gsm.id, m.full_name, gsm.joined_at FROM bible_study_group_members gsm JOIN members m ON gsm.member_id = m.id WHERE gsm.group_id = :group_id ORDER BY m.full_name ASC');
    $stmt->execute(['group_id' => $group['id']]);
    $memberships = $stmt->fetchAll();
}

include __DIR__ . '/../../includes/header.php';

?>

<section class="page">
    <h1>Bible Study Group Memberships</h1>

    <?php if ($group): ?>
        <h2>Group: <?= htmlspecialchars($group['name'], ENT_QUOTES) ?></h2>
    <?php endif; ?>

    <?php if ($memberContext): ?>
        <h2>Member: <?= htmlspecialchars($memberContext['full_name'], ENT_QUOTES) ?></h2>
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

    <h2>Add / Update Membership</h2>

    <form method="post" class="contact-form" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">

        <div class="form-group">
            <label for="group_id">Group</label>
            <select id="group_id" name="group_id" required>
                <option value="">-- Select Group --</option>
                <?php foreach ($groups as $g): ?>
                    <option value="<?= (int) $g['id'] ?>" <?= $group && $group['id'] === $g['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['name'], ENT_QUOTES) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="member_id">Member</label>
            <select id="member_id" name="member_id" required>
                <option value="">-- Select Member --</option>
                <?php foreach ($members as $m): ?>
                    <option value="<?= (int) $m['id'] ?>" <?= $memberContext && $memberContext['id'] === $m['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['full_name'], ENT_QUOTES) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="joined_at">Joined Date</label>
            <input type="date" id="joined_at" name="joined_at" required>
        </div>

        <button type="submit" name="add_member" class="btn-primary">Save Membership</button>
    </form>

    <?php if ($group): ?>
        <h2>Members in this Group</h2>

        <table class="pages-table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Joined At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($memberships as $ms): ?>
                <tr>
                    <td><?= htmlspecialchars($ms['full_name'], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($ms['joined_at'], ENT_QUOTES) ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">
                            <input type="hidden" name="membership_id" value="<?= (int) $ms['id'] ?>">
                            <input type="hidden" name="current_group_id" value="<?= (int) $group['id'] ?>">
                            <button type="submit" name="remove_member" class="btn-action">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="members-list.php">&larr; Back to Members</a></p>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
