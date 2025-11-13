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
            $sanLuongTong = $kehoachModel->getSanLuongTong($_GET['kehoach']);
            $selectedKeHoach['SanLuongTong'] = $sanLuongTong;
        }
        // Truy vấn danh sách dây chuyền
        $dayChuyenModel = $this->loadModel('DayChuyen');
        $dayChuyenList = $dayChuyenModel->getAll(); // [{MaDayChuyen, TenDayChuyen}]
        // Truy vấn danh sách tổ trưởng
        $nhanVienModel = $this->loadModel('NhanVien');
        $toTruongList = $nhanVienModel->getToTruongList(); // [{MaNV, HoTen}]
        $this->loadView('xuongtruong/lapkehoachcapxuong', [
            'kehoachs' => $kehoachs,
            'kehoach' => $selectedKeHoach,
            'dayChuyenList' => $dayChuyenList,
            'toTruongList' => $toTruongList,
            'pageTitle' => 'Lập kế hoạch cấp xưởng'
        ]);
    }
    // Xóa hàm create, không cần chuyển trang nữa
    public function store() {
        $this->requireAuth();
        $data = $_POST;

        // 1. Sửa Validation:
        // Chỉ cần kiểm tra xem mảng 'ca' có tồn tại và không rỗng hay không.
        if (empty($data['ca']) || !is_array($data['ca'])) {
            $_SESSION['error'] = 'Vui lòng thêm ít nhất một ca làm việc/dây chuyền.';
            // Redirect về trang lập kế hoạch với mã kế hoạch đã chọn
            $this->redirect('xuongtruong/lapkehoachcapxuong?kehoach=' . ($data['ma_kehoach'] ?? ''));
            return; // Dừng thực thi
        }

        // 2. Sửa Logic Lưu Dữ Liệu:
        // Dùng transaction để đảm bảo tất cả lệnh được tạo hoặc không tạo cái nào
        $db = $this->db; // Lấy đối tượng DB từ BaseController
        try {
            $lenhModel = $this->loadModel('LenhSanXuat');
            
            // Bắt đầu transaction
            $db->beginTransaction();

            // Lặp qua từng ca làm việc đã thêm
            foreach ($data['ca'] as $ca_item) {
                
                // Kiểm tra dữ liệu bên trong mỗi ca
                if (empty($ca_item['day_chuyen']) || empty($ca_item['san_luong']) || empty($ca_item['to_truong'])) {
                    // Nếu một ca bị thiếu thông tin, hủy toàn bộ
                    throw new Exception('Thông tin ca làm việc không đầy đủ. Vui lòng kiểm tra lại.');
                }

                // Chuẩn bị dữ liệu cho model LenhSanXuat
                // LƯU Ý: Tên các key (cột) ở đây phải khớp với tên cột
                // trong bảng 'lenhsanxuat' của bạn.
                $lenhData = [
                    'ma_ke_hoach_tong' => $data['ma_kehoach'], // Tham chiếu về kế hoạch tổng
                    'ngay_lap_lenh' => $data['ngay_lap'],      // Ngày lập lệnh (lấy từ form)
                    'ma_day_chuyen' => $ca_item['day_chuyen'],
                    'ma_to_truong' => $ca_item['to_truong'],
                    'san_luong_muc_tieu' => $ca_item['san_luong'],
                    'trang_thai' => 'Mới tạo' // Hoặc 'Chưa thực hiện'
                    // Bạn có thể cần thêm các trường khác như:
                    // 'ngay_bat_dau' => $data['ngay_bat_dau'],
                    // 'ngay_ket_thuc' => $data['ngay_ket_thuc'],
                ];

                // Gọi create() cho TỪNG ca
                $result = $lenhModel->create($lenhData);
                
                if (!$result) {
                    // Nếu tạo 1 lệnh thất bại, hủy toàn bộ
                    throw new Exception('Không thể tạo lệnh sản xuất cho dây chuyền ' . $ca_item['day_chuyen']);
                }
            }

            // Nếu tất cả thành công, commit transaction
            $db->commit();
            
            $_SESSION['success'] = 'Lập kế hoạch cấp xưởng và tạo các lệnh sản xuất thành công!';
            $this->redirect('xuongtruong/lapkehoachcapxuong');

        } catch (Exception $e) {
            // Nếu có bất kỳ lỗi nào, rollback lại
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $_SESSION['error'] = 'Đã xảy ra lỗi: ' . $e->getMessage();
            // Redirect về trang lập kế hoạch với mã kế hoạch đã chọn
            $this->redirect('xuongtruong/lapkehoachcapxuong?kehoach=' . ($data['ma_kehoach'] ?? ''));
        }
    }

    

}
?>