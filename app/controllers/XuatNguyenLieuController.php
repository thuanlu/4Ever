<?php
require_once APP_PATH . '/controllers/BaseController.php';

class XuatNguyenLieuController extends BaseController {

    // 1. INDEX — Danh sách chờ + Form lập phiếu (nếu chọn) + Lịch sử
    public function index() {
        $this->requireRole(['NVK']);
        $model = $this->loadModel('XuatNguyenLieu');
        $requests = $model->getPendingRequests();
        $history = $model->getExportHistory();
        $selectedRequest = null; 
        $items = [];
        $criticalAlerts = [];
        
        if (isset($_GET['ma_yc'])) {
            $maYC = $_GET['ma_yc'];
            // Lấy thông tin phiếu yêu cầu
            $checkRequest = $model->getRequestById($maYC);

            // Kiểm tra hợp lệ
            if ($checkRequest && $checkRequest['TrangThai'] == 'Chờ duyệt') {
                $selectedRequest = $checkRequest;
                $items = $model->getRequestItems($maYC);
                $criticalAlerts = $model->getCriticalAlerts($maYC);
            } else {
                // Reset nếu mã không đúng
                header('Location: ' . BASE_URL . 'kho/xuatnguyenlieu');
                exit;
            }
        }
        $msg = '';
        if (!empty($_GET['msg']) && $_GET['msg'] == 'success') {
            $msg = "Lập phiếu xuất nguyên liệu thành công.";
        }

        $this->loadView('kho/index', [
            'requests'       => $requests,      
            'history'        => $history,
            'request'        => $selectedRequest, 
            'items'          => $items,     
            'criticalAlerts' => $criticalAlerts,
            'msg'            => $msg
        ]);
    }

    // 2. STORE — Xử lý lưu
    public function store() {
        $this->requireRole(['NVK']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        try {
            $maNV = $_SESSION['user_id'] ?? null;
            if (!$maNV) throw new Exception("Vui lòng đăng nhập.");

            $maPhieuYC = $_POST['MaPhieuYC'];
            $ngayXuat = $_POST['NgayLap'];
            $model = $this->loadModel('XuatNguyenLieu');

            $lastPX = $model->getLastMaPX();
            $num = $lastPX ? (int)substr($lastPX, 4) + 1 : 1;
            $maPX = "PXNL" . str_pad($num, 2, '0', STR_PAD_LEFT);

            $inputItems = $_POST['items'];  
            $requestItems = $model->getRequestItems($maPhieuYC);

            $items = [];
            foreach ($requestItems as $row) {
                $maNL = $row['MaNguyenLieu'];
                if (isset($inputItems[$maNL])) {
                     $items[] = [
                        'MaNguyenLieu'    => $maNL,
                        'SoLuongYeuCau'   => $row['SoLuong'],
                        'TonKho'          => $row['SoLuongTonKho'],
                        'ThucXuat'        => floatval($inputItems[$maNL]['sl_thuc_xuat'])
                    ];
                }
            }

            if (empty($items)) throw new Exception("Dữ liệu lỗi.");

            $data = [
                'MaPX' => $maPX, 'MaPhieuYC' => $maPhieuYC, 'NgayLap' => $ngayXuat,
                'MaNV' => $maNV, 'GhiChu' => "Xuất kho theo phiếu yêu cầu $maPhieuYC"
            ];

            if ($model->createExport($data, $items)) {
                header('Location: ' . BASE_URL . 'kho/xuatnguyenlieu?msg=success');
                exit;
            } else {
                throw new Exception("Lỗi hệ thống.");
            }
        } catch (Exception $e) {
            echo "<script>alert('Lỗi: " . $e->getMessage() . "'); window.history.back();</script>";
        }
    }

    // public function show($maPX) {
    //     $this->requireRole(["NVK"]);
    //     $model = $this->loadModel("XuatNguyenLieu");
    //     echo json_encode([
    //         'phieu' => $model->getExportById($maPX),
    //         'items' => $model->getExportItems($maPX)
    //     ]);
    // }
}
?>