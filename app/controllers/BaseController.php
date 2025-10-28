<?php
/**
 * Lớp Controller cơ sở
 * Hệ thống quản lý sản xuất nhà máy 4Ever
 */

class BaseController {
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
            return new $modelName();
        } else {
            throw new Exception("Model không tồn tại: " . $modelName);
        }
    }
    
    protected function redirect($url) {
        header('Location: ' . BASE_URL . $url);
        exit();
    }
    
    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }
    
    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
        }
    }
    
    protected function requireRole($allowedRoles) {
        $this->requireAuth();
        
        if (!in_array($_SESSION['user_role'], $allowedRoles)) {
            $this->loadView('errors/403', ['message' => 'Bạn không có quyền truy cập tính năng này']);
            exit();
        }
    }
    
    protected function getCurrentUser() {
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'full_name' => $_SESSION['full_name'],
                'role' => $_SESSION['user_role']
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