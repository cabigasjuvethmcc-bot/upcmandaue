<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

$page = get_page_by_slug('home');

include __DIR__ . '/../includes/header.php';

?>

<section class="page">
    <h1><?= htmlspecialchars($page['title'] ?? 'Home', ENT_QUOTES) ?></h1>
    <div class="page-content">
        <?= $page !== null ? nl2br(htmlspecialchars($page['content'], ENT_QUOTES)) : 'Welcome to UPC Mandaue. Content is not yet configured.'; ?>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
