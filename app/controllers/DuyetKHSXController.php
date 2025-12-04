<?php
// Tệp: app/controllers/DuyetKHSXController.php

require_once APP_PATH . '/controllers/BaseController.php';
require_once APP_PATH . '/models/KeHoachSanXuat.php';
require_once APP_PATH . '/models/ChiTietKeHoach.php';
require_once APP_PATH . '/models/DonHang.php';
require_once APP_PATH . '/models/PhanXuong.php';
require_once APP_PATH . '/models/DinhMucNguyenLieu.php';

class DuyetKHSXController extends BaseController {

    private $keHoachModel;
    private $chiTietKeHoachModel;
    private $donHangModel;
    private $phanXuongModel;
    private $dinhMucModel;

    public function __construct() {
        parent::__construct();
        // Chỉ cho phép BGD hoặc XT truy cập
        $this->requireRole(['BGD', 'XT']); 
        
        $this->keHoachModel = new KeHoachSanXuat($this->db);
        $this->chiTietKeHoachModel = new ChiTietKeHoach($this->db);
        $this->donHangModel = new DonHang($this->db);
        $this->phanXuongModel = new PhanXuong($this->db);
        $this->dinhMucModel = new DinhMucNguyenLieu($this->db);
    }

    /**
     * Danh sách các kế hoạch CHỜ DUYỆT
     */
    public function index() {
        $allKeHoachs = $this->keHoachModel->getAllWithDetails();
        
        $pendingKeHoachs = array_filter($allKeHoachs, function($k) {
            return $k['TrangThai'] === 'Chờ duyệt';
        });

        $this->loadView('giamdoc/index', ['kehoachs' => $pendingKeHoachs]);
    }

    /**
     * Xem chi tiết để Duyệt
     */
    public function view($maKeHoach) {
        $kehoach = $this->keHoachModel->getByIdWithNguoiLap($maKeHoach);
        
        if (!$kehoach) {
            $_SESSION['error'] = 'Không tìm thấy kế hoạch.';
            $this->redirect('giamdoc');
            return;
        }

        $donhangs = $this->donHangModel->getAll();
        $xuongs = $this->phanXuongModel->getAll();
        $plan_details = $this->chiTietKeHoachModel->getByMaKeHoach($maKeHoach);
        
        $product_ids = array_column($plan_details, 'MaSanPham');
        $bom_data = !empty($product_ids) ? $this->dinhMucModel->getBomDataForProducts($product_ids) : [];

        $data = [
            'kehoach' => $kehoach,
            'donhangs' => $donhangs,
            'xuongs' => $xuongs,
            'plan_details' => $plan_details,
            'bom_data' => $bom_data,
            'is_viewing' => true,
            'is_editing' => false,
            'form_title' => 'DUYỆT KẾ HOẠCH: ' . $maKeHoach
        ];

        $this->loadView('giamdoc/view', $data);
    }

    /**
     * Xử lý hành động DUYỆT (Đã sửa lỗi undefined method)
     */
    public function approve($maKeHoach) {
        $this->db->beginTransaction();
        try {
            // Kiểm tra trạng thái hiện tại
            $current = $this->keHoachModel->getById($maKeHoach);
            
            if ($current && $current['TrangThai'] === 'Chờ duyệt') {
                // Cập nhật trạng thái thành Đã duyệt
                $this->keHoachModel->update($maKeHoach, ['TrangThai' => 'Đã duyệt']);
                
                $this->db->commit();
                $_SESSION['success'] = 'Đã duyệt kế hoạch thành công.';
            } else {
                $_SESSION['error'] = 'Kế hoạch không ở trạng thái chờ duyệt hoặc không tồn tại.';
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
        }
        
        // Quay về danh sách
        $this->redirect('giamdoc');
    }

    /**
     * Xử lý hành động TỪ CHỐI (Kèm lý do)
     */
    public function reject($maKeHoach) {
        // 1. Lấy lý do từ form (Modal)
        $lyDo = $_POST['LyDoTuChoi'] ?? '';
        
        // 2. Tạo nội dung ghi chú mới
        // [CẬP NHẬT] Đưa lý do lên ĐẦU dòng ghi chú để dễ thấy
        $ghiChuTuChoi = "[TỪ CHỐI - " . date('d/m H:i') . "]: " . $lyDo . "\n----------------\n";

        $this->db->beginTransaction();
        try {
            $current = $this->keHoachModel->getById($maKeHoach);
            
            // Cho phép từ chối nếu đang Chờ duyệt
            if ($current && $current['TrangThai'] === 'Chờ duyệt') {
                
                // Nối lý do mới vào trước ghi chú cũ
                $newGhiChu = $ghiChuTuChoi . $current['GhiChu'];
                
                $this->keHoachModel->update($maKeHoach, [
                    'TrangThai' => 'Từ chối', // [QUAN TRỌNG] Đổi thành 'Từ chối'
                    'GhiChu'    => $newGhiChu 
                ]);

                $this->db->commit();
                $_SESSION['success'] = 'Đã từ chối kế hoạch. Trạng thái chuyển sang "Từ chối".';
            } else {
                $_SESSION['error'] = 'Trạng thái không hợp lệ để duyệt/hủy.';
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        $this->redirect('giamdoc');
    }
}
?>