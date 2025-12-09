<?php
require_once APP_PATH . '/controllers/BaseController.php';

class KetQuaKiemDinhController extends BaseController {

    // 1. INDEX: Hiển thị Danh sách chờ + Form xử lý (nếu chọn) + Lịch sử
    public function index() {
        $this->requireRole(['QC']);
        $model = $this->loadModel('KetQuaKiemDinh');

        // A. Lấy danh sách chờ
        $phieus = $model->getPendingRequests();

        // B. Lấy lịch sử
        $history = $model->getHistory();

        // C. Xử lý khi người dùng chọn 1 phiếu để kiểm định
        $selectedPhieu = null;
        if (isset($_GET['ma_phieu'])) {
            $maPhieuKT = $_GET['ma_phieu'];
            $checkPhieu = $model->getById($maPhieuKT);

            // Chỉ hiện form nếu phiếu tồn tại và đang chờ
            if ($checkPhieu && $checkPhieu['TrangThai'] == 'Chờ kiểm tra') {
                $selectedPhieu = $checkPhieu;
            } else {
                // Reset nếu mã sai hoặc phiếu đã xử lý xong
                header('Location: ' . BASE_URL . 'qc');
                exit;
            }
        }

        // D. Thông báo
        $msg = '';
        if (isset($_GET['msg']) && $_GET['msg'] == 'success') {
            $msg = 'Đã lưu kết quả kiểm định thành công.';
        }

        $this->loadView('qc/index', [
            'phieus' => $phieus,
            'history' => $history,
            'selectedPhieu' => $selectedPhieu,
            'msg' => $msg
        ]);
    }

    // 2. STORE: Lưu kết quả
    public function store() {
        $this->requireRole(['QC']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $maPhieuKT = $_POST['MaPhieuKT'] ?? null;
                $maLoHang = $_POST['MaLoHang'] ?? null;
                $ketQua = $_POST['KetQua'] ?? null;
                $ghiChu = $_POST['GhiChu'] ?? ''; // Lấy ghi chú

                $maNV = $_SESSION['user_id'] ?? null;
                if (!$maNV) throw new Exception('Vui lòng đăng nhập.');

                $model = $this->loadModel('KetQuaKiemDinh');
                $maKD = $model->generateMaKD(); 

                // Gọi hàm saveResult mới
                $model->saveResult($maPhieuKT, $maLoHang, $maNV, $ketQua, $maKD, $ghiChu);

                header('Location: ' . BASE_URL . 'qc?msg=success');
                exit;

            } catch (Exception $e) {
                echo "<script>alert('Lỗi: " . $e->getMessage() . "'); window.history.back();</script>";
            }
        }
    }

    // API xem chi tiết cho Modal (History)
    public function view($maPhieuKT) {
        $this->requireRole(['QC']);
        $model = $this->loadModel('KetQuaKiemDinh');
        $phieu = $model->getById($maPhieuKT); 
        echo json_encode($phieu);
    }
}
?>