<?php
ob_start();
?>
<div class="container mt-4">
    <h2><i class="fas fa-file-alt me-2"></i>Tạo phiếu kiểm tra</h2>
    <!-- Nội dung tạo phiếu kiểm tra -->
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>