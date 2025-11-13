<?php
class DayChuyen extends BaseModel {
    protected $tableName = 'daychuyen';
    protected $primaryKey = 'MaDayChuyen';
    public function getAll() {

        $sql = "SELECT d.MaDayChuyen, d.TenDayChuyen, d.MaToTruong, n.HoTen AS HoTenToTruong

                FROM daychuyen d
                LEFT JOIN nhanvien n ON d.MaToTruong = n.MaNV
                ORDER BY d.TenDayChuyen ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
