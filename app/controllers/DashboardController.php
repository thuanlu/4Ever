<?php
/**
 * Controller Dashboard
 * Hệ thống quản lý sản xuất nhà máy 4Ever
 *
 * ĐÃ CẬP NHẬT: Sử dụng $this->db từ BaseController cho tất cả các hàm thống kê.
 */

require_once APP_PATH . '/controllers/BaseController.php';

class DashboardController extends BaseController {
    
    // Hàm index() chung cho các vai trò không có dashboard riêng
    public function index() {
        $this->requireAuth();
        
        $currentUser = $this->getCurrentUser();
        
        // Chuyển hướng đến dashboard cụ thể nếu có
        // (Logic này nên nằm trong AuthController, nhưng để đây dự phòng)
        $roleMap = [
            'KH' => 'kehoachsanxuat/dashboard',
            'ADMIN' => 'admin/dashboard',
            'BGD' => 'giamdoc/dashboard',
            'XT' => 'xuongtruong/dashboard',
            'TT' => 'totruong/dashboard',
            'QC' => 'qc/dashboard',
            'NVK' => 'kho/dashboard',
            'CN' => 'congnhan/dashboard'
        ];
        
        if (isset($roleMap[strtoupper($currentUser['role'])])) {
             $this->redirect($roleMap[strtoupper($currentUser['role'])]);
        }
        
        // Dashboard chung (nếu có)
        $dashboardData = $this->getDashboardData($currentUser['role']);
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Trang chủ'
        ];
        
        $this->loadView('dashboard/index', $data);
    }
    
    // --- CÁC DASHBOARD CỤ THỂ THEO VAI TRÒ ---

    public function kh() {
        $this->requireRole(['KH']); // Yêu cầu vai trò KH
        $currentUser = $this->getCurrentUser();
        $dashboardData = $this->getDashboardData('KH');
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Dashboard Kế hoạch'
        ];
        $this->loadView('kehoachsanxuat/dashboard', $data);
    }

    public function tt() {
        $this->requireRole(['TT']);
        $currentUser = $this->getCurrentUser();
        $dashboardData = $this->getDashboardData('TT');
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Dashboard Tổ trưởng'
        ];
        $this->loadView('totruong/dashboard', $data);
    }

    public function qc() {
        $this->requireRole(['QC']);
        $currentUser = $this->getCurrentUser();
        $dashboardData = $this->getDashboardData('QC');
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Dashboard QC'
        ];
        $this->loadView('qc/dashboard', $data);
    }

    public function nvk() {
        $this->requireRole(['NVK']);
        $currentUser = $this->getCurrentUser();
        $dashboardData = $this->getDashboardData('NVK');
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Dashboard NV Kho'
        ];
        $this->loadView('kho/dashboard', $data);
    }

    public function cn() {
        $this->requireRole(['CN']);
        $currentUser = $this->getCurrentUser();
        $dashboardData = $this->getDashboardData('CN');
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Dashboard Công nhân'
        ];
        $this->loadView('congnhan/dashboard', $data);
    }

    public function admin() {
        $this->requireRole(['ADMIN']);
        $currentUser = $this->getCurrentUser();
        $dashboardData = $this->getDashboardData('ADMIN');
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Dashboard Admin'
        ];
        $this->loadView('admin/dashboard', $data);
    }

    public function bgd() {
        $this->requireRole(['BGD']);
        $currentUser = $this->getCurrentUser();
        $dashboardData = $this->getDashboardData('BGD');
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Dashboard BGD'
        ];
        $this->loadView('giamdoc/dashboard', $data);
    }

    public function xt() {
        $this->requireRole(['XT']);
        $currentUser = $this->getCurrentUser();
        $dashboardData = $this->getDashboardData('XT');
        $data = [
            'currentUser' => $currentUser,
            'dashboardData' => $dashboardData,
            'pageTitle' => 'Dashboard XT'
        ];
        $this->loadView('xuongtruong/dashboard', $data);
    }
    
    // --- CÁC HÀM TRUY VẤN DỮ LIỆU THỐNG KÊ ---
    
    private function getDashboardData($userRole) {
        // Sử dụng $this->db từ BaseController
        $db = $this->db;
        $data = [];

        try {
            // Thống kê chung (có thể cache lại)
            $data['total_users'] = $this->getCount($db, 'NhanVien', "TrangThai = 'Đang làm việc'");
            $data['total_workshops'] = $this->getCount($db, 'PhanXuong');
            $data['total_products'] = $this->getCount($db, 'SanPham');
            $data['total_materials'] = $this->getCount($db, 'NguyenLieu');

            // Thống kê kế hoạch sản xuất
            $data['plans_draft'] = $this->getCount($db, 'KeHoachSanXuat', "TrangThai = 'Chờ duyệt'");
            $data['plans_approved'] = $this->getCount($db, 'KeHoachSanXuat', "TrangThai = 'Đã duyệt'");
            $data['plans_in_progress'] = $this->getCount($db, 'KeHoachSanXuat', "TrangThai = 'Đang thực hiện'");
            $data['plans_completed'] = $this->getCount($db, 'KeHoachSanXuat', "TrangThai = 'Hoàn thành'");

            // Thống kê kho
            $data['materials_low_stock'] = $this->getCount($db, 'NguyenLieu', 'SoLuongTonKho <= 10');
            $data['pending_orders'] = $this->getCount($db, 'DonHang', "TrangThai = 'Đang xử lý'");

            // Thống kê chấm công hôm nay
            $today = date('Y-m-d');
            $data['today_present'] = $this->getCount($db, 'ChamCongSanPham', "Ngay = :today", [':today' => $today]);
            
            // Kế hoạch sản xuất gần đây
            $data['recent_plans'] = $this->getRecentProductionPlans($db);

            // Nguyên vật liệu sắp hết
            $data['low_stock_materials'] = $this->getLowStockMaterials($db);
            
            // (Thêm các thống kê riêng theo $userRole nếu cần)

            return $data;

        } catch (Exception $e) {
            error_log("GetDashboardData Error: " . $e->getMessage());
            return []; // Trả về mảng rỗng nếu có lỗi
        }
    }
    
    private function getCount($db, $table, $condition = '1=1', $params = []) {
        $query = "SELECT COUNT(*) as count FROM $table WHERE $condition";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    
    private function getRecentProductionPlans($db) {
        $query = "SELECT k.MaKeHoach, k.TenKeHoach, k.TrangThai, d.TenDonHang, nv.HoTen as NguoiLap
                  FROM KeHoachSanXuat k
                  LEFT JOIN DonHang d ON k.MaDonHang = d.MaDonHang
                  LEFT JOIN NhanVien nv ON k.MaNV = nv.MaNV
                  ORDER BY k.NgayLap DESC
                  LIMIT 5";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getLowStockMaterials($db) {
        $query = "SELECT MaNguyenLieu, TenNguyenLieu, SoLuongTonKho, DonViTinh 
                  FROM NguyenLieu 
                  WHERE SoLuongTonKho <= 10
                  ORDER BY SoLuongTonKho ASC
                  LIMIT 5";
        $stmt = $db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>