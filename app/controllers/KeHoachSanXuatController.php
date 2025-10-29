<?php
/**
 * Controller KeHoachSanXuat - CRUD cho Nhân viên Kế hoạch

 * ĐÃ ĐƯỢC CẬP NHẬT ĐỂ HỖ TRỢ FORM ĐỘNG VÀ AJAX
 */
require_once APP_PATH . '/controllers/BaseController.php';

class KeHoachSanXuatController extends BaseController {

    public function __construct() {
        parent::__construct();
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
        
        $kehoachs = []; // Khởi tạo mảng rỗng
        
        try {
            // Câu lệnh SQL JOIN để lấy Tên Đơn hàng và Tên Người lập
            $sql = "
                SELECT 
                    k.MaKeHoach, k.TenKeHoach, k.NgayBatDau, k.NgayKetThuc, k.TrangThai, 
                    d.TenDonHang, 
                    n.HoTen AS NguoiLap
                FROM kehoachsanxuat k
                JOIN donhang d ON k.MaDonHang = d.MaDonHang
                JOIN nhanvien n ON k.MaNV = n.MaNV
                ORDER BY k.NgayLap DESC;
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $kehoachs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $_SESSION['error'] = "Lỗi CSDL khi tải danh sách: " . $e->getMessage();
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
                    'TongChiPhiDuKien' => 0.00, // Cần tính toán
                    'SoLuongCongNhanCan' => 0, // Cần tính toán
                    'NgayLap' => date('Y-m-d H:i:s'),
                    'GhiChu' => $_POST['GhiChu']
                ];
                $this->loadModel('KeHoachSanXuat')->create($data_kh);

                // 2. Lưu Chi tiết Kế hoạch (Sản phẩm)
                $ctkhModel = $this->loadModel('ChiTietKeHoach');
                if (!empty($_POST['products'])) {
                    foreach ($_POST['products'] as $index => $product) {
                        $maCTKH = $_POST['MaKeHoach'] . '-' . str_pad($index, 2, '0', STR_PAD_LEFT); 
                        
                        $data_ctkh = [
                            'MaChiTietKeHoach' => $maCTKH,
                            'MaKeHoach' => $_POST['MaKeHoach'],
                            'MaSanPham' => $product['MaSanPham'],
                            'SanLuongMucTieu' => $product['SanLuongMucTieu'],
                            'MaPhanXuong' => $product['MaPhanXuong'],
                            'CanBoSung' => 0 
                        ];
                        $ctkhModel->create($data_ctkh); 
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

        // 1. Tải dữ liệu tham chiếu
        $donhangModel = $this->loadModel('DonHang');
        $phanXuongModel = $this->loadModel('PhanXuong');
        $khModel = $this->loadModel('KeHoachSanXuat'); // Cần model KH để lấy ds đã lập

        // Lấy tất cả đơn hàng
        $all_donhangs = $donhangModel->getAll();
        // Lấy danh sách MaDonHang đã có trong bảng kehoachsanxuat
        $planned_donhang_ids = $khModel->getPlannedDonHangIds(); // Cần tạo hàm này trong Model

        // Lọc bỏ các đơn hàng đã có kế hoạch
        $available_donhangs = [];
        foreach ($all_donhangs as $dh) {
            // Nếu MaDonHang KHÔNG CÓ trong danh sách đã lập kế hoạch
            if (!in_array($dh['MaDonHang'], $planned_donhang_ids)) {
                $available_donhangs[] = $dh; // Giữ lại đơn hàng này
            }
        }

        // Truyền danh sách ĐÃ LỌC sang view
        $data['donhangs'] = $available_donhangs;
        $data['xuongs'] = $phanXuongModel->getAll();

        // 2. Tạo Mã KH tự động (Giữ nguyên)
        $lastMa = $khModel->getLastMaKeHoach();
        if ($lastMa) {
            $num = (int)substr($lastMa, 2) + 1;
            $newMa = 'KH' . str_pad($num, 2, '0', STR_PAD_LEFT);
        } else {
            $newMa = 'KH01';
        }
        $data['kehoach']['MaKeHoach'] = $newMa;

        // 3. Lấy thông tin người dùng (Giữ nguyên)
        $data['currentUserId'] = $_SESSION['user_id'] ?? 'NKH001';
        $data['currentUserFullName'] = $_SESSION['full_name'] ?? 'Nhân viên Kế hoạch';
        $data['kehoach']['HoTenNguoiLap'] = $data['currentUserFullName'];

        // 4. Thiết lập biến cho form (Giữ nguyên)
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
        
        $khModel = $this->loadModel('KeHoachSanXuat');
        $ctkhModel = $this->loadModel('ChiTietKeHoach');
        
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
                    'GhiChu' => $_POST['GhiChu']
                ];
                $khModel->update($maKeHoach, $data_kh);
                
                // 2. Xóa Chi tiết cũ và Thêm Chi tiết mới
                $ctkhModel->deleteByMaKeHoach($maKeHoach); 
                
                if (!empty($_POST['products'])) {
                    foreach ($_POST['products'] as $index => $product) {
                        $maCTKH = $maKeHoach . '-' . str_pad($index, 2, '0', STR_PAD_LEFT);
                        $data_ctkh = [
                            'MaChiTietKeHoach' => $maCTKH,
                            'MaKeHoach' => $maKeHoach,
                            'MaSanPham' => $product['MaSanPham'],
                            'SanLuongMucTieu' => $product['SanLuongMucTieu'],
                            'MaPhanXuong' => $product['MaPhanXuong'],
                            'CanBoSung' => 0
                        ];
                        $ctkhModel->create($data_ctkh);
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
        
        // 1. Tải Kế hoạch chính
        // Giả định hàm getByIdWithNguoiLap JOIN với NhanVien
        $data['kehoach'] = $this->loadModel('KeHoachSanXuat')->getByIdWithNguoiLap($maKeHoach);
        // Log debug kế hoạch lấy được
        error_log('DEBUG: KeHoachSanXuatController::loadEditView - $kehoach = ' . print_r($data['kehoach'], true));
        if (empty($data['kehoach']) || !is_array($data['kehoach'])) {
            $data['error_message'] = 'Không tìm thấy kế hoạch hoặc dữ liệu bị lỗi.';
            // Vẫn truyền sang view để hiển thị lỗi cụ thể
            $view_name = $is_view_only ? 'kehoachsanxuat/view' : 'kehoachsanxuat/edit';
            $this->loadView($view_name, $data);
            return;
        }
        
        // 2. Tải dữ liệu tham chiếu
        $data['donhangs'] = $this->loadModel('DonHang')->getAll();
        $data['xuongs'] = $this->loadModel('PhanXuong')->getAll();

        // 3. Tải Chi tiết Kế hoạch
        // Giả định hàm getByMaKeHoach JOIN với SanPham
        $data['plan_details'] = $this->loadModel('ChiTietKeHoach')->getByMaKeHoach($maKeHoach);

        // 4. Tải dữ liệu BOM cho các sản phẩm
        $product_ids = array_column($data['plan_details'], 'MaSanPham');
        if (!empty($product_ids)) {
            // Giả định hàm getBomDataForProducts JOIN với NguyenLieu
            $data['bom_data'] = $this->loadModel('DinhMucNguyenLieu')->getBomDataForProducts($product_ids); 
        } else {
            $data['bom_data'] = [];
        }
        
        // 5. Thiết lập biến cho form
        $data['is_editing'] = !$is_view_only;
        $data['is_viewing'] = $is_view_only;
        $data['form_title'] = ($is_view_only ? 'Chi tiết Kế hoạch: ' : 'Chỉnh sửa Kế hoạch: ') . $data['kehoach']['MaKeHoach'];
        
        $data = array_merge($data, $extra_data);
        
        $view_name = $is_view_only ? 'kehoachsanxuat/view' : 'kehoachsanxuat/edit';
        $this->loadView($view_name, $data);
    }

    /**
     * Xem chi tiết (Chỉ đọc)
     */
    public function view($maKeHoach) {
        $this->requireRole(['KH', 'BGD', 'XT']); // Cho phép BGD và XT xem
        $this->loadEditView($maKeHoach, [], true); // Gọi hàm trợ giúp ở chế độ chỉ xem
    }

    
    public function delete($maKeHoach) {
        $this->requireRole(['KH']); // Chỉ KH mới được xóa
        
        $this->db->beginTransaction();
        try {
            // Xóa chi tiết trước
            $this->loadModel('ChiTietKeHoach')->deleteByMaKeHoach($maKeHoach);
            // Xóa kế hoạch chính
            $this->loadModel('KeHoachSanXuat')->delete($maKeHoach);
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
     * Được gọi bởi AJAX từ form.php
     * URL: /kehoachsanxuat/getDonHangDetails/{MaDonHang}
     */
    public function getDonHangDetails($maDonHang) {
        $this->requireRole(['KH']); 
        
        header('Content-Type: application/json');
        
        try {
            // 1. Lấy Sản phẩm từ Đơn hàng
            $ctdhModel = $this->loadModel('ChiTietDonHang');
            // Giả định model 'ChiTietDonHang' có hàm 'getProductsByMaDonHang'
            // (JOIN với bảng sanpham để lấy TenSanPham)
            $products = $ctdhModel->getProductsByMaDonHang($maDonHang);

            if (empty($products)) {
                $this->json(['products' => [], 'bom_data' => []]);
                return;
            }

            // 2. Lấy danh sách MaSanPham để truy vấn BOM
            $product_ids = array_column($products, 'MaSanPham');

            // 3. Lấy Định mức BOM và Tồn kho
            $bomModel = $this->loadModel('DinhMucNguyenLieu');
            // Giả định model 'DinhMucNguyenLieu' có hàm 'getBomDataForProducts'
            // (JOIN với nguyenlieu để lấy TenNguyenLieu, SoLuongTonKho)
            $bom_data = $bomModel->getBomDataForProducts($product_ids);
            
            // 4. Trả về JSON
            $this->json([
                'products' => $products,
                'bom_data' => $bom_data
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            $this->json(['error' => 'Lỗi máy chủ: ' . $e->getMessage()]);
        }
        
        // Hàm json() đã bao gồm exit()
    }
}
?>

