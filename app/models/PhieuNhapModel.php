<?php
/**
 * Model quản lý Phiếu Nhập Nguyên Vật Liệu
 * Hệ thống quản lý sản xuất nhà máy 4Ever
 */

require_once __DIR__ . '/../../config/database.php';

class PhieuNhapModel {
    private $conn;
    private $table = 'PhieuNhaNguyenLieu';
    private $tableDetail = 'ChiTietPhieuNhapNguyenLieu';
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Lấy danh sách kế hoạch đã duyệt nhưng thiếu nguyên vật liệu
     */
    public function getKeHoachThieuNVL() {
        $query = "
            SELECT DISTINCT 
                kh.MaKeHoach,
                kh.TenKeHoach,
                kh.NgayBatDau,
                kh.NgayKetThuc,
                kh.TrangThai,
                nv.HoTen as NguoiLap
            FROM KeHoachSanXuat kh
            INNER JOIN NhanVien nv ON kh.MaNV = nv.MaNV
            WHERE kh.TrangThai = 'Đã duyệt'
            AND kh.MaKeHoach IN (
                SELECT DISTINCT MaKeHoach 
                FROM ChiTietKeHoach 
                WHERE CanBoSung > 0
            )
            ORDER BY kh.NgayLap DESC
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy chi tiết nguyên vật liệu cần nhập theo kế hoạch
     */
    public function getChiTietNVLCanNhap($maKeHoach) {
        $query = "
            SELECT 
                ckh.MaChiTietKeHoach,
                ckh.MaNguyenLieu,
                nl.TenNguyenLieu,
                nl.DonViTinh,
                nl.SoLuongTonKho,
                ckh.CanBoSung as SoLuongCanNhap,
                nl.GiaNhap as DonGia,
                (nl.GiaNhap * ckh.CanBoSung) as ThanhTien,
                ncc.MaNhaCungCap,
                ncc.TenNhaCungCap,
                ncc.DiaChi,
                ncc.SoDienThoai,
                ncc.Email
            FROM ChiTietKeHoach ckh
            INNER JOIN NguyenLieu nl ON ckh.MaNguyenLieu = nl.MaNguyenLieu
            INNER JOIN NhaCungCap ncc ON nl.MaNhaCungCap = ncc.MaNhaCungCap
            WHERE ckh.MaKeHoach = :maKeHoach 
            AND ckh.CanBoSung > 0
            ORDER BY nl.TenNguyenLieu
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':maKeHoach', $maKeHoach);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy danh sách nhà cung cấp
     */
    public function getNhaCungCap() {
        $query = "
            SELECT 
                MaNhaCungCap,
                TenNhaCungCap,
                DiaChi,
                SoDienThoai,
                Email
            FROM NhaCungCap
            ORDER BY TenNhaCungCap
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Tạo phiếu nhập mới
     */
    public function createPhieuNhap($data) {
        try {
            $this->conn->beginTransaction();
            
            // Tạo mã phiếu nhập tự động
            $maPhieuNhap = $this->generateMaPhieuNhap();
            
            // Thêm phiếu nhập chính
            $query = "
                INSERT INTO PhieuNhaNguyenLieu 
                (MaPhieuNhap, MaNhaCungCap, NgayNhap, MaNhanVien, TongGiaTri, TrangThai, GhiChu, ThoiGianGiaoHang, ChiPhiKhac, VAT, MaKeHoach)
                VALUES (:maPhieuNhap, :maNhaCungCap, :ngayNhap, :maNhanVien, :tongGiaTri, :trangThai, :ghiChu, :thoiGianGiaoHang, :chiPhiKhac, :vat, :maKeHoach)
            ";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maPhieuNhap', $maPhieuNhap);
            $stmt->bindParam(':maNhaCungCap', $data['maNhaCungCap']);
            $stmt->bindParam(':ngayNhap', $data['ngayNhap']);
            $stmt->bindParam(':maNhanVien', $data['maNhanVien']);
            $stmt->bindParam(':tongGiaTri', $data['tongGiaTri']);
            $stmt->bindParam(':trangThai', $data['trangThai'] ?? 'Chờ duyệt');
            $stmt->bindParam(':ghiChu', $data['ghiChu'] ?? '');
            $stmt->bindParam(':thoiGianGiaoHang', $data['thoiGianGiaoHang']);
            $stmt->bindParam(':chiPhiKhac', $data['chiPhiKhac'] ?? 0);
            $stmt->bindParam(':vat', $data['vat'] ?? 10);
            $stmt->bindParam(':maKeHoach', $data['maKeHoach']);
            $stmt->execute();
            
            // Thêm chi tiết phiếu nhập
            foreach ($data['chiTiet'] as $item) {
                $queryDetail = "
                    INSERT INTO ChiTietPhieuNhapNguyenLieu 
                    (MaPhieuNhap, MaNguyenLieu, SoLuongNhap, DonGia, ThanhTien)
                    VALUES (:maPhieuNhap, :maNguyenLieu, :soLuongNhap, :donGia, :thanhTien)
                ";
                
                $stmtDetail = $this->conn->prepare($queryDetail);
                $stmtDetail->bindParam(':maPhieuNhap', $maPhieuNhap);
                $stmtDetail->bindParam(':maNguyenLieu', $item['maNguyenLieu']);
                $stmtDetail->bindParam(':soLuongNhap', $item['soLuongNhap']);
                $stmtDetail->bindParam(':donGia', $item['donGia']);
                $stmtDetail->bindParam(':thanhTien', $item['thanhTien']);
                $stmtDetail->execute();
            }
            
            $this->conn->commit();
            return $maPhieuNhap;
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
    
    /**
     * Lấy danh sách phiếu nhập
     */
    public function getDanhSachPhieuNhap($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "
            SELECT 
                pn.MaPhieuNhap,
                pn.NgayNhap,
                pn.TongGiaTri,
                'Chờ duyệt' as TrangThai,
                ncc.TenNhaCungCap,
                nv.HoTen as NguoiLap
            FROM PhieuNhaNguyenLieu pn
            INNER JOIN NhaCungCap ncc ON pn.MaNhaCungCap = ncc.MaNhaCungCap
            LEFT JOIN NhanVien nv ON pn.MaNhanVien = nv.MaNV
            ORDER BY pn.NgayNhap DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy chi tiết phiếu nhập
     */
    public function getChiTietPhieuNhap($maPhieuNhap) {
        $query = "
            SELECT 
                pn.MaPhieuNhap,
                pn.NgayNhap,
                pn.TongGiaTri,
                ncc.MaNhaCungCap,
                ncc.TenNhaCungCap,
                ncc.DiaChi,
                ncc.SoDienThoai,
                ncc.Email,
                nv.HoTen as NguoiLap
            FROM PhieuNhaNguyenLieu pn
            INNER JOIN NhaCungCap ncc ON pn.MaNhaCungCap = ncc.MaNhaCungCap
            LEFT JOIN NhanVien nv ON pn.MaNhanVien = nv.MaNV
            WHERE pn.MaPhieuNhap = :maPhieuNhap
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':maPhieuNhap', $maPhieuNhap);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy chi tiết nguyên vật liệu trong phiếu nhập
     */
    public function getChiTietNVLPhieuNhap($maPhieuNhap) {
        $query = "
            SELECT 
                ct.MaNguyenLieu,
                nl.TenNguyenLieu,
                nl.DonViTinh,
                ct.SoLuongNhap,
                ct.DonGia,
                ct.ThanhTien
            FROM ChiTietPhieuNhapNguyenLieu ct
            INNER JOIN NguyenLieu nl ON ct.MaNguyenLieu = nl.MaNguyenLieu
            WHERE ct.MaPhieuNhap = :maPhieuNhap
            ORDER BY nl.TenNguyenLieu
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':maPhieuNhap', $maPhieuNhap);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Đếm tổng số phiếu nhập
     */
    public function countPhieuNhap() {
        $query = "SELECT COUNT(*) as total FROM PhieuNhaNguyenLieu";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    /**
     * Tạo mã phiếu nhập tự động
     */
    private function generateMaPhieuNhap() {
        $query = "SELECT COUNT(*) as count FROM PhieuNhaNguyenLieu WHERE MaPhieuNhap LIKE 'PN%'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $nextNumber = $result['count'] + 1;
        return 'PN' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Cập nhật tồn kho nguyên vật liệu sau khi nhập
     */
    public function updateTonKho($maNguyenLieu, $soLuongNhap) {
        $query = "
            UPDATE NguyenLieu 
            SET SoLuongTonKho = SoLuongTonKho + :soLuongNhap,
                NgayCapNhat = CURRENT_TIMESTAMP
            WHERE MaNguyenLieu = :maNguyenLieu
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':soLuongNhap', $soLuongNhap);
        $stmt->bindParam(':maNguyenLieu', $maNguyenLieu);
        return $stmt->execute();
    }
}
?>
