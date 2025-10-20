<?php
ob_start();
?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-3">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard Xưởng trưởng
        </h2>
    </div>
</div>
<!-- Nội dung dashboard XT/Admin riêng biệt ở đây -->
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>