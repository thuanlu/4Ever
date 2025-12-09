<?php
require_once CONFIG_PATH . '/database.php';

class KetQuaKiemDinh {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    // 1. Lấy danh sách phiếu chờ (Chỉ lấy những phiếu có trạng thái 'Chờ kiểm tra')
    public function getPendingRequests() {
        $sql = "
            SELECT pyk.MaPhieuKT, lh.MaLoHang, sp.TenSanPham, pyk.NgayKiemTra, pyk.TrangThai, lh.TrangThaiQC
            FROM PhieuYeuCauKiemTraLoSP pyk
            JOIN LoHang lh ON pyk.MaLoHang = lh.MaLoHang
            JOIN SanPham sp ON lh.MaSanPham = sp.MaSanPham
            WHERE pyk.TrangThai = 'Chờ kiểm tra'
            ORDER BY pyk.NgayKiemTra ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Lấy chi tiết phiếu
    public function getById($maPhieuKT) {
        $sql = "
            SELECT pyk.*, lh.MaLoHang, sp.TenSanPham, lh.SoLuong, nv.HoTen AS NguoiYeuCau, lh.TrangThaiQC
            FROM PhieuYeuCauKiemTraLoSP pyk
            JOIN LoHang lh ON pyk.MaLoHang = lh.MaLoHang
            JOIN SanPham sp ON lh.MaSanPham = sp.MaSanPham
            JOIN NhanVien nv ON pyk.MaNV = nv.MaNV
            WHERE pyk.MaPhieuKT = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$maPhieuKT]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 3. Sinh mã tự động KD...
    public function generateMaKD() {
        $sql = "SELECT MaKD FROM KetQuaKiemDinh WHERE MaKD LIKE 'KD%' ORDER BY LENGTH(MaKD) DESC, MaKD DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $lastId = $stmt->fetchColumn();
        $number = $lastId ? (int)substr($lastId, 2) + 1 : 1;
        return 'KD' . str_pad($number, 2, '0', STR_PAD_LEFT);
    }

    // 4. LƯU KẾT QUẢ VÀ CẬP NHẬT TRẠNG THÁI (CORE LOGIC)
    public function saveResult($maPhieuKT, $maLoHang, $maNV, $ketQua, $maKD, $ghiChu) {
        try {
            $this->conn->beginTransaction();

            // --- Logic xác định trạng thái ---
            $trangThaiKD = '';      // Trạng thái cho phiếu Kết Quả (KetQuaKiemDinh)
            $trangThaiQC_Lo = '';   // Trạng thái cho Lô Hàng (LoHang)
            
            if ($ketQua === 'Đạt') {
                $trangThaiKD = 'Đã kiểm tra';
                $trangThaiQC_Lo = 'Đạt';
            } else {
                $trangThaiKD = 'Bị từ chối';
                $trangThaiQC_Lo = 'Không đạt';
            }

            // A. Insert vào bảng KetQuaKiemDinh (Lưu kết quả QC)
            $stmt1 = $this->conn->prepare("
                INSERT INTO KetQuaKiemDinh (MaKD, MaLoHang, MaPhieuKT, MaNV, KetQua, GhiChu, TrangThai, NgayLap)
                VALUES (:MaKD, :MaLoHang, :MaPhieuKT, :MaNV, :KetQua, :GhiChu, :TrangThai, NOW())
            ");
            $stmt1->execute([
                'MaKD' => $maKD,
                'MaLoHang' => $maLoHang,
                'MaPhieuKT' => $maPhieuKT,
                'MaNV' => $maNV,
                'KetQua' => $ketQua,
                'GhiChu' => $ghiChu,
                'TrangThai' => $trangThaiKD 
            ]);

            // B. Cập nhật bảng PhieuYeuCauKiemTraLoSP -> Chuyển thành 'Hoàn thành'
            // (ĐÂY LÀ PHẦN QUAN TRỌNG ĐỂ PHIẾU BIẾN MẤT KHỎI DANH SÁCH CHỜ)
            $stmt2 = $this->conn->prepare("
                UPDATE PhieuYeuCauKiemTraLoSP
                SET TrangThai = 'Hoàn thành'
                WHERE MaPhieuKT = :MaPhieuKT
            ");
            $stmt2->execute([
                'MaPhieuKT' => $maPhieuKT
            ]);

            // C. Cập nhật bảng LoHang -> Cập nhật trạng thái chất lượng ('Đạt' hoặc 'Không đạt')
            $stmt3 = $this->conn->prepare("
                UPDATE LoHang
                SET TrangThaiQC = :TrangThaiQC
                WHERE MaLoHang = :MaLoHang
            ");
            $stmt3->execute([
                'TrangThaiQC' => $trangThaiQC_Lo, 
                'MaLoHang' => $maLoHang
            ]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    // 5. Lịch sử
    public function getHistory() {
        $sql = "
            SELECT kq.MaKD,pyk.MaPhieuKT,kq.GhiChu, kq.MaPhieuKT, lh.MaLoHang, sp.TenSanPham, kq.KetQua, kq.NgayLap, kq.TrangThai, nv.HoTen AS NguoiKiemTra
            FROM KetQuaKiemDinh kq
            JOIN PhieuYeuCauKiemTraLoSP pyk ON kq.MaPhieuKT = pyk.MaPhieuKT
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