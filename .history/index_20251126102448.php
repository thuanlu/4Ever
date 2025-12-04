
<?php

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'app/Router.php';
require_once 'app/controllers/BaseController.php';

// Bắt đầu session (nếu chưa có) để kiểm tra quyền truy cập
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Khởi tạo router
$router = new Router();

// Kiểm tra truy cập toàn cục: nếu chưa đăng nhập thì redirect về trang login
// Chỉ cho phép một số route công khai (whitelist) như trang login, tài nguyên tĩnh, trang home
$requestPathForAuth = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/4Ever';
if (strpos($requestPathForAuth, $basePath) === 0) {
	$requestPathForAuth = substr($requestPathForAuth, strlen($basePath));
}
if ($requestPathForAuth === '' || $requestPathForAuth === '/') {
	$requestPathForAuth = '/home';
}

$publicPrefixes = [
	'/login', // trang và POST login
	'/public', // thư mục tài nguyên công khai
	'/assets', // css/js/images
	'/home'
];

$isPublic = false;
foreach ($publicPrefixes as $p) {
	if ($p === $requestPathForAuth || strpos($requestPathForAuth, $p) === 0) {
		$isPublic = true;
		break;
	}
}

if (!$isPublic && empty($_SESSION['user_id'])) {
	// Nếu là AJAX request, trả về 401
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
		http_response_code(401);
		echo json_encode(['error' => 'Unauthorized']);
		exit;
	}
	header('Location: ' . BASE_URL . 'login');
	exit;
}

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
$router->addRoute('GET', '/xuongtruong/lapkehoachcapxuong/', 'LapKeHoachCapXuongController', 'index');
$router->addRoute('GET', '/xuongtruong/lapkehoachcapxuong/create/(.*)', 'LapKeHoachCapXuongController', 'create');
$router->addRoute('POST', '/xuongtruong/lapkehoachcapxuong/store', 'LapKeHoachCapXuongController', 'store');

// Theo dõi tiến độ sản xuất (Xưởng trưởng)
$router->addRoute('GET', '/xuongtruong/tien-do', 'TienDoController', 'index');
$router->addRoute('GET', '/xuongtruong/tien-do/show/(.*)', 'TienDoController', 'show');
$router->addRoute('GET', '/xuongtruong/tien-do/show', 'TienDoController', 'show');


// Routes cho quản lý kế hoạch sản xuất (KeHoachSanXuat)
$router->addRoute('GET', '/kehoachsanxuat', 'KeHoachSanXuatController', 'index');
$router->addRoute('GET', '/kehoachsanxuat/create', 'KeHoachSanXuatController', 'create');
$router->addRoute('POST', '/kehoachsanxuat/create', 'KeHoachSanXuatController', 'create');
$router->addRoute('GET', '/kehoachsanxuat/edit/(.*)', 'KeHoachSanXuatController', 'edit');
$router->addRoute('POST', '/kehoachsanxuat/edit/(.*)', 'KeHoachSanXuatController', 'edit');
$router->addRoute('GET', '/kehoachsanxuat/delete/(.*)', 'KeHoachSanXuatController', 'delete');
$router->addRoute('GET', '/kehoachsanxuat/view/(.*)', 'KeHoachSanXuatController', 'view');

// Routes cho Phiếu Đặt Hàng Nguyên Vật Liệu (NVL)
$router->addRoute('GET', '/kehoachsanxuat/phieudatnvl', 'PhieuDatHangNVLController', 'index');
$router->addRoute('GET', '/kehoachsanxuat/phieudatnvl/create', 'PhieuDatHangNVLController', 'create');
$router->addRoute('POST', '/kehoachsanxuat/phieudatnvl/store', 'PhieuDatHangNVLController', 'store');
$router->addRoute('GET', '/kehoachsanxuat/phieudatnvl/view/(.*)', 'PhieuDatHangNVLController', 'view');
$router->addRoute('GET', '/kehoachsanxuat/getThongTinThieuHutNVL/(.*)', 'PhieuDatHangNVLController', 'getThongTinThieuHutNVL');
$router->addRoute('GET', '/kehoachsanxuat/getDonHangDetails/(.*)', 'KeHoachSanXuatController', 'getDonHangDetails');
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
// Route xem chi tiết phiếu yêu cầu
$router->addRoute('GET', '/yeucauxuat/view/(.*)', 'YeuCauXuatController', 'view');
// Accept /yeucauxuat/view with querystring ?ma=... as well
$router->addRoute('GET', '/yeucauxuat/view', 'YeuCauXuatController', 'view');

// Routes cho Phiếu Kiểm Tra Lô/Sản Phẩm
$router->addRoute('GET', '/phieu-kiem-tra/create', 'PhieuKiemTraSPController', 'create');
$router->addRoute('POST', '/phieu-kiem-tra/store', 'PhieuKiemTraSPController', 'store');
$router->addRoute('GET', '/phieu-kiem-tra/index', 'PhieuKiemTraSPController', 'index');
// View ticket detail (with or without path param)
$router->addRoute('GET', '/phieu-kiem-tra/view/(.*)', 'PhieuKiemTraSPController', 'view');
$router->addRoute('GET', '/phieu-kiem-tra/view', 'PhieuKiemTraSPController', 'view');

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
$router->addRoute('GET', '/totruong/phancalamviec', 'ToTruongController', 'index');


// Routes cho báo cáo
$router->addRoute('GET', '/reports', 'ReportController', 'index');
$router->addRoute('GET', '/reports/production', 'ReportController', 'production');
$router->addRoute('GET', '/reports/attendance', 'ReportController', 'attendance');


// Routes cho Nhập Kho Thành Phẩm
$router->addRoute('GET', '/nhapkho', 'NhapKhoController', 'index');
$router->addRoute('POST', '/nhapkho/confirm', 'NhapKhoController', 'confirmImport');
$router->addRoute('POST', '/nhapkho/confirm-multi', 'NhapKhoController', 'confirmImportMulti');
$router->addRoute('GET', '/nhapkho/detail', 'NhapKhoController', 'getDetail');



//Routes cho QC
$router->addRoute('GET', '/qc', 'KetQuaKiemDinhController', 'index');
$router->addRoute('GET', '/qc/view/(.*)', 'KetQuaKiemDinhController', 'view');
$router->addRoute('POST', '/qc/view/(.*)', 'KetQuaKiemDinhController', 'view');  
$router->addRoute('POST', '/qc/save', 'KetQuaKiemDinhController', 'save');
$router->addRoute('GET', '/qc/history', 'KetQuaKiemDinhController', 'history');


// Xử lý request
$router->dispatch();
?>