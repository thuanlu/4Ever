<?php
require_once APP_PATH . '/controllers/BaseController.php';
class XuongTruongXemKeHoachSanXuatController extends BaseController {
    public function index() {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        // Lấy danh sách kế hoạch đã phê duyệt cho xưởng
        $kehoachs = $this->getApprovedPlans($currentUser['BoPhan'] ?? null);
        $data = [
            'currentUser' => $currentUser,
            'kehoachs' => $kehoachs,
            'pageTitle' => 'Xem kế hoạch sản xuất'
        ];
        $this->loadView('xuongtruong/xemkehoachsanxuat', $data);
    }
    private function getApprovedPlans($boPhan) {
        $database = new Database();
        $conn = $database->getConnection();
        $query = "SELECT * FROM KeHoachSanXuat WHERE TrangThai = 'Đã duyệt' ORDER BY NgayBatDau DESC";
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
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':ma_kehoach', $maKeHoach);
        $stmt->execute();
    }
}
?>
