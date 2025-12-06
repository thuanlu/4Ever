<?php
require_once APP_PATH . '/controllers/BaseController.php';
class XuongTruongXemKeHoachSanXuatController extends BaseController {
    public function index() {
        $this->requireAuth();
        $this->requireRole(['XT']); // Chỉ xưởng trưởng mới được xem
        $currentUser = $this->getCurrentUser();
        
        // Xác định phân xưởng của xưởng trưởng đang đăng nhập
        $ycModel = $this->loadModel('YeuCauXuat');
        $maNV = $_SESSION['user_id'] ?? ($_SESSION['username'] ?? null);
        $maPX = $maNV ? $ycModel->getPhanXuongForUser($maNV) : null;
        
        if (!$maPX) {
            $this->loadView('xuongtruong/xemkehoachsanxuat', [
                'currentUser' => $currentUser,
                'kehoachs' => [],
                'pageTitle' => 'Xem kế hoạch sản xuất',
                'error' => 'Không xác định phân xưởng cho tài khoản hiện tại. Vui lòng liên hệ quản trị.'
            ]);
            return;
        }
        
        // Lấy danh sách kế hoạch đã phê duyệt cho phân xưởng của xưởng trưởng
        $kehoachModel = $this->loadModel('KeHoachSanXuat');
        $kehoachs = $kehoachModel->getApprovedPlans($maPX);
        
        $data = [
            'currentUser' => $currentUser,
            'kehoachs' => $kehoachs,
            'pageTitle' => 'Xem kế hoạch sản xuất'
        ];
        $this->loadView('xuongtruong/xemkehoachsanxuat', $data);
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
