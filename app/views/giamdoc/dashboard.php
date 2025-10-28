<?php
ob_start();
?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-3">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard Ban Giám Đốc
            <small class="text-muted">Tổng quan cho Ban Giám Đốc</small>
        </h2>
    </div>
</div>
<!-- Nội dung dashboard BGD riêng biệt ở đây -->
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>