<?php
ob_start();
?>
<div class="container mt-4">
    <h2><i class="fas fa-tasks me-2"></i>Xem Kế hoạch sản xuất</h2>
    <!-- Nội dung xem kế hoạch xưởng trưởng -->
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>