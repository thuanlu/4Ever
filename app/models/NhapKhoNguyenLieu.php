<?php
/**
 * Model NhapKhoNguyenLieu - Quản lý nhập kho nguyên liệu
 * Xử lý logic nghiệp vụ liên quan đến việc nhập nguyên liệu vào kho từ đơn đặt hàng
 */

require_once CONFIG_PATH . '/database.php';

class NhapKhoNguyenLieu {
    private $conn;
    private $database;

    /**
     * Constructor - Khởi tạo kết nối database
     * @param PDO|null $db Kết nối database từ BaseController (nếu có)
     */
    public function __construct($db = null) {
        if ($db !== null && $db instanceof PDO) {
            $this->conn = $db;
        } else {
            $this->database = new Database();
            $this->conn = $this->database->getConnection();
        }
    }

    /**
     * Lấy danh sách các phiếu nhập nguyên liệu cần nhập kho
     * (Các phiếu nhập từ nhà cung cấp nhưng chưa được nhập vào kho)
     * 
     * @return array Danh sách phiếu nhập
     */
    public function getDanhSachPhieuNhapCanNhap() {
        if ($this->conn === null) {
            error_log("NhapKhoNguyenLieu::getDanhSachPhieuNhapCanNhap - Database connection is null!");
            return [];
        }

        // Query đơn giản: lấy tất cả phiếu nhập có chi tiết
        $query = "SELECT DISTINCT
                    pn.MaPhieuNhap,
                    pn.NgayNhap,
                    pn.MaNhanVien,
                    pn.TongGiaTri,
                    ncc.TenNhaCungCap,
                    nv.HoTen AS TenNhanVien,
                    'Chưa nhập' AS TrangThaiNhap
                  FROM phieunhanguyenlieu pn
                  INNER JOIN nhacungcap ncc ON pn.MaNhaCungCap = ncc.MaNhaCungCap
                  LEFT JOIN nhanvien nv ON pn.MaNhanVien = nv.MaNV
                  INNER JOIN chitietphieunhapnguyenlieu ctn ON pn.MaPhieuNhap = ctn.MaPhieuNhap
                  ORDER BY pn.NgayNhap DESC, pn.MaPhieuNhap ASC";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("NhapKhoNguyenLieu::getDanhSachPhieuNhapCanNhap - Found " . count($results) . " phieu nhap");
            
            return $results;
        } catch (PDOException $e) {
            error_log("Error getting danh sach phieu nhap can nhap: " . $e->getMessage());
            error_log("SQL Query: " . $query);
            return [];
        }
    }

    /**
     * Lấy chi tiết phiếu nhập nguyên liệu theo mã phiếu nhập
     * 
     * @param string $maPhieuNhap Mã phiếu nhập
     * @return array|null Thông tin phiếu và chi tiết
     */
    public function getChiTietPhieuNhap($maPhieuNhap) {
        if ($this->conn === null) {
            error_log("NhapKhoNguyenLieu::getChiTietPhieuNhap - Database connection is null!");
            return null;
        }

        // Lấy thông tin phiếu nhập
        $queryPhieu = "SELECT 
                        pn.*,
                        ncc.TenNhaCungCap,
                        nv.HoTen AS TenNhanVien
                      FROM phieunhanguyenlieu pn
                      INNER JOIN nhacungcap ncc ON pn.MaNhaCungCap = ncc.MaNhaCungCap
                      LEFT JOIN nhanvien nv ON pn.MaNhanVien = nv.MaNV
                      WHERE pn.MaPhieuNhap = :maPhieuNhap";

        try {
            $stmt = $this->conn->prepare($queryPhieu);
            $stmt->bindParam(':maPhieuNhap', $maPhieuNhap);
            $stmt->execute();
            $phieu = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$phieu) {
                return null;
            }

            // Lấy chi tiết phiếu nhập với thông tin nguyên liệu
            $queryChiTiet = "SELECT 
                              ctn.MaPhieuNhap,
                              ctn.MaNguyenLieu,
                              ctn.SoLuongNhap,
                              ctn.DonGia,
                              ctn.ThanhTien,
                              nl.TenNguyenLieu,
                              nl.LoaiNguyenLieu,
                              nl.DonViTinh,
                              nl.SoLuongTonKho
                            FROM chitietphieunhapnguyenlieu ctn
                            INNER JOIN nguyenlieu nl ON ctn.MaNguyenLieu = nl.MaNguyenLieu
                            WHERE ctn.MaPhieuNhap = :maPhieuNhap
                            ORDER BY ctn.MaNguyenLieu ASC";

            $stmt = $this->conn->prepare($queryChiTiet);
            $stmt->bindParam(':maPhieuNhap', $maPhieuNhap);
            $stmt->execute();
            $chiTiet = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'phieu' => $phieu,
                'chiTiet' => $chiTiet
            ];
        } catch (PDOException $e) {
            error_log("Error getting chi tiet phieu nhap: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Kiểm tra phiếu nhập đã được nhập kho chưa
     * (Kiểm tra xem tồn kho đã được cập nhật từ phiếu nhập này chưa)
     * 
     * @param string $maPhieuNhap Mã phiếu nhập
     * @return bool True nếu đã nhập, False nếu chưa
     */
    public function daNhapKho($maPhieuNhap) {
        if ($this->conn === null) {
            return false;
        }

        // Lấy ngày nhập của phiếu
        $queryPhieu = "SELECT NgayNhap FROM phieunhanguyenlieu WHERE MaPhieuNhap = :maPhieuNhap";
        $stmt = $this->conn->prepare($queryPhieu);
        $stmt->bindParam(':maPhieuNhap', $maPhieuNhap);
        $stmt->execute();
        $phieu = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$phieu) {
            return false;
        }

        // Kiểm tra xem tất cả nguyên liệu trong phiếu đã được cập nhật tồn kho chưa
        // (tồn kho >= số lượng nhập và ngày cập nhật >= ngày nhập)
        $query = "SELECT COUNT(*) 
                  FROM chitietphieunhapnguyenlieu ctn
                  INNER JOIN nguyenlieu nl ON ctn.MaNguyenLieu = nl.MaNguyenLieu
                  WHERE ctn.MaPhieuNhap = :maPhieuNhap
                  AND nl.NgayCapNhat >= :ngayNhap
                  AND nl.SoLuongTonKho >= ctn.SoLuongNhap";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maPhieuNhap', $maPhieuNhap);
            $stmt->bindParam(':ngayNhap', $phieu['NgayNhap']);
            $stmt->execute();
            $count = (int)$stmt->fetchColumn();
            
            // Lấy tổng số nguyên liệu trong phiếu nhập
            $queryCount = "SELECT COUNT(*) FROM chitietphieunhapnguyenlieu WHERE MaPhieuNhap = :maPhieuNhap";
            $stmt = $this->conn->prepare($queryCount);
            $stmt->bindParam(':maPhieuNhap', $maPhieuNhap);
            $stmt->execute();
            $totalCount = (int)$stmt->fetchColumn();
            
            // Nếu tất cả nguyên liệu đã được cập nhật tồn kho thì coi như đã nhập
            return $totalCount > 0 && $count === $totalCount;
        } catch (PDOException $e) {
            error_log("Error checking daNhapKho: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xử lý nhập kho nguyên liệu từ phiếu nhập
     * (Cập nhật tồn kho từ chi tiết phiếu nhập)
     * 
     * @param string $maPhieuNhap Mã phiếu nhập
     * @param string $maNV Mã nhân viên (người xác nhận nhập kho)
     * @return array Kết quả xử lý
     */
    public function nhapKhoTuPhieuNhap($maPhieuNhap, $maNV) {
        if ($this->conn === null) {
            return [
                'success' => false,
                'message' => 'Lỗi kết nối database! Vui lòng kiểm tra MySQL đã chạy chưa.'
            ];
        }

        try {
            $this->conn->beginTransaction();

            // 1. Lấy thông tin phiếu nhập
            $phieuNhap = $this->getChiTietPhieuNhap($maPhieuNhap);
            if (!$phieuNhap) {
                throw new Exception("Không tìm thấy phiếu nhập ($maPhieuNhap)!");
            }

            // 2. Kiểm tra đã nhập chưa
            if ($this->daNhapKho($maPhieuNhap)) {
                throw new Exception("Phiếu nhập ($maPhieuNhap) đã được nhập kho rồi!");
            }

            // 3. Cập nhật tồn kho từ chi tiết phiếu nhập
            foreach ($phieuNhap['chiTiet'] as $chiTiet) {
                $maNguyenLieu = $chiTiet['MaNguyenLieu'];
                $soLuongNhap = $chiTiet['SoLuongNhap'];

                // Cập nhật tồn kho nguyên liệu
                $queryUpdateTonKho = "UPDATE nguyenlieu 
                                     SET SoLuongTonKho = SoLuongTonKho + :soLuongNhap,
                                         NgayCapNhat = NOW()
                                     WHERE MaNguyenLieu = :maNguyenLieu";

                $stmt = $this->conn->prepare($queryUpdateTonKho);
                $stmt->bindParam(':soLuongNhap', $soLuongNhap);
                $stmt->bindParam(':maNguyenLieu', $maNguyenLieu);
                $stmt->execute();
            }

            $this->conn->commit();

            return [
                'success' => true,
                'maPhieuNhap' => $maPhieuNhap,
                'message' => 'Nhập kho thành công!'
            ];

        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Error in nhapKhoTuPhieuNhap: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Từ chối nhập kho phiếu nhập (ghi log lý do)
     * 
     * @param string $maPhieuNhap Mã phiếu nhập
     * @param string $lyDo Lý do từ chối
     * @return array Kết quả
     */
    public function tuChoiNhapKho($maPhieuNhap, $lyDo) {
        // Có thể lưu vào bảng log hoặc cập nhật trạng thái
        // Ở đây chỉ log lại
        error_log("Từ chối nhập kho phiếu nhập $maPhieuNhap. Lý do: $lyDo");
        
        return [
            'success' => true,
            'message' => 'Đã ghi nhận lý do từ chối nhập kho.'
        ];
    }
}
?>

