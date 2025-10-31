<?php
// Tệp: app/models/PhanXuong.php

require_once APP_PATH . '/models/BaseModel.php';

class PhanXuong extends BaseModel {
    
    // Tên bảng trong CSDL
    protected $tableName = 'phanxuong';
    
    // Khóa chính của bảng
    protected $primaryKey = 'MaPhanXuong';
    
    /**
     * Lấy tất cả các phân xưởng
     * (Hàm này đã có trong BaseModel, nhưng viết lại cho rõ)
     */
    public function getAll() {
        try {
            $sql = "SELECT * FROM {$this->tableName} ORDER BY {$this->primaryKey} ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            return [];
        }
    }
}
?>