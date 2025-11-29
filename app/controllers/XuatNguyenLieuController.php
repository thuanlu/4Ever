<?php
require_once APP_PATH . '/controllers/BaseController.php';

class XuatNguyenLieuController extends BaseController {


    // 1. INDEX — Danh sách phiếu yêu cầu
    public function index() {
        $this->requireRole(['NVK']);
        $model = $this->loadModel('XuatNguyenLieu');

        $requests = $model->getPendingRequests();

        $msg = '';
        if (!empty($_GET['msg']) && $_GET['msg'] == 'success') {
            $msg = "Lập phiếu xuất nguyên liệu thành công.";
        }

        $this->loadView('kho/index', [
            'requests' => $requests,
            'msg' => $msg
        ]);
    }

    // 2. CREATE — Hiển thị form lập phiếu
    public function create($maPhieuYC) {
        $this->requireRole(['NVK']);
        $model = $this->loadModel('XuatNguyenLieu');

        $request = $model->getRequestById($maPhieuYC);
        $items = $model->getRequestItems($maPhieuYC);
        $criticalAlerts = $model->getCriticalAlerts($maPhieuYC);

        if (!$request || $request['TrangThai'] != 'Chờ duyệt') {
            header('Location: ' . BASE_URL . 'kho/xuatnguyenlieu');
            exit;
        }

        $this->loadView('kho/create', [
            'request' => $request,
            'items'   => $items,
            'criticalAlerts' => $criticalAlerts
        ]);
    }

    // 3. STORE — Lưu phiếu xuất
    public function store() {
        $this->requireRole(['NVK']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        try {
            $maNV = $_SESSION['user_id'] ?? null;
            if (!$maNV) throw new Exception("Vui lòng đăng nhập.");

            $maPhieuYC = $_POST['MaPhieuYC'];
            $ngayXuat = $_POST['NgayLap'];

            $model = $this->loadModel('XuatNguyenLieu');

            // tạo mã phiếu xuất
            $lastPX = $model->getLastMaPX();
            $num = $lastPX ? (int)substr($lastPX, 4) + 1 : 1;
            $maPX = "PXNL" . str_pad($num, 2, '0', STR_PAD_LEFT);

            // lấy danh sách nguyên liệu cần xuất từ chi tiết phiếu yêu cầu
            // $items = $model->getRequestItems($maPhieuYC);
            // dữ liệu thực xuất từ form
            $inputItems = $_POST['items'];  

            // dữ liệu yêu cầu từ DB
            $requestItems = $model->getRequestItems($maPhieuYC);

            // gom lại dữ liệu để gửi xuống model
            $items = [];

            foreach ($requestItems as $row) {
                $maNL = $row['MaNguyenLieu'];

                $items[] = [
                    'MaNguyenLieu'     => $maNL,
                    'SoLuongYeuCau'    => $row['SoLuong'],
                    'TonKho'           => $row['SoLuongTonKho'],
                    'ThucXuat'         => floatval($inputItems[$maNL]['sl_thuc_xuat'])
                ];
            }

            if (empty($items)) {
                throw new Exception("Phiếu yêu cầu không có nguyên liệu.");
            }

            // tạo dữ liệu gửi xuống model
            $data = [
                'MaPX'      => $maPX,
                'MaPhieuYC' => $maPhieuYC,
                'NgayLap'   => $ngayXuat,
                'MaNV'      => $maNV,
                'GhiChu'    => "Xuất kho theo phiếu yêu cầu $maPhieuYC"
            ];

            $ok = $model->createExport($data, $items);

            if ($ok) {
                header('Location: ' . BASE_URL . 'kho/xuatnguyenlieu?msg=success');
                exit;
            } else {
                throw new Exception("Không thể tạo phiếu xuất.");
            }

        } catch (Exception $e) {
            echo "<div class='alert alert-danger m-5'>Lỗi: {$e->getMessage()}</div>";
        }
    }



}
?>
