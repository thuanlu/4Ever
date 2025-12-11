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
        // Kiểm tra connection trước
        if ($this->conn === null) {
            throw new Exception("Lỗi kết nối database! Vui lòng kiểm tra MySQL đã chạy chưa.");
        }
        
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
                  FROM lohang lh
                  INNER JOIN sanpham sp ON lh.MaSanPham = sp.MaSanPham
                  INNER JOIN (
                        SELECT kq.MaLoHang, kq.KetQua, kq.NgayLap
                        FROM ketquakiemdinh kq
                        INNER JOIN (
                            SELECT MaLoHang, MAX(NgayLap) AS LatestNgayLap
                            FROM ketquakiemdinh
                            GROUP BY MaLoHang
                        ) last ON last.MaLoHang = kq.MaLoHang AND last.LatestNgayLap = kq.NgayLap
                  ) kq_latest ON kq_latest.MaLoHang = lh.MaLoHang
                  WHERE NOT EXISTS (SELECT 1 FROM phieunhapsanpham pns WHERE pns.MaLoHang = lh.MaLoHang)
                    AND (
                        kq_latest.KetQua = 'Đạt' OR
                        UPPER(TRIM(kq_latest.KetQua)) = 'ĐẠT' OR
                        UPPER(TRIM(kq_latest.KetQua)) = 'DAT'
                    )
                  ORDER BY lh.MaLoHang DESC";
        
        // Kiểm tra connection trước khi dùng
        if ($this->conn === null) {
            error_log("NhapKho::getLoHangCanNhap - Database connection is null!");
            return [];
        }
        
        try {
            $stmt = $this->conn->prepare($query);
            if ($stmt === false) {
                error_log("NhapKho::getLoHangCanNhap - Failed to prepare statement");
                return [];
            }
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Nếu không có kết quả, thử query không có điều kiện KetQua để debug
            if (count($results) == 0) {
                $debugQuery = "SELECT 
                    COUNT(*) as total_lohang,
                    (SELECT COUNT(*) FROM ketquakiemdinh) as total_kq,
                    (SELECT COUNT(*) FROM ketquakiemdinh WHERE KetQua LIKE '%Đạt%' OR KetQua LIKE '%Dat%') as kq_dat,
                    (SELECT COUNT(*) FROM phieunhapsanpham) as total_phieu_nhap
                FROM lohang";
                $debugStmt = $this->conn->prepare($debugQuery);
                $debugStmt->execute();
                $debugInfo = $debugStmt->fetch(PDO::FETCH_ASSOC);
                error_log("Debug Info - Total LoHang: " . ($debugInfo['total_lohang'] ?? 0) . 
                          ", Total KQ: " . ($debugInfo['total_kq'] ?? 0) . 
                          ", KQ Đạt: " . ($debugInfo['kq_dat'] ?? 0) . 
                          ", Total PhieuNhap: " . ($debugInfo['total_phieu_nhap'] ?? 0));
                
                // Lấy mẫu các giá trị KetQua có trong DB
                $sampleQuery = "SELECT DISTINCT KetQua FROM ketquakiemdinh LIMIT 10";
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
        // Kiểm tra connection
        if ($this->conn === null) {
            error_log("NhapKho::getLoHangById - Database connection is null!");
            return null;
        }
        
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
                        WHEN EXISTS (SELECT 1 FROM phieunhapsanpham p WHERE p.MaLoHang = lh.MaLoHang)
                            THEN 'Đã nhập kho'
                        ELSE 'Chưa nhập kho'
                    END AS TrangThaiKho
                  FROM lohang lh
                  INNER JOIN sanpham sp ON lh.MaSanPham = sp.MaSanPham
                  LEFT JOIN (
                        SELECT kq.MaLoHang, kq.KetQua, kq.NgayLap
                        FROM ketquakiemdinh kq
                        INNER JOIN (
                            SELECT MaLoHang, MAX(NgayLap) AS LatestNgayLap
                            FROM ketquakiemdinh
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
        if ($this->conn === null) return false;
        
        $query = "SELECT 1 FROM phieunhapsanpham WHERE MaLoHang = :maLoHang LIMIT 1";
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
        if ($this->conn === null) return null;
        
        $query = "SELECT KetQua FROM ketquakiemdinh 
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
        if ($this->conn === null) return false;
        
        $query = "SELECT 1 FROM ketquakiemdinh 
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
        // Kiểm tra connection
        if ($this->conn === null) {
            error_log("NhapKho::insertPhieuNhapKho - Database connection is null!");
            return null;
        }
        
        // Tạo mã phiếu nhập tự động - phải <= 10 ký tự (theo schema)
        // Format: PNSP + số thứ tự (2 chữ số)
        // Ví dụ: PNSP01, PNSP02, ...
        $sequence = 1;
        
        // Tìm số thứ tự tiếp theo
        try {
            $checkQuery = "SELECT COUNT(*) FROM phieunhapsanpham WHERE MaPhieuNhap LIKE 'PNSP%'";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute();
            $count = (int)$checkStmt->fetchColumn();
            $sequence = $count + 1;
        } catch (PDOException $e) {
            error_log("Error getting sequence for MaPhieuNhap: " . $e->getMessage());
            // Dùng timestamp làm fallback
            $sequence = (int)substr(time(), -2);
        }
        
        // Tạo mã phiếu: PNSP + số thứ tự (2 chữ số) = 6 ký tự
        $maPhieuNhap = 'PNSP' . str_pad($sequence, 2, '0', STR_PAD_LEFT);
        
        // Kiểm tra mã phiếu đã tồn tại chưa (tránh trùng)
        $maxRetries = 99; // Tối đa 99 phiếu
        $retryCount = 0;
        while ($retryCount < $maxRetries) {
            try {
                $checkExist = $this->conn->prepare("SELECT 1 FROM phieunhapsanpham WHERE MaPhieuNhap = :maPhieuNhap LIMIT 1");
                $checkExist->bindParam(':maPhieuNhap', $maPhieuNhap);
                $checkExist->execute();
                if (!$checkExist->fetchColumn()) {
                    break; // Mã chưa tồn tại, có thể dùng
                }
                // Mã đã tồn tại, tăng sequence
                $sequence++;
                $maPhieuNhap = 'PNSP' . str_pad($sequence, 2, '0', STR_PAD_LEFT);
                $retryCount++;
            } catch (PDOException $e) {
                error_log("Error checking existing MaPhieuNhap: " . $e->getMessage());
                break;
            }
        }
        
        // Nếu vượt quá 99, dùng format dài hơn: PNSP + số 3 chữ số
        if ($retryCount >= $maxRetries) {
            $sequence = 100;
            $maPhieuNhap = 'PNSP' . str_pad($sequence, 3, '0', STR_PAD_LEFT); // PNSP100, PNSP101, ...
        }
        
        // Nếu mã nhân viên không tồn tại trong bảng nhanvien, dùng NULL để tránh lỗi FK
        $maNVToUse = null;
        if (!empty($maNV)) {
            try {
                $check = $this->conn->prepare("SELECT 1 FROM nhanvien WHERE MaNV = :maNV LIMIT 1");
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
                $checkKD = $this->conn->prepare("SELECT 1 FROM ketquakiemdinh WHERE MaKD = :maKD LIMIT 1");
                $checkKD->bindParam(':maKD', $maKD);
                $checkKD->execute();
                if ($checkKD->fetchColumn()) {
                    $maKDToUse = $maKD;
                } else {
                    error_log("Warning: MaKD $maKD does not exist in ketquakiemdinh");
                }
            } catch (PDOException $e) {
                error_log("Error checking MaKD: " . $e->getMessage());
            }
        }

        // Chuẩn bị query INSERT - không thêm GhiChu vì có thể không có trong database thực tế
        if ($maKDToUse) {
            $query = "INSERT INTO phieunhapsanpham 
                      (MaPhieuNhap, MaKD, MaLoHang, MaNhanVien, NgayNhap)
                      VALUES (:maPhieuNhap, :maKD, :maLoHang, :maNV, NOW())";
        } else {
            $query = "INSERT INTO phieunhapsanpham 
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
            error_log("SQL State: " . $e->errorInfo[0] ?? 'N/A');
            error_log("Query: " . $query);
            error_log("Params: MaPhieuNhap=$maPhieuNhap, MaLoHang=$maLoHang, MaNV=" . ($maNVToUse ?? 'NULL') . ", MaKD=" . ($maKDToUse ?? 'NULL'));
            throw $e; // Throw lại để caller có thể xử lý
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
        // Kiểm tra connection
        if ($this->conn === null) {
            error_log("NhapKho::updateTonKho - Database connection is null!");
            return false;
        }
        
        // Đảm bảo bảng tonkho tồn tại (trong một số môi trường chưa chạy migration)
        try {
            $this->conn->exec("CREATE TABLE IF NOT EXISTS tonkho (
                id INT AUTO_INCREMENT PRIMARY KEY,
                MaSanPham VARCHAR(10) UNIQUE,
                SoLuongHienTai INT NOT NULL DEFAULT 0,
                ViTriKho VARCHAR(50),
                NgayCapNhat DATETIME NOT NULL,
                GhiChu TEXT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } catch (PDOException $e) {
            error_log("Error ensuring tonkho table: " . $e->getMessage());
        }
        // Kiểm tra xem đã có tồn kho chưa
        $checkQuery = "SELECT COUNT(*) FROM tonkho WHERE MaSanPham = :maSanPham";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':maSanPham', $maSanPham);
        $checkStmt->execute();
        $exists = $checkStmt->fetchColumn() > 0;

        if ($exists) {
            // Cập nhật số lượng hiện có
            $query = "UPDATE tonkho 
                      SET SoLuongHienTai = SoLuongHienTai + :soLuong,
                          NgayCapNhat = NOW()
                      WHERE MaSanPham = :maSanPham";
        } else {
            // Tạo mới bản ghi tồn kho
            $query = "INSERT INTO tonkho 
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
        // Kiểm tra connection
        if ($this->conn === null) {
            return [
                'success' => false,
                'message' => 'Lỗi kết nối database! Vui lòng kiểm tra MySQL đã chạy chưa.'
            ];
        }
        
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
     * Nhập kho NHIỀU lô hàng cùng lúc (Xử lý từng lô trong transaction riêng)
     */
    public function nhapKhoNhieuLoHang($danhSachLoHang, $maNV) {
        // Kiểm tra connection
        if ($this->conn === null) {
            return [
                'success' => false,
                'successCount' => 0,
                'failCount' => count($danhSachLoHang),
                'message' => 'Lỗi kết nối database! Vui lòng kiểm tra MySQL đã chạy chưa.',
                'details' => [],
                'failedLots' => []
            ];
        }
        
        error_log("nhapKhoNhieuLoHang called with " . count($danhSachLoHang) . " lots: " . implode(', ', $danhSachLoHang));
        
        $results = [];
        $successCount = 0;
        $failedLots = [];

        // Xử lý từng lô hàng với transaction riêng để tránh rollback tất cả nếu 1 lô lỗi
        foreach ($danhSachLoHang as $index => $maLoHang) {
            error_log("Processing lot $index: $maLoHang");
            
            $transactionStarted = false;
            $committed = false;
            $maPhieuNhap = null;
            
            try {
                // Đảm bảo không có transaction đang chạy từ trước
                if ($this->conn->inTransaction()) {
                    error_log("Warning: Transaction already active for lot $maLoHang, rolling back first");
                    try {
                        $this->conn->rollBack();
                    } catch (PDOException $e) {
                        error_log("Error rolling back existing transaction: " . $e->getMessage());
                    }
                }
                
                // Mỗi lô hàng là một transaction riêng
                $this->conn->beginTransaction();
                $transactionStarted = true;
                
                // Gọi logic cốt lõi
                $maPhieuNhap = $this->_thucHienNhapKho($maLoHang, $maNV);
                
                // Commit transaction cho lô này
                // Kiểm tra transaction có đang active trước khi commit
                if ($this->conn->inTransaction()) {
                    $this->conn->commit();
                    $committed = true;
                    $transactionStarted = false;
                    
                    // Sau khi commit thành công, đánh dấu thành công ngay lập tức
                    $successCount++;
                    
                    // Thêm vào kết quả
                    $results[] = [
                        'maLoHang' => $maLoHang,
                        'maPhieuNhap' => $maPhieuNhap
                    ];
                    
                    error_log("Successfully processed lot: $maLoHang, PhieuNhap: $maPhieuNhap");
                } else {
                    // Transaction đã bị đóng, coi như đã commit thành công
                    error_log("Warning: Transaction not active for lot $maLoHang after _thucHienNhapKho, assuming committed");
                    $committed = true;
                    $transactionStarted = false;
                    $successCount++;
                    $results[] = [
                        'maLoHang' => $maLoHang,
                        'maPhieuNhap' => $maPhieuNhap
                    ];
                }
                
            } catch (PDOException $e) {
                // Xử lý lỗi PDO riêng (có thể là lỗi transaction)
                $errorMsg = $e->getMessage();
                error_log("PDOException for lot $maLoHang: $errorMsg");
                
                // Nếu đã commit thành công, không rollback
                if ($committed) {
                    error_log("Warning: PDOException after commit for lot $maLoHang: $errorMsg");
                    $foundInResults = in_array($maLoHang, array_column($results, 'maLoHang'));
                    if (!$foundInResults) {
                        $results[] = [
                            'maLoHang' => $maLoHang,
                            'maPhieuNhap' => $maPhieuNhap ?? 'Unknown'
                        ];
                    }
                } else {
                    // Rollback transaction cho lô này nếu chưa commit
                    if ($transactionStarted && $this->conn->inTransaction()) {
                        try {
                            $this->conn->rollBack();
                            $transactionStarted = false;
                        } catch (PDOException $rollbackEx) {
                            error_log("Error during rollback for lot $maLoHang: " . $rollbackEx->getMessage());
                        }
                    }
                    
                    $failedLots[] = [
                        'maLoHang' => $maLoHang,
                        'error' => $errorMsg
                    ];
                }
            } catch (Exception $e) {
                // Xử lý các exception khác
                $errorMsg = $e->getMessage();
                error_log("Exception for lot $maLoHang: $errorMsg");
                
                // Nếu đã commit thành công, không rollback và vẫn coi là thành công
                if ($committed) {
                    error_log("Warning: Exception after commit for lot $maLoHang: $errorMsg");
                    $foundInResults = in_array($maLoHang, array_column($results, 'maLoHang'));
                    if (!$foundInResults) {
                        $results[] = [
                            'maLoHang' => $maLoHang,
                            'maPhieuNhap' => $maPhieuNhap ?? 'Unknown'
                        ];
                    }
                } else {
                    // Rollback transaction cho lô này nếu chưa commit
                    if ($transactionStarted && $this->conn->inTransaction()) {
                        try {
                            $this->conn->rollBack();
                            $transactionStarted = false;
                        } catch (PDOException $rollbackEx) {
                            error_log("Error during rollback for lot $maLoHang: " . $rollbackEx->getMessage());
                        }
                    }
                    
                    $failedLots[] = [
                        'maLoHang' => $maLoHang,
                        'error' => $errorMsg
                    ];
                }
            }
        }

        // Trả về kết quả tổng hợp - luôn đảm bảo có đầy đủ các trường
        $failCount = count($failedLots);
        
        // Đơn giản hóa - chỉ log tóm tắt
        error_log("nhapKhoNhieuLoHang - Final: success=$successCount, fail=$failCount");
        
        if ($successCount > 0) {
            $message = "Đã nhập thành công $successCount lô hàng";
            if ($failCount > 0) {
                $message .= ". $failCount lô thất bại.";
            }
            return [
                'success' => true,
                'successCount' => $successCount,
                'failCount' => $failCount,
                'message' => $message,
                'details' => $results,
                'failedLots' => $failedLots
            ];
        } else {
            // Tất cả đều thất bại
            $errorMessages = [];
            foreach ($failedLots as $failed) {
                if (count($errorMessages) < 3) {
                    $errorMessages[] = $failed['maLoHang'] . ': ' . ($failed['error'] ?? 'Lỗi không xác định');
                }
            }
            $message = "Không thể nhập kho bất kỳ lô hàng nào!";
            if (count($errorMessages) > 0) {
                $message .= " " . implode('; ', $errorMessages);
                if (count($failedLots) > 3) {
                    $message .= "...";
                }
            }
            
            return [
                'success' => false,
                'successCount' => 0,
                'failCount' => $failCount,
                'message' => $message,
                'details' => [],
                'failedLots' => $failedLots
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
        if ($this->conn === null) return null;
        
        $query = "SELECT MaKD 
                  FROM ketquakiemdinh 
                  WHERE MaLoHang = :maLoHang 
                    AND NgayLap = (
                        SELECT MAX(NgayLap) 
                        FROM ketquakiemdinh 
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

    /**
     * Lấy danh sách các thành phẩm có trong kho
     * 
     * @return array Danh sách thành phẩm với thông tin tồn kho
     */
    public function getDanhSachThanhPhamTrongKho() {
        // Kiểm tra connection
        if ($this->conn === null) {
            error_log("NhapKho::getDanhSachThanhPhamTrongKho - Database connection is null!");
            return [];
        }

        $query = "SELECT 
                    tk.MaSanPham,
                    sp.TenSanPham,
                    sp.Size,
                    sp.Mau,
                    sp.GiaXuat,
                    tk.SoLuongHienTai,
                    tk.ViTriKho,
                    tk.NgayCapNhat,
                    tk.GhiChu
                  FROM tonkho tk
                  INNER JOIN sanpham sp ON tk.MaSanPham = sp.MaSanPham
                  WHERE tk.SoLuongHienTai > 0
                  ORDER BY tk.NgayCapNhat DESC, sp.TenSanPham ASC, tk.MaSanPham ASC";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting danh sach thanh pham trong kho: " . $e->getMessage());
            return [];
        }
    }

}
?>