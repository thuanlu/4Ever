<?php
ob_start();
?>
<div class="container mt-4">
    <h2><i class="fas fa-chart-line me-2"></i>Kế hoạch xưởng</h2>
    <!-- Nội dung kế hoạch xưởng -->
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>