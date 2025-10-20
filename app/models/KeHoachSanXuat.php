<?php
/**
 * Model KeHoachSanXuat - Quản lý kế hoạch sản xuất
 */
require_once CONFIG_PATH . '/database.php';

class KeHoachSanXuat {
    public function getApprovedPlansByBoPhan($boPhan) {
        $query = "SELECT k.*, nv.HoTen as NguoiLap, dh.TenDonHang FROM KeHoachSanXuat k
                  LEFT JOIN NhanVien nv ON k.MaNV = nv.MaNV
                  LEFT JOIN DonHang dh ON k.MaDonHang = dh.MaDonHang
                  WHERE k.TrangThai = 'Đã duyệt' AND nv.BoPhan = :boPhan
                  ORDER BY k.NgayBatDau DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':boPhan', $boPhan);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getApprovedPlans() {
        $query = "SELECT k.*, nv.HoTen as NguoiLap, dh.TenDonHang FROM KeHoachSanXuat k
                  LEFT JOIN NhanVien nv ON k.MaNV = nv.MaNV
                  LEFT JOIN DonHang dh ON k.MaDonHang = dh.MaDonHang
                  WHERE k.TrangThai = 'Đã duyệt'
                  ORDER BY k.NgayBatDau DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    private $conn;
    private $table = 'KeHoachSanXuat';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT k.*, nv.HoTen as NguoiLap, dh.TenDonHang FROM KeHoachSanXuat k
                  LEFT JOIN NhanVien nv ON k.MaNV = nv.MaNV
                  LEFT JOIN DonHang dh ON k.MaDonHang = dh.MaDonHang
                  ORDER BY k.NgayLap DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($maKeHoach) {
        $query = "SELECT * FROM KeHoachSanXuat WHERE MaKeHoach = :maKeHoach";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':maKeHoach', $maKeHoach);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO KeHoachSanXuat (MaKeHoach, TenKeHoach, NgayBatDau, NgayKetThuc, MaNV, MaDonHang, TrangThai, TongChiPhiDuKien, SoLuongCongNhanCan, GhiChu)
                  VALUES (:MaKeHoach, :TenKeHoach, :NgayBatDau, :NgayKetThuc, :MaNV, :MaDonHang, :TrangThai, :TongChiPhiDuKien, :SoLuongCongNhanCan, :GhiChu)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function update($maKeHoach, $data) {
        $data['MaKeHoach'] = $maKeHoach;
        $query = "UPDATE KeHoachSanXuat SET TenKeHoach = :TenKeHoach, NgayBatDau = :NgayBatDau, NgayKetThuc = :NgayKetThuc, MaDonHang = :MaDonHang, TrangThai = :TrangThai, TongChiPhiDuKien = :TongChiPhiDuKien, SoLuongCongNhanCan = :SoLuongCongNhanCan, GhiChu = :GhiChu WHERE MaKeHoach = :MaKeHoach";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function delete($maKeHoach) {
        $query = "DELETE FROM KeHoachSanXuat WHERE MaKeHoach = :maKeHoach";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':maKeHoach', $maKeHoach);
        return $stmt->execute();
    }
}
?>
