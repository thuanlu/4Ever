<?php
/**
 * Cấu hình kết nối cơ sở dữ liệu
 * Hệ thống quản lý sản xuất nhà máy 4Ever
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'qlsx_4ever';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Lỗi kết nối: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}
?>
