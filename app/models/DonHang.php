<?php
/**
 * Model DonHang - Quản lý đơn hàng (dùng cho dropdown kế hoạch sản xuất)
 */
require_once CONFIG_PATH . '/database.php';

class DonHang {
    private $conn;
    private $table = 'DonHang';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM DonHang ORDER BY NgayDat DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
