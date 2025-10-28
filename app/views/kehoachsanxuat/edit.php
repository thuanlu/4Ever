<<<<<<< HEAD
<?php include APP_PATH . '/views/kehoachsanxuat/form.php'; ?>
=======
<?php
// Tệp: app/views/kehoachsanxuat/edit.php

// --- BẮT ĐẦU SỬA LỖI ---
// Xóa toàn bộ logic kết nối và truy vấn CSDL ở đây.
// Dữ liệu ($kehoach, $donhangs) phải được truyền từ KeHoachSanXuatController::edit()

// Các biến này được Controller truyền sang:
// $kehoach (array)
// $donhangs (array)
// --- KẾT THÚC SỬA LỖI ---

// Thiết lập chế độ chỉnh sửa
$is_editing = true; 
$is_viewing = false;
$form_title = 'CHỈNH SỬA Kế hoạch Sản xuất: ' . ($kehoach['MaKeHoach'] ?? 'N/A');

ob_start();
?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-body">
            <?php 
            // Truyền biến is_editing, is_viewing, và dữ liệu $kehoach vào form.php
            // $donhangs cũng được truyền ngầm (vì nó đã được controller nạp)
            include APP_PATH . '/views/kehoachsanxuat/form.php'; 
            ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>
>>>>>>> origin/ke_hoach_san_xuat
