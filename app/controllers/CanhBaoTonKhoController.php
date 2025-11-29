<?php
require_once APP_PATH . '/controllers/BaseController.php';
require_once APP_PATH . '/models/CanhBaoTonKho.php';

class CanhBaoTonKhoController extends BaseController {

    public function stock() {
        // 1. Kiểm tra quyền
        $this->requireRole(['NVK']); 

        // 2. Load Model
        $model = $this->loadModel('CanhBaoTonKho');

        // 3. Lấy dữ liệu
        $lowStockList = $model->getLowStockMaterials();
        $expiringList = $model->getExpiringMaterials(30); 
        $counts       = $model->getCounts();
        $criticalAlerts = $model->getCriticalAlerts();

        // 4. Gọi View (Giả sử file view bạn lưu ở views/kho/tonkho.php)
        $this->loadView('kho/stock', [
            'lowStockList' => $lowStockList,
            'expiringList' => $expiringList,
            'counts'       => $counts,
            'criticalAlerts' => $criticalAlerts
        ]);
    }


}