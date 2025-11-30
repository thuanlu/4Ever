<?php
require_once __DIR__ . '/BaseModel.php';
class NhanVien extends BaseModel {
    protected $tableName = 'nhanvien';
    protected $primaryKey = 'MaNV';
    public function getToTruongList() {
        $sql = "SELECT MaNV, HoTen FROM nhanvien WHERE ChucVu = 'TT' ORDER BY HoTen ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
