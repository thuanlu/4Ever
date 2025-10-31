<?php
/**
 * Controller quản lý Phiếu Nhập Nguyên Vật Liệu
 * Hệ thống quản lý sản xuất nhà máy 4Ever
 */

class PhieuNhapController extends BaseController {
    private $phieuNhapModel;
    
    public function __construct() {
        $this->phieuNhapModel = $this->loadModel('PhieuNhapModel');
    }
    
    /**
     * Hiển thị danh sách phiếu nhập
     */
    public function index() {
        $this->requireRole(['KH', 'BGD']);
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        
        $phieuNhap = $this->phieuNhapModel->getDanhSachPhieuNhap($page, $limit);
        $total = $this->phieuNhapModel->countPhieuNhap();
        $totalPages = ceil($total / $limit);
        
        $data = [
            'phieuNhap' => $phieuNhap,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ];
        
    $this->loadView('kehoachsanxuat/phieu_nhap/list', $data);
    }
    
    /**
     * Hiển thị form tạo phiếu nhập
     */
    public function create() {
        $this->requireRole(['KH']);
        
        $keHoach = $this->phieuNhapModel->getKeHoachThieuNVL();
        $nhaCungCap = $this->phieuNhapModel->getNhaCungCap();
        
        $data = [
            'keHoach' => $keHoach,
            'nhaCungCap' => $nhaCungCap
        ];
        
        $this->loadView('phieu_nhap/create', $data);
    }
    
