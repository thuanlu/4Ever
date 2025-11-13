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
     * LOGIC CỐT LÕI: Thực hiện nhập kho (không có transaction)
     * Sẽ throw Exception nếu thất bại.
     */
    private function _thucHienNhapKho($maLoHang, $maNV) {
        // 1. Lấy thông tin lô hàng
        $loHang = $this->getLoHangById($maLoHang);
        if (!$loHang) {
            throw new Exception("Không tìm thấy lô hàng ($maLoHang)!");
        }

        // 2. Kiểm tra QC: chỉ cần có bất kỳ kết quả 'Đạt' nào
        if (!$this->isLoHangDatQC($maLoHang)) {
            $ketQuaLatest = $this->getKetQuaLatest($maLoHang);
            throw new Exception("Lô hàng ($maLoHang) chưa đạt QC! (KQ gần nhất: " . ($ketQuaLatest ?? 'NULL') . ")");
        }

        // 3. Kiểm tra đã nhập chưa
        if ($this->daNhapKho($maLoHang)) {
            throw new Exception("Lô hàng ($maLoHang) đã được nhập kho rồi!");
        }

        // 4. Tạo phiếu nhập kho (đồng thời là mốc đánh dấu đã nhập)
        $maKDLatest = $this->getMaKDLatest($maLoHang);
        $maPhieuNhap = $this->insertPhieuNhapKho($maLoHang, $maNV, '', $maKDLatest);
        if (!$maPhieuNhap) {
            throw new Exception("Lỗi tạo phiếu nhập kho cho lô ($maLoHang)!");
        }

        // 5. Cập nhật tồn kho
        if (!$this->updateTonKho($loHang['MaSanPham'], $loHang['SoLuong'])) {
            throw new Exception("Lỗi cập nhật tồn kho cho sản phẩm (" . $loHang['MaSanPham'] . ")!");
        }

        // Trả về mã phiếu nhập nếu thành công
        return $maPhieuNhap;
    }

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
        // Lấy lô hàng có KQKD mới nhất là "Đạt" và chưa nhập kho
        $query = "SELECT 
                    lh.MaLoHang, 
                    lh.MaSanPham, 
                    sp.TenSanPham,
                    sp.Size,
                    sp.Mau,
                    lh.SoLuong, 
                    lh.TrangThaiQC,
                    kq_latest.KetQua AS KetQuaKiemDinh,
                    CASE 
                        WHEN EXISTS (SELECT 1 FROM PhieuNhapSanPham p WHERE p.MaLoHang = lh.MaLoHang)
                            THEN 'Đã nhập kho'
                        ELSE 'Chưa nhập kho'
                    END AS TrangThaiKho
                  FROM LoHang lh
                  INNER JOIN SanPham sp ON lh.MaSanPham = sp.MaSanPham
                  INNER JOIN (
                        SELECT kq.MaLoHang, kq.KetQua, kq.NgayLap
                        FROM KetQuaKiemDinh kq
                        INNER JOIN (
                            SELECT MaLoHang, MAX(NgayLap) AS LatestNgayLap
                            FROM KetQuaKiemDinh
                            GROUP BY MaLoHang
                        ) last ON last.MaLoHang = kq.MaLoHang AND last.LatestNgayLap = kq.NgayLap
                  ) kq_latest ON kq_latest.MaLoHang = lh.MaLoHang
                  WHERE NOT EXISTS (SELECT 1 FROM PhieuNhapSanPham pns WHERE pns.MaLoHang = lh.MaLoHang)
                    AND (
                        kq_latest.KetQua = 'Đạt' OR
                        UPPER(TRIM(kq_latest.KetQua)) = 'ĐẠT' OR
                        UPPER(TRIM(kq_latest.KetQua)) = 'DAT'
                    )
                  ORDER BY lh.MaLoHang DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Nếu không có kết quả, thử query không có điều kiện KetQua để debug
            if (count($results) == 0) {
                $debugQuery = "SELECT 
                    COUNT(*) as total_lohang,
                    (SELECT COUNT(*) FROM KetQuaKiemDinh) as total_kq,
                    (SELECT COUNT(*) FROM KetQuaKiemDinh WHERE KetQua LIKE '%Đạt%' OR KetQua LIKE '%Dat%') as kq_dat,
                    (SELECT COUNT(*) FROM PhieuNhapSanPham) as total_phieu_nhap
                FROM LoHang";
                $debugStmt = $this->conn->prepare($debugQuery);
                $debugStmt->execute();
                $debugInfo = $debugStmt->fetch(PDO::FETCH_ASSOC);
                error_log("Debug Info - Total LoHang: " . ($debugInfo['total_lohang'] ?? 0) . 
                          ", Total KQ: " . ($debugInfo['total_kq'] ?? 0) . 
                          ", KQ Đạt: " . ($debugInfo['kq_dat'] ?? 0) . 
                          ", Total PhieuNhap: " . ($debugInfo['total_phieu_nhap'] ?? 0));
                
                // Lấy mẫu các giá trị KetQua có trong DB
                $sampleQuery = "SELECT DISTINCT KetQua FROM KetQuaKiemDinh LIMIT 10";
                $sampleStmt = $this->conn->prepare($sampleQuery);
                $sampleStmt->execute();
                $sampleResults = $sampleStmt->fetchAll(PDO::FETCH_COLUMN);
                error_log("Sample KetQua values: " . implode(', ', $sampleResults));
            }
            
            return $results;
        } catch(PDOException $e) {
            error_log("Error getting lo hang can nhap: " . $e->getMessage());
            error_log("SQL Query: " . $query);
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
                    lh.MaLoHang,
                    lh.MaSanPham,
                    lh.SoLuong,
                    lh.TrangThaiQC,
                    sp.TenSanPham,
                    sp.Size,
                    sp.Mau,
                    sp.GiaXuat,
                    kq_latest.KetQua AS KetQuaKiemDinh,
                    CASE 
                        WHEN EXISTS (SELECT 1 FROM PhieuNhapSanPham p WHERE p.MaLoHang = lh.MaLoHang)
                            THEN 'Đã nhập kho'
                        ELSE 'Chưa nhập kho'
                    END AS TrangThaiKho
                  FROM LoHang lh
                  INNER JOIN SanPham sp ON lh.MaSanPham = sp.MaSanPham
                  LEFT JOIN (
                        SELECT kq.MaLoHang, kq.KetQua, kq.NgayLap
                        FROM KetQuaKiemDinh kq
                        INNER JOIN (
                            SELECT MaLoHang, MAX(NgayLap) AS LatestNgayLap
                            FROM KetQuaKiemDinh
                            GROUP BY MaLoHang
                        ) last ON last.MaLoHang = kq.MaLoHang AND last.LatestNgayLap = kq.NgayLap
                  ) kq_latest ON kq_latest.MaLoHang = lh.MaLoHang
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
    // Không cập nhật trạng thái trên bảng LoHang vì không có cột. Trạng thái kho được suy ra từ PhieuNhapSanPham.
    private function daNhapKho($maLoHang) {
        $query = "SELECT 1 FROM PhieuNhapSanPham WHERE MaLoHang = :maLoHang LIMIT 1";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maLoHang', $maLoHang);
            $stmt->execute();
            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error checking daNhapKho: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy kết quả QC mới nhất của lô hàng
     */
    private function getKetQuaLatest($maLoHang) {
        $query = "SELECT KetQua FROM KetQuaKiemDinh 
                  WHERE MaLoHang = :maLoHang 
                  ORDER BY NgayLap DESC 
                  LIMIT 1";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maLoHang', $maLoHang);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['KetQua'] ?? null;
        } catch (PDOException $e) {
            error_log("Error getKetQuaLatest: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Kiểm tra lô hàng có bất kỳ kết quả 'Đạt' nào không (an toàn hơn kiểm tra bản ghi mới nhất)
     */
    private function isLoHangDatQC($maLoHang) {
        $query = "SELECT 1 FROM KetQuaKiemDinh 
                  WHERE MaLoHang = :maLoHang 
                    AND (
                        KetQua = 'Đạt' OR
                        KetQua LIKE '%Đạt%' OR
                        KetQua LIKE '%Dat%' OR
                        UPPER(REPLACE(TRIM(KetQua),'Đ','D')) = 'DAT'
                    )
                  ORDER BY NgayLap DESC LIMIT 1";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maLoHang', $maLoHang);
            $stmt->execute();
            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error isLoHangDatQC: " . $e->getMessage());
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
    public function insertPhieuNhapKho($maLoHang, $maNV, $ghiChu = '', $maKD = null) {
        // Tạo mã phiếu nhập tự động
        $maPhieuNhap = 'PNTP' . date('YmdHis') . rand(100, 999);
        
        // Nếu mã nhân viên không tồn tại trong bảng NhanVien, dùng NULL để tránh lỗi FK
        $maNVToUse = null;
        if (!empty($maNV)) {
            try {
                $check = $this->conn->prepare("SELECT 1 FROM NhanVien WHERE MaNV = :maNV LIMIT 1");
                $check->bindParam(':maNV', $maNV);
                $check->execute();
                if ($check->fetchColumn()) {
                    $maNVToUse = $maNV;
                }
            } catch (PDOException $e) {
                error_log("Error checking MaNV: " . $e->getMessage());
            }
        }

        // Kiểm tra MaKD có tồn tại không (nếu được cung cấp)
        $maKDToUse = null;
        if (!empty($maKD)) {
            try {
                $checkKD = $this->conn->prepare("SELECT 1 FROM KetQuaKiemDinh WHERE MaKD = :maKD LIMIT 1");
                $checkKD->bindParam(':maKD', $maKD);
                $checkKD->execute();
                if ($checkKD->fetchColumn()) {
                    $maKDToUse = $maKD;
                } else {
                    error_log("Warning: MaKD $maKD does not exist in KetQuaKiemDinh");
                }
            } catch (PDOException $e) {
                error_log("Error checking MaKD: " . $e->getMessage());
            }
        }

        // Nếu có MaKD thì insert với MaKD, không thì bỏ qua
        if ($maKDToUse) {
            $query = "INSERT INTO PhieuNhapSanPham 
                      (MaPhieuNhap, MaKD, MaLoHang, MaNhanVien, NgayNhap)
                      VALUES (:maPhieuNhap, :maKD, :maLoHang, :maNV, NOW())";
        } else {
            $query = "INSERT INTO PhieuNhapSanPham 
                      (MaPhieuNhap, MaLoHang, MaNhanVien, NgayNhap)
                      VALUES (:maPhieuNhap, :maLoHang, :maNV, NOW())";
        }
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maPhieuNhap', $maPhieuNhap);
            if ($maKDToUse) {
                $stmt->bindParam(':maKD', $maKDToUse);
            }
            $stmt->bindParam(':maLoHang', $maLoHang);
            if ($maNVToUse === null) {
                $stmt->bindValue(':maNV', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':maNV', $maNVToUse);
            }
            
            if ($stmt->execute()) {
                error_log("Successfully inserted PhieuNhapSanPham: $maPhieuNhap for LoHang: $maLoHang");
                return $maPhieuNhap;
            }
            error_log("Failed to execute insert PhieuNhapSanPham query for LoHang: $maLoHang");
            return null;
        } catch(PDOException $e) {
            error_log("Error inserting phieu nhap kho for LoHang $maLoHang: " . $e->getMessage());
            error_log("SQL Error Code: " . $e->getCode());
            error_log("Query: " . $query);
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
        // Đảm bảo bảng TonKho tồn tại (trong một số môi trường chưa chạy migration)
        try {
            $this->conn->exec("CREATE TABLE IF NOT EXISTS TonKho (
                id INT AUTO_INCREMENT PRIMARY KEY,
                MaSanPham VARCHAR(10) UNIQUE,
                SoLuongHienTai INT NOT NULL DEFAULT 0,
                ViTriKho VARCHAR(50),
                NgayCapNhat DATETIME NOT NULL,
                GhiChu TEXT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (PDOException $e) {
            error_log("Error ensuring TonKho table: " . $e->getMessage());
        }
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
     * Xử lý nhập kho MỘT lô hàng (Bao bọc trong transaction)
     */
    public function nhapKhoLoHang($maLoHang, $maNV) {
        try {
            // Bắt đầu transaction
            $this->conn->beginTransaction();

            // Gọi logic cốt lõi
            $maPhieuNhap = $this->_thucHienNhapKho($maLoHang, $maNV);

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
     * Nhập kho NHIỀU lô hàng cùng lúc (Toàn bộ là một transaction)
     */
    public function nhapKhoNhieuLoHang($danhSachLoHang, $maNV) {
        error_log("nhapKhoNhieuLoHang called with " . count($danhSachLoHang) . " lots: " . implode(', ', $danhSachLoHang));
        
        // BẮT ĐẦU GIAO DỊCH LỚN
        $this->conn->beginTransaction();
        
        $results = [];
        $successCount = 0;
        $failedLotsInfo = []; // Chỉ để debug

        try {
            foreach ($danhSachLoHang as $index => $maLoHang) {
                error_log("Processing lot $index: $maLoHang (trong transaction)");
                
                // Gọi logic cốt lõi (KHÔNG có transaction lồng nhau)
                // Nếu hàm này throw Exception, toàn bộ khối try...catch sẽ bắt được
                $maPhieuNhap = $this->_thucHienNhapKho($maLoHang, $maNV);
                
                $results[] = [
                    'maLoHang' => $maLoHang,
                    'maPhieuNhap' => $maPhieuNhap
                ];
                $successCount++;
            }

            // Nếu vòng lặp chạy xong mà không có Exception
            // -> TẤT CẢ đều thành công -> Commit
            $this->conn->commit();
            error_log("nhapKhoNhieuLoHang: COMMIT successful for $successCount lots.");

            return [
                'success' => true,
                'successCount' => $successCount,
                'failCount' => 0,
                'message' => "Đã nhập thành công $successCount lô hàng.",
                'details' => $results
            ];

        } catch (Exception $e) {
            // NẾU CÓ BẤT KỲ LỖI NÀO (kể cả chỉ 1 lô)
            // -> ROLLBACK TẤT CẢ
            $this->conn->rollBack();
            
            // Lấy lỗi
            $errorMsg = $e->getMessage();
            error_log("nhapKhoNhieuLoHang: ROLLBACK triggered. Error: $errorMsg");

            // Trả về lỗi
            return [
                'success' => false,
                'successCount' => 0,
                'failCount' => count($danhSachLoHang),
                'message' => "Nhập kho thất bại! Lỗi: " . $errorMsg,
                'details' => []
            ];
        }
    }

    /**
     * Lấy mã kết quả kiểm định mới nhất của lô hàng
     * 
     * @param string $maLoHang Mã lô hàng
     * @return string|null Mã KD hoặc null
     */
    private function getMaKDLatest($maLoHang) {
        $query = "SELECT MaKD 
                  FROM KetQuaKiemDinh 
                  WHERE MaLoHang = :maLoHang 
                    AND NgayLap = (
                        SELECT MAX(NgayLap) 
                        FROM KetQuaKiemDinh 
                        WHERE MaLoHang = :maLoHang2
                    )
                  LIMIT 1";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maLoHang', $maLoHang);
            $stmt->bindParam(':maLoHang2', $maLoHang);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['MaKD'] : null;
        } catch(PDOException $e) {
            error_log("Error getting MaKD latest: " . $e->getMessage());
            return null;
        }
    }

}
?>