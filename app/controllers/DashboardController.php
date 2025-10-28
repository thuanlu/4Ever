<?php
/**
 * Controller Dashboard
 * Hệ thống quản lý sản xuất nhà máy 4Ever
 */

require_once APP_PATH . '/controllers/BaseController.php';

class DashboardController extends BaseController {
    public function tt() {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        $dashboardData = $this->getDashboardData('to_truong');
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Dashboard Tổ trưởng'
        ];
        $this->loadView('totruong/dashboard', $data);
    }

    public function qc() {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        $dashboardData = $this->getDashboardData('qc');
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Dashboard QC'
        ];
        $this->loadView('qc/dashboard', $data);
    }

    public function nvk() {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        $dashboardData = $this->getDashboardData('nhan_vien_kho_nl');
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Dashboard NV Kho'
        ];
        $this->loadView('kho/dashboard', $data);
    }

    public function cn() {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        $dashboardData = $this->getDashboardData('cong_nhan');
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Dashboard Công nhân'
        ];
        $this->loadView('congnhan/dashboard', $data);
    }

    public function admin() {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        $dashboardData = $this->getDashboardData('admin');
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Dashboard Admin'
        ];
        $this->loadView('admin/dashboard', $data);
    }
    public function kh() {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        $dashboardData = $this->getDashboardData('nhan_vien_ke_hoach');
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Dashboard KH'
        ];
    $this->loadView('kehoachsanxuat/dashboard', $data);
    }

    public function bgd() {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        $dashboardData = $this->getDashboardData('ban_giam_doc');
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Dashboard BGD'
        ];
    $this->loadView('giamdoc/dashboard', $data);
    }

    public function xt() {
        $this->requireAuth();
        $currentUser = $this->getCurrentUser();
        $dashboardData = $this->getDashboardData('xuong_truong');
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Dashboard XT'
        ];
    $this->loadView('xuongtruong/dashboard', $data);
    }
    
    public function index() {
        $this->requireAuth();
        
        $currentUser = $this->getCurrentUser();
        
        // Lấy dữ liệu thống kê cho dashboard
        $dashboardData = $this->getDashboardData($currentUser['role']);
        
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Trang chủ'
        ];
        
        $this->loadView('dashboard/index', $data);
    }
    
    private function getDashboardData($userRole) {
        try {
            $database = new Database();
            $conn = $database->getConnection();

            $data = [];

            // Thống kê chung
            $data['total_users'] = $this->getCount($conn, 'NhanVien', "TrangThai = 'Đang làm việc'");
            $data['total_workshops'] = $this->getCount($conn, 'PhanXuong');
            $data['total_products'] = $this->getCount($conn, 'SanPham');
            $data['total_materials'] = $this->getCount($conn, 'NguyenLieu');

            // Thống kê kế hoạch sản xuất
            $data['plans_draft'] = $this->getCount($conn, 'KeHoachSanXuat', "TrangThai = 'Chờ duyệt'");
            $data['plans_approved'] = $this->getCount($conn, 'KeHoachSanXuat', "TrangThai = 'Đã duyệt'");
            $data['plans_in_progress'] = $this->getCount($conn, 'KeHoachSanXuat', "TrangThai = 'Đang thực hiện'");
            $data['plans_completed'] = $this->getCount($conn, 'KeHoachSanXuat', "TrangThai = 'Hoàn thành'");

            // Thống kê nguyên vật liệu
            $data['materials_low_stock'] = $this->getCount($conn, 'NguyenLieu', 'SoLuongTonKho <= 10');
            // Đơn hàng chờ: Đơn hàng chưa xử lý
            $data['pending_orders'] = $this->getCount($conn, 'DonHang', "TrangThai = 'Đang xử lý'");

            // Thống kê chấm công hôm nay
            $today = date('Y-m-d');
            $data['today_present'] = $this->getCount($conn, 'ChamCongSanPham', "Ngay = '$today'");
            $data['today_absent'] = 0; // Không có trạng thái vắng mặt trong bảng này, có thể bổ sung nếu cần

            // Kế hoạch sản xuất gần đây
            $data['recent_plans'] = $this->getRecentProductionPlans($conn);

            // Nguyên vật liệu sắp hết
            $data['low_stock_materials'] = $this->getLowStockMaterials($conn);

            return $data;

        } catch (Exception $e) {
            return [];
        }
    }
    
    private function getCount($conn, $table, $condition = '1=1') {
        $query = "SELECT COUNT(*) as count FROM $table WHERE $condition";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    
    private function getRecentProductionPlans($conn) {
        $query = "SELECT k.*, d.TenDonHang, nv.HoTen as NguoiLap
                  FROM KeHoachSanXuat k
                  LEFT JOIN DonHang d ON k.MaDonHang = d.MaDonHang
                  LEFT JOIN NhanVien nv ON k.MaNV = nv.MaNV
                  ORDER BY k.NgayLap DESC
                  LIMIT 5";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getLowStockMaterials($conn) {
        $query = "SELECT * FROM NguyenLieu 
                  WHERE SoLuongTonKho <= 10
                  ORDER BY SoLuongTonKho ASC
                  LIMIT 5";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>