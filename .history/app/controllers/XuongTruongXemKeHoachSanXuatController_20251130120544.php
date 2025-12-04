<?php
require_once APP_PATH . '/controllers/BaseController.php';
class XuongTruongXemKeHoachSanXuatController extends BaseController {
    public function index() {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        // Lấy tham số lọc từ form
        $ky = isset($_GET['ky']) ? $_GET['ky'] : '';
        $makehoach = isset($_GET['makehoach']) ? $_GET['makehoach'] : '';
        $donhang = isset($_GET['donhang']) ? $_GET['donhang'] : '';
        $kehoachs = $this->getApprovedPlans($ky, $makehoach, $donhang);

        // Xử lý hiển thị chi tiết kế hoạch nếu có tham số 'xem'
        $kehoach = null;
        $nhanvien = null;
        $donhangInfo = null;
        $chitietkehoach = [];
        if (isset($_GET['xem']) && $_GET['xem']) {
            $maKeHoach = $_GET['xem'];
            $database = new Database();
            $conn = $database->getConnection();
            // Lấy thông tin tổng kế hoạch
            $stmt = $conn->prepare("SELECT k.*, nv.HoTen as NguoiLap, dh.TenDonHang FROM KeHoachSanXuat k LEFT JOIN NhanVien nv ON k.MaNV = nv.MaNV LEFT JOIN DonHang dh ON k.MaDonHang = dh.MaDonHang WHERE k.MaKeHoach = ? LIMIT 1");
            $stmt->execute([$maKeHoach]);
            $kehoach = $stmt->fetch(PDO::FETCH_ASSOC);
            // Lấy thông tin người lập
            if ($kehoach && !empty($kehoach['MaNV'])) {
                $stmt = $conn->prepare("SELECT * FROM NhanVien WHERE MaNV = ? LIMIT 1");
                $stmt->execute([$kehoach['MaNV']]);
                $nhanvien = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            // Lấy thông tin đơn hàng
            if ($kehoach && !empty($kehoach['MaDonHang'])) {
                $stmt = $conn->prepare("SELECT * FROM DonHang WHERE MaDonHang = ? LIMIT 1");
                $stmt->execute([$kehoach['MaDonHang']]);
                $donhangInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            // Lấy chi tiết sản phẩm, phân xưởng
            $stmt = $conn->prepare("SELECT ct.*, sp.TenSanPham, px.TenPhanXuong FROM ChiTietKeHoach ct LEFT JOIN SanPham sp ON ct.MaSanPham = sp.MaSanPham LEFT JOIN PhanXuong px ON ct.MaPhanXuong = px.MaPhanXuong WHERE ct.MaKeHoach = ?");
            $stmt->execute([$maKeHoach]);
            $chitietkehoach = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $data = [
            'currentUser' => $currentUser,
            'kehoachs' => $kehoachs,
            'pageTitle' => 'Xem kế hoạch sản xuất',
            'kehoach' => $kehoach,
            'nhanvien' => $nhanvien,
            'donhang' => $donhangInfo,
            'chitietkehoach' => $chitietkehoach
        ];
        $this->loadView('xuongtruong/xemkehoachsanxuat', $data);
    }
    private function getApprovedPlans($ky = '', $makehoach = '', $donhang = '') {
        $database = new Database();
        $conn = $database->getConnection();
        $where = "WHERE k.TrangThai = 'Đã duyệt'";
        $params = [];

        if ($makehoach) {
            $where .= " AND k.MaKeHoach LIKE ?";
            $params[] = "%$makehoach%";
        }
        if ($donhang) {
            $where .= " AND k.MaDonHang LIKE ?";
            $params[] = "%$donhang%";
        }
        if ($ky == 'week') {
            $where .= " AND WEEK(k.NgayBatDau) = WEEK(CURDATE())";
        } elseif ($ky == 'month') {
            $where .= " AND MONTH(k.NgayBatDau) = MONTH(CURDATE())";
        }

        $query = "SELECT k.*, nv.HoTen as NguoiLap, dh.TenDonHang FROM KeHoachSanXuat k
                  LEFT JOIN NhanVien nv ON k.MaNV = nv.MaNV
                  LEFT JOIN DonHang dh ON k.MaDonHang = dh.MaDonHang
                  $where
                  ORDER BY k.NgayBatDau DESC";
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function exportPDF($maKeHoach) {
        // ... code xuất PDF ...
        $this->logAudit('export_pdf', $maKeHoach);
    }
    public function pinImportant($maKeHoach) {
        // ... code ghim quan trọng ...
        $this->logAudit('pin_important', $maKeHoach);
    }
    private function logAudit($action, $maKeHoach) {
        $database = new Database();
        $conn = $database->getConnection();
        $query = "INSERT INTO audit_log (user_id, action, ma_kehoach, timestamp) VALUES (:user_id, :action, :ma_kehoach, NOW())";
        $stmt = $conn->prepare($query);
        $userId = $_SESSION['user']['MaNV'] ?? null;
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':ma_kehoach', $maKeHoach);
        $stmt->execute();
    }
}
?>
