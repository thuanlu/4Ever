
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

        /**
     * Tìm kiếm nhân viên theo mã NV hoặc tên NV
     */
    public function search($keyword) {
        $sql = "SELECT MaNV, HoTen, GioiTinh, NamSinh, ChucVu, BoPhan, SoDienThoai, TrangThai FROM nhanvien WHERE MaNV LIKE :kw OR HoTen LIKE :kw ORDER BY MaNV ASC";
        $stmt = $this->db->prepare($sql);
        $kw = '%' . $keyword . '%';
        $stmt->bindParam(':kw', $kw, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
