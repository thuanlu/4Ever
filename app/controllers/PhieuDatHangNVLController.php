<?php
// [THAY THẾ TOÀN BỘ FILE: app/controllers/PhieuDatHangNVLController.php]

require_once APP_PATH . '/models/PhieuDatHangNVL.php';

class PhieuDatHangNVLController extends BaseController {
    
    private $phieuDatHangNVLModel;

    public function __construct() {
        parent::__construct(); 
        $this->phieuDatHangNVLModel = new PhieuDatHangNVL($this->db);
    }

    /**
     * Hiển thị danh sách
     */
    public function index() {
        $data['phieuDatList'] = $this->phieuDatHangNVLModel->getAll();
        $data['pageTitle'] = 'Danh sách Phiếu Đặt NVL';
        $this->loadView('kehoachsanxuat/index_nvl', $data);
    }

    /**
     * Hiển thị form tạo mới
     */
    public function create() {
        $data['nhaCungCapList'] = $this->phieuDatHangNVLModel->getNhaCungCapList(); 
        $data['kehoach_list'] = $this->phieuDatHangNVLModel->getKeHoachThieuNVL(); 
        $data['currentUserName'] = $_SESSION['full_name'] ?? 'N/A'; 
        $nextMaPhieu = $this->phieuDatHangNVLModel->generateNewMaPhieu();

        $data['phieu'] = [
            'MaPhieu' => $nextMaPhieu
        ];

        $data['chiTiet'] = [];
        $data['isView'] = false;

        $data['pageTitle'] = 'Tạo Phiếu Đặt NVL';
        $this->loadView('kehoachsanxuat/create_nvl', $data);
    }

    /**
     * Lưu dữ liệu từ form tạo mới
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'TenPhieu' => $_POST['TenPhieu'] ?? 'Phiếu đặt NVL',
                'NgayLapPhieu' => $_POST['NgayLapPhieu'] ?? date('Y-m-d'),
                'NguoiLapPhieu' => $_SESSION['full_name'] ?? 'N/A', 
                'MaKHSX' => $_POST['MaKHSX'] ?? null, 
                'MaNhaCungCap' => $_POST['MaNhaCungCap'] ?? null,
                'TongChiPhiDuKien' => $_POST['TongChiPhiDuKien'] ?? 0,
                
                // [SỬA ĐỔI THEO YÊU CẦU]
                // Đổi trạng thái mặc định từ 'MoiTao' sang 'DaDuyet'
                'TrangThai' => 'Đã duyệt' 
            ];

            $chiTietData = [];
            if (isset($_POST['materials']) && is_array($_POST['materials'])) {
                foreach ($_POST['materials'] as $item) {
                    $chiTietData[] = [
                        'MaNVL' => $item['MaNVL'] ?? null,
                        'TenNVL' => $item['TenNVL'] ?? '',
                        'SoLuongCan' => $item['SoLuongCan'] ?? 0,
                        'DonGia' => $item['DonGia'] ?? 0,
                        'ThanhTien' => $item['ThanhTien'] ?? 0,
                    ];
                }
            }

            try {
                $this->phieuDatHangNVLModel->createPhieuVaChiTiet($data, $chiTietData);
                $_SESSION['success'] = 'Tạo phiếu đặt hàng NVL thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi khi tạo phiếu: ' . $e->getMessage();
            }
            
            header('Location: ' . BASE_URL . 'kehoachsanxuat/phieudatnvl');
            exit;
        }
    }

    /**
     * Hiển thị chi tiết một phiếu
     */
    public function view($id) {
        $phieuData = $this->phieuDatHangNVLModel->getById($id);
        
        if (!$phieuData || !$phieuData['phieu']) {
            $_SESSION['error'] = 'Không tìm thấy phiếu đặt hàng!';
            header('Location: ' . BASE_URL . 'kehoachsanxuat/phieudatnvl');
            exit;
        }

        $data['nhaCungCapList'] = $this->phieuDatHangNVLModel->getNhaCungCapList();
        $data['phieu'] = $phieuData['phieu'];
        $data['chiTiet'] = $phieuData['chiTiet'];
        $data['isView'] = true; 
        
        $data['pageTitle'] = 'Xem Phiếu Đặt NVL - ' . $phieuData['phieu']['MaPhieu'];
        $this->loadView('kehoachsanxuat/view_nvl', $data);
    }
    
    // -----------------------------------------------------------------
    // HÀM AJAX
    // -----------------------------------------------------------------

    /**
     * Endpoint AJAX
     */
    public function getThongTinThieuHutNVL($maKeHoach) {
        header('Content-Type: application/json');
        
        try {
            $materials = $this->phieuDatHangNVLModel->getChiTietThieuHut($maKeHoach);
            
            if (empty($materials)) {
                echo json_encode(['materials' => []]);
            } else {
                echo json_encode(['materials' => $materials]);
            }
            
        } catch (Throwable $t) { 
            http_response_code(500);
            echo json_encode(['error' => 'Lỗi máy chủ nghiêm trọng: ' . $t->getMessage()]);
        }
        exit; // Dừng thực thi
    }
}
?>