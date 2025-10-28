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

    public function ensureTables(): void {
        // create sample tables as per requirement
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS kehoach_sanxuat (
          ma_kehoach VARCHAR(20) PRIMARY KEY,
          ma_px VARCHAR(20) NOT NULL,
          sanpham VARCHAR(100) NOT NULL,
          soluong INT NOT NULL,
          ngay_batdau DATE NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS dinhmuc_nguyenlieu (
          id INT AUTO_INCREMENT PRIMARY KEY,
          ma_nguyenlieu VARCHAR(20) NOT NULL,
          ten VARCHAR(100) NOT NULL,
          so_luong_dinhmuc DECIMAL(12,2) NOT NULL,
          ma_kehoach VARCHAR(20) NOT NULL,
          INDEX idx_kh (ma_kehoach)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS phieu_yeu_cau_xuat (
          ma_phieu VARCHAR(30) PRIMARY KEY,
          ma_kehoach VARCHAR(20) NOT NULL,
          ngay_yeucau DATE NOT NULL,
          trangthai VARCHAR(30) NOT NULL DEFAULT 'Nháp',
          ghichu TEXT,
          ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS phieu_yeu_cau_xuat_ct (
          id INT AUTO_INCREMENT PRIMARY KEY,
          ma_phieu VARCHAR(30) NOT NULL,
          ma_nguyenlieu VARCHAR(20) NOT NULL,
          ten_nguyenlieu VARCHAR(100) NOT NULL,
          so_luong DECIMAL(12,2) NOT NULL,
          so_luong_toi_da DECIMAL(12,2) NOT NULL,
          CONSTRAINT fk_ct_phieu FOREIGN KEY (ma_phieu) REFERENCES phieu_yeu_cau_xuat(ma_phieu)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // seed demo data if empty
        $countKH = (int)$this->pdo->query('SELECT COUNT(*) FROM kehoach_sanxuat')->fetchColumn();
        if ($countKH === 0) {
            $this->pdo->exec("INSERT INTO kehoach_sanxuat (ma_kehoach, ma_px, sanpham, soluong, ngay_batdau) VALUES
                ('KH001','PX01','Giày Sneaker A',1000, CURDATE()),
                ('KH002','PX02','Giày Sandal B', 600,  DATE_SUB(CURDATE(), INTERVAL 2 DAY)),
                ('KH003','PX03','Giày Boot C',   350,  DATE_ADD(CURDATE(), INTERVAL 5 DAY))");
        }

        $countDM = (int)$this->pdo->query('SELECT COUNT(*) FROM dinhmuc_nguyenlieu')->fetchColumn();
        if ($countDM === 0) {
            $this->pdo->exec("INSERT INTO dinhmuc_nguyenlieu (ma_nguyenlieu, ten, so_luong_dinhmuc, ma_kehoach) VALUES
                ('NL001','Da PU',       1.20,'KH001'),
                ('NL002','Đế cao su',   1.00,'KH001'),
                ('NL003','Keo dán',     0.05,'KH001'),
                ('NL004','Chỉ may',     0.02,'KH001'),
                ('NL001','Da PU',       0.90,'KH002'),
                ('NL002','Đế cao su',   1.10,'KH002'),
                ('NL005','Khóa dán',    0.08,'KH002'),
                ('NL006','Da bò',       1.50,'KH003'),
                ('NL007','Lót nỉ',      0.30,'KH003'),
                ('NL002','Đế cao su',   1.20,'KH003')");
        }
    }

    public function getPlans(): array {
        $sql = 'SELECT ma_kehoach, ma_px, sanpham, soluong, ngay_batdau FROM kehoach_sanxuat ORDER BY ngay_batdau DESC';
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPlan(string $maKeHoach): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM kehoach_sanxuat WHERE ma_kehoach = :id');
        $stmt->execute([':id' => $maKeHoach]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getMaterialsForPlan(string $maKeHoach, int $sanLuong): array {
        $stmt = $this->pdo->prepare('SELECT ma_nguyenlieu, ten, so_luong_dinhmuc FROM dinhmuc_nguyenlieu WHERE ma_kehoach = :kh');
        $stmt->execute([':kh' => $maKeHoach]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r) {
            $base = (float)$r['so_luong_dinhmuc'] * max($sanLuong, 0);
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
        $this->pdo->beginTransaction();
        try {
            $prefix = 'PYC' . date('Ymd');
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM phieu_yeu_cau_xuat WHERE ma_phieu LIKE :pfx');
            $like = $prefix . '%';
            $stmt->execute([':pfx' => $like]);
            $seq = (int)$stmt->fetchColumn() + 1;
            $ma_phieu = $prefix . '-' . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);

            $stmt = $this->pdo->prepare('INSERT INTO phieu_yeu_cau_xuat (ma_phieu, ma_kehoach, ngay_yeucau, trangthai, ghichu) VALUES (:m,:kh,:d,:st,:g)');
            $stmt->execute([
                ':m' => $ma_phieu,
                ':kh' => $ma_kehoach,
                ':d' => $ngay_yeucau,
                ':st' => $status,
                ':g' => $ghichu,
            ]);

            $insertCt = $this->pdo->prepare('INSERT INTO phieu_yeu_cau_xuat_ct (ma_phieu, ma_nguyenlieu, ten_nguyenlieu, so_luong, so_luong_toi_da) VALUES (:m,:nl,:ten,:sl,:max)');
            foreach ($materials as $m) {
                $ma_nl = trim($m['ma_nguyenlieu'] ?? '');
                $ten   = trim($m['ten'] ?? '');
                $sl    = (float)($m['so_luong'] ?? 0);
                $slmax = (float)($m['so_luong_max'] ?? 0);
                if ($ma_nl === '' || $ten === '' || $sl <= 0 || $slmax <= 0) continue;
                if ($sl > $slmax) $sl = $slmax;
                $insertCt->execute([
                    ':m' => $ma_phieu,
                    ':nl' => $ma_nl,
                    ':ten' => $ten,
                    ':sl' => $sl,
                    ':max' => $slmax,
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
        $where = [];
        $params = [];
        if ($status !== '') { $where[] = 'trangthai = :st'; $params[':st'] = $status; }
        if ($keyword !== '') { $where[] = '(ma_phieu LIKE :kw OR ma_kehoach LIKE :kw OR ghichu LIKE :kw)'; $params[':kw'] = "%$keyword%"; }
        $sql = 'SELECT ma_phieu, ma_kehoach, ngay_yeucau, trangthai, ghichu, ngay_tao FROM phieu_yeu_cau_xuat';
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY ngay_tao DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>