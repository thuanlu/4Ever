<?php
require_once APP_PATH . '/controllers/BaseController.php';

class KetQuaKiemDinhController extends BaseController {

    public function index() {
        $this->requireRole(['QC']);
        $model = $this->loadModel('KetQuaKiemDinh');
        $phieus = $model->getPendingRequests();
        $this->loadView('qc/index', ['phieus' => $phieus]);
    }

    public function view($maPhieuKT) {
        $this->requireRole(['QC']);
        $model = $this->loadModel('KetQuaKiemDinh');
        $phieu = $model->getById($maPhieuKT);
        if (!$phieu) {
            $this->loadView('errors/404');
            return;
        }
        $this->loadView('qc/view', ['phieu' => $phieu]);
    }

    // public function save() {
    //     $this->requireRole(['QC']);
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $maPhieuKT = $_POST['MaPhieuKT'];
    //         $maLoHang = $_POST['MaLoHang'];
    //         $ketQua = $_POST['KetQua'];
    //         // $maNV = $_SESSION['user']['MaNV'];
    //         $maNV = $_SESSION['user_id'] ?? null;
    //         if (!$maNV) {
    //             die('Bạn cần đăng nhập để thực hiện thao tác này.');
    //         }
    //          // Tạo MaKD ngắn gọn
    //          $maKD = 'KD' . substr(uniqid(), -6);

    //         $model = $this->loadModel('KetQuaKiemDinh');
    //         $model->saveResult($maPhieuKT, $maLoHang, $maNV, $ketQua);

    //         $this->redirect('qc/index');
    //     }
    // }

    public function save() {
    $this->requireRole(['QC']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $maPhieuKT = $_POST['MaPhieuKT'] ?? null;
        $maLoHang = $_POST['MaLoHang'] ?? null;
        $ketQua = $_POST['KetQua'] ?? null;

        $maNV = $_SESSION['user_id'] ?? null;
        if (!$maNV) {
            die('Bạn cần đăng nhập để thực hiện thao tác này.');
        }

        // Tạo MaKD ngắn gọn
        $maKD = 'KD' . substr(uniqid(), -6);

        $model = $this->loadModel('KetQuaKiemDinh');
        $model->saveResult($maPhieuKT, $maLoHang, $maNV, $ketQua, $maKD);

        // $this->redirect('qc/index');
        // Redirect về trang index hoặc history sau khi lưu
        header('Location: ' . BASE_URL . 'qc'); 
        exit;
    }
}

    

    public function history() {
        $this->requireRole(['QC']);
        $model = $this->loadModel('KetQuaKiemDinh');
        $history = $model->getHistory();
        $this->loadView('qc/history', ['history' => $history]);
    }
}
?>