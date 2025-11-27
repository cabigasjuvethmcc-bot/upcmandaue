<?php

declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth.php';

require_admin_or_editor();

include __DIR__ . '/../../includes/header.php';

?>

<section class="page dashboard-layout">
    <header class="dashboard-header">
        <h1 class="dashboard-header-title">Admin Dashboard</h1>
        <p class="dashboard-header-subtitle">
            Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User', ENT_QUOTES) ?>.
        </p>
    </header>

    <div class="dashboard-grid">
        <article class="dashboard-card">
            <h2>Content Management</h2>
            <p class="page-content">From here you can manage site pages and, later, posts and contact messages.</p>
            <ul class="dashboard-links">
                <li><a class="dashboard-link" href="pages-list.php">Edit Pages (home, about, contact)</a></li>
            </ul>
            <p class="dashboard-meta">More tools like posts and contact messages will appear here as the site grows.</p>
        </article>
        <article class="dashboard-card">
            <h2>Member Management</h2>
            <p class="page-content">Manage members, their families, and their Bible Study Groups.</p>
            <ul class="dashboard-links">
                <li><a class="dashboard-link" href="members-list.php">Members</a></li>
                <li><a class="dashboard-link" href="bible-study-groups.php">Bible Study Groups</a></li>
            </ul>
        </article>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
