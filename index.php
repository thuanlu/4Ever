

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

// Routes cho quản lý xưởng
$router->addRoute('GET', '/workshops', 'WorkshopController', 'index');
$router->addRoute('GET', '/workshops/assignments', 'WorkshopController', 'assignments');
$router->addRoute('POST', '/workshops/assign', 'WorkshopController', 'assign');

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


//Routes cho QC
$router->addRoute('GET', '/qc', 'KetQuaKiemDinhController', 'index');
$router->addRoute('GET', '/qc/view/(.*)', 'KetQuaKiemDinhController', 'view');
$router->addRoute('POST', '/qc/view/(.*)', 'KetQuaKiemDinhController', 'view');  
$router->addRoute('POST', '/qc/store', 'KetQuaKiemDinhController', 'store');
$router->addRoute('GET', '/qc/history', 'KetQuaKiemDinhController', 'history');

// Quản lý Xuất nguyên liệu
$router->addRoute('GET', '/kho/xuatnguyenlieu', 'XuatNguyenLieuController', 'index');
$router->addRoute('GET', '/kho/xuatnguyenlieu/create/(.*)', 'XuatNguyenLieuController', 'create');
$router->addRoute('POST', '/kho/xuatnguyenlieu/store', 'XuatNguyenLieuController', 'store');
$router->addRoute('GET', '/kho/tonkho', 'CanhBaoTonKhoController', 'stock');
 // Bạn sẽ viết hàm này sau
// Xử lý request
$router->dispatch();
?>
