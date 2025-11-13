<?php
/**
 * Controller KeHoachSanXuat - CRUD cho Nhân viên Kế hoạch
 * ĐÃ ĐƯỢC CẬP NHẬT ĐỂ HỖ TRỢ FORM ĐỘNG VÀ AJAX
 *
 * [SỬA LỖI TOÀN BỘ] - Đã thay thế tất cả các lệnh $this->loadModel()
 * và các biến model cục bộ (vd: $khModel)
 * bằng các thuộc tính của class (vd: $this->keHoachSanXuatModel)
 */
require_once APP_PATH . '/controllers/BaseController.php';
require_once APP_PATH . '/models/KeHoachSanXuat.php';
require_once APP_PATH . '/models/ChiTietKeHoach.php';
require_once APP_PATH . '/models/DonHang.php';
require_once APP_PATH . '/models/PhanXuong.php';
require_once APP_PATH . '/models/ChiTietDonHang.php';
require_once APP_PATH . '/models/DinhMucNguyenLieu.php';

class KeHoachSanXuatController extends BaseController {

    private $keHoachSanXuatModel;
    private $chiTietKeHoachModel;
    private $donHangModel;
    private $phanXuongModel;
    private $chiTietDonHangModel;
    private $dinhMucNguyenLieuModel;

    /**
     * Sửa hàm __construct
     */
    public function __construct() {
        parent::__construct();
        
        $this->keHoachSanXuatModel = new KeHoachSanXuat($this->db);
        $this->chiTietKeHoachModel = new ChiTietKeHoach($this->db);
        $this->donHangModel = new DonHang($this->db);
        $this->phanXuongModel = new PhanXuong($this->db);
        $this->chiTietDonHangModel = new ChiTietDonHang($this->db);
        $this->dinhMucNguyenLieuModel = new DinhMucNguyenLieu($this->db);
    }

    
    /**
     * Dashboard riêng cho Kế hoạch
     */
    public function dashboard() {
        // Chuyển hướng đến hàm 'kh' của DashboardController
        $this->redirect('dashboard/kh');
    }

    /**
     * Hiển thị danh sách Kế hoạch
     */
    public function index() {
        $this->requireRole(['KH']);
        
        // [SỬA] Sử dụng Model để lấy danh sách
        try {
            // Giả định hàm getAllWithDetails() tồn tại trong KeHoachSanXuatModel
            $kehoachs = $this->keHoachSanXuatModel->getAllWithDetails(); 

        } catch (PDOException $e) {
            $_SESSION['error'] = "Lỗi CSDL khi tải danh sách: " . $e->getMessage();
            $kehoachs = [];
        }
        
        $this->loadView('kehoachsanxuat/index', ['kehoachs' => $kehoachs]);
    }

    /**
     * Hiển thị form tạo mới và xử lý POST
     */
    public function create() {
        $this->requireRole(['KH']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // --- XỬ LÝ LƯU DỮ LIỆU ---
            $this->db->beginTransaction(); 
            try {
                // 1. Lưu Kế hoạch chính
                $data_kh = [
                    'MaKeHoach' => $_POST['MaKeHoach'],
                    'TenKeHoach' => $_POST['TenKeHoach'],
                    'NgayBatDau' => $_POST['NgayBatDau'],
                    'NgayKetThuc' => $_POST['NgayKetThuc'],
                    'MaNV' => $_SESSION['user_id'],
                    'MaDonHang' => $_POST['MaDonHang'],
                    'TrangThai' => 'Chờ duyệt',
                    'TongChiPhiDuKien' => $_POST['TongChiPhiDuKien'] ?? 0.00,
                    'ChiPhiNguyenLieu' => $_POST['ChiPhiNguyenLieu'] ?? 0,
                    'ChiPhiNhanCong' => $_POST['ChiPhiNhanCong'] ?? 0,
                    'ChiPhiKhac' => $_POST['ChiPhiKhac'] ?? 0,
                    'SoLuongCongNhanCan' => 0, // Cần tính toán
                    'NgayLap' => date('Y-m-d H:i:s'),
                    'GhiChu' => $_POST['GhiChu']
                ];
                // [SỬA LỖI]
                $this->keHoachSanXuatModel->create($data_kh);

                // 2. Lưu Chi tiết Kế hoạch (Sản phẩm)
                if (!empty($_POST['products'])) {
                    foreach ($_POST['products'] as $index => $product) {
                        $maCTKH = $_POST['MaKeHoach'] . '-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT); 
                        
                        $data_ctkh = [
                            'MaChiTietKeHoach' => $maCTKH,
                            'MaKeHoach' => $_POST['MaKeHoach'],
                            'MaSanPham' => $product['MaSanPham'],
                            'SanLuongMucTieu' => $product['SanLuongMucTieu'],
                            'MaPhanXuong' => $product['MaPhanXuong'],
                            'CanBoSung' => 0 // Tạm thời
                        ];
                        // [SỬA LỖI]
                        $this->chiTietKeHoachModel->create($data_ctkh); 
                    }
                }
                
                $this->db->commit(); 
                $this->redirect('kehoachsanxuat');
            } catch (Exception $e) {
                $this->db->rollBack(); 
                $_SESSION['error'] = 'Lỗi khi lưu kế hoạch: ' . $e->getMessage();
                $this->loadCreateView(['_POST' => $_POST, 'error_message' => $e->getMessage()]);
            }

        } else {
            // --- HIỂN THỊ FORM (GET) ---
            $this->loadCreateView();
        }
    }
    
