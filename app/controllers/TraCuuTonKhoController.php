<?php
/**
 * Controller TraCuuTonKhoController - Xử lý tra cứu tồn kho
 */

require_once APP_PATH . '/controllers/BaseController.php';

class TraCuuTonKhoController extends BaseController {
    private $model;

    /**
     * Constructor - Khởi tạo Model
     */
    public function __construct() {
        $this->model = $this->loadModel('TraCuuTonKho');
    }

    /**
     * Hiển thị trang tra cứu tồn kho
     * Route: GET /tracuutonkho
     */
    public function index() {
        // Kiểm tra quyền truy cập - chỉ nhân viên kho được phép
        $this->requireRole(['NVK', 'nhan_vien_kho_tp']);

        try {
            // Xác định tab đang chọn (mặc định là Nguyên Liệu)
            $activeTab = $_GET['tab'] ?? 'nguyenlieu';
            
            // Lấy dữ liệu cho tab Nguyên Liệu
            $filtersNL = [];
            if ($activeTab === 'nguyenlieu') {
                $filtersNL['maNL'] = $_GET['maNL'] ?? '';
                $filtersNL['tenNL'] = $_GET['tenNL'] ?? '';
                $filtersNL['nhaCungCap'] = $_GET['nhaCungCap'] ?? '';
            }
            $danhSachNguyenLieu = $this->model->getDanhSachNguyenLieu($filtersNL);
            $danhSachNhaCungCap = $this->model->getDanhSachNhaCungCap();

            // Lấy dữ liệu cho tab Thành Phẩm
            $filtersTP = [];
            if ($activeTab === 'thanhpham') {
                $filtersTP['loai'] = $_GET['loai'] ?? '';
                $filtersTP['maLH'] = $_GET['maLH'] ?? '';
                $filtersTP['ngayNhap'] = $_GET['ngayNhap'] ?? '';
            }
            $danhSachThanhPham = $this->model->getDanhSachThanhPham($filtersTP);

            // Lấy thông tin user hiện tại
            $currentUser = $this->getCurrentUser();

            // Truyền dữ liệu vào View
            $data = [
                'activeTab' => $activeTab,
                'danhSachNguyenLieu' => $danhSachNguyenLieu,
                'danhSachThanhPham' => $danhSachThanhPham,
                'danhSachNhaCungCap' => $danhSachNhaCungCap,
                'filtersNL' => $filtersNL,
                'filtersTP' => $filtersTP,
                'currentUser' => $currentUser,
                'pageTitle' => 'Tra Cứu Tồn Kho'
            ];

            // Hiển thị view
            $this->loadView('kho/tra_cuu_ton_kho', $data);

        } catch (PDOException $e) {
            // Xử lý lỗi database
            error_log("Database error in TraCuuTonKhoController::index: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi kết nối database! Vui lòng kiểm tra MySQL đã chạy chưa.";
            $this->redirect('kho/dashboard');
        } catch (Exception $e) {
            // Xử lý lỗi khác
            error_log("Error in TraCuuTonKhoController::index: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            $this->redirect('kho/dashboard');
        }
    }
}
?>