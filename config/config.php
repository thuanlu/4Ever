<?php
/**
 * Cấu hình ứng dụng chính
 * Hệ thống quản lý sản xuất nhà máy 4Ever
 */

// Cấu hình ứng dụng
define('APP_NAME', 'Hệ thống Quản lý Sản xuất Nhà máy');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/4Ever/');

// Cấu hình đường dẫn
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('CONFIG_PATH', ROOT_PATH . '/config');

// Cấu hình phiên làm việc
define('SESSION_TIMEOUT', 3600); // 1 giờ

// Cấu hình múi giờ
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Cấu hình hiển thị lỗi (chỉ trong môi trường phát triển)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cấu hình debug
define('DEBUG_LOGIN', true); // Bật debug cho đăng nhập

// Khởi động session
session_start();
?>
