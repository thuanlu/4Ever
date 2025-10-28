<?php
// Tệp: app/models/ChiTietDonHang.php

require_once APP_PATH . '/models/BaseModel.php';

class ChiTietDonHang extends BaseModel {
    
    protected $tableName = 'chitietdonhang';
    protected $primaryKey = 'MaCTDH';

    /**
     * HÀM QUAN TRỌNG CHO AJAX: Lấy danh sách sản phẩm theo Mã Đơn Hàng
     *
     * Hàm này được gọi bởi KeHoachSanXuatController::getDonHangDetails()
     * Nó JOIN với bảng 'sanpham' để lấy Tên Sản phẩm.
     */
    public function getProductsByMaDonHang($maDonHang) {
        try {
            $sql = "
                SELECT 
                    ct.MaSanPham, 
                    sp.TenSanPham, 
                    ct.SoLuong 
                FROM {$this->tableName} ct
                JOIN sanpham sp ON ct.MaSanPham = sp.MaSanPham
                WHERE ct.MaDonHang = ?
                ORDER BY sp.TenSanPham ASC
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$maDonHang]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            return []; // Trả về mảng rỗng nếu có lỗi
        }
    }
}
?>