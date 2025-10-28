
<?php

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'app/Router.php';
require_once 'app/controllers/BaseController.php';

// Khởi tạo router
$router = new Router();

// Định nghĩa các routes
$router->addRoute('GET', '/home', 'HomeController', 'index');
$router->addRoute('GET', '/dashboard', 'HomeController', 'index');
$router->addRoute('GET', '/login', 'AuthController', 'showLogin');
$router->addRoute('POST', '/login', 'AuthController', 'processLogin');
$router->addRoute('GET', '/logout', 'AuthController', 'logout');

// Routes cho dashboard các vai trò
$router->addRoute('GET', '/giamdoc/dashboard', 'DashboardController', 'bgd');
$router->addRoute('GET', '/kehoachsanxuat/dashboard', 'DashboardController', 'kh');
$router->addRoute('GET', '/xuongtruong/dashboard', 'DashboardController', 'xt');
$router->addRoute('GET', '/totruong/dashboard', 'DashboardController', 'tt');
$router->addRoute('GET', '/qc/dashboard', 'DashboardController', 'qc');
$router->addRoute('GET', '/kho/dashboard', 'DashboardController', 'nvk');
$router->addRoute('GET', '/congnhan/dashboard', 'DashboardController', 'cn');

// Routes cho xưởng trưởng
$router->addRoute('GET', '/xuongtruong/xemkehoachsanxuat', 'XuongTruongXemKeHoachSanXuatController', 'index');
$router->addRoute('GET', '/xuongtruong/lapkehoachcapxuong', 'LapKeHoachCapXuongController', 'index');
$router->addRoute('GET', '/xuongtruong/lapkehoachcapxuong/create/(.*)', 'LapKeHoachCapXuongController', 'create');
$router->addRoute('POST', '/xuongtruong/lapkehoachcapxuong/store', 'LapKeHoachCapXuongController', 'store');


// Routes cho quản lý kế hoạch sản xuất (KeHoachSanXuat)
$router->addRoute('GET', '/kehoachsanxuat', 'KeHoachSanXuatController', 'index');
$router->addRoute('GET', '/kehoachsanxuat/create', 'KeHoachSanXuatController', 'create');
$router->addRoute('POST', '/kehoachsanxuat/create', 'KeHoachSanXuatController', 'create');
$router->addRoute('GET', '/kehoachsanxuat/edit/(.*)', 'KeHoachSanXuatController', 'edit');
$router->addRoute('POST', '/kehoachsanxuat/edit/(.*)', 'KeHoachSanXuatController', 'edit');
$router->addRoute('GET', '/kehoachsanxuat/delete/(.*)', 'KeHoachSanXuatController', 'delete');
$router->addRoute('GET', '/kehoachsanxuat/view/(.*)', 'KeHoachSanXuatController', 'view');

// 
// ===== DÒNG BỊ THIẾU GÂY LỖI 404 ĐÃ ĐƯỢC THÊM VÀO ĐÂY =====
//
// Route cho API (AJAX) lấy chi tiết đơn hàng
$router->addRoute('GET', '/kehoachsanxuat/getDonHangDetails/(.*)', 'KeHoachSanXuatController', 'getDonHangDetails');
//

// Routes cho quản lý xưởng
$router->addRoute('GET', '/workshops', 'WorkshopController', 'index');
$router->addRoute('GET', '/workshops/assignments', 'WorkshopController', 'assignments');
$router->addRoute('POST', '/workshops/assign', 'WorkshopController', 'assign');

// Routes: Tạo phiếu / Quản lý yêu cầu xuất nguyên liệu (Xưởng trưởng)
$router->addRoute('GET', '/yeucauxuat', 'YeuCauXuatController', 'index');
$router->addRoute('POST', '/yeucauxuat/save', 'YeuCauXuatController', 'save');
$router->addRoute('GET', '/yeucauxuat/list', 'YeuCauXuatController', 'list');

// Routes cho quản lý nguyên vật liệu
$router->addRoute('GET', '/materials', 'MaterialController', 'index');
$router->addRoute('GET', '/materials/orders', 'MaterialController', 'orders');
$router->addRoute('POST', '/materials/order', 'MaterialController', 'createOrder');

// Routes cho Quality Control
$router->addRoute('GET', '/quality-control', 'QualityController', 'index');
$router->addRoute('POST', '/quality-control/check', 'QualityController', 'performCheck');

// Routes cho quản lý nhân viên và chấm công
$router->addRoute('GET', '/employees', 'EmployeeController', 'index');
$router->addRoute('GET', '/attendance', 'AttendanceController', 'index');
$router->addRoute('POST', '/attendance/checkin', 'AttendanceController', 'checkIn');
$router->addRoute('POST', '/attendance/checkout', 'AttendanceController', 'checkOut');

// Routes cho báo cáo
$router->addRoute('GET', '/reports', 'ReportController', 'index');
$router->addRoute('GET', '/reports/production', 'ReportController', 'production');
$router->addRoute('GET', '/reports/attendance', 'ReportController', 'attendance');

// Routes cho Nhập Kho Thành Phẩm
$router->addRoute('GET', '/nhapkho', 'NhapKhoController', 'index');
$router->addRoute('POST', '/nhapkho/confirm', 'NhapKhoController', 'confirmImport');
$router->addRoute('POST', '/nhapkho/confirm-multi', 'NhapKhoController', 'confirmImportMulti');
$router->addRoute('GET', '/nhapkho/detail', 'NhapKhoController', 'getDetail');


// Xử lý request
$router->dispatch();
?>
