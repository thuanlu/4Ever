<?php
require_once APP_PATH . '/controllers/BaseController.php';
class LapKeHoachCapXuongController extends BaseController {
    public function index() {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        $kehoachModel = $this->loadModel('KeHoachSanXuat');
        $kehoachs = $kehoachModel->getApprovedPlansByBoPhan($currentUser['bo_phan'] ?? null);
        $this->loadView('xuongtruong/lapkehoachcapxuong', ['kehoachs' => $kehoachs, 'pageTitle' => 'Kế hoạch tổng đã duyệt']);
    }
    public function create($maKeHoach) {
        $this->requireAuth();
        $kehoachModel = $this->loadModel('KeHoachSanXuat');
        $kehoach = $kehoachModel->getById($maKeHoach);
        $this->loadView('xuongtruong/lapkehoachcapxuong', ['kehoach' => $kehoach, 'pageTitle' => 'Lập kế hoạch cấp xưởng']);
    }
    public function store() {
        $this->requireAuth();
        // Validate dữ liệu POST
        $data = $_POST;
        if (empty($data['day_chuyen']) || empty($data['ca']) || empty($data['san_luong']) || empty($data['to_truong'])) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin.';
            $this->redirect('xuongtruong/lapkehoachcapxuong/create/' . $data['ma_kehoach']);
        }
        // Tạo lệnh sản xuất
        $lenhModel = $this->loadModel('LenhSanXuat');
        $lenhModel->create($data);
        // Gửi thông báo cho Tổ trưởng (giả lập)
        // ...
        $_SESSION['success'] = 'Lập kế hoạch cấp xưởng thành công!';
        $this->redirect('xuongtruong/lapkehoachcapxuong');
    }
}
?>
