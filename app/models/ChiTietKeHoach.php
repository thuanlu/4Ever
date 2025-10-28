<?php
// Tệp: app/models/ChiTietKeHoach.php

require_once APP_PATH . '/models/BaseModel.php';

class ChiTietKeHoach extends BaseModel {

    protected $tableName = 'chitietkehoach';
    protected $primaryKey = 'MaChiTietKeHoach';

    /**
     * Lấy tất cả chi tiết của một Kế hoạch Sản xuất
     * (JOIN với SanPham để lấy TenSanPham)
     * Được gọi bởi: KeHoachSanXuatController::loadEditView()
     */
    public function getByMaKeHoach($maKeHoach) {
        try {
            $sql = "
                SELECT
                    ct.*,
                    sp.TenSanPham
                FROM {$this->tableName} ct
                JOIN sanpham sp ON ct.MaSanPham = sp.MaSanPham
                WHERE ct.MaKeHoach = ?
                ORDER BY ct.MaChiTietKeHoach ASC
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$maKeHoach]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * HÀM QUAN TRỌNG CHO VIỆC EDIT: Xóa tất cả chi tiết theo Mã Kế hoạch
     * Được gọi bởi: KeHoachSanXuatController::edit() trước khi lưu chi tiết mới
     */
    public function deleteByMaKeHoach($maKeHoach) {
        try {
            $sql = "DELETE FROM {$this->tableName} WHERE MaKeHoach = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$maKeHoach]);
            return $stmt->rowCount(); // Trả về số dòng đã xóa
        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            throw $e; // Ném lỗi ra để Transaction bắt
        }
    }

    // Các hàm CRUD chung (getAll, getById, create, update, delete)
    // đã được kế thừa từ BaseModel.php
}
?>