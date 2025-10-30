<?php
/**
 * Model: Yêu cầu xuất nguyên liệu
 * - Gói gọn thao tác DB cho plans, materials, requests
 */
require_once CONFIG_PATH . '/database.php';

class YeuCauXuat {
    private PDO $pdo;
    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    public function getPlans(): array {
        $sql = "
            SELECT
                k.MaKeHoach AS ma_kehoach,
                (SELECT kc.MaPhanXuong FROM kehoachcapxuong kc WHERE kc.MaKeHoach = k.MaKeHoach LIMIT 1) AS ma_px,
                k.TenKeHoach AS sanpham,
                COALESCE(kcx.total_qty, cts.total_ct_qty, 0) AS soluong,
                k.NgayBatDau AS ngay_batdau
            FROM kehoachsanxuat k
            LEFT JOIN (
                SELECT MaKeHoach, SUM(SoLuong) AS total_qty FROM kehoachcapxuong GROUP BY MaKeHoach
            ) kcx ON kcx.MaKeHoach = k.MaKeHoach
            LEFT JOIN (
                SELECT MaKeHoach, SUM(SanLuongMucTieu) AS total_ct_qty FROM chitietkehoach GROUP BY MaKeHoach
            ) cts ON cts.MaKeHoach = k.MaKeHoach
            WHERE k.TrangThai = 'Đã duyệt'
            ORDER BY k.NgayBatDau DESC
        ";

        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    public function getPlan(string $maKeHoach): ?array {
        // Lấy thông tin kế hoạch kèm một MaPhanXuong (nếu có) và tổng số lượng
        $sql = "
            SELECT
                k.MaKeHoach AS ma_kehoach,
                (SELECT kc.MaPhanXuong FROM kehoachcapxuong kc WHERE kc.MaKeHoach = k.MaKeHoach LIMIT 1) AS ma_px,
                k.TenKeHoach AS sanpham,
                COALESCE(kcx.total_qty, cts.total_ct_qty, 0) AS soluong,
                k.NgayBatDau AS ngay_batdau,
                k.GhiChu AS ghichu
            FROM kehoachsanxuat k
            LEFT JOIN (
                SELECT MaKeHoach, SUM(SoLuong) AS total_qty FROM kehoachcapxuong GROUP BY MaKeHoach
            ) kcx ON kcx.MaKeHoach = k.MaKeHoach
            LEFT JOIN (
                SELECT MaKeHoach, SUM(SanLuongMucTieu) AS total_ct_qty FROM chitietkehoach GROUP BY MaKeHoach
            ) cts ON cts.MaKeHoach = k.MaKeHoach
                        WHERE k.MaKeHoach = :id
                            AND k.TrangThai = 'Đã duyệt'
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $maKeHoach]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getMaterialsForPlan(string $maKeHoach, int $sanLuong): array {
        // Tính tổng nguyên liệu cho toàn bộ kế hoạch bằng cách tổng hợp DinhMucSuDung * SanLuongMucTieu từ chitietkehoach và dinhmucnguyenlieu
        $sql = "
            SELECT
                dm.MaNguyenLieu AS ma_nguyenlieu,
                nl.TenNguyenLieu AS ten,
                SUM(dm.DinhMucSuDung * ct.SanLuongMucTieu) AS required_qty
            FROM chitietkehoach ct
            JOIN dinhmucnguyenlieu dm ON ct.MaSanPham = dm.MaSanPham
            JOIN nguyenlieu nl ON dm.MaNguyenLieu = nl.MaNguyenLieu
            WHERE ct.MaKeHoach = :kh
            GROUP BY dm.MaNguyenLieu
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':kh' => $maKeHoach]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r) {
            $base = (float)$r['required_qty'];
            $max  = $base * 1.05;
            $out[] = [
                'ma_nguyenlieu' => $r['ma_nguyenlieu'],
                'ten' => $r['ten'],
                'base' => $base,
                'max' => $max,
            ];
        }
        return $out;
    }

    public function saveRequest(string $ma_kehoach, string $ngay_yeucau, string $ghichu, array $materials, string $status): string {
        // Lưu vào các bảng thực tế của schema: phieuyeucauxuatnguyenlieu + chitietphieuyeucauxuatnguyenlieu
        $this->pdo->beginTransaction();
        try {
            // Sinh mã phiếu yêu cầu (MaPhieuYC)
            $prefix = 'PYC' . date('Ymd');
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM phieuyeucauxuatnguyenlieu WHERE MaPhieuYC LIKE :pfx');
            $like = $prefix . '%';
            $stmt->execute([':pfx' => $like]);
            $seq = (int)$stmt->fetchColumn() + 1;
            $ma_phieu = $prefix . str_pad((string)$seq, 3, '0', STR_PAD_LEFT); // PYC20251029001

            // Lấy phân xưởng liên quan từ kế hoạch (nếu có)
            $ma_px = null;
            $plan = $this->getPlan($ma_kehoach);
            if ($plan && !empty($plan['ma_px'])) $ma_px = $plan['ma_px'];

            // Người lập (= maNV) lấy từ session nếu có, fallback 'SYSTEM'
            $maNV = $_SESSION['user_id'] ?? ($_SESSION['username'] ?? 'SYSTEM');

            // Chuẩn hóa trạng thái sang giá trị phù hợp với DB
            $dbStatus = $status;
            if ($dbStatus === 'Chờ xử lý') $dbStatus = 'Chờ duyệt';

            $ins = $this->pdo->prepare('INSERT INTO phieuyeucauxuatnguyenlieu (MaPhieuYC, MaKeHoach, MaPhanXuong, NgayLap, MaNV, TrangThai) VALUES (:id, :kh, :mx, :nl, :nv, :st)');
            $ins->execute([
                ':id' => $ma_phieu,
                ':kh' => $ma_kehoach,
                ':mx' => $ma_px,
                ':nl' => $ngay_yeucau . ' 00:00:00',
                ':nv' => $maNV,
                ':st' => $dbStatus,
            ]);

            // Chi tiết phiếu — dùng bảng có trong dump: chitietphieuyeucauxuatnguyenlieu
            $insertCt = $this->pdo->prepare('INSERT INTO chitietphieuyeucauxuatnguyenlieu (MaCTNL, MaPhieuYC, MaNguyenLieu, SoLuong) VALUES (:id, :pyc, :nl, :sl)');
            $ctSeq = 1;
            foreach ($materials as $m) {
                $ma_nl = trim($m['ma_nguyenlieu'] ?? ($m['ma_nguyenlieu'] ?? ''));
                $sl    = (float)($m['so_luong'] ?? $m['base'] ?? 0);
                if ($ma_nl === '' || $sl <= 0) continue;
                $ma_ct = 'CTNL' . date('Ymd') . str_pad((string)$ctSeq, 3, '0', STR_PAD_LEFT);
                $ctSeq++;
                $insertCt->execute([
                    ':id' => $ma_ct,
                    ':pyc' => $ma_phieu,
                    ':nl' => $ma_nl,
                    ':sl' => $sl,
                ]);
            }

            $this->pdo->commit();
            return $ma_phieu;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            throw $e;
        }
    }

    public function listRequests(string $status = '', string $keyword = ''): array {
                // Truy vấn theo schema thực tế (phieuyeucauxuatnguyenlieu + chitiet...)
                $where = [];
                $params = [];
                if ($status !== '') { $where[] = 'p.TrangThai = :st'; $params[':st'] = $status; }
                if ($keyword !== '') { $where[] = '(p.MaPhieuYC LIKE :kw OR p.MaPhanXuong LIKE :kw)'; $params[':kw'] = "%$keyword%"; }

                $sql = "SELECT
                        p.MaPhieuYC AS ma_phieu,
                        p.MaKeHoach AS ma_kehoach,
                        DATE(p.NgayLap) AS ngay_yeucau,
                        p.TrangThai AS trangthai,
                        NULL AS ghichu,
                        p.NgayLap AS ngay_tao
                    FROM phieuyeucauxuatnguyenlieu p";

                if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
                $sql .= ' ORDER BY p.NgayLap DESC';

                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
