<?php
require_once CONFIG_PATH . '/database.php';

class KetQuaKiemDinh {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    // 1. Danh sách phiếu chờ kiểm tra
    public function getPendingRequests() {
        $sql = "
            SELECT pyk.MaPhieuKT, lh.MaLoHang, sp.TenSanPham, pyk.NgayKiemTra, pyk.TrangThai
            FROM PhieuYeuCauKiemTraLoSP pyk
            JOIN LoHang lh ON pyk.MaLoHang = lh.MaLoHang
            JOIN SanPham sp ON lh.MaSanPham = sp.MaSanPham
            WHERE pyk.TrangThai = 'Chờ kiểm tra'
            ORDER BY pyk.NgayKiemTra ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Lấy phiếu theo MaPhieuKT
    public function getById($maPhieuKT) {
        $sql = "
            SELECT pyk.*, lh.MaLoHang, sp.TenSanPham, nv.HoTen AS NguoiYeuCau
            FROM PhieuYeuCauKiemTraLoSP pyk
            JOIN LoHang lh ON pyk.MaLoHang = lh.MaLoHang
            JOIN SanPham sp ON lh.MaSanPham = sp.MaSanPham
            JOIN NhanVien nv ON pyk.MaNV = nv.MaNV
            WHERE pyk.MaPhieuKT = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$maPhieuKT]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. Lưu kết quả kiểm định và cập nhật trạng thái phiếu
    // public function saveResult($maPhieuKT, $maLoHang, $maNV, $ketQua) {
    //     $this->conn->beginTransaction();

    //     // 3a. Thêm kết quả kiểm định
    //     $maKD = uniqid('KD'); // tự sinh MaKD
    //     $stmt1 = $this->conn->prepare("
    //         INSERT INTO KetQuaKiemDinh (MaKD, MaLoHang, MaPhieuKT, MaNV, KetQua)
    //         VALUES (:MaKD, :MaLoHang, :MaPhieuKT, :MaNV, :KetQua)
    //     ");
    //     $stmt1->execute([
    //         'MaKD' => $maKD,
    //         'MaLoHang' => $maLoHang,
    //         'MaPhieuKT' => $maPhieuKT,
    //         'MaNV' => $maNV,
    //         'KetQua' => $ketQua
    //     ]);

    //     // 3b. Cập nhật phiếu yêu cầu kiểm tra
    //     $stmt2 = $this->conn->prepare("
    //         UPDATE PhieuYeuCauKiemTraLoSP
    //         SET TrangThai = :TrangThai, KetQua = :KetQua
    //         WHERE MaPhieuKT = :MaPhieuKT
    //     ");
    //     $trangThai = ($ketQua === 'Đạt') ? 'Đã kiểm định - Đạt' : 'Đã kiểm định - Không đạt';
    //     $stmt2->execute([
    //         'TrangThai' => $trangThai,
    //         'KetQua' => $ketQua,
    //         'MaPhieuKT' => $maPhieuKT
    //     ]);

    //     $this->conn->commit();
    //     return $maKD;
    // }
    public function saveResult($maPhieuKT, $maLoHang, $maNV, $ketQua, $maKD) {
    $this->conn->beginTransaction();

    $stmt1 = $this->conn->prepare("
        INSERT INTO KetQuaKiemDinh (MaKD, MaLoHang, MaPhieuKT, MaNV, KetQua)
        VALUES (:MaKD, :MaLoHang, :MaPhieuKT, :MaNV, :KetQua)
    ");
    $stmt1->execute([
        'MaKD' => $maKD,
        'MaLoHang' => $maLoHang,
        'MaPhieuKT' => $maPhieuKT,
        'MaNV' => $maNV,
        'KetQua' => $ketQua
    ]);

    $stmt2 = $this->conn->prepare("
        UPDATE PhieuYeuCauKiemTraLoSP
        SET TrangThai = :TrangThai, KetQua = :KetQua
        WHERE MaPhieuKT = :MaPhieuKT
    ");
    $trangThai = ($ketQua === 'Đạt') ? 'Đã kiểm định - Đạt' : 'Đã kiểm định - Không đạt';
    $stmt2->execute([
        'TrangThai' => $trangThai,
        'KetQua' => $ketQua,
        'MaPhieuKT' => $maPhieuKT
    ]);

    $this->conn->commit();
    return $maKD;
}



    // 4. Lịch sử QC
    public function getHistory() {
        $sql = "
            SELECT kq.MaKD, kq.MaPhieuKT, lh.MaLoHang, sp.TenSanPham, kq.KetQua, kq.NgayLap, kq.TrangThai, nv.HoTen AS NguoiKiemTra
            FROM KetQuaKiemDinh kq
            JOIN LoHang lh ON kq.MaLoHang = lh.MaLoHang
            JOIN SanPham sp ON lh.MaSanPham = sp.MaSanPham
            JOIN NhanVien nv ON kq.MaNV = nv.MaNV
            ORDER BY kq.NgayLap DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
