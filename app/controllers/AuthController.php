<?php
/**
 * Controller Authentication
 * Hệ thống quản lý sản xuất nhà máy 4Ever
 *
 * ĐÃ CẬP NHẬT: Sử dụng $this->db từ BaseController cho logActivity.
 */

require_once APP_PATH . '/controllers/BaseController.php';

class AuthController extends BaseController {
    
    public function showLogin() {
        // Nếu đã đăng nhập, chuyển về dashboard
        if (isset($_SESSION['user_id'])) {
            // Chuyển hướng theo vai trò khi đã đăng nhập
            $this->redirectBasedOnRole($_SESSION['user_role']);
        }
        
        $this->loadView('auth/login');
    }
    
    public function processLogin() {
        try {
            $identifier = trim($_POST['username'] ?? ''); // MaNV hoặc SoDienThoai
            $password = $_POST['password'] ?? '';

            if (empty($identifier) || empty($password)) {
                $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin đăng nhập và mật khẩu';
                $this->redirect('login');
            }
            
            // Giả định User model đã được cấu hình để nhận $this->db
            $userModel = $this->loadModel('User'); 
            $user = $userModel->authenticate($identifier, $password);

            if ($user) {
                // Lưu thông tin vào session (theo bảng NhanVien)
                $_SESSION['user_id'] = $user['MaNV'];
                $_SESSION['username'] = $user['MaNV'];
                $_SESSION['full_name'] = $user['HoTen'];
                $_SESSION['user_role'] = $user['ChucVu']; // Sử dụng ChucVu làm role
                $_SESSION['bo_phan'] = $user['BoPhan'];

                $_SESSION['success'] = 'Đăng nhập thành công! Chào mừng ' . $user['HoTen'] . ' (' . $user['ChucVu'] . ')';
                
                // Ghi log đăng nhập
                $this->logActivity($user['MaNV'], 'login', 'nhanvien', $user['MaNV']);
                
                // Chuyển hướng theo vai trò
                $this->redirectBasedOnRole($user['ChucVu']);
                
            } else {
                $_SESSION['error'] = 'Thông tin đăng nhập hoặc mật khẩu không chính xác';
                $this->redirect('login');
            }

        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra trong quá trình đăng nhập. Vui lòng thử lại.';
            $this->redirect('login');
        }
    }
    
    /**
     * Hàm trợ giúp chuyển hướng theo vai trò
     */
    private function redirectBasedOnRole($role) {
        switch (strtoupper($role)) {
            case 'KH':
                $this->redirect('kehoachsanxuat/dashboard'); // Chuyển đến dashboard KH
                break;
            case 'ADMIN':
                $this->redirect('admin/dashboard'); // Sửa lại
                break;
            case 'BGD':
                $this->redirect('giamdoc/dashboard');
                break;
            case 'XT':
                $this->redirect('xuongtruong/dashboard');
                break;
            case 'TT':
                $this->redirect('totruong/dashboard');
                break;
            case 'QC':
                $this->redirect('qc/dashboard');
                break;
            case 'NVK':
                $this->redirect('kho/dashboard');
                break;
            case 'CN':
                $this->redirect('congnhan/dashboard');
                break;
            default:
                // Nếu vai trò không xác định, về dashboard chung
                $this->redirect('dashboard'); 
                break;
        }
    }
    
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->logActivity($_SESSION['user_id'], 'logout', 'nhanvien', $_SESSION['user_id']);
        }
        
        session_destroy();
        session_start(); // Khởi tạo session mới để lưu thông báo
        $_SESSION['success'] = 'Đăng xuất thành công!';
        
        $this->redirect('login');
    }
    
    /**
     * Ghi log hoạt động (sử dụng $this->db)
     */
    private function logActivity($userId, $action, $tableName, $recordId) {
        try {
            // Giả định bạn có bảng system_logs
            $query = "INSERT INTO system_logs (user_id, action, table_name, record_id, ip_address, user_agent) 
                      VALUES (:user_id, :action, :table_name, :record_id, :ip_address, :user_agent)";
            
            // Sử dụng $this->db từ BaseController
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':table_name', $tableName);
            $stmt->bindParam(':record_id', $recordId);
            $stmt->bindValue(':ip_address', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
            $stmt->bindValue(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
            
            $stmt->execute();
        } catch (Exception $e) {
            // Ghi log lỗi vào file thay vì dừng ứng dụng
            error_log('Log activity failed: ' . $e->getMessage());
        }
    }
}
?>