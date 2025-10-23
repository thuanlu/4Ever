<?php
require_once CONFIG_PATH . '/database.php';
class LenhSanXuat {
    private $conn;
    private $table = 'LenhSanXuat';
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    public function create($data) {
        $query = "INSERT INTO LenhSanXuat (MaKeHoach, MaDayChuyen, Ca, SanLuongMucTieu, MaToTruong, NgayLap) VALUES (:ma_kehoach, :day_chuyen, :ca, :san_luong, :to_truong, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ma_kehoach', $data['ma_kehoach']);
        $stmt->bindParam(':day_chuyen', $data['day_chuyen']);
        $stmt->bindParam(':ca', $data['ca']);
        $stmt->bindParam(':san_luong', $data['san_luong']);
        $stmt->bindParam(':to_truong', $data['to_truong']);
        return $stmt->execute();
    }
}
?>
