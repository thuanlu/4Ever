
<?php
require_once __DIR__ . '/BaseModel.php';
class NhanVien extends BaseModel {
    protected $tableName = 'nhanvien';
    protected $primaryKey = 'MaNV';
    public function getAllFull() {
        $sql = "SELECT MaNV, HoTen, GioiTinh, NamSinh, ChucVu, BoPhan, SoDienThoai, TrangThai FROM nhanvien ORDER BY MaNV ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
