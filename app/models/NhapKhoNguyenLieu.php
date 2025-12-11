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
     * Sinh mã phiếu nhập nguyên liệu mới (PNNL01, PNNL02, ...)
     */
    private function generateMaPhieuNhap() {
        $prefix = 'PNNL';
        $query = "SELECT MaPhieuNhap 
                  FROM phieunhanguyenlieu 
                  WHERE MaPhieuNhap LIKE '{$prefix}%' 
                  ORDER BY CAST(SUBSTRING(MaPhieuNhap, " . (strlen($prefix) + 1) . ") AS UNSIGNED) DESC 
                  LIMIT 1";

        try {
            $stmt = $this->conn->query($query);
            $lastCode = $stmt ? $stmt->fetchColumn() : null;
            $nextNumber = $lastCode ? ((int)substr($lastCode, strlen($prefix))) + 1 : 1;
            return sprintf('%s%02d', $prefix, $nextNumber);
        } catch (PDOException $e) {
            error_log("Error generateMaPhieuNhap: " . $e->getMessage());
            // Fallback để không chặn quy trình
            return $prefix . date('ymdHis');
        }
    }

    /**
     * Lấy danh sách các phiếu ĐẶT NVL cần nhập kho
     * (Truy xuất từ bảng phieudatnvl)
     * 
     * @return array Danh sách phiếu đặt
     */
    public function getDanhSachPhieuNhapCanNhap() {
        if ($this->conn === null) {
            error_log("NhapKhoNguyenLieu::getDanhSachPhieuNhapCanNhap - Database connection is null!");
            return [];
        }

        $query = "SELECT 
                    pd.MaPhieu,
                    pd.TenPhieu,
                    pd.NgayLapPhieu,
                    pd.TongChiPhiDuKien,
                    pd.TrangThai,
                    ncc.TenNhaCungCap
                  FROM phieudatnvl pd
                  LEFT JOIN nhacungcap ncc ON pd.MaNhaCungCap = ncc.MaNhaCungCap
                  WHERE pd.TrangThai <> 'Đã nhập kho'
                  ORDER BY pd.NgayLapPhieu DESC, pd.MaPhieu ASC";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("NhapKhoNguyenLieu::getDanhSachPhieuNhapCanNhap - Found " . count($results) . " phieu dat");
            
            return $results;
        } catch (PDOException $e) {
            error_log("Error getting danh sach phieu dat can nhap: " . $e->getMessage());
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

        // Lấy thông tin phiếu ĐẶT
        $queryPhieu = "SELECT 
                        pd.*,
                        ncc.TenNhaCungCap,
                        kh.TenKeHoach
                      FROM phieudatnvl pd
                      LEFT JOIN nhacungcap ncc ON pd.MaNhaCungCap = ncc.MaNhaCungCap
                      LEFT JOIN kehoachsanxuat kh ON pd.MaKHSX = kh.MaKeHoach
                      WHERE pd.MaPhieu = :maPhieuNhap";

        try {
            $stmt = $this->conn->prepare($queryPhieu);
            $stmt->bindParam(':maPhieuNhap', $maPhieuNhap);
            $stmt->execute();
            $phieu = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$phieu) {
                return null;
            }

            // Lấy chi tiết phiếu đặt + tồn kho hiện tại của NVL
            $queryChiTiet = "SELECT 
                              ctd.MaPhieu,
                              ctd.MaNVL AS MaNguyenLieu,
                              ctd.TenNVL AS TenNguyenLieu,
                              ctd.DonViTinh,
                              ctd.SoLuongCan AS SoLuongNhap,
                              ctd.DonGia,
                              ctd.ThanhTien,
                              nl.LoaiNguyenLieu,
                              nl.SoLuongTonKho
                            FROM chitietphieudatnvl ctd
                            LEFT JOIN nguyenlieu nl ON ctd.MaNVL = nl.MaNguyenLieu
                            WHERE ctd.MaPhieu = :maPhieuNhap
                            ORDER BY ctd.MaNVL ASC";

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
     * Kiểm tra phiếu đặt đã được nhập kho chưa
     * (Dựa vào trạng thái phiếu đặt)
     * 
     * @param string $maPhieuNhap Mã phiếu đặt
     * @return bool True nếu đã nhập, False nếu chưa
     */
    public function daNhapKho($maPhieuNhap) {
        if ($this->conn === null) {
            return false;
        }

        $queryPhieu = "SELECT TrangThai FROM phieudatnvl WHERE MaPhieu = :maPhieu LIMIT 1";
        $stmt = $this->conn->prepare($queryPhieu);
        $stmt->bindParam(':maPhieu', $maPhieuNhap);
        $stmt->execute();
        $phieu = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$phieu) {
            return false;
        }

        return strtolower(trim($phieu['TrangThai'])) === strtolower('Đã nhập kho');
    }

    /**
     * Xử lý nhập kho nguyên liệu từ PHIẾU ĐẶT
     * - Tạo mới phiếu nhập trong bảng phieunhanguyenlieu
     * - Ghi chi tiết vào chitietphieunhapnguyenlieu
     * - Cập nhật tồn kho nguyenlieu
     * - Cập nhật trạng thái phiếu đặt thành "Đã nhập kho"
     * 
     * @param string $maPhieuNhap Mã phiếu đặt
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

            // 1. Lấy thông tin phiếu đặt + chi tiết
            $phieuNhap = $this->getChiTietPhieuNhap($maPhieuNhap);
            if (!$phieuNhap) {
                throw new Exception("Không tìm thấy phiếu đặt ($maPhieuNhap)!");
            }

            // 2. Kiểm tra đã nhập chưa
            if ($this->daNhapKho($maPhieuNhap)) {
                throw new Exception("Phiếu đặt ($maPhieuNhap) đã được nhập kho rồi!");
            }

            $phieuDat = $phieuNhap['phieu'];
            $chiTiet = $phieuNhap['chiTiet'];

            // 3. Tính tổng giá trị thực tế từ chi tiết (fallback: TongChiPhiDuKien)
            $tongGiaTri = 0;
            foreach ($chiTiet as $item) {
                $thanhTien = $item['ThanhTien'] ?? ($item['SoLuongNhap'] * ($item['DonGia'] ?? 0));
                $tongGiaTri += $thanhTien;
            }
            if ($tongGiaTri <= 0 && isset($phieuDat['TongChiPhiDuKien'])) {
                $tongGiaTri = $phieuDat['TongChiPhiDuKien'];
            }

            // 4. Tạo phiếu nhập mới
            $maPhieuNhapMoi = $this->generateMaPhieuNhap();
            $queryInsertPhieu = "INSERT INTO phieunhanguyenlieu (MaPhieuNhap, MaNhaCungCap, NgayNhap, MaNhanVien, TongGiaTri)
                                 VALUES (:maPhieuNhap, :maNhaCungCap, NOW(), :maNhanVien, :tongGiaTri)";
            $stmt = $this->conn->prepare($queryInsertPhieu);
            $stmt->execute([
                ':maPhieuNhap' => $maPhieuNhapMoi,
                ':maNhaCungCap' => $phieuDat['MaNhaCungCap'],
                ':maNhanVien' => $maNV,
                ':tongGiaTri' => $tongGiaTri
            ]);

            // 5. Thêm chi tiết phiếu nhập + cập nhật tồn kho
            $queryInsertChiTiet = "INSERT INTO chitietphieunhapnguyenlieu (MaPhieuNhap, MaNguyenLieu, SoLuongNhap, DonGia, ThanhTien)
                                   VALUES (:maPhieuNhap, :maNguyenLieu, :soLuongNhap, :donGia, :thanhTien)";
            $stmtChiTiet = $this->conn->prepare($queryInsertChiTiet);

            foreach ($chiTiet as $item) {
                $maNguyenLieu = $item['MaNguyenLieu'];
                $soLuongNhap = $item['SoLuongNhap'];
                $donGia = $item['DonGia'] ?? 0;
                $thanhTien = $item['ThanhTien'] ?? ($soLuongNhap * $donGia);

                $stmtChiTiet->execute([
                    ':maPhieuNhap' => $maPhieuNhapMoi,
                    ':maNguyenLieu' => $maNguyenLieu,
                    ':soLuongNhap' => $soLuongNhap,
                    ':donGia' => $donGia,
                    ':thanhTien' => $thanhTien
                ]);

                // Cập nhật tồn kho nguyên liệu
                $queryUpdateTonKho = "UPDATE nguyenlieu 
                                      SET SoLuongTonKho = SoLuongTonKho + :soLuongNhap,
                                          NgayCapNhat = NOW()
                                      WHERE MaNguyenLieu = :maNguyenLieu";

                $stmtUpdate = $this->conn->prepare($queryUpdateTonKho);
                $stmtUpdate->bindParam(':soLuongNhap', $soLuongNhap);
                $stmtUpdate->bindParam(':maNguyenLieu', $maNguyenLieu);
                $stmtUpdate->execute();
            }

            // 6. Cập nhật trạng thái phiếu đặt
            $queryUpdatePhieuDat = "UPDATE phieudatnvl SET TrangThai = 'Đã nhập kho' WHERE MaPhieu = :maPhieu";
            $stmt = $this->conn->prepare($queryUpdatePhieuDat);
            $stmt->bindParam(':maPhieu', $maPhieuNhap);
            $stmt->execute();

            $this->conn->commit();

            return [
                'success' => true,
                'maPhieuNhap' => $maPhieuNhapMoi,
                'message' => 'Tạo phiếu nhập và cập nhật kho thành công!'
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

