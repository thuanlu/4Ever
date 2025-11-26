<?php
require_once APP_PATH . '/controllers/BaseController.php';
class XuongTruongXemKeHoachSanXuatController extends BaseController {
    public function index() {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        // Lấy danh sách kế hoạch đã phê duyệt cho xưởng
    $kehoachs = $this->getApprovedPlans();
        $data = [
            'currentUser' => $currentUser,
            'kehoachs' => $kehoachs,
            'pageTitle' => 'Xem kế hoạch sản xuất'
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
