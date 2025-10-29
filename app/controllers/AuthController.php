<?php
/**
 * Controller Authentication
 * Hệ thống quản lý sản xuất nhà máy 4Ever
 */

require_once APP_PATH . '/controllers/BaseController.php';

class AuthController extends BaseController {
    
    public function showLogin() {
        // Nếu đã đăng nhập, chuyển về dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
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

            $userModel = $this->loadModel('User');
            $user = $userModel->authenticate($identifier, $password);

            if ($user) {
                // Lưu thông tin vào session (theo bảng NhanVien)
                $_SESSION['user_id'] = $user['MaNV'];
                $_SESSION['username'] = $user['MaNV'];
                $_SESSION['full_name'] = $user['HoTen'];
                $_SESSION['user_role'] = $user['ChucVu'];
                $_SESSION['bo_phan'] = $user['BoPhan'];

                // if (strtoupper($user['ChucVu']) === 'QC') {
                //     $_SESSION['MaNV_QC'] = $user['MaNV'];
                //     $_SESSION['TenNV_QC'] = $user['HoTen'];
                // }

                $_SESSION['success'] = 'Đăng nhập thành công! Chào mừng ' . $user['HoTen'] . ' (' . $user['ChucVu'] . ')';
                // Chuyển hướng theo vai trò
                switch (strtoupper($user['ChucVu'])) {
                    case 'KH':
                        $this->redirect('kehoachsanxuat/dashboard');
                        break;
                    case 'ADMIN':
                        $this->redirect('xuongtruong/dashboard');
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
                        $this->redirect('dashboard');
                        break;
                }
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
    
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->logActivity($_SESSION['user_id'], 'logout', 'users', $_SESSION['user_id']);
        }
        
        // Xóa tất cả session
        session_destroy();
        
        // Khởi tạo session mới
        session_start();
        $_SESSION['success'] = 'Đăng xuất thành công!';
        
        $this->redirect('login');
    }
    
    private function logActivity($userId, $action, $tableName, $recordId) {
        try {
            $query = "INSERT INTO system_logs (user_id, action, table_name, record_id, ip_address, user_agent) 
                      VALUES (:user_id, :action, :table_name, :record_id, :ip_address, :user_agent)";
                      
            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare($query);
            
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':table_name', $tableName);
            $stmt->bindParam(':record_id', $recordId);
            $stmt->bindValue(':ip_address', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
            $stmt->bindValue(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
            
            $stmt->execute();
        } catch (Exception $e) {
            // Không làm gì nếu log thất bại
        }
    }
    
    // (Bỏ hàm detectLoginMethod vì không còn dùng)
}
?>