<?php
ob_start();
?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-body">
            <?php 
            // Form.php có logic hiển thị title, không cần title ở đây
            include APP_PATH . '/views/kehoachsanxuat/form.php'; 
            ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>