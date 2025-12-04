<?php
// Tệp: app/views/kehoachsanxuat/view.php

// --- BẮT ĐẦU SỬA LỖI ---
// Xóa toàn bộ logic kết nối và truy vấn CSDL ở đây.
// Dữ liệu ($kehoach, $donhangs) phải được truyền từ KeHoachSanXuatController::view()

// Các biến này được Controller truyền sang:
// $kehoach (array)
// $donhangs (array)
// --- KẾT THÚC SỬA LỖI ---

// Thiết lập chế độ XEM CHI TIẾT
$is_editing = false;
$is_viewing = true; 
$form_title = 'CHI TIẾT Kế hoạch Sản xuất: ' . ($kehoach['MaKeHoach'] ?? 'N/A');

// --- PHẦN HIỂN THỊ ---
ob_start();
?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-body">
            <?php 
            if (!empty($error_message)) {
                echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($error_message) . '</div>';
            }
            // Truyền biến is_viewing, is_editing, và dữ liệu $kehoach vào form.php
            // $donhangs cũng được truyền ngầm
            include APP_PATH . '/views/kehoachsanxuat/form.php'; 
            ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
// Giả định layout chính là main.php
include APP_PATH . '/views/layouts/main.php';
?>