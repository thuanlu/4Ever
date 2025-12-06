<?php
/**
 * Controller NhapKhoNguyenLieuController - Xử lý nhập kho nguyên liệu
 */

require_once APP_PATH . '/controllers/BaseController.php';

class NhapKhoNguyenLieuController extends BaseController {
    private $model;

    /**
     * Constructor - Khởi tạo Model
     */
    public function __construct() {
        $this->model = $this->loadModel('NhapKhoNguyenLieu');
    }

    /**
     * Hiển thị danh sách đơn đặt cần nhập kho
     * Route: GET /nhapkhonguyenlieu
     */
    public function index() {
        // Kiểm tra quyền truy cập - chỉ nhân viên kho được phép
        try {
            $this->requireRole(['NVK', 'nhan_vien_kho_nl']);
        } catch (Exception $e) {
            $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này!";
            $this->redirect('kho/dashboard');
            return;
        }

        try {
            // Lấy danh sách phiếu nhập cần nhập kho
            $danhSachPhieuNhap = $this->model->getDanhSachPhieuNhapCanNhap();

            // Lấy thông tin user hiện tại
            $currentUser = $this->getCurrentUser();

            // Truyền dữ liệu vào View
            $data = [
                'danhSachPhieuNhap' => $danhSachPhieuNhap,
                'currentUser' => $currentUser,
                'pageTitle' => 'Nhập Kho Nguyên Liệu'
            ];

            // Hiển thị view
            $this->loadView('kho/nhap_kho_nguyen_lieu', $data);

        } catch (PDOException $e) {
            error_log("Database error in NhapKhoNguyenLieuController::index: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi kết nối database! Vui lòng kiểm tra MySQL đã chạy chưa.";
            $this->redirect('kho/dashboard');
        } catch (Exception $e) {
            error_log("Error in NhapKhoNguyenLieuController::index: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            $this->redirect('kho/dashboard');
        }
    }

    /**
     * Hiển thị chi tiết đơn đặt
     * Route: GET /nhapkhonguyenlieu/detail?maPhieu=...
     */
    public function detail() {
        try {
            $this->requireRole(['NVK', 'nhan_vien_kho_nl']);
        } catch (Exception $e) {
            $_SESSION['error'] = "Bạn không có quyền truy cập chức năng này!";
            $this->redirect('nhapkhonguyenlieu');
            return;
        }

        $maPhieuNhap = $_GET['maPhieuNhap'] ?? '';

        if (empty($maPhieuNhap)) {
            $_SESSION['error'] = "Mã phiếu nhập không hợp lệ!";
            $this->redirect('nhapkhonguyenlieu');
            return;
        }

        try {
            // Lấy chi tiết phiếu nhập
            $chiTietPhieuNhap = $this->model->getChiTietPhieuNhap($maPhieuNhap);

            if (!$chiTietPhieuNhap) {
                $_SESSION['error'] = "Không tìm thấy phiếu nhập!";
                $this->redirect('nhapkhonguyenlieu');
                return;
            }

            // Kiểm tra đã nhập chưa
            $daNhap = $this->model->daNhapKho($maPhieuNhap);

            // Lấy thông tin user hiện tại
            $currentUser = $this->getCurrentUser();

            $data = [
                'chiTietPhieuNhap' => $chiTietPhieuNhap,
                'daNhap' => $daNhap,
                'currentUser' => $currentUser,
                'pageTitle' => 'Chi Tiết Phiếu Nhập - ' . $maPhieuNhap
            ];

            $this->loadView('kho/nhap_kho_nguyen_lieu_detail', $data);

        } catch (PDOException $e) {
            error_log("Database error in NhapKhoNguyenLieuController::detail: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi kết nối database! Vui lòng kiểm tra MySQL đã chạy chưa.";
            $this->redirect('nhapkhonguyenlieu');
        } catch (Exception $e) {
            error_log("Error in NhapKhoNguyenLieuController::detail: " . $e->getMessage());
            $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            $this->redirect('nhapkhonguyenlieu');
        }
    }

    /**
     * Xử lý xác nhận nhập kho
     * Route: POST /nhapkhonguyenlieu/confirm
     */
    public function confirm() {
        try {
            $this->requireRole(['NVK', 'nhan_vien_kho_nl']);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này!'
            ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json([
                'success' => false,
                'message' => 'Phương thức không hợp lệ!'
            ]);
            return;
        }

        $maPhieuNhap = $_POST['maPhieuNhap'] ?? '';

        if (empty($maPhieuNhap)) {
            $this->json([
                'success' => false,
                'message' => 'Mã phiếu nhập không hợp lệ!'
            ]);
            return;
        }

        try {
            $currentUser = $this->getCurrentUser();
            $maNV = $currentUser['MaNV'] ?? $currentUser['id'] ?? null;

            if (!$maNV) {
                $this->json([
                    'success' => false,
                    'message' => 'Không xác định được nhân viên!'
                ]);
                return;
            }

            // Xử lý nhập kho
            $result = $this->model->nhapKhoTuPhieuNhap($maPhieuNhap, $maNV);

            if ($result['success']) {
                $this->json([
                    'success' => true,
                    'message' => 'Nhập kho thành công!',
                    'maPhieuNhap' => $result['maPhieuNhap'] ?? ''
                ]);
            } else {
                $this->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Lỗi kết nối! Vui lòng thử lại sau.'
                ]);
            }

        } catch (PDOException $e) {
            error_log("Database error in NhapKhoNguyenLieuController::confirm: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Lỗi kết nối! Vui lòng thử lại sau.'
            ]);
        } catch (Exception $e) {
            error_log("Error in NhapKhoNguyenLieuController::confirm: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Xử lý từ chối nhập kho
     * Route: POST /nhapkhonguyenlieu/reject
     */
    public function reject() {
        try {
            $this->requireRole(['NVK', 'nhan_vien_kho_nl']);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này!'
            ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json([
                'success' => false,
                'message' => 'Phương thức không hợp lệ!'
            ]);
            return;
        }

        $maPhieuNhap = $_POST['maPhieuNhap'] ?? '';
        $lyDo = $_POST['lyDo'] ?? '';

        if (empty($maPhieuNhap)) {
            $this->json([
                'success' => false,
                'message' => 'Mã phiếu nhập không hợp lệ!'
            ]);
            return;
        }

        if (empty($lyDo)) {
            $this->json([
                'success' => false,
                'message' => 'Vui lòng nhập lý do từ chối!'
            ]);
            return;
        }

        try {
            $result = $this->model->tuChoiNhapKho($maPhieuNhap, $lyDo);

            $this->json([
                'success' => $result['success'],
                'message' => $result['message']
            ]);

        } catch (Exception $e) {
            error_log("Error in NhapKhoNguyenLieuController::reject: " . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>