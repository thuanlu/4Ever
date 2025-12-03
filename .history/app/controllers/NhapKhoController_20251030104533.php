<?php
/**
 * Controller NhapKhoController - Xử lý nhập kho thành phẩm
 * Xử lý các request từ View và gọi Model để cập nhật dữ liệu
 */

require_once APP_PATH . '/controllers/BaseController.php';

class NhapKhoController extends BaseController {
    private $model;

    /**
     * Constructor - Khởi tạo Model
     */
    public function __construct() {
        $this->model = $this->loadModel('NhapKho');
    }

    /**
     * Hiển thị danh sách lô hàng cần nhập kho
     * Route: GET /nhapkho
     */
    public function index() {
        // Kiểm tra quyền truy cập - chỉ nhân viên kho được phép
        $this->requireRole(['NVK', 'nhan_vien_kho_tp']);

        try {
            // Lấy danh sách lô hàng cần nhập
            $loHangs = $this->model->getLoHangCanNhap();

            // Debug: Log số lượng lô hàng tìm được
            error_log("NhapKhoController::index - Found " . count($loHangs) . " lo hang");
            if (count($loHangs) == 0) {
                error_log("No lo hang found - may need to check database data");
            }

            // Lấy thông tin user hiện tại
            $currentUser = $this->getCurrentUser();

            // Truyền dữ liệu vào View
            $data = [
                'loHangs' => $loHangs,
                'currentUser' => $currentUser,
                'pageTitle' => 'Nhập Kho Thành Phẩm',
                'debug_count' => count($loHangs) // Thêm để debug
            ];

            // Hiển thị view
            $this->loadView('kho/nhap_kho_thanh_pham', $data);

        } catch (Exception $e) {
            // Xử lý lỗi
            error_log("Error in NhapKhoController::index: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi khi tải danh sách lô hàng!";
            $this->redirect('kho/dashboard');
        }
    }

    /**
     * Xử lý request nhập kho một lô hàng
     * Route: POST /nhapkho/confirm
     */
    public function confirmImport() {
        // Kiểm tra quyền
        $this->requireRole(['NVK', 'nhan_vien_kho_tp']);

        // Kiểm tra method là POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Yêu cầu không hợp lệ!";
            $this->redirect('nhapkho');
            return;
        }

        try {
            // Lấy dữ liệu từ request
            $maLoHang = $_POST['maLoHang'] ?? '';
            
            // Validate dữ liệu
            if (empty($maLoHang)) {
                $this->json([
                    'success' => false,
                    'message' => 'Vui lòng chọn lô hàng cần nhập!'
                ]);
                return;
            }

            // Lấy thông tin user hiện tại
            $currentUser = $this->getCurrentUser();
            if (!$currentUser) {
                $this->json([
                    'success' => false,
                    'message' => 'Không xác định được người dùng!'
                ]);
                return;
            }

            // Gọi Model để xử lý nhập kho
            $result = $this->model->nhapKhoLoHang($maLoHang, $currentUser['id']);

            // Trả kết quả về dạng JSON
            $this->json($result);

        } catch (Exception $e) {
            error_log("Error in NhapKhoController::confirmImport: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Lỗi kết nối! Vui lòng thử lại sau.'
            ]);
        }
    }

    /**
     * Xử lý request nhập kho nhiều lô hàng cùng lúc
     * Route: POST /nhapkho/confirm-multi
     */
    public function confirmImportMulti() {
        // Kiểm tra quyền
        $this->requireRole(['NVK', 'nhan_vien_kho_tp']);

        // Kiểm tra method là POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = "Yêu cầu không hợp lệ!";
            $this->redirect('nhapkho');
            return;
        }

        try {
            // Lấy dữ liệu từ request - FormData gửi dưới dạng danhSachLoHang[]
            $danhSachLoHang = [];
            
            // Kiểm tra cả danhSachLoHang[] (mảng từ FormData) và danhSachLoHang (nếu có)
            if (isset($_POST['danhSachLoHang']) && is_array($_POST['danhSachLoHang'])) {
                $danhSachLoHang = $_POST['danhSachLoHang'];
            }
            
            // Validate dữ liệu
            if (empty($danhSachLoHang) || !is_array($danhSachLoHang)) {
                error_log("NhapKhoController::confirmImportMulti - Empty or invalid danhSachLoHang. POST data: " . print_r($_POST, true));
                $this->json([
                    'success' => false,
                    'message' => 'Vui lòng chọn ít nhất một lô hàng!'
                ]);
                return;
            }
            
            error_log("NhapKhoController::confirmImportMulti - Processing " . count($danhSachLoHang) . " lots: " . implode(', ', $danhSachLoHang));

            // Lấy thông tin user hiện tại
            $currentUser = $this->getCurrentUser();
            if (!$currentUser) {
                $this->json([
                    'success' => false,
                    'message' => 'Không xác định được người dùng!'
                ]);
                return;
            }

            // Gọi Model để xử lý nhập kho nhiều lô hàng
            $result = $this->model->nhapKhoNhieuLoHang($danhSachLoHang, $currentUser['id']);

            // Trả kết quả về dạng JSON
            $this->json($result);

        } catch (Exception $e) {
            error_log("Error in NhapKhoController::confirmImportMulti: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Lỗi kết nối! Vui lòng thử lại sau.'
            ]);
        }
    }

    /**
     * Lấy thông tin chi tiết của một lô hàng
     * Route: GET /nhapkho/detail/:id
     */
    public function getDetail() {
        // Kiểm tra quyền
        $this->requireRole(['NVK', 'nhan_vien_kho_tp']);

        try {
            $maLoHang = $_GET['maLoHang'] ?? '';
            
            if (empty($maLoHang)) {
                $this->json([
                    'success' => false,
                    'message' => 'Mã lô hàng không hợp lệ!'
                ]);
                return;
            }

            $loHang = $this->model->getLoHangById($maLoHang);
            
            if ($loHang) {
                $this->json([
                    'success' => true,
                    'data' => $loHang
                ]);
            } else {
                $this->json([
                    'success' => false,
                    'message' => 'Không tìm thấy lô hàng!'
                ]);
            }

        } catch (Exception $e) {
            error_log("Error in NhapKhoController::getDetail: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thông tin lô hàng!'
            ]);
        }
    }

}
?>

