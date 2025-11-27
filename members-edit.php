<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';
require_login();
require_once __DIR__ . '/../../includes/functions.php';

$pdo = get_pdo();

$id = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int) $_GET['id'] : 0;

$member = null;
if ($id > 0) {
    $stmt = $pdo->prepare('SELECT id, full_name, birthday, water_baptism_date, holy_ghost_baptism_date FROM members WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $member = $stmt->fetch();

    if (!$member) {
        header('Location: members-list.php');
        exit;
    }
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? null;

    if (!verify_csrf_token($token)) {
        $errors[] = 'Invalid form token. Please try again.';
    } else {
        $fullName = sanitize_string($_POST['full_name'] ?? '');
        $birthday = trim($_POST['birthday'] ?? '');
        $waterBaptismDate = trim($_POST['water_baptism_date'] ?? '');
        $holyGhostBaptismDate = trim($_POST['holy_ghost_baptism_date'] ?? '');

        if ($fullName === '') {
            $errors[] = 'Full name is required.';
        }

        $birthday = $birthday !== '' ? $birthday : null;
        $waterBaptismDate = $waterBaptismDate !== '' ? $waterBaptismDate : null;
        $holyGhostBaptismDate = $holyGhostBaptismDate !== '' ? $holyGhostBaptismDate : null;

        if (!$errors) {
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE members SET full_name = :full_name, birthday = :birthday, water_baptism_date = :water_baptism_date, holy_ghost_baptism_date = :holy_ghost_baptism_date, updated_at = NOW() WHERE id = :id');
                $stmt->execute([
                    'full_name' => $fullName,
                    'birthday' => $birthday,
                    'water_baptism_date' => $waterBaptismDate,
                    'holy_ghost_baptism_date' => $holyGhostBaptismDate,
                    'id' => $id,
                ]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO members (full_name, birthday, water_baptism_date, holy_ghost_baptism_date) VALUES (:full_name, :birthday, :water_baptism_date, :holy_ghost_baptism_date)');
                $stmt->execute([
                    'full_name' => $fullName,
                    'birthday' => $birthday,
                    'water_baptism_date' => $waterBaptismDate,
                    'holy_ghost_baptism_date' => $holyGhostBaptismDate,
                ]);
                $id = (int) $pdo->lastInsertId();
            }

            $success = true;

            $stmt = $pdo->prepare('SELECT id, full_name, birthday, water_baptism_date, holy_ghost_baptism_date FROM members WHERE id = :id LIMIT 1');
            $stmt->execute(['id' => $id]);
            $member = $stmt->fetch();
        }
    }
}

include __DIR__ . '/../../includes/header.php';

?>

<section class="page">
    <h1><?= $id > 0 ? 'Edit Member' : 'Add Member' ?></h1>

    <?php if ($success): ?>
        <div class="alert alert-success">Member saved successfully.</div>
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
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" required value="<?= htmlspecialchars($member['full_name'] ?? '', ENT_QUOTES) ?>">
        </div>

        <div class="form-group">
            <label for="birthday">Birthday</label>
            <input type="date" id="birthday" name="birthday" value="<?= htmlspecialchars($member['birthday'] ?? '', ENT_QUOTES) ?>">
        </div>

        <div class="form-group">
            <label for="water_baptism_date">Water Baptism Date</label>
            <input type="date" id="water_baptism_date" name="water_baptism_date" value="<?= htmlspecialchars($member['water_baptism_date'] ?? '', ENT_QUOTES) ?>">
        </div>

        <div class="form-group">
            <label for="holy_ghost_baptism_date">Holy Ghost Baptism Date</label>
            <input type="date" id="holy_ghost_baptism_date" name="holy_ghost_baptism_date" value="<?= htmlspecialchars($member['holy_ghost_baptism_date'] ?? '', ENT_QUOTES) ?>">
        </div>

        <button type="submit" class="btn-primary">Save Member</button>
    </form>

    <p><a href="members-list.php">&larr; Back to Members</a></p>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
