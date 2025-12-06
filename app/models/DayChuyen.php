<?php
class DayChuyen extends BaseModel {
    protected $tableName = 'daychuyen';
    protected $primaryKey = 'MaDayChuyen';
    public function getAll() {

        $sql = "SELECT d.MaDayChuyen, d.TenDayChuyen, d.MaPhanXuong, d.MaToTruong, n.HoTen AS HoTenToTruong
            FROM daychuyen d
            LEFT JOIN nhanvien n ON d.MaToTruong = n.MaNV
            ORDER BY d.TenDayChuyen ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy danh sách dây chuyền theo phân xưởng
     * @param string $maPhanXuong Mã phân xưởng
     * @return array
     */
    public function getByPhanXuong(string $maPhanXuong) {
        $sql = "SELECT d.MaDayChuyen, d.TenDayChuyen, d.MaPhanXuong, d.MaToTruong, n.HoTen AS HoTenToTruong
                FROM daychuyen d
                LEFT JOIN nhanvien n ON d.MaToTruong = n.MaNV
                WHERE TRIM(d.MaPhanXuong) = TRIM(:ma_px)
                ORDER BY d.TenDayChuyen ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ma_px' => trim($maPhanXuong)]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
