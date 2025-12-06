<?php
require_once APP_PATH . '/models/BaseModel.php';

class TienDoModel extends BaseModel {
    protected $tableName = 'daychuyen';
    protected $primaryKey = 'MaDayChuyen';

    /**
     * Lấy danh sách tất cả dây chuyền
     * (gồm trạng thái để hiển thị màu sắc, số lượng CN nếu cần).
     */
    public function getAllLines() {
        $sql = "SELECT MaDayChuyen, TenDayChuyen, MaPhanXuong, TrangThai, SoLuongCongNhan FROM daychuyen ORDER BY TenDayChuyen";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách dây chuyền cho một phân xưởng
     * kèm trạng thái (TrangThai) để render badge màu trong UI.
     */
    public function getLinesByPhanXuong($maPhanXuong) {
        // Use TRIM in SQL to avoid mismatches due to stray spaces in DB or input
        $sql = "SELECT MaDayChuyen, TenDayChuyen, MaPhanXuong, TrangThai, SoLuongCongNhan
                FROM daychuyen
                WHERE TRIM(MaPhanXuong) = TRIM(:mapx)
                ORDER BY TenDayChuyen";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':mapx' => trim((string)$maPhanXuong)]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy mã phân xưởng của dây chuyền
     */
    public function getPhanXuongByDayChuyen($maDayChuyen) {
        $sql = "SELECT MaPhanXuong FROM daychuyen WHERE MaDayChuyen = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$maDayChuyen]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['MaPhanXuong'] ?? null;
    }

    /**
     * Tổng kế hoạch (KeHoachCapXuong.SoLuong) cho một phân xưởng trong khoảng thời gian
     */
    public function getPlannedTotalByPhanXuong($maPhanXuong, $startDate = null, $endDate = null) {
        $sql = "SELECT COALESCE(SUM(SoLuong),0) AS planned FROM kehoachcapxuong WHERE MaPhanXuong = :mapx";
        $params = [':mapx' => $maPhanXuong];
        if ($startDate && $endDate) {
            $sql .= " AND DATE(NgayLap) BETWEEN :start AND :end";
            $params[':start'] = $startDate;
            $params[':end'] = $endDate;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($row['planned'] ?? 0);
    }

    /**
     * Lấy tổng sản lượng đã hoàn thành (theo kết quả kiểm định = 'Đạt') cho phân xưởng, theo ngày
     * Trả về mảng [ 'labels' => [...dates...], 'data' => [...qty...] ] trong khoảng ngày.
     * Cách ánh xạ: dùng lohang.MaSanPham -> chitietkehoach.MaSanPham có MaPhanXuong
     */
    public function getFinishedSeriesByPhanXuong($maPhanXuong, $startDate, $endDate) {
        $sql = "SELECT DATE(k.NgayLap) AS day, COALESCE(SUM(l.SoLuong),0) AS qty
                FROM ketquakiemdinh k
                JOIN lohang l ON k.MaLoHang = l.MaLoHang
                JOIN chitietkehoach c ON l.MaSanPham = c.MaSanPham
                WHERE c.MaPhanXuong = :mapx AND k.KetQua = 'Đạt' AND DATE(k.NgayLap) BETWEEN :start AND :end
                GROUP BY DATE(k.NgayLap)
                ORDER BY DATE(k.NgayLap) ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':mapx' => $maPhanXuong, ':start' => $startDate, ':end' => $endDate]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Build full series (fill zero for missing dates)
        $period = new DatePeriod(new DateTime($startDate), new DateInterval('P1D'), (new DateTime($endDate))->modify('+1 day'));
        $map = [];
        foreach ($rows as $r) {
            $map[$r['day']] = intval($r['qty']);
        }

        $labels = [];
        $data = [];
        foreach ($period as $dt) {
            $d = $dt->format('Y-m-d');
            $labels[] = $d;
            $data[] = $map[$d] ?? 0;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Trả về thống kê tổng hợp cho một dây chuyền: planned total, finished total, series
     */
    /**
     * Lấy thống kê cho một dây chuyền.
     * Nếu $startDate và $endDate được cung cấp thì ưu tiên dùng khoảng đó,
     * ngược lại dùng $days để lấy khoảng thời gian từ hôm nay lùi về.
     *
     * @param string $maDayChuyen
     * @param int $days
     * @param string|null $startDate YYYY-MM-DD
     * @param string|null $endDate YYYY-MM-DD
     */
    public function getStatistics($maDayChuyen, $days = 30, $startDate = null, $endDate = null) {
        try {
            $maPhanXuong = $this->getPhanXuongByDayChuyen($maDayChuyen);
            if (!$maPhanXuong) {
                return null;
            }

            // Nếu không truyền start/end thì tính từ ngày hiện tại lùi $days-1 ngày
            if (!$startDate || !$endDate) {
                $end = new DateTime();
                $start = (new DateTime())->modify(sprintf('-%d days', max(1,intval($days)-1)));
                $startDate = $start->format('Y-m-d');
                $endDate = $end->format('Y-m-d');
            }

            $planned = $this->getPlannedTotalByPhanXuong($maPhanXuong, $startDate, $endDate);
            $series = $this->getFinishedSeriesByPhanXuong($maPhanXuong, $startDate, $endDate);
            $finishedTotal = array_sum($series['data']);

            return [
                'MaDayChuyen' => $maDayChuyen,
                'MaPhanXuong' => $maPhanXuong,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'planned' => $planned,
                'finished' => $finishedTotal,
                'labels' => $series['labels'],
                'data' => $series['data']
            ];
        } catch (Exception $e) {
            error_log("TienDoModel::getStatistics error: " . $e->getMessage() . "\n", 3, APP_PATH . '/logs/error.log');
            return null;
        }
    }
}

?>
