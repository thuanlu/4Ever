<?php
// [THAY THẾ TOÀN BỘ FILE: app/views/kehoachsanxuat/view_nvl.php]

// Các biến $phieu, $chiTiet, $nhaCungCapList, $pageTitle
// đã được controller cung cấp trong hàm view()

ob_start();

// --- Controller Đã Cung Cấp ---
// 1. $phieu (Thông tin phiếu chính)
// 2. $chiTiet (Danh sách chi tiết NVL đã đặt)
// 3. $nhaCungCapList (Danh sách tất cả NCC)
// 4. $pageTitle
// ----------------------------------

// Thiết lập biến cho form chung
$isView = true; // RẤT QUAN TRỌNG: Báo cho form_nvl.php là chế độ CHỈ XEM

// Gọi form chung
// Các biến $phieu, $chiTiet, $nhaCungCapList...
// sẽ được tự động truyền vào form_nvl.php
include 'form_nvl.php';

$content = ob_get_clean();
require_once APP_PATH . '/views/layouts/main.php';
?>