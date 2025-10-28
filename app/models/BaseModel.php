<?php
// Tệp: app/models/BaseModel.php

class BaseModel {
    
    // Biến lưu kết nối CSDL (PDO)
    protected $db;
    
    // Tên bảng (sẽ được các model con ghi đè)
    protected $tableName;
    
    // Khóa chính (sẽ được các model con ghi đè)
    protected $primaryKey = 'id'; // Mặc định, ví dụ: MaKeHoach, MaPhanXuong...

    /**
     * Hàm khởi tạo, nhận kết nối CSDL từ BaseController
     * @param PDO $db
     */
    public function __construct($db) {
        if ($db === null) {
            throw new Exception("BaseModel yêu cầu một kết nối CSDL (PDO).");
        }
        $this->db = $db;
    }

    /**
     * Lấy tất cả bản ghi
     */
    public function getAll() {
        if (empty($this->tableName)) {
            throw new Exception("tableName chưa được định nghĩa trong Model " . get_class($this));
        }
        
        try {
            $sql = "SELECT * FROM {$this->tableName}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy bản ghi theo ID (Khóa chính)
     */
    public function getById($id) {
         if (empty($this->tableName)) {
            throw new Exception("tableName chưa được định nghĩa trong Model " . get_class($this));
        }
        
        try {
            $sql = "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo bản ghi mới
     * $data là một mảng (key => value)
     */
    public function create($data) {
        if (empty($this->tableName)) {
            throw new Exception("tableName chưa được định nghĩa trong Model " . get_class($this));
        }
        
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        try {
            $sql = "INSERT INTO {$this->tableName} ($columns) VALUES ($placeholders)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($data);
            // Trả về ID nếu là AUTO_INCREMENT, hoặc true nếu thành công
            return $this->db->lastInsertId() ?: true; 
        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            throw $e; // Ném lỗi ra để Controller (Transaction) bắt
        }
    }

    /**
     * Cập nhật bản ghi theo ID
     */
    public function update($id, $data) {
        if (empty($this->tableName)) {
            throw new Exception("tableName chưa được định nghĩa trong Model " . get_class($this));
        }

        $setPart = [];
        foreach ($data as $key => $value) {
            $setPart[] = "$key = :$key";
        }
        $setString = implode(', ', $setPart);
        
        // Thêm khóa chính vào mảng data để bind
        $data[$this->primaryKey] = $id;

        try {
            $sql = "UPDATE {$this->tableName} SET $setString WHERE {$this->primaryKey} = :{$this->primaryKey}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($data);
            return $stmt->rowCount(); // Trả về số hàng bị ảnh hưởng
        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Xóa bản ghi theo ID
     */
    public function delete($id) {
         if (empty($this->tableName)) {
            throw new Exception("tableName chưa được định nghĩa trong Model " . get_class($this));
        }
        
        try {
            $sql = "DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            throw $e;
        }
    }
}
?>