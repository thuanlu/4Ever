<?php
require_once APP_PATH . '/controllers/BaseController.php';
class LapKeHoachCapXuongController extends BaseController {
    public function index() {
        $this->requireAuth();
        $kehoachModel = $this->loadModel('KeHoachSanXuat');
        $kehoachs = $kehoachModel->getApprovedPlans();
        $selectedKeHoach = null;
        if (isset($_GET['kehoach'])) {
            $selectedKeHoach = $kehoachModel->getById($_GET['kehoach']);
        }
        $this->loadView('xuongtruong/lapkehoachcapxuong', [
            'kehoachs' => $kehoachs,
            'kehoach' => $selectedKeHoach,
            'pageTitle' => 'Lập kế hoạch cấp xưởng'
        ]);
    }
    // Xóa hàm create, không cần chuyển trang nữa
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
