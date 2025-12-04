<?php
/**
 * PhieuKiemTraSPModel
 * Model xử lý dữ liệu cho chức năng tạo phiếu yêu cầu kiểm tra lô/sản phẩm
 * PHP 8+, PDO
 *
 * Methods:
 *  - getPlans(): lấy danh sách kế hoạch sản xuất (Đã duyệt)
 *  - getLots(): lấy danh sách lô hàng có trong hệ thống
 *  - createTicket(...): lưu phiếu vào bảng phieuyeucaukiemtralosp
 *  - listTickets(): liệt kê phiếu
 */
require_once CONFIG_PATH . '/database.php';

class PhieuKiemTraSPModel {
    private PDO $pdo;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    /**
     * Lấy danh sách kế hoạch sản xuất đang ở trạng thái 'Đã duyệt'
     * @return array
     */
    public function getPlans(): array
    {
        $sql = "SELECT MaKeHoach, TenKeHoach, NgayBatDau, NgayKetThuc FROM kehoachsanxuat WHERE TrangThai = 'Đã duyệt' ORDER BY NgayBatDau DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách lô hàng (LoHang). Lọc sơ để chỉ ra lô có sản phẩm.
     * @return array
     */
    public function getLots(): array
    {
        $sql = "SELECT MaLoHang, MaSanPham, SoLuong, TrangThaiQC FROM lohang ORDER BY MaLoHang DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Sinh mã phiếu dạng KT-{YYYYMMDD}-{increment}
     * @return string
     */
    private function generateTicketCode(): string
    {
        $base = 'KT-' . date('Ymd') . '-';
        $like = $base . '%';
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM phieuyeucaukiemtralosp WHERE MaPhieuKT LIKE :pfx');
        $stmt->execute([':pfx' => $like]);
        $seq = (int)$stmt->fetchColumn() + 1;
        // ensure uniqueness
        do {
            $code = $base . str_pad((string)$seq, 3, '0', STR_PAD_LEFT);
            $chk = $this->pdo->prepare('SELECT 1 FROM phieuyeucaukiemtralosp WHERE MaPhieuKT = :id LIMIT 1');
            $chk->execute([':id' => $code]);
            if ($chk->fetchColumn()) { $seq++; continue; }
            break;
        } while (true);
        return $code;
    }

    /**
     * Tạo phiếu kiểm tra
     * @param string $maLoHang
     * @param string $ngayKiemTra (Y-m-d)
     * @param string $maNV
     * @param string $status
     * @return string MaPhieuKT
     * @throws Exception on DB error
     */
    public function createTicket(string $maLoHang, string $ngayKiemTra, string $maNV, string $status = 'Chờ xử lý'): string
    {
        $this->pdo->beginTransaction();
        try {
            $maPhieu = $this->generateTicketCode();
            $now = (new DateTime())->format('Y-m-d H:i:s');
            $sql = 'INSERT INTO phieuyeucaukiemtralosp (MaPhieuKT, MaLoHang, NgayKiemTra, MaNV, TrangThai) VALUES (:id, :lo, :ng, :nv, :st)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $maPhieu,
                ':lo' => $maLoHang,
                ':ng' => $ngayKiemTra . ' 00:00:00',
                ':nv' => $maNV,
                ':st' => $status,
            ]);

            $this->pdo->commit();

            // Ghi log thông báo giả lập đến QC (file qc_notify.log)
            $msg = sprintf("[%s] NOTIFY QC: New check ticket %s for lot %s by %s\n", $now, $maPhieu, $maLoHang, $maNV);
            @file_put_contents(APP_PATH . '/logs/qc_notify.log', $msg, FILE_APPEND | LOCK_EX);

            return $maPhieu;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Liệt kê các phiếu kiểm tra
     * @return array
     */
    public function listTickets(): array
    {
        $sql = "SELECT MaPhieuKT, MaLoHang, DATE(NgayKiemTra) AS ngay_kiemtra, TrangThai, MaNV, NgayLap FROM phieuyeucaukiemtralosp ORDER BY NgayLap DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy chi tiết một phiếu kiểm tra (header + thông tin lô hàng nếu có)
     */
    public function getTicketDetails(string $maPhieu): ?array {
        $sql = 'SELECT p.MaPhieuKT, p.MaLoHang, DATE(p.NgayKiemTra) AS ngay_kiemtra, p.TrangThai, p.MaNV, p.NgayLap, l.MaSanPham, l.SoLuong, l.TrangThaiQC FROM phieuyeucaukiemtralosp p LEFT JOIN lohang l ON l.MaLoHang = p.MaLoHang WHERE p.MaPhieuKT = :id LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $maPhieu]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}

?>
