<?php
// [THAY THẾ TOÀN BỘ FILE: app/models/PhieuDatHangNVL.php]

class PhieuDatHangNVL extends BaseModel {
    
    protected $table = 'phieudatnvl'; 
    protected $detailTable = 'chitietphieudatnvl';

    /**
     * Lấy tất cả phiếu đặt hàng (Không đổi)
     */
    public function getAll() {
        return $this->db->query("
            SELECT p.*, ncc.TenNhaCungCap 
            FROM $this->table p
            LEFT JOIN nhacungcap ncc ON p.MaNhaCungCap = ncc.MaNhaCungCap
            ORDER BY p.NgayLapPhieu DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy thông tin 1 phiếu và chi tiết của nó (Không đổi)
     */
    public function getById($id) {
        $sql = "SELECT p.*, ncc.TenNhaCungCap, kh.TenKeHoach 
                FROM $this->table p
                LEFT JOIN nhacungcap ncc ON p.MaNhaCungCap = ncc.MaNhaCungCap
                LEFT JOIN kehoachsanxuat kh ON p.MaKHSX = kh.MaKeHoach
                WHERE p.MaPhieu = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $phieu = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$phieu) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT * FROM $this->detailTable WHERE MaPhieu = ?");
        $stmt->execute([$id]);
        $chiTiet = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'phieu' => $phieu,
            'chiTiet' => $chiTiet
        ];
    }
    
    /**
     * [HÀM MỚI] - (Requirement 1)
     * Tạo mã phiếu mới tự động (ví dụ: PDNL04)
     * Logic này sẽ lấy mã 'PDNL03' -> tách số 3 -> + 1 = 4 -> trả về 'PDNL04'
     */
    public function generateNewMaPhieu() {
        $prefix = 'PDNL'; // Tiền tố của bạn
        
        // Lấy MaPhieu cuối cùng dựa trên phần SỐ
        $sql = "SELECT MaPhieu 
                FROM $this->table 
                WHERE MaPhieu LIKE '{$prefix}%' 
                ORDER BY CAST(SUBSTRING(MaPhieu, " . (strlen($prefix) + 1) . ") AS UNSIGNED) DESC 
                LIMIT 1";
                
        $stmt = $this->db->query($sql);
        $lastMaPhieu = $stmt->fetchColumn();
        
        $newNumber = 1; // Bắt đầu từ 1 nếu chưa có phiếu nào
        if ($lastMaPhieu) {
            // Lấy số từ chuỗi (ví dụ: 'PDNL03' -> 3)
            $lastNumber = (int)substr($lastMaPhieu, strlen($prefix));
            $newNumber = $lastNumber + 1;
        }
        
        // Định dạng lại với 2 chữ số 0 đệm (ví dụ: 4 -> 'PDNL04', 12 -> 'PDNL12')
        return sprintf('%s%02d', $prefix, $newNumber); 
    }


    /**
     * [HÀM ĐÃ SỬA] - (Requirement 1)
     * Tạo phiếu mới VÀ chi tiết (sử dụng Transaction)
     * - [SỬA] Thêm logic tự tạo MaPhieu
     * - [SỬA] Sửa lỗi không dùng lastInsertId() cho Primary Key dạng VARCHAR
     */
    public function createPhieuVaChiTiet($phieuData, $chiTietData) {
        try {
            $this->db->beginTransaction();

            // 1. [SỬA] Tạo MaPhieu mới tự động
            $newMaPhieu = $this->generateNewMaPhieu();
            
            // 2. [SỬA] Thêm MaPhieu mới vào mảng data để insert
            $phieuData['MaPhieu'] = $newMaPhieu;

            // 3. [SỬA] Thêm cột MaPhieu vào câu SQL INSERT
            $sqlPhieu = "INSERT INTO $this->table (MaPhieu, TenPhieu, NgayLapPhieu, NguoiLapPhieu, MaKHSX, MaNhaCungCap, TongChiPhiDuKien, TrangThai) 
                         VALUES (:MaPhieu, :TenPhieu, :NgayLapPhieu, :NguoiLapPhieu, :MaKHSX, :MaNhaCungCap, :TongChiPhiDuKien, :TrangThai)";
            
            $stmtPhieu = $this->db->prepare($sqlPhieu);
            // $phieuData (từ Controller) giờ đã chứa :MaPhieu
            $stmtPhieu->execute($phieuData); 
            
            // 4. [SỬA] Gán ID mới (VARCHAR) cho chi tiết, không dùng lastInsertId()
            $lastPhieuId = $newMaPhieu; 

            $sqlChiTiet = "INSERT INTO $this->detailTable (MaPhieu, MaNVL, TenNVL, SoLuongCan, DonGia, ThanhTien) 
                           VALUES (:MaPhieu, :MaNVL, :TenNVL, :SoLuongCan, :DonGia, :ThanhTien)";
            $stmtChiTiet = $this->db->prepare($sqlChiTiet);

            foreach ($chiTietData as $item) {
                if (empty($item['MaNVL'])) continue;
                $item['MaPhieu'] = $lastPhieuId; // Dùng ID mới 'PDNL04'
                $stmtChiTiet->execute($item);
            }

            $this->db->commit();
            return $lastPhieuId; // Trả về mã 'PDNL04'

        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Lỗi CSDL khi tạo phiếu: " . $e->getMessage());
        }
    }

    /**
     * Lấy danh sách nhà cung cấp (Không đổi)
     */
    public function getNhaCungCapList() {
        return $this->db->query("SELECT MaNhaCungCap, TenNhaCungCap FROM nhacungcap ORDER BY TenNhaCungCap")
                      ->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // -----------------------------------------------------------------
    // CÁC HÀM MỚI VÀ SỬA LỖI
    // -----------------------------------------------------------------

    /**
     * [HÀM ĐÃ SỬA LỖI] - (Requirement 2 & 3)
     * Lấy danh sách KHSX ở trạng thái "Đã duyệt" VÀ bị thiếu NVL
     * VÀ [SỬA] chưa từng được lập phiếu đặt hàng
     */
    public function getKeHoachThieuNVL() {
        
        $sql = "
            SELECT
                kh.MaKeHoach,
                kh.TenKeHoach
            FROM
                kehoachsanxuat kh
            WHERE
                kh.TrangThai = 'Đã duyệt'
                
                -- [SỬA MỚI] (Req 3) Chỉ lấy KHSX CHƯA có trong bảng phieudatnvl
                AND NOT EXISTS (
                    SELECT 1
                    FROM $this->table p -- $this->table là 'phieudatnvl'
                    WHERE p.MaKHSX = kh.MaKeHoach
                )

                -- (Req 2) VÀ KHSX này có thiếu NVL
                AND EXISTS (
                    -- Tìm ít nhất 1 NVL bị thiếu hụt cho KHSX này
                    SELECT 1
                    FROM
                        chitietkehoach ct
                    JOIN
                        dinhmucnguyenlieu dm ON ct.MaSanPham = dm.MaSanPham
                    JOIN
                        nguyenlieu nl ON dm.MaNguyenLieu = nl.MaNguyenLieu
                    WHERE
                        ct.MaKeHoach = kh.MaKeHoach -- Correlated Subquery
                    GROUP BY
                        dm.MaNguyenLieu, nl.SoLuongTonKho
                    HAVING
                        -- So sánh TỔNG CẦN > TỒN KHO
                        SUM(ct.SanLuongMucTieu * dm.DinhMucSuDung) > nl.SoLuongTonKho
                )
            ORDER BY
                kh.NgayLap DESC;
        ";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * [HÀM MỚI] - (Requirement 4 - Đã đúng, không đổi)
     * Lấy chi tiết các NVL đang bị thiếu hụt cho 1 KHSX cụ thể (Dùng cho AJAX)
     */
    public function getChiTietThieuHut($maKeHoach) {
        $sql = "
            SELECT
                NhuCau.MaNguyenLieu AS MaNVL,
                nl.TenNguyenLieu AS TenNVL,
                ROUND((NhuCau.TongSoLuongCan - nl.SoLuongTonKho), 2) AS SoLuongThieu,
                nl.GiaNhap AS DonGia
            FROM (
                SELECT
                    dm.MaNguyenLieu,
                    SUM(ct.SanLuongMucTieu * dm.DinhMucSuDung) AS TongSoLuongCan
                FROM
                    chitietkehoach ct
                JOIN
                    dinhmucnguyenlieu dm ON ct.MaSanPham = dm.MaSanPham
                WHERE
                    ct.MaKeHoach = ?
                GROUP BY
                    dm.MaNguyenLieu
            ) AS NhuCau
            JOIN
                nguyenlieu nl ON NhuCau.MaNguyenLieu = nl.MaNguyenLieu
            WHERE
                NhuCau.TongSoLuongCan > nl.SoLuongTonKho;
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$maKeHoach]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>