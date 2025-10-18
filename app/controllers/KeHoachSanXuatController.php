<?php
/**
 * Controller KeHoachSanXuat - CRUD cho Nhân viên Kế hoạch
 */
require_once APP_PATH . '/controllers/BaseController.php';

class KeHoachSanXuatController extends BaseController {
    public function index() {
        $this->requireRole(['KH']);
        $model = $this->loadModel('KeHoachSanXuat');
        $kehoachs = $model->getAll();
        $this->loadView('kehoachsanxuat/index', ['kehoachs' => $kehoachs]);
    }

    public function create() {
        $this->requireRole(['KH']);
        $donhangModel = $this->loadModel('DonHang');
        $donhangs = $donhangModel->getAll();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'MaKeHoach' => $_POST['MaKeHoach'],
                'TenKeHoach' => $_POST['TenKeHoach'],
                'NgayBatDau' => $_POST['NgayBatDau'],
                'NgayKetThuc' => $_POST['NgayKetThuc'],
                'MaNV' => $_SESSION['user_id'],
                'MaDonHang' => $_POST['MaDonHang'],
                'TrangThai' => 'Chờ duyệt',
                'TongChiPhiDuKien' => $_POST['TongChiPhiDuKien'],
                'SoLuongCongNhanCan' => $_POST['SoLuongCongNhanCan'],
                'GhiChu' => $_POST['GhiChu']
            ];
            $model = $this->loadModel('KeHoachSanXuat');
            $model->create($data);
            $this->redirect('kehoachsanxuat');
        }
        $this->loadView('kehoachsanxuat/create', ['donhangs' => $donhangs]);
    }

    public function edit($maKeHoach) {
        $this->requireRole(['KH']);
        $model = $this->loadModel('KeHoachSanXuat');
        $donhangModel = $this->loadModel('DonHang');
        $donhangs = $donhangModel->getAll();
        $kehoach = $model->getById($maKeHoach);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'TenKeHoach' => $_POST['TenKeHoach'],
                'NgayBatDau' => $_POST['NgayBatDau'],
                'NgayKetThuc' => $_POST['NgayKetThuc'],
                'MaDonHang' => $_POST['MaDonHang'],
                'TrangThai' => $_POST['TrangThai'],
                'TongChiPhiDuKien' => $_POST['TongChiPhiDuKien'],
                'SoLuongCongNhanCan' => $_POST['SoLuongCongNhanCan'],
                'GhiChu' => $_POST['GhiChu']
            ];
            $model->update($maKeHoach, $data);
            $this->redirect('kehoachsanxuat');
        }
        $this->loadView('kehoachsanxuat/edit', ['kehoach' => $kehoach, 'donhangs' => $donhangs]);
    }

    public function delete($maKeHoach) {
        $this->requireRole(['KH']);
        $model = $this->loadModel('KeHoachSanXuat');
        $model->delete($maKeHoach);
        $this->redirect('kehoachsanxuat');
    }
}
?>
