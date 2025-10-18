<?php
/**
 * Controller Home
 * Hệ thống quản lý sản xuất nhà máy 4Ever
 */

require_once APP_PATH . '/controllers/BaseController.php';

class HomeController extends BaseController {
    
    public function index() {
        // Nếu đã đăng nhập, chuyển về dashboard
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
        }
        
        // Nếu chưa đăng nhập, chuyển về trang login
        $this->redirect('login');
    }
}
?>
