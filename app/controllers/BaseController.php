<?php
/**
 * Lớp Controller cơ sở
 * Hệ thống quản lý sản xuất nhà máy 4Ever
 */

class BaseController {
    protected $db;

    public function __construct() {
        // Khởi tạo kết nối CSDL
        $database = new Database();
        $this->db = $database->getConnection();
    }
    protected function loadView($viewName, $data = []) {
        // Truyền dữ liệu vào view
        extract($data);
        
        $viewFile = APP_PATH . '/views/' . $viewName . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "View không tồn tại: " . $viewName;
        }
    }
    
    protected function loadModel($modelName) {
        $modelFile = APP_PATH . '/models/' . $modelName . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $modelName($this->db);
        } else {
            throw new Exception("Model không tồn tại: " . $modelName);
        }
    }
    
    protected function redirect($url) {
        header('Location: ' . BASE_URL . $url);
        exit();
    }
    
    protected function json($data) {
        // Xóa output buffer nếu có để đảm bảo chỉ trả về JSON
        if (ob_get_level()) {
            ob_clean();
        }
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    protected function requireAuth() {
        // Ưu tiên kiểm tra $_SESSION['user'] (mảng user đầy đủ)
        if (empty($_SESSION['user']) || !isset($_SESSION['user']['MaNV'])) {
            $this->redirect('login');
        }
    }
    
    protected function requireRole($allowedRoles) {
        $this->requireAuth();
        
            // Normalize short role codes from session to canonical role names
            $roleMap = [
                'KH' => 'nhan_vien_ke_hoach',
                'BGD' => 'ban_giam_doc',
                'XT' => 'xuong_truong',
                'TT' => 'to_truong',
                'QC' => 'nhan_vien_qc',
                'NVK' => 'nhan_vien_kho_nl',
                'CN' => 'cong_nhan',
                'ADMIN' => 'admin'
            ];

            $current = $_SESSION['user_role'] ?? '';
            $currentNorm = $roleMap[$current] ?? $current;

            $normalizedAllowed = array_map(function($r) use ($roleMap) {
                return $roleMap[$r] ?? $r;
            }, $allowedRoles);

            if (!in_array($currentNorm, $normalizedAllowed)) {
                // If a dedicated error view exists, use it; otherwise render a simple 403 message
                $errorView = APP_PATH . '/views/errors/403.php';
                http_response_code(403);
                $message = 'Bạn không có quyền truy cập tính năng này';
                if (file_exists($errorView)) {
                    // Use existing view if present
                    $this->loadView('errors/403', ['message' => $message]);
                } else {
                    // Minimal inline rendering using the main layout so look-and-feel is consistent
                    $content = '<div class="container mt-5"><div class="text-center"><h1 class="display-6">403 — Không được phép</h1><p class="lead text-muted">' . htmlspecialchars($message) . '</p><p><a href="' . BASE_URL . '" class="btn btn-primary">Về trang chủ</a></p></div></div>';
                    include APP_PATH . '/views/layouts/main.php';
                }
                exit();
            }
    }
    
    protected function getCurrentUser() {
        // Ưu tiên trả về $_SESSION['user'] nếu có
        if (!empty($_SESSION['user'])) {
            return $_SESSION['user'];
        }
        // Nếu không có, trả về các biến lẻ (cũ)
        if (isset($_SESSION['user_id'])) {
            return [
                'MaNV' => $_SESSION['user_id'],
                'HoTen' => $_SESSION['full_name'] ?? '',
                'ChucVu' => $_SESSION['user_role'] ?? '',
                'BoPhan' => $_SESSION['bo_phan'] ?? ''
            ];
        }
        return null;
    }
    
    protected function getRoleDisplayName($role) {
        $roleNames = [
            'ban_giam_doc' => 'Ban Giám Đốc',
            'nhan_vien_ke_hoach' => 'Nhân viên Kế hoạch',
            'xuong_truong' => 'Xưởng trưởng',
            'to_truong' => 'Tổ trưởng',
            'nhan_vien_qc' => 'Nhân viên QC',
            'nhan_vien_kho_nl' => 'Nhân viên Kho NL',
            'nhan_vien_kho_tp' => 'Nhân viên Kho TP',
            'cong_nhan' => 'Công nhân'
        ];
        return $roleNames[$role] ?? $role;
    }
}
?>