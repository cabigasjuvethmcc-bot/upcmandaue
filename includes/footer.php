<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

$baseUrl = $config['app']['base_url'];

?>
</main>

<footer class="site-footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> UPC Mandaue. All rights reserved.</p>
    </div>
</footer>

<script src="<?= htmlspecialchars($baseUrl, ENT_QUOTES) ?>/assets/js/app.js"></script>
</body>
</html>
