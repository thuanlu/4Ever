<?php
// [FILE CẬP NHẬT: app/views/kehoachsanxuat/create_nvl.php]

$pageTitle = "Tạo Phiếu Đặt Hàng NVL";
ob_start();

// --- Controller Đã Cung Cấp ---
// 1. $kehoach_list (Danh sách KHSX bị thiếu NVL)
// 2. $nhaCungCapList (Danh sách tất cả NCC)
// 3. $currentUserName (Tên người dùng)
// 4. $phieu, $chiTiet (rỗng)
// ----------------------------------

// Thiết lập các biến cho form
$isView = false; // Form này dùng để TẠO MỚI

// Gọi form chung
// Các biến $kehoach_list, $nhaCungCapList, $currentUserName...
// sẽ được tự động truyền vào form_nvl.php
include 'form_nvl.php';

$content = ob_get_clean();
require_once APP_PATH . '/views/layouts/main.php';
?>