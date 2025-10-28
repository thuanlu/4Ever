<?php
/**
 * Controller Home
 * Hệ thống quản lý sản xuất nhà máy 4Ever
 */

require_once APP_PATH . '/controllers/BaseController.php';

class HomeController extends BaseController {
    
    public function index() {
        // Luôn chuyển về trang login (không tự động chuyển tới dashboard)
        $this->redirect('login');
    }
}
?>