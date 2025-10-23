<?php
/**
 * Model User - Quản lý người dùng
 * Hệ thống quản lý sản xuất nhà máy 4Ever
 */

require_once CONFIG_PATH . '/database.php';

class User {
    private $conn;
    private $table = 'NhanVien';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Xác thực đăng nhập: cho phép dùng MaNV hoặc SoDienThoai, kiểm tra Password (hash)
     */
    public function authenticate($identifier, $password) {
        $query = "SELECT * FROM NhanVien WHERE (MaNV = :identifier OR SoDienThoai = :identifier) AND TrangThai = 'Đang làm việc'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':identifier', $identifier);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!empty($user['Password']) && password_verify($password, $user['Password'])) {
                return $user;
            }
        }
        return false;
    }
    
    public function findById($maNV) {
        $query = "SELECT * FROM NhanVien WHERE MaNV = :maNV AND TrangThai = 'Đang làm việc'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':maNV', $maNV);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    
    public function findByIdentifier($identifier) {
        $query = "SELECT u.*, r.role_name 
                  FROM " . $this->table . " u
                  INNER JOIN roles r ON u.role_id = r.id
                  WHERE (u.username = :identifier OR u.email = :identifier OR u.phone = :identifier) 
                  AND u.is_active = 1";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':identifier', $identifier);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAll() {
        $query = "SELECT * FROM NhanVien WHERE TrangThai = 'Đang làm việc' ORDER BY HoTen";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo mới nhân viên (có hash password)
     */
    public function create($data) {
        $data['Password'] = password_hash($data['Password'], PASSWORD_DEFAULT);
        $query = "INSERT INTO NhanVien (MaNV, HoTen, ChucVu, BoPhan, SoDienThoai, Password, TrangThai) VALUES (:MaNV, :HoTen, :ChucVu, :BoPhan, :SoDienThoai, :Password, :TrangThai)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    /**
     * Đổi mật khẩu nhân viên
     */
    public function changePassword($maNV, $newPassword) {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE NhanVien SET Password = :Password WHERE MaNV = :MaNV";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':Password', $hash);
        $stmt->bindParam(':MaNV', $maNV);
        return $stmt->execute();
    }
    
    public function getByChucVu($chucVu) {
        $query = "SELECT * FROM NhanVien WHERE ChucVu = :chucVu AND TrangThai = 'Đang làm việc' ORDER BY HoTen";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':chucVu', $chucVu);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Các hàm create/update/changeStatus cần viết lại nếu muốn thêm/sửa/xóa nhân viên
    // ...
}
?>