    /**
     * Hàm trợ giúp để tải view Create (tránh lặp code)
     */
    private function loadCreateView($extra_data = []) {
        $data = [];

        // [SỬA LỖI]
        $all_donhangs = $this->donHangModel->getAll();
        $planned_donhang_ids = $this->keHoachSanXuatModel->getPlannedDonHangIds();

        // Lọc bỏ các đơn hàng đã có kế hoạch
        $available_donhangs = [];
        foreach ($all_donhangs as $dh) {
            if (!in_array($dh['MaDonHang'], $planned_donhang_ids)) {
                $available_donhangs[] = $dh; 
            }
        }

        $data['donhangs'] = $available_donhangs;
        
        // [SỬA LỖI] - Đây là lỗi của bạn
        $data['xuongs'] = $this->phanXuongModel->getAll();

        // [SỬA LỖI]
        $lastMa = $this->keHoachSanXuatModel->getLastMaKeHoach();
        if ($lastMa) {
            $num = (int)substr($lastMa, 2) + 1;
            $newMa = 'KH' . str_pad($num, 2, '0', STR_PAD_LEFT);
        } else {
            $newMa = 'KH01';
        }
        $data['kehoach']['MaKeHoach'] = $newMa;

        // 3. Lấy thông tin người dùng
        $data['currentUserId'] = $_SESSION['user_id'] ?? 'NKH001';
        $data['currentUserFullName'] = $_SESSION['full_name'] ?? 'Nhân viên Kế hoạch';
        $data['kehoach']['HoTenNguoiLap'] = $data['currentUserFullName'];

        // 4. Thiết lập biến cho form
        $data['is_editing'] = false;
        $data['is_viewing'] = false;
        $data['form_title'] = 'Tạo Kế hoạch Sản xuất Mới';

        $data = array_merge($data, $extra_data);
        $this->loadView('kehoachsanxuat/create', $data);
    }

    /**
     * Hiển thị form chỉnh sửa và xử lý POST
     */
    public function edit($maKeHoach) {
        $this->requireRole(['KH']);
        
        // [SỬA LỖI]
        $kehoach = $this->keHoachSanXuatModel->getByIdWithNguoiLap($maKeHoach); 

        if (!$kehoach) {
            $_SESSION['error'] = 'Không tìm thấy kế hoạch.';
            $this->redirect('kehoachsanxuat');
            return;
        }

        if ($kehoach['TrangThai'] !== 'Chờ duyệt') {
            $_SESSION['error'] = 'Không thể sửa kế hoạch đã duyệt.';
            $this->redirect('kehoachsanxuat/view/' . $maKeHoach);
            return; 
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // --- XỬ LÝ LƯU DỮ LIỆU ---
            $this->db->beginTransaction();
            try {
                // 1. Cập nhật Kế hoạch chính
                $data_kh = [
                    'TenKeHoach' => $_POST['TenKeHoach'],
                    'NgayBatDau' => $_POST['NgayBatDau'],
                    'NgayKetThuc' => $_POST['NgayKetThuc'],
                    'TrangThai' => $_POST['TrangThai'], 
                    'GhiChu' => $_POST['GhiChu'],
                    'ChiPhiNguyenLieu' => $_POST['ChiPhiNguyenLieu'] ?? 0,
                    'ChiPhiNhanCong' => $_POST['ChiPhiNhanCong'] ?? 0,
                    'ChiPhiKhac' => $_POST['ChiPhiKhac'] ?? 0,
                    'TongChiPhiDuKien' => $_POST['TongChiPhiDuKien'] ?? 0
                ];
                // [SỬA LỖI]
                $this->keHoachSanXuatModel->update($maKeHoach, $data_kh);
                
                // 2. Xóa Chi tiết cũ và Thêm Chi tiết mới
                // [SỬA LỖI]
                $this->chiTietKeHoachModel->deleteByMaKeHoach($maKeHoach); 
                
                if (!empty($_POST['products'])) {
                    foreach ($_POST['products'] as $index => $product) {
                        $maCTKH = $maKeHoach . '-' . str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                        $data_ctkh = [
                            'MaChiTietKeHoach' => $maCTKH,
                            'MaKeHoach' => $maKeHoach,
                            'MaSanPham' => $product['MaSanPham'],
                            'SanLuongMucTieu' => $product['SanLuongMucTieu'],
                            'MaPhanXuong' => $product['MaPhanXuong'],
                            'CanBoSung' => 0 // Tạm thời
                        ];
                        // [SỬA LỖI]
                        $this->chiTietKeHoachModel->create($data_ctkh);
                    }
                }
                
                $this->db->commit();
                $this->redirect('kehoachsanxuat');
            } catch (Exception $e) {
                $this->db->rollBack();
                $_SESSION['error'] = 'Lỗi khi cập nhật kế hoạch: ' . $e->getMessage();
                $this->loadEditView($maKeHoach, ['_POST' => $_POST, 'error_message' => $e->getMessage()]);
            }
            
        } else {
            // --- HIỂN THỊ FORM (GET) ---
            $this->loadEditView($maKeHoach);
        }
    }
    
