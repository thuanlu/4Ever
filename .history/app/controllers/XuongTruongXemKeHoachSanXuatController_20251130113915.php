<?php
require_once APP_PATH . '/controllers/BaseController.php';
class XuongTruongXemKeHoachSanXuatController extends BaseController {
    public function index() {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        $kehoachs = $this->getApprovedPlans();

        // Xử lý hiển thị chi tiết kế hoạch nếu có tham số 'xem'
        $kehoach = null;
        $nhanvien = null;
        $donhang = null;
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
                $donhang = $stmt->fetch(PDO::FETCH_ASSOC);
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
            'donhang' => $donhang,
            'chitietkehoach' => $chitietkehoach
        ];
        $this->loadView('xuongtruong/xemkehoachsanxuat', $data);
    }
        private function getApprovedPlans() {
            $database = new Database();
            $conn = $database->getConnection();
            $query = "SELECT k.*, nv.HoTen as NguoiLap, dh.TenDonHang FROM KeHoachSanXuat k
                      LEFT JOIN NhanVien nv ON k.MaNV = nv.MaNV
                      LEFT JOIN DonHang dh ON k.MaDonHang = dh.MaDonHang
                      WHERE k.TrangThai = 'Đã duyệt'
                      ORDER BY k.NgayBatDau DESC";
            $stmt = $conn->prepare($query);
            $stmt->execute();
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
