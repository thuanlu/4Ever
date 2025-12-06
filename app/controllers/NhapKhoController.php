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

        } catch (PDOException $e) {
            // Xử lý lỗi database
            error_log("Database error in NhapKhoController::index: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi kết nối database! Vui lòng kiểm tra MySQL đã chạy chưa.";
            $this->redirect('kho/dashboard');
        } catch (Exception $e) {
            // Xử lý lỗi khác
            error_log("Error in NhapKhoController::index: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
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
            $maNV = $currentUser['MaNV'] ?? $currentUser['id'] ?? null;
            if (!$maNV) {
                $this->json([
                    'success' => false,
                    'message' => 'Không xác định được mã nhân viên!'
                ]);
                return;
            }
            $result = $this->model->nhapKhoLoHang($maLoHang, $maNV);

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
        // Đảm bảo không có output trước khi trả JSON
        if (ob_get_level()) {
            ob_clean();
        }
        
        // Tăng thời gian timeout cho request này
        set_time_limit(60);
        
        // Kiểm tra quyền
        try {
            $this->requireRole(['NVK', 'nhan_vien_kho_tp']);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Không có quyền truy cập!'
            ]);
            return;
        }

        // Kiểm tra method là POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json([
                'success' => false,
                'message' => 'Yêu cầu không hợp lệ!'
            ]);
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
            $maNV = $currentUser['MaNV'] ?? $currentUser['id'] ?? null;
            if (!$maNV) {
                $this->json([
                    'success' => false,
                    'message' => 'Không xác định được mã nhân viên!'
                ]);
                return;
            }
            
            $result = $this->model->nhapKhoNhieuLoHang($danhSachLoHang, $maNV);
            
            // Log kết quả từ model
            error_log("NhapKhoController::confirmImportMulti - Model returned: " . json_encode($result));
            
            // Đảm bảo result có đầy đủ các trường cần thiết
            if (!isset($result) || !is_array($result)) {
                error_log("NhapKhoController::confirmImportMulti - Invalid result from model!");
                $result = [
                    'success' => false,
                    'message' => 'Lỗi không xác định từ model!',
                    'successCount' => 0,
                    'failCount' => count($danhSachLoHang)
                ];
            }
            
            if (!isset($result['success'])) {
                error_log("NhapKhoController::confirmImportMulti - Missing 'success' field, defaulting to false");
                $result['success'] = false;
            }
            if (!isset($result['message'])) {
                error_log("NhapKhoController::confirmImportMulti - Missing 'message' field");
                $result['message'] = 'Không xác định được kết quả!';
            }
            if (!isset($result['successCount'])) {
                error_log("NhapKhoController::confirmImportMulti - Missing 'successCount' field");
                $result['successCount'] = 0;
            }
            if (!isset($result['failCount'])) {
                error_log("NhapKhoController::confirmImportMulti - Missing 'failCount' field");
                $result['failCount'] = 0;
            }

            // Log kết quả cuối cùng trước khi trả về
            error_log("NhapKhoController::confirmImportMulti - Final result before JSON: " . json_encode($result));

            // Trả kết quả về dạng JSON
            $this->json($result);

        } catch (PDOException $e) {
            error_log("Database error in NhapKhoController::confirmImportMulti: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Lỗi kết nối database! Vui lòng kiểm tra MySQL đã chạy chưa.',
                'successCount' => 0,
                'failCount' => 0
            ]);
        } catch (Exception $e) {
            error_log("Error in NhapKhoController::confirmImportMulti: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage(),
                'successCount' => 0,
                'failCount' => 0
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