<?php
class CaLamViec extends BaseModel {
    protected $tableName = 'calamviec';
    protected $primaryKey = 'MaCa';
    public function getAll() {
        $sql = "SELECT MaCa, LoaiCa FROM calamviec ORDER BY LoaiCa ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
