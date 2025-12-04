<?php
// Tệp: app/models/KeHoachSanXuat.php

// Kế thừa từ BaseModel để có các hàm CRUD chung
// [SỬA] Dùng __DIR__ để đường dẫn luôn đúng
require_once __DIR__ . '/BaseModel.php';

class KeHoachSanXuat extends BaseModel {

    // Ghi đè các thuộc tính của BaseModel
    protected $tableName = 'kehoachsanxuat';
    protected $primaryKey = 'MaKeHoach';

    // --- ============================================= ---
    // --- HÀM MỚI ĐƯỢC THÊM VÀO ĐỂ SỬA LỖI CỦA BẠN ---
    // --- ============================================= ---
    /**
     * Lấy TẤT CẢ kế hoạch (cho trang index)
     * (Hàm này bị thiếu, gây ra lỗi 'undefined method')
     */
    public function getAllWithDetails() {
        try {
            $sql = "SELECT k.MaKeHoach, k.TenKeHoach, k.NgayBatDau, k.NgayKetThuc, k.TrangThai, 
                    d.TenDonHang, 
                    n.HoTen AS NguoiLap
                    FROM {$this->tableName} k
                    LEFT JOIN donhang d ON k.MaDonHang = d.MaDonHang
                    LEFT JOIN nhanvien n ON k.MaNV = n.MaNV
                    ORDER BY k.NgayLap DESC"; // Không có WHERE, lấy tất cả
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            return [];
        }
    }
    // --- KẾT THÚC HÀM SỬA LỖI ---


    /**
     * Lấy danh sách kế hoạch đã duyệt
     */
    // Lấy kế hoạch đã duyệt mà xưởng trưởng hiện tại phụ trách ít nhất một phân xưởng liên quan
    public function getApprovedPlansByXuongTruong($maXuongTruong) {
        try {
            $sql = "SELECT DISTINCT k.*, n.HoTen AS NguoiLap, d.TenDonHang
                    FROM {$this->tableName} k
                    LEFT JOIN nhanvien n ON k.MaNV = n.MaNV
                    LEFT JOIN donhang d ON k.MaDonHang = d.MaDonHang
                    JOIN chitietkehoach ct ON ct.MaKeHoach = k.MaKeHoach
                    JOIN phanxuong px ON ct.MaPhanXuong = px.MaPhanXuong
                    WHERE k.TrangThai = 'Đã duyệt' AND px.MaXuongTruong = ?
                    ORDER BY k.NgayBatDau DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$maXuongTruong]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * LẤY MÃ KẾ HOẠCH CUỐI CÙNG
     * [SỬA] Sửa lại logic sắp xếp để tìm số lớn nhất
     * (vd: 'KH10' phải lớn hơn 'KH09')
     */
    public function getLastMaKeHoach() {
        try {
            // Sắp xếp theo PHẦN SỐ của MaKeHoach
            $sql = "SELECT {$this->primaryKey} 
                    FROM {$this->tableName} 
                    ORDER BY CAST(SUBSTRING({$this->primaryKey}, 3) AS UNSIGNED) DESC 
                    LIMIT 1";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchColumn(0); 
            
            return $result; // Sẽ trả về 'KH06'
            
        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy kế hoạch theo ID, JOIN với tên người lập
     */
    public function getByIdWithNguoiLap($maKeHoach) {
         try {
            $sql = "SELECT k.*, n.HoTen AS HoTenNguoiLap 
                    FROM {$this->tableName} k
                    LEFT JOIN nhanvien n ON k.MaNV = n.MaNV
                    WHERE k.{$this->primaryKey} = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$maKeHoach]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy các MaDonHang đã được lập kế hoạch
     */
    public function getPlannedDonHangIds() {
        try {
            $sql = "SELECT DISTINCT MaDonHang FROM {$this->tableName} WHERE MaDonHang IS NOT NULL";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $ids = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

            return $ids ?? [];

        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tính tổng sản lượng mục tiêu của một kế hoạch tổng
     * (Dựa trên bảng chitietkehoach)
     * * @param string $maKeHoach Mã kế hoạch tổng
     * @return int Tổng sản lượng
     */
    public function getSanLuongTong($maKeHoach) {
        try {
            // Dựa theo file qlsx_4ever_final.sql, 
            // tổng sản lượng là SUM(SanLuongMucTieu) từ bảng chitietkehoach
            $sql = "SELECT SUM(SanLuongMucTieu) AS TongSanLuong 
                    FROM chitietkehoach 
                    WHERE MaKeHoach = ?";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$maKeHoach]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Trả về tổng sản lượng, hoặc 0 nếu không có kết quả
            return (int)($result['TongSanLuong'] ?? 0);

        } catch (PDOException $e) {
            // Ghi lại lỗi và trả về 0
            error_log(__METHOD__ . '::Error: ' . $e->getMessage());
            return 0;
        }
    }

    // Các hàm CRUD chung (getAll, getById, create, update, delete)
    // đã được kế thừa từ BaseModel.php
}
?>