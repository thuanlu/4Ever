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

    /**
     * Trả về MaPhanXuong nếu user (xưởng trưởng) được gán trong bảng phanxuong
     */
    public function getPhanXuongForUser(string $maNV): ?string {
        $stmt = $this->pdo->prepare('SELECT MaPhanXuong FROM phanxuong WHERE MaXuongTruong = :maNV LIMIT 1');
        $stmt->execute([':maNV' => $maNV]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['MaPhanXuong'] ?? null;
    }

    public function getPlans(?string $maPhanXuong = null): array {
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
        ";

        // Nếu truyền MaPhanXuong thì chỉ lấy các kế hoạch có cấp xưởng cho phân xưởng đó
        if ($maPhanXuong !== null && $maPhanXuong !== '') {
            $sql .= " AND EXISTS(SELECT 1 FROM kehoachcapxuong kc2 WHERE kc2.MaKeHoach = k.MaKeHoach AND kc2.MaPhanXuong = :ma_px)";
            $sql .= " ORDER BY k.NgayBatDau DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':ma_px' => $maPhanXuong]);
        } else {
            $sql .= " ORDER BY k.NgayBatDau DESC";
            $stmt = $this->pdo->query($sql);
        }

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
            // NOTE: column MaPhieuYC in schema is varchar(10). Generate a 10-char id to avoid truncation
            // Format: P + ymd (6) + seq(3) => total 10 chars, e.g. P251103001
            $base = 'P' . date('ymd');
            $like = $base . '%';
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM phieuyeucauxuatnguyenlieu WHERE MaPhieuYC LIKE :pfx');
            $stmt->execute([':pfx' => $like]);
            $seq = (int)$stmt->fetchColumn() + 1;
            // Ensure uniqueness in case of race or unexpected duplicates: loop until unused
            do {
                $ma_phieu = $base . str_pad((string)$seq, 3, '0', STR_PAD_LEFT); // e.g. P251103001
                $chk = $this->pdo->prepare('SELECT 1 FROM phieuyeucauxuatnguyenlieu WHERE MaPhieuYC = :id LIMIT 1');
                $chk->execute([':id' => $ma_phieu]);
                if ($chk->fetchColumn()) {
                    $seq++;
                    continue;
                }
                break;
            } while (true);

            // Lấy phân xưởng liên quan: ưu tiên lấy từ tài khoản xưởng trưởng đang đăng nhập
            // nếu không tìm thấy thì dùng MaPhanXuong từ kế hoạch (nếu có), cuối cùng fallback chuỗi rỗng.
            $plan = $this->getPlan($ma_kehoach);
            $ma_px = '';
            $maNV = $_SESSION['user_id'] ?? ($_SESSION['username'] ?? null);
            if ($maNV) {
                // Tìm phân xưởng do người dùng làm xưởng trưởng
                $q = $this->pdo->prepare('SELECT MaPhanXuong FROM phanxuong WHERE MaXuongTruong = :maNV LIMIT 1');
                $q->execute([':maNV' => $maNV]);
                $found = $q->fetch(PDO::FETCH_ASSOC);
                if ($found && !empty($found['MaPhanXuong'])) {
                    $ma_px = $found['MaPhanXuong'];
                }
            }
            if ($ma_px === '') {
                $ma_px = ($plan['ma_px'] ?? '') ?: '';
            }

            // Nếu vẫn chưa xác định được phân xưởng thì không thể tiếp tục vì có ràng buộc FK
            if ($ma_px === '') {
                throw new Exception('Không xác định phân xưởng cho phiếu. Vui lòng kiểm tra tài khoản xưởng trưởng hoặc cấu hình phân xưởng.');
            }

            // Người lập (= maNV) lấy từ session nếu có
            $maNV = $_SESSION['user_id'] ?? ($_SESSION['username'] ?? null);
            if (!$maNV) {
                throw new Exception('Không xác định người dùng hiện tại. Vui lòng đăng nhập lại.');
            }

            // Kiểm tra tài khoản có tồn tại trong bảng nhanvien (ràng buộc FK)
            $chk = $this->pdo->prepare('SELECT 1 FROM nhanvien WHERE MaNV = :maNV LIMIT 1');
            $chk->execute([':maNV' => $maNV]);
            if (!$chk->fetchColumn()) {
                throw new Exception('Tài khoản người dùng không tồn tại trong hệ thống.');
            }

            // Chuẩn hóa trạng thái sang giá trị phù hợp với DB
            $dbStatus = $status;
            if ($dbStatus === 'Chờ xử lý') $dbStatus = 'Chờ duyệt';

            // Use current timestamp as NgayLap (time of submit)
            $now = (new DateTime())->format('Y-m-d H:i:s');
            $ins = $this->pdo->prepare('INSERT INTO phieuyeucauxuatnguyenlieu (MaPhieuYC, MaKeHoach, MaPhanXuong, NgayLap, MaNV, TrangThai) VALUES (:id, :kh, :mx, :nl, :nv, :st)');
            $ins->execute([
                ':id' => $ma_phieu,
                ':kh' => $ma_kehoach,
                ':mx' => $ma_px,
                ':nl' => $now,
                ':nv' => $maNV,
                ':st' => $dbStatus,
            ]);

            // Chi tiết phiếu — dùng bảng có trong dump: chitietphieuyeucauxuatnguyenlieu
            $insertCt = $this->pdo->prepare('INSERT INTO chitietphieuyeucauxuatnguyenlieu (MaCTNL, MaPhieuYC, MaNguyenLieu, SoLuong) VALUES (:id, :pyc, :nl, :sl)');

            // Prepare a date-based base and start sequence from existing count to reduce collisions
            $baseCt = 'CTNL' . date('Ymd');
            $stmtCtCount = $this->pdo->prepare('SELECT COUNT(*) FROM chitietphieuyeucauxuatnguyenlieu WHERE MaCTNL LIKE :pfx');
            $stmtCtCount->execute([':pfx' => $baseCt . '%']);
            $ctSeq = (int)$stmtCtCount->fetchColumn() + 1;

            foreach ($materials as $m) {
                $ma_nl = trim($m['ma_nguyenlieu'] ?? ($m['ma_nguyenlieu'] ?? ''));
                $sl    = (float)($m['so_luong'] ?? $m['base'] ?? 0);
                if ($ma_nl === '' || $sl <= 0) continue;

                // Loop until we find an unused MaCTNL (protect against duplicates)
                do {
                    $ma_ct = $baseCt . str_pad((string)$ctSeq, 3, '0', STR_PAD_LEFT);
                    $chk = $this->pdo->prepare('SELECT 1 FROM chitietphieuyeucauxuatnguyenlieu WHERE MaCTNL = :id LIMIT 1');
                    $chk->execute([':id' => $ma_ct]);
                    if ($chk->fetchColumn()) {
                        $ctSeq++;
                        continue;
                    }
                    break;
                } while (true);

                // Insert detail line
                $insertCt->execute([
                    ':id' => $ma_ct,
                    ':pyc' => $ma_phieu,
                    ':nl' => $ma_nl,
                    ':sl' => $sl,
                ]);

                // increment for next line
                $ctSeq++;
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

    /**
     * Lấy thông tin chi tiết một phiếu yêu cầu (header + danh sách nguyên liệu)
     * Trả về null nếu không tìm thấy
     */
    public function getRequestDetails(string $maPhieu): ?array {
        // Header
    // Note: table phieuyeucauxuatnguyenlieu in the schema does not include a GhiChu column
    $stmt = $this->pdo->prepare('SELECT p.MaPhieuYC AS ma_phieu, p.MaKeHoach AS ma_kehoach, p.MaPhanXuong AS ma_phanxuong, p.NgayLap AS ngay_lap, p.MaNV AS ma_nv, p.TrangThai AS trang_thai FROM phieuyeucauxuatnguyenlieu p WHERE p.MaPhieuYC = :id LIMIT 1');
        $stmt->execute([':id' => $maPhieu]);
        $header = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$header) return null;

        // Lines
        $sql = "SELECT c.MaCTNL AS ma_ct, c.MaNguyenLieu AS ma_nguyenlieu, nl.TenNguyenLieu AS ten, c.SoLuong AS so_luong FROM chitietphieuyeucauxuatnguyenlieu c LEFT JOIN nguyenlieu nl ON nl.MaNguyenLieu = c.MaNguyenLieu WHERE c.MaPhieuYC = :id";
        $stmt2 = $this->pdo->prepare($sql);
        $stmt2->execute([':id' => $maPhieu]);
        $lines = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        return [
            'header' => $header,
            'lines' => $lines,
        ];
    }
}
?>