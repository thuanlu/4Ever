<?php
ob_start();
?>
<div class="container mt-4">
    <h2><i class="fas fa-file-alt me-2"></i>Theo dõi tiến độ</h2>
    <!-- Nội dung theo dõi tiến độ sản xuất -->
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>