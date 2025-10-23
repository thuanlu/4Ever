<?php
ob_start();
?>
<div class="container mt-4">
    <h2><i class="fas fa-file-alt me-2"></i>Quản lý yêu cầu xuất nguyên liệu</h2>
    <!-- Nội dung quản lý yêu cầu xuất nguyên liệu -->
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>