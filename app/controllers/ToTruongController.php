<?php
require_once APP_PATH . '/controllers/BaseController.php';

/**
 * Controller Tổ trưởng – Quản lý phân công & lập ca làm việc
 */
class ToTruongController extends BaseController {

    public function index() {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();

        // Dữ liệu mẫu tạm để view chạy được
        $phieuCongViecs = [
            (object)['ma_phieu' => 'P001', 'cong_viec' => 'Lắp ráp linh kiện', 'so_luong' => 1000, 'ngay_ket_thuc' => '2025-11-10'],
            (object)['ma_phieu' => 'P002', 'cong_viec' => 'Kiểm tra chất lượng', 'so_luong' => 500, 'ngay_ket_thuc' => '2025-11-12'],
        ];
        $caLamViecs = [
            (object)['id' => 1, 'ten_ca' => 'Ca sáng', 'so_cong_nhan' => 10],
            (object)['id' => 2, 'ten_ca' => 'Ca chiều', 'so_cong_nhan' => 8],
            (object)['id' => 3, 'ten_ca' => 'Ca đêm', 'so_cong_nhan' => 6],
        ];

        $data = [
            'currentUser' => $currentUser,
            'phieuCongViecs' => $phieuCongViecs,
            'caLamViecs' => $caLamViecs,
            'pageTitle' => 'Phân công & Lập ca làm việc'
        ];

        $this->loadView('totruong/phancalamviec', $data);
    }
}
?>
