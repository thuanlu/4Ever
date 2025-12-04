<?php
require_once APP_PATH . '/controllers/BaseController.php';
class LapKeHoachCapXuongController extends BaseController {
    public function index() {
        $this->requireAuth();
        $kehoachModel = $this->loadModel('KeHoachSanXuat');
        $lapKeHoachCapXuongModel = $this->loadModel('LapKeHoachCapXuong');
        // Lấy mã xưởng trưởng từ session
        $maXuongTruong = $_SESSION['user']['MaNV'] ?? '';
        $allKeHoachs = $kehoachModel->getApprovedPlansByXuongTruong($maXuongTruong);
        $kehoachCapXuongs = $lapKeHoachCapXuongModel->getAll();
        // Bổ sung MaDayChuyen và NgayKetThuc cho từng kế hoạch cấp xưởng
        $lenhModel = $this->loadModel('LenhSanXuat');
        foreach ($kehoachCapXuongs as &$khcx) {
            // Truy vấn lệnh sản xuất theo MaKHCapXuong
            $stmt = $this->db->prepare('SELECT ma_day_chuyen, ngay_ket_thuc_thuc_te FROM lenhsanxuat WHERE ma_ke_hoach_tong = ? LIMIT 1');
            $stmt->execute([$khcx['MaKeHoach']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $khcx['MaDayChuyen'] = $row['ma_day_chuyen'] ?? null;
            $khcx['NgayKetThuc'] = $row['ngay_ket_thuc_thuc_te'] ?? null;
        }
        unset($khcx);
        // Lọc các kế hoạch chưa được lập cấp xưởng
        $kehoachCapXuongMaKeHoachArr = array_column($kehoachCapXuongs, 'MaKeHoach');
        $kehoachs = array_filter($allKeHoachs, function($kh) use ($kehoachCapXuongMaKeHoachArr) {
            return !in_array($kh['MaKeHoach'], $kehoachCapXuongMaKeHoachArr);
        });
        // Sắp xếp kế hoạch theo ngày bắt đầu tăng dần (kế hoạch lập trước ở trên)
        usort($kehoachs, function($a, $b) {
            $dateA = strtotime($a['NgayBatDau'] ?? '');
            $dateB = strtotime($b['NgayBatDau'] ?? '');
            return $dateA <=> $dateB;
        });
        $selectedKeHoach = null;
        if (isset($_GET['kehoach'])) {
            $selectedKeHoach = $kehoachModel->getById($_GET['kehoach']);
            $sanLuongTong = $kehoachModel->getSanLuongTong($_GET['kehoach']);
            $selectedKeHoach['SanLuongTong'] = $sanLuongTong;

            // Lấy mã phân xưởng của xưởng trưởng đang đăng nhập (mapping trực tiếp từ bảng phanxuong)
            $maPhanXuong = null;
            if (!empty($_SESSION['user']) && !empty($_SESSION['user']['MaNV'])) {
                $maNV = $_SESSION['user']['MaNV'];
                $stmt = $this->db->prepare('SELECT MaPhanXuong FROM phanxuong WHERE MaXuongTruong = ? LIMIT 1');
                $stmt->execute([$maNV]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row && !empty($row['MaPhanXuong'])) {
                    $maPhanXuong = $row['MaPhanXuong'];
                }
            }
            // Bổ sung lấy mã sản phẩm, tên sản phẩm, số lượng sản phẩm từ ChiTietKeHoach, chỉ lấy đúng phân xưởng
            $chiTietModel = $this->loadModel('ChiTietKeHoach');
            $chiTietList = $chiTietModel->getByMaKeHoach($selectedKeHoach['MaKeHoach']);
            // Lọc chi tiết theo phân xưởng
            $chiTietListFiltered = array_filter($chiTietList, function($item) use ($maPhanXuong) {
                return $item['MaPhanXuong'] === $maPhanXuong;
            });
            if (!empty($chiTietListFiltered)) {
                $selectedKeHoach['SanPhamList'] = array_map(function($item) {
                    return [
                        'MaSanPham' => $item['MaSanPham'] ?? '',
                        'TenSanPham' => $item['TenSanPham'] ?? '',
                        'SanLuongMucTieu' => $item['SanLuongMucTieu'] ?? ''
                    ];
                }, $chiTietListFiltered);
            } else {
                $selectedKeHoach['SanPhamList'] = [];
            }
        }
        // Truy vấn danh sách dây chuyền
        $dayChuyenModel = $this->loadModel('DayChuyen');
        $dayChuyenList = $dayChuyenModel->getAll();
        // Truy vấn danh sách tổ trưởng
        $nhanVienModel = $this->loadModel('NhanVien');
        $toTruongList = $nhanVienModel->getToTruongList();
        // Truy vấn danh sách phân xưởng
        $phanXuongModel = $this->loadModel('PhanXuong');
        $phanXuongList = $phanXuongModel->getAll();

        $this->loadView('xuongtruong/lapkehoachcapxuong', [
            'kehoachs' => $kehoachs,
            'kehoach' => $selectedKeHoach,
            'dayChuyenList' => $dayChuyenList,
            'toTruongList' => $toTruongList,
            'phanXuongList' => $phanXuongList,
            'kehoachCapXuongs' => $kehoachCapXuongs,
            'pageTitle' => 'Lập kế hoạch cấp xưởng',
            'db' => $this->db
        ]);
    }
    // Xóa hàm create, không cần chuyển trang nữa
    public function store() {
        $this->requireAuth();
        $data = $_POST;

        // 1. Sửa Validation:
        if (empty($data['ca']) || !is_array($data['ca'])) {
            $_SESSION['error'] = 'Vui lòng thêm ít nhất một ca làm việc/dây chuyền.';
            $this->redirect('xuongtruong/lapkehoachcapxuong?kehoach=' . ($data['ma_kehoach'] ?? ''));
            return;
        }

        $db = $this->db;
        try {
            $lenhModel = $this->loadModel('LenhSanXuat');
            $lapKeHoachCapXuongModel = $this->loadModel('LapKeHoachCapXuong');

            $db->beginTransaction();

            // Lưu kế hoạch cấp xưởng tổng thể (1 dòng cho mỗi lần lập kế hoạch)
            $tongSanLuong = 0;
            foreach ($data['ca'] as $ca_item) {
                $tongSanLuong += (int)$ca_item['san_luong'];
            }
            // Lấy mã phân xưởng từ mã xưởng trưởng (giả sử mã nhân viên đăng nhập là xưởng trưởng)
            $maPhanXuong = null;
            if (!empty($_SESSION['user']) && !empty($_SESSION['user']['MaNV'])) {
                $maNV = $_SESSION['user']['MaNV'];
                // Truy vấn bảng nhanvien để lấy MaPhanXuong
                $stmt = $this->db->prepare('SELECT BoPhan FROM nhanvien WHERE MaNV = ? LIMIT 1');
                $stmt->execute([$maNV]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row && !empty($row['BoPhan'])) {
                    // BoPhan lưu tên phân xưởng, cần map sang mã phân xưởng
                    // Truy vấn bảng phanxuong để lấy MaPhanXuong từ TenPhanXuong
                    $stmt2 = $this->db->prepare('SELECT MaPhanXuong FROM phanxuong WHERE TenPhanXuong = ? LIMIT 1');
                    $stmt2->execute([$row['BoPhan']]);
                    $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                    if ($row2 && !empty($row2['MaPhanXuong'])) {
                        $maPhanXuong = $row2['MaPhanXuong'];
                    }
                }
            }
            // ĐẢM BẢO: Controller luôn lấy đúng giá trị mã KCX từ form POST, không tự sinh lại nếu đã có
            // Nếu phải sinh lại, dùng đúng regex như ở view: lấy 2 số cuối của mã kế hoạch tổng
            // Nếu $data['ma_kehoach_xuong'] có giá trị, dùng luôn giá trị này
            if (!empty($data['ma_kehoach_xuong'])) {
                $maKHCapXuong = $data['ma_kehoach_xuong'];
            } else {
                // Nếu không có, sinh lại đúng logic như view
                $soKeHoach = '';
                if (!empty($data['ma_kehoach'])) {
                    // Lấy đúng 2 số cuối của mã kế hoạch tổng (KH05 -> 05)
                    if (preg_match('/KH(\d{2})$/', $data['ma_kehoach'], $matches)) {
                        $soKeHoach = $matches[1];
                    } else {
                        // Nếu không đúng định dạng, lấy 2 số cuối bất kỳ
                        $soKeHoach = substr(preg_replace('/\D/', '', $data['ma_kehoach']), -2);
                    }
                }
                $maKHCapXuong = 'KCX-' . date('Ymd') . '-' . $soKeHoach;
            }
                // Log giá trị thực tế của $maKHCapXuong để debug
                error_log('DEBUG $maKHCapXuong: ' . $maKHCapXuong);
            // Ngày lập lấy thời gian hiện tại
            $ngayLap = date('Y-m-d H:i:s');
            // Công suất dự kiến lấy từ form, mặc định 0 nếu không có
            $congSuatDuKien = isset($data['cong_suat_du_kien']) ? (float)$data['cong_suat_du_kien'] : 0;
            $ngayBatDau = isset($data['ngay_bat_dau']) ? $data['ngay_bat_dau'] : null;
            $ngayKetThuc = isset($data['ngay_ket_thuc']) ? $data['ngay_ket_thuc'] : null;
            $keHoachCapXuongData = [
                'ma_kh_cap_xuong' => $maKHCapXuong,
                'ma_kehoach' => $data['ma_kehoach'],
                'ma_phan_xuong' => $maPhanXuong,
                'ngay_lap' => $ngayLap,
                'so_luong' => $tongSanLuong,
                'cong_suat_du_kien' => $congSuatDuKien,
                'trang_thai' => 'Chờ duyệt',
            ];
            error_log('DEBUG keHoachCapXuongData: ' . print_r($keHoachCapXuongData, true));
            $createResult = $lapKeHoachCapXuongModel->create($keHoachCapXuongData);
            if (!$createResult) {
                throw new Exception('Không thể lưu kế hoạch cấp xưởng. Dữ liệu: ' . print_r($keHoachCapXuongData, true));
            }

            // Lưu từng lệnh sản xuất cho từng ca
            foreach ($data['ca'] as $ca_item) {
                if (empty($ca_item['day_chuyen']) || empty($ca_item['san_luong']) || empty($ca_item['to_truong'])) {
                    throw new Exception('Thông tin ca làm việc không đầy đủ. Vui lòng kiểm tra lại.');
                }
                $lenhData = [
                    'ma_ke_hoach_tong' => $data['ma_kehoach'],
                    'ngay_lap_lenh' => $data['ngay_lap'],
                    'ma_day_chuyen' => $ca_item['day_chuyen'],
                    'ma_to_truong' => $ca_item['to_truong'],
                    'san_luong_muc_tieu' => $ca_item['san_luong'],
                    'trang_thai' => 'Mới tạo',
                    'ngay_bat_dau_thuc_te' => $ngayBatDau,
                    'ngay_ket_thuc_thuc_te' => $ngayKetThuc
                ];
                $result = $lenhModel->create($lenhData);
                if (!$result) {
                    throw new Exception('Không thể tạo lệnh sản xuất cho dây chuyền ' . $ca_item['day_chuyen']);
                }
            }

            $db->commit();
            $_SESSION['success'] = 'Lập kế hoạch cấp xưởng và tạo các lệnh sản xuất thành công!';
            $this->redirect('xuongtruong/lapkehoachcapxuong');

        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log('ERROR store LapKeHoachCapXuong: ' . $e->getMessage());
            $_SESSION['error'] = 'Đã xảy ra lỗi: ' . $e->getMessage();
            $this->redirect('xuongtruong/lapkehoachcapxuong?kehoach=' . ($data['ma_kehoach'] ?? ''));
        }
    }

    

}
?>