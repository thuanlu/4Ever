<?php
/**
 * Model NhapKho - Quản lý nhập kho thành phẩm
 * Xử lý logic nghiệp vụ liên quan đến việc nhập thành phẩm vào kho
 */

require_once CONFIG_PATH . '/database.php';

class NhapKho {
    private $conn;
    private $database;

    /**
     * Constructor - Khởi tạo kết nối database
     */
    public function __construct() {
        $this->database = new Database();
        $this->conn = $this->database->getConnection();
    }

    /**
     * Lấy danh sách lô hàng cần nhập vào kho thành phẩm
     * Chỉ lấy các lô hàng có TrangThaiQC = 'Đạt' (đã được QC duyệt)
     * 
     * @return array Danh sách lô hàng với thông tin sản phẩm
     */
    public function getLoHangCanNhap() {
        $query = "SELECT 
                    lh.MaLoHang, 
                    lh.MaSanPham, 
                    sp.TenSanPham,
                    sp.Size,
                    sp.Mau,
                    lh.SoLuong, 
                    lh.TrangThaiQC,
                    lh.TrangThaiKho
                  FROM LoHang lh
                  INNER JOIN SanPham sp ON lh.MaSanPham = sp.MaSanPham
                  WHERE lh.TrangThaiQC = 'Đạt'
                  ORDER BY lh.MaLoHang DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting lo hang can nhap: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thông tin chi tiết của một lô hàng
     * 
     * @param string $maLoHang Mã lô hàng
     * @return array|null Thông tin lô hàng hoặc null
     */
    public function getLoHangById($maLoHang) {
        $query = "SELECT 
                    lh.*, 
                    sp.TenSanPham,
                    sp.Size,
                    sp.Mau,
                    sp.GiaXuat
                  FROM LoHang lh
                  INNER JOIN SanPham sp ON lh.MaSanPham = sp.MaSanPham
                  WHERE lh.MaLoHang = :maLoHang";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maLoHang', $maLoHang);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting lo hang by id: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Cập nhật trạng thái lô hàng thành "Đã nhập kho"
     * 
     * @param string $maLoHang Mã lô hàng
     * @return bool True nếu thành công, False nếu thất bại
     */
    public function updateTrangThaiLoHang($maLoHang) {
        $query = "UPDATE LoHang 
                  SET TrangThaiKho = 'Đã nhập kho'
                  WHERE MaLoHang = :maLoHang AND TrangThaiQC = 'Đạt'";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maLoHang', $maLoHang);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating trang thai lo hang: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo phiếu nhập kho thành phẩm
     * 
     * @param string $maLoHang Mã lô hàng
     * @param string $maNV Mã nhân viên
     * @param string $ghiChu Ghi chú
     * @return string|null Mã phiếu nhập hoặc null nếu thất bại
     */
    public function insertPhieuNhapKho($maLoHang, $maNV, $ghiChu = '') {
        // Tạo mã phiếu nhập tự động
        $maPhieuNhap = 'PNTP' . date('YmdHis') . rand(100, 999);
        
        $query = "INSERT INTO PhieuNhapSanPham 
                  (MaPhieuNhap, MaLoHang, MaNhanVien, NgayNhap)
                  VALUES (:maPhieuNhap, :maLoHang, :maNV, NOW())";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maPhieuNhap', $maPhieuNhap);
            $stmt->bindParam(':maLoHang', $maLoHang);
            $stmt->bindParam(':maNV', $maNV);
            
            if ($stmt->execute()) {
                return $maPhieuNhap;
            }
            return null;
        } catch(PDOException $e) {
            error_log("Error inserting phieu nhap kho: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Cập nhật tồn kho sản phẩm
     * Thêm số lượng thành phẩm vào kho
     * 
     * @param string $maSanPham Mã sản phẩm
     * @param int $soLuong Số lượng nhập thêm
     * @return bool True nếu thành công, False nếu thất bại
     */
    public function updateTonKho($maSanPham, $soLuong) {
        // Kiểm tra xem đã có tồn kho chưa
        $checkQuery = "SELECT COUNT(*) FROM TonKho WHERE MaSanPham = :maSanPham";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':maSanPham', $maSanPham);
        $checkStmt->execute();
        $exists = $checkStmt->fetchColumn() > 0;

        if ($exists) {
            // Cập nhật số lượng hiện có
            $query = "UPDATE TonKho 
                      SET SoLuongHienTai = SoLuongHienTai + :soLuong,
                          NgayCapNhat = NOW()
                      WHERE MaSanPham = :maSanPham";
        } else {
            // Tạo mới bản ghi tồn kho
            $query = "INSERT INTO TonKho 
                      (MaSanPham, SoLuongHienTai, ViTriKho, NgayCapNhat)
                      VALUES (:maSanPham, :soLuong, 'Kho A', NOW())";
        }
        
        try {
            $stmt = $this->conn->prepare($query);
            if ($exists) {
                $stmt->bindParam(':soLuong', $soLuong);
                $stmt->bindParam(':maSanPham', $maSanPham);
            } else {
                $stmt->bindParam(':maSanPham', $maSanPham);
                $stmt->bindParam(':soLuong', $soLuong);
            }
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating ton kho: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xử lý nhập kho một lô hàng
     * Thực hiện tất cả các bước: cập nhật trạng thái, tạo phiếu, cập nhật tồn kho
     * 
     * @param string $maLoHang Mã lô hàng
     * @param string $maNV Mã nhân viên
     * @return array Kết quả với mã phiếu nhập và thông báo
     */
    public function nhapKhoLoHang($maLoHang, $maNV) {
        try {
            // Bắt đầu transaction
            $this->conn->beginTransaction();

            // 1. Lấy thông tin lô hàng
            $loHang = $this->getLoHangById($maLoHang);
            if (!$loHang) {
                throw new Exception("Không tìm thấy lô hàng!");
            }

            if ($loHang['TrangThaiQC'] !== 'Đạt') {
                throw new Exception("Lô hàng chưa được QC duyệt!");
            }

            if ($loHang['TrangThaiKho'] === 'Đã nhập kho') {
                throw new Exception("Lô hàng đã được nhập kho rồi!");
            }

            // 2. Cập nhật trạng thái lô hàng
            if (!$this->updateTrangThaiLoHang($maLoHang)) {
                throw new Exception("Lỗi cập nhật trạng thái lô hàng!");
            }

            // 3. Tạo phiếu nhập kho
            $maPhieuNhap = $this->insertPhieuNhapKho($maLoHang, $maNV);
            if (!$maPhieuNhap) {
                throw new Exception("Lỗi tạo phiếu nhập kho!");
            }

            // 4. Cập nhật tồn kho
            if (!$this->updateTonKho($loHang['MaSanPham'], $loHang['SoLuong'])) {
                throw new Exception("Lỗi cập nhật tồn kho!");
            }

            // Commit transaction
            $this->conn->commit();

            return [
                'success' => true,
                'maPhieuNhap' => $maPhieuNhap,
                'message' => 'Nhập kho thành công!'
            ];

        } catch (Exception $e) {
            // Rollback transaction nếu có lỗi
            $this->conn->rollBack();
            error_log("Error in nhapKhoLoHang: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Nhập kho nhiều lô hàng cùng lúc
     * 
     * @param array $danhSachLoHang Mảng mã lô hàng
     * @param string $maNV Mã nhân viên
     * @return array Kết quả tổng hợp
     */
    public function nhapKhoNhieuLoHang($danhSachLoHang, $maNV) {
        $results = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($danhSachLoHang as $maLoHang) {
            $result = $this->nhapKhoLoHang($maLoHang, $maNV);
            if ($result['success']) {
                $successCount++;
            } else {
                $failCount++;
            }
            $results[] = [
                'maLoHang' => $maLoHang,
                'result' => $result
            ];
        }

        return [
            'success' => $failCount === 0,
            'successCount' => $successCount,
            'failCount' => $failCount,
            'details' => $results
        ];
    }
}
?>

