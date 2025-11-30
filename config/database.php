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
            // Log lỗi thay vì echo (tránh làm hỏng output HTML)
            error_log("Database connection error: " . $exception->getMessage());
            // Không echo ra màn hình, chỉ return null
            // Caller sẽ xử lý lỗi này
        }
        
        return $this->conn;
    }
}


if (!function_exists('getPDO')) {
    function getPDO() {
        static $pdo = null;
        if ($pdo) return $pdo;

        // Tạo instance Database và lấy kết nối
        $db = new Database();
        $pdo = $db->getConnection();
        return $pdo;
    }
}

?>