    /**
     * Xử lý tạo phiếu nhập
     */
    public function store() {
        $this->requireRole(['KH']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('phieu-nhap/create');
        }
        
        try {
            // Validate dữ liệu đầu vào
            $errors = $this->validatePhieuNhap($_POST);
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $this->redirect('phieu-nhap/create');
            }
            
            // Chuẩn bị dữ liệu
            $data = [
                'maNhaCungCap' => $_POST['maNhaCungCap'],
                'ngayNhap' => $_POST['ngayNhap'],
                'maNhanVien' => $_SESSION['user_id'],
                'tongGiaTri' => $_POST['tongGiaTri'],
                'maKeHoach' => $_POST['maKeHoach'],
                'thoiGianGiaoHang' => $_POST['thoiGianGiaoHang'],
                'chiPhiKhac' => $_POST['chiPhiKhac'] ?? 0,
                'vat' => $_POST['vat'] ?? 10,
                'ghiChu' => $_POST['ghiChu'] ?? '',
                'chiTiet' => json_decode($_POST['chiTiet'], true)
            ];
            
            // Tạo phiếu nhập
            $maPhieuNhap = $this->phieuNhapModel->createPhieuNhap($data);
            
            $_SESSION['success'] = "Tạo phiếu nhập thành công! Mã phiếu: " . $maPhieuNhap;
            $this->redirect('phieu-nhap');
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi khi tạo phiếu nhập: " . $e->getMessage();
            $this->redirect('phieu-nhap/create');
        }
    }
    
    /**
     * Hiển thị chi tiết phiếu nhập
     */
    public function show($maPhieuNhap) {
        $this->requireRole(['KH', 'BGD']);
        
        $phieuNhap = $this->phieuNhapModel->getChiTietPhieuNhap($maPhieuNhap);
        $chiTietNVL = $this->phieuNhapModel->getChiTietNVLPhieuNhap($maPhieuNhap);
        
        if (!$phieuNhap) {
            $_SESSION['error'] = "Không tìm thấy phiếu nhập!";
            $this->redirect('phieu-nhap');
        }
        
        $data = [
            'phieuNhap' => $phieuNhap,
            'chiTietNVL' => $chiTietNVL
        ];
        
        $this->loadView('phieu_nhap/show', $data);
    }
    
    /**
     * API: Lấy chi tiết nguyên vật liệu cần nhập theo kế hoạch
     */
    public function getChiTietNVL() {
        $this->requireRole(['KH']);
        
        if (!isset($_GET['maKeHoach'])) {
            $this->json(['error' => 'Thiếu mã kế hoạch']);
        }
        
        $maKeHoach = $_GET['maKeHoach'];
        $chiTietNVL = $this->phieuNhapModel->getChiTietNVLCanNhap($maKeHoach);
        
        $this->json(['success' => true, 'data' => $chiTietNVL]);
    }
    
    /**
     * API: Lấy thông tin nhà cung cấp
     */
    public function getNhaCungCap() {
        $this->requireRole(['KH']);
        
        if (!isset($_GET['maNhaCungCap'])) {
            $this->json(['error' => 'Thiếu mã nhà cung cấp']);
        }
        
        $nhaCungCap = $this->phieuNhapModel->getNhaCungCap();
        $selectedNCC = null;
        
        foreach ($nhaCungCap as $ncc) {
            if ($ncc['MaNhaCungCap'] === $_GET['maNhaCungCap']) {
                $selectedNCC = $ncc;
                break;
            }
        }
        
        if (!$selectedNCC) {
            $this->json(['error' => 'Không tìm thấy nhà cung cấp']);
        }
        
        $this->json(['success' => true, 'data' => $selectedNCC]);
    }
    
    /**
     * Validate dữ liệu phiếu nhập
     */
    private function validatePhieuNhap($data) {
        $errors = [];
        
        // Kiểm tra mã nhà cung cấp
        if (empty($data['maNhaCungCap'])) {
            $errors[] = "Vui lòng chọn nhà cung cấp";
        }
        
        // Kiểm tra ngày nhập
        if (empty($data['ngayNhap'])) {
            $errors[] = "Vui lòng chọn ngày nhập";
        } elseif (!strtotime($data['ngayNhap'])) {
            $errors[] = "Ngày nhập không hợp lệ";
        }
        
        // Kiểm tra thời gian giao hàng
        if (empty($data['thoiGianGiaoHang'])) {
            $errors[] = "Vui lòng chọn thời gian giao hàng";
        } elseif (!strtotime($data['thoiGianGiaoHang'])) {
            $errors[] = "Thời gian giao hàng không hợp lệ";
        }
        
        // Kiểm tra chi phí khác
        if (isset($data['chiPhiKhac']) && !empty($data['chiPhiKhac'])) {
            if (!is_numeric($data['chiPhiKhac']) || $data['chiPhiKhac'] < 0) {
                $errors[] = "Chi phí khác phải là số dương";
            }
        }
        
        // Kiểm tra chi tiết nguyên vật liệu
        if (empty($data['chiTiet'])) {
            $errors[] = "Vui lòng chọn ít nhất một nguyên vật liệu";
        } else {
            $chiTiet = json_decode($data['chiTiet'], true);
            if (empty($chiTiet) || !is_array($chiTiet)) {
                $errors[] = "Dữ liệu chi tiết không hợp lệ";
            }
        }
        
        return $errors;
    }
    
    /**
     * Tính toán tổng chi phí
     */
    public function calculateTotal() {
        $this->requireRole(['KH']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed']);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $tongThanhTien = 0;
        $chiPhiKhac = isset($data['chiPhiKhac']) ? (float)$data['chiPhiKhac'] : 0;
        $vat = isset($data['vat']) ? (float)$data['vat'] : 0;
        
        if (isset($data['chiTiet']) && is_array($data['chiTiet'])) {
            foreach ($data['chiTiet'] as $item) {
                $thanhTien = (float)$item['soLuongNhap'] * (float)$item['donGia'];
                $tongThanhTien += $thanhTien;
            }
        }
        
        $tongTruocVAT = $tongThanhTien + $chiPhiKhac;
        $tienVAT = $tongTruocVAT * ($vat / 100);
        $tongChiPhi = $tongTruocVAT + $tienVAT;
        
        $this->json([
            'success' => true,
            'data' => [
                'tongThanhTien' => $tongThanhTien,
                'chiPhiKhac' => $chiPhiKhac,
                'tongTruocVAT' => $tongTruocVAT,
                'tienVAT' => $tienVAT,
                'tongChiPhi' => $tongChiPhi
            ]
        ]);
    }
    
    /**
     * Duyệt phiếu nhập
     */
    public function duyet($maPhieuNhap) {
        $this->requireRole(['BGD', 'KH']);
        
        try {
            // Cập nhật trạng thái phiếu nhập
            $database = new Database();
            $conn = $database->getConnection();
            
            $query = "UPDATE PhieuNhaNguyenLieu SET TrangThai = 'Đã duyệt' WHERE MaPhieuNhap = :maPhieuNhap";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':maPhieuNhap', $maPhieuNhap);
            $result = $stmt->execute();
            
            if ($result) {
                // Cập nhật tồn kho nguyên vật liệu
                $this->updateTonKhoAfterApproval($maPhieuNhap);
                $this->json(['success' => true, 'message' => 'Duyệt phiếu nhập thành công']);
            } else {
                $this->json(['success' => false, 'error' => 'Không thể duyệt phiếu nhập']);
            }
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'error' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Hủy phiếu nhập
     */
    public function huy($maPhieuNhap) {
        $this->requireRole(['BGD', 'KH']);
        
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            $query = "UPDATE PhieuNhaNguyenLieu SET TrangThai = 'Đã hủy' WHERE MaPhieuNhap = :maPhieuNhap";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':maPhieuNhap', $maPhieuNhap);
            $result = $stmt->execute();
            
            if ($result) {
                $this->json(['success' => true, 'message' => 'Hủy phiếu nhập thành công']);
            } else {
                $this->json(['success' => false, 'error' => 'Không thể hủy phiếu nhập']);
            }
            
        } catch (Exception $e) {
            $this->json(['success' => false, 'error' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Cập nhật tồn kho sau khi duyệt phiếu nhập
     */
    private function updateTonKhoAfterApproval($maPhieuNhap) {
        try {
            $database = new Database();
            $conn = $database->getConnection();
            
            // Lấy chi tiết phiếu nhập
            $query = "
                SELECT MaNguyenLieu, SoLuongNhap 
                FROM ChiTietPhieuNhapNguyenLieu 
                WHERE MaPhieuNhap = :maPhieuNhap
            ";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':maPhieuNhap', $maPhieuNhap);
            $stmt->execute();
            $chiTiet = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Cập nhật tồn kho cho từng nguyên vật liệu
            foreach ($chiTiet as $item) {
                $updateQuery = "
                    UPDATE NguyenLieu 
                    SET SoLuongTonKho = SoLuongTonKho + :soLuongNhap,
                        NgayCapNhat = CURRENT_TIMESTAMP
                    WHERE MaNguyenLieu = :maNguyenLieu
                ";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bindParam(':soLuongNhap', $item['SoLuongNhap']);
                $updateStmt->bindParam(':maNguyenLieu', $item['MaNguyenLieu']);
                $updateStmt->execute();
            }
            
        } catch (Exception $e) {
            // Log error nhưng không throw để không ảnh hưởng đến việc duyệt phiếu
            error_log("Lỗi cập nhật tồn kho: " . $e->getMessage());
        }
    }
}
?>
