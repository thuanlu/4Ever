<?php
require_once CONFIG_PATH . '/database.php';

class PhieuKiemTraSP
{
    private PDO $pdo;

    public function __construct(PDO $db)
    {
        $this->pdo = $db;
    }

    public function getPlans(?string $maPhanXuong = null): array
    {
        $sql = "SELECT k.MaKeHoach, k.TenKeHoach, k.NgayBatDau, k.NgayKetThuc,
                   (SELECT kc.MaPhanXuong FROM kehoachcapxuong kc WHERE kc.MaKeHoach = k.MaKeHoach LIMIT 1) AS MaPhanXuong,
                   GROUP_CONCAT(DISTINCT ct.MaSanPham SEPARATOR ',') AS MaSanPhamList,
                   GROUP_CONCAT(DISTINCT CONCAT(ct.MaSanPham,':',sp.TenSanPham) SEPARATOR ',') AS MaSanPhamNamedList
            FROM kehoachsanxuat k
            LEFT JOIN chitietkehoach ct ON ct.MaKeHoach = k.MaKeHoach
            LEFT JOIN sanpham sp ON sp.MaSanPham = ct.MaSanPham
            WHERE k.TrangThai = 'Đã duyệt'";

        if ($maPhanXuong) {
            $sql .= " AND EXISTS(SELECT 1 FROM kehoachcapxuong kc WHERE kc.MaKeHoach = k.MaKeHoach AND kc.MaPhanXuong = :ma_px)";
            $sql .= " GROUP BY k.MaKeHoach ORDER BY k.NgayBatDau DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':ma_px' => $maPhanXuong]);
        } else {
            $sql .= " GROUP BY k.MaKeHoach ORDER BY k.NgayBatDau DESC";
            $stmt = $this->pdo->query($sql);
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLots(?string $maPhanXuong = null): array
    {
        if ($maPhanXuong) {
            $sql = "SELECT lo.MaLoHang, lo.MaSanPham, lo.SoLuong, lo.TrangThaiQC
                    FROM lohang lo
                    WHERE EXISTS(
                        SELECT 1 FROM chitietkehoach ct
                        WHERE ct.MaSanPham = lo.MaSanPham
                          AND ct.MaPhanXuong = :ma_px
                    )
                    ORDER BY lo.MaLoHang DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':ma_px' => $maPhanXuong]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $sql = "SELECT MaLoHang, MaSanPham, SoLuong, TrangThaiQC FROM lohang ORDER BY MaLoHang DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function generateTicketCode(): string
    {
        $base = 'KT' . date('ymd');
        $like = $base . '%';
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM phieuyeucaukiemtralosp WHERE MaPhieuKT LIKE :pfx');
        $stmt->execute([':pfx' => $like]);
        $seq = (int)$stmt->fetchColumn() + 1;

        do {
            if ($seq <= 99) {
                $suffix = str_pad((string)$seq, 2, '0', STR_PAD_LEFT);
                $code = $base . $suffix;
            } else {
                $hex = strtoupper(substr(dechex($seq), -2));
                $rand = strtoupper(substr(md5(uniqid((string)time(), true)), 0, 2));
                $code = substr($base, 0, 6) . $hex . $rand;
            }

            $chk = $this->pdo->prepare('SELECT 1 FROM phieuyeucaukiemtralosp WHERE MaPhieuKT = :id LIMIT 1');
            $chk->execute([':id' => $code]);
            if ($chk->fetchColumn()) { $seq++; continue; }
            break;
        } while (true);

        return $code;
    }

    public function createTicket(string $maLoHang, string $ngayKiemTra, string $maNV, string $status = 'Chờ xử lý'): string
    {
        $this->pdo->beginTransaction();
        try {
            $maPhieu = $this->generateTicketCode();
            $sql = 'INSERT INTO phieuyeucaukiemtralosp (MaPhieuKT, MaLoHang, NgayKiemTra, MaNV, TrangThai) VALUES (:id, :lo, :ng, :nv, :st)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $maPhieu,
                ':lo' => $maLoHang,
                ':ng' => (new DateTime($ngayKiemTra))->format('Y-m-d'),
                ':nv' => $maNV,
                ':st' => $status,
            ]);
            $this->pdo->commit();

            $msg = sprintf("[%s] NOTIFY QC: New check ticket %s for lot %s by %s\n", (new DateTime())->format('Y-m-d H:i:s'), $maPhieu, $maLoHang, $maNV);
            @file_put_contents(APP_PATH . '/logs/qc_notify.log', $msg, FILE_APPEND | LOCK_EX);

            return $maPhieu;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
    }

    public function listTickets(): array
    {
        $sql = "SELECT MaPhieuKT, MaLoHang, DATE(NgayKiemTra) AS ngay_kiemtra, TrangThai, MaNV FROM phieuyeucaukiemtralosp ORDER BY NgayKiemTra DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTicketDetails(string $maPhieu): ?array
    {
        // Include product name by joining sanpham
        $sql = 'SELECT p.MaPhieuKT, p.MaLoHang, DATE(p.NgayKiemTra) AS ngay_kiemtra, p.TrangThai, p.MaNV, p.KetQua,
                       l.MaSanPham, l.SoLuong, l.TrangThaiQC, sp.TenSanPham
                FROM phieuyeucaukiemtralosp p
                LEFT JOIN lohang l ON l.MaLoHang = p.MaLoHang
                LEFT JOIN sanpham sp ON sp.MaSanPham = l.MaSanPham
                WHERE p.MaPhieuKT = :id LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $maPhieu]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}

?>