    /**
     * Hàm trợ giúp tải view Edit/View (tránh lặp code)
     */
    private function loadEditView($maKeHoach, $extra_data = [], $is_view_only = false) {
        $data = [];
        
        // [SỬA LỖI]
        $data['kehoach'] = $this->keHoachSanXuatModel->getByIdWithNguoiLap($maKeHoach);
        if (empty($data['kehoach']) || !is_array($data['kehoach'])) {
            // ... (xử lý lỗi)
        }
        
        // [SỬA LỖI]
        $data['donhangs'] = $this->donHangModel->getAll();
        $data['xuongs'] = $this->phanXuongModel->getAll();
        $data['plan_details'] = $this->chiTietKeHoachModel->getByMaKeHoach($maKeHoach);

        // 4. Tải dữ liệu BOM cho các sản phẩm
        $product_ids = array_column($data['plan_details'], 'MaSanPham');
        if (!empty($product_ids)) {
            // [SỬA LỖI]
            $data['bom_data'] = $this->dinhMucNguyenLieuModel->getBomDataForProducts($product_ids); 
        } else {
            $data['bom_data'] = [];
        }
        
        // 5. Thiết lập biến cho form
        $data['is_editing'] = !$is_view_only;
        $data['is_viewing'] = $is_view_only;
        $data['form_title'] = ($is_view_only ? 'Chi tiết Kế hoạch: ' : 'Chỉnh sửa Kế hoạch: ') . ($data['kehoach']['MaKeHoach'] ?? 'N/A');
        
        $data = array_merge($data, $extra_data);
        
        $view_name = $is_view_only ? 'kehoachsanxuat/view' : 'kehoachsanxuat/edit';
        $this->loadView($view_name, $data);
    }

    /**
     * Xem chi tiết (Chỉ đọc)
     */
    public function view($maKeHoach) {
        $this->requireRole(['KH', 'BGD', 'XT']);
        $this->loadEditView($maKeHoach, [], true); 
    }

    
    public function delete($maKeHoach) {
        $this->requireRole(['KH']);
        
        $this->db->beginTransaction();
        try {
            // [SỬA LỖI]
            $this->chiTietKeHoachModel->deleteByMaKeHoach($maKeHoach);
            $this->keHoachSanXuatModel->delete($maKeHoach);
            
            $this->db->commit();
            $_SESSION['success'] = 'Đã xóa thành công kế hoạch ' . $maKeHoach;
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Lỗi khi xóa kế hoạch ' . $maKeHoach . ': ' . $e->getMessage();
        }
        $this->redirect('kehoachsanxuat');
    }

    // --- BẮT ĐẦU PHƯƠNG THỨC API MỚI ---
    
    /**
     * API Endpoint để lấy chi tiết Đơn hàng (Sản phẩm và BOM)
     */
    public function getDonHangDetails($maDonHang) {
        $this->requireRole(['KH']); 
        header('Content-Type: application/json');
        
        try {
            // [SỬA LỖI]
            $products = $this->chiTietDonHangModel->getProductsByMaDonHang($maDonHang);

            if (empty($products)) {
                $this->json(['products' => [], 'bom_data' => []]);
                return;
            }

            $product_ids = array_column($products, 'MaSanPham');
            
            // [SỬA LỖI]
            $bom_data = $this->dinhMucNguyenLieuModel->getBomDataForProducts($product_ids);
            
            $this->json([
                'products' => $products,
                'bom_data' => $bom_data
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            $this->json(['error' => 'Lỗi máy chủ: ' . $e->getMessage()]);
        }
    }
}
?>