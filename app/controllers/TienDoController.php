<?php
class TienDoController extends BaseController {
    public function index() {
        $this->requireAuth();
        // Allow xưởng trưởng and related roles
        $this->requireRole(['xuong_truong','ban_giam_doc','nhan_vien_ke_hoach']);

        $model = $this->loadModel('TienDoModel');

        // Determine the current user's phân xưởng (xưởng trưởng -> MaPhanXuong)
        $ycModel = $this->loadModel('YeuCauXuat');
        $maNV = $_SESSION['user_id'] ?? ($_SESSION['username'] ?? null);
        $maPX = $maNV ? $ycModel->getPhanXuongForUser($maNV) : null;

        if (!$maPX) {
            // If we cannot determine the phân xưởng for this user, show an instructive error
            $this->loadView('xuongtruong/tien_do/index', ['lines' => [], 'error' => 'Không xác định phân xưởng cho tài khoản hiện tại. Vui lòng liên hệ quản trị.']);
            return;
        }

        // Only fetch dây chuyền that belong to this phân xưởng
        $lines = $model->getLinesByPhanXuong($maPX);

        // Debug logging: record MaPhanXuong and fetched rows count (helpful when UI shows empty select)
        try {
            $dbg = 'TienDoController::index - MaPhanXuong=' . ($maPX ?? 'NULL') . ' - fetched=' . count($lines);
            error_log($dbg . "\n", 3, APP_PATH . '/logs/error.log');
            error_log('TienDoController::index - rows: ' . json_encode($lines, JSON_UNESCAPED_UNICODE) . "\n", 3, APP_PATH . '/logs/error.log');
        } catch (Exception $e) {
            // ignore logging errors
        }


        // Reorder / prioritize the six standard dây chuyền the factory uses
        // Desired order: Cắt, May, Gấp đế, Hoàn thiện, Kiểm tra, Đóng gói
        $preferred = [
            ['label' => 'Cắt', 'keyword' => 'cắt'],
            ['label' => 'May', 'keyword' => 'may'],
            ['label' => 'Gấp đế', 'keyword' => 'đế'],
            ['label' => 'Hoàn thiện', 'keyword' => 'hoàn'],
            ['label' => 'Kiểm tra', 'keyword' => 'kiểm'],
            ['label' => 'Đóng gói', 'keyword' => 'đóng']
        ];

        $all = $lines; // original fetched lines
        $usedIndexes = [];
        $final = [];

        // For each preferred label, try to find a matching line from DB; if none, insert placeholder
        foreach ($preferred as $p) {
            $kw = mb_strtolower($p['keyword'], 'UTF-8');
            $found = null;
            foreach ($all as $i => $row) {
                if (in_array($i, $usedIndexes)) continue;
                $nameLower = mb_strtolower($row['TenDayChuyen'] ?? '', 'UTF-8');
                if ($kw !== '' && mb_stripos($nameLower, $kw, 0, 'UTF-8') !== false) {
                    $found = $row;
                    $usedIndexes[] = $i;
                    break;
                }
            }
            if ($found) {
                $found['is_placeholder'] = false;
                $final[] = $found;
            } else {
                // placeholder entry
                $final[] = [
                    'MaDayChuyen' => '',
                    'TenDayChuyen' => 'Dây chuyền ' . $p['label'],
                    'MaPhanXuong' => '',
                    'is_placeholder' => true
                ];
            }
        }

        // Ensure exactly 6 entries (we already have 6 preferred entries)
        $this->loadView('xuongtruong/tien_do/index', ['lines' => $final]);
    }

    /**
     * API: trả về danh sách dây chuyền (JSON) cho phân xưởng của người dùng đang đăng nhập
     * GET /xuongtruong/tien-do/api-lines
     */
    public function apiLines() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $this->requireAuth();
            $this->requireRole(['xuong_truong','ban_giam_doc','nhan_vien_ke_hoach']);

            $ycModel = $this->loadModel('YeuCauXuat');
            $maNV = $_SESSION['user_id'] ?? ($_SESSION['username'] ?? null);
            $maPX = $maNV ? $ycModel->getPhanXuongForUser($maNV) : null;

            if (!$maPX) {
                echo json_encode(['error' => 'Không xác định phân xưởng cho tài khoản hiện tại.']);
                return;
            }

            $model = $this->loadModel('TienDoModel');
            $lines = $model->getLinesByPhanXuong($maPX);

            echo json_encode(['MaPhanXuong' => $maPX, 'lines' => $lines], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Lỗi nội bộ', 'message' => $e->getMessage()]);
        }
    }

    public function show($ma = null) {
        $this->requireAuth();
        $this->requireRole(['xuong_truong','ban_giam_doc','nhan_vien_ke_hoach']);

        // Accept both path param or ?ma=
        $maDayChuyen = $ma ?: ($_GET['ma'] ?? null);
        if (!$maDayChuyen) {
            $this->loadView('errors/400', ['message' => 'Thiếu mã dây chuyền']);
            return;
        }

        // Determine the current user's phân xưởng early so any reloads show only that phân xưởng's lines
        $ycModel = $this->loadModel('YeuCauXuat');
        $maNV = $_SESSION['user_id'] ?? ($_SESSION['username'] ?? null);
        $maPXUser = $maNV ? $ycModel->getPhanXuongForUser($maNV) : null;
        if (!$maPXUser) {
            // If we cannot determine the phân xưởng for this user, show an instructive error
            $this->loadView('xuongtruong/tien_do/index', ['lines' => [], 'error' => 'Không xác định phân xưởng cho tài khoản hiện tại. Vui lòng liên hệ quản trị.']);
            return;
        }

        // Require explicit start_date and end_date (user requested only date-range mode)
        $startDate = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? trim($_GET['start_date']) : null;
        $endDate = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? trim($_GET['end_date']) : null;

        if (!$startDate || !$endDate) {
            // Load index with an error message to prompt the user to select both dates
            $model = $this->loadModel('TienDoModel');
            $lines = $model->getLinesByPhanXuong($maPXUser);
            $this->loadView('xuongtruong/tien_do/index', ['lines' => $lines, 'error' => 'Vui lòng chọn cả ngày bắt đầu và ngày kết thúc để xem thống kê.']);
            return;
        }

        // Validate provided dates (expected YYYY-MM-DD)
        try {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) throw new Exception('Ngày bắt đầu không hợp lệ');
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) throw new Exception('Ngày kết thúc không hợp lệ');
            $dStart = new DateTime($startDate);
            $dEnd = new DateTime($endDate);
            if ($dStart > $dEnd) throw new Exception('Ngày bắt đầu phải nhỏ hơn hoặc bằng ngày kết thúc');
            $intervalDays = (int)$dStart->diff($dEnd)->format('%a') + 1;
            if ($intervalDays > 365) {
                // cap to reasonable maximum
                throw new Exception('Khoảng ngày quá lớn (tối đa 365 ngày)');
            }
        } catch (Exception $e) {
            $model = $this->loadModel('TienDoModel');
            $lines = $model->getAllLines();
            $this->loadView('xuongtruong/tien_do/index', ['lines' => $lines, 'error' => $e->getMessage()]);
            return;
        }

        try {
            $model = $this->loadModel('TienDoModel');
            $linesModel = $this->loadModel('DayChuyen');
            // Use model's getById to retrieve full daychuyen row including MaPhanXuong
            $lineInfo = $linesModel->getById($maDayChuyen);
            // If for any reason MaPhanXuong is not present (some queries return custom columns),
            // fetch it explicitly from TienDoModel to avoid false authorization failures.
            if ($lineInfo && !isset($lineInfo['MaPhanXuong'])) {
                try {
                    $px = $model->getPhanXuongByDayChuyen($maDayChuyen);
                    if ($px) $lineInfo['MaPhanXuong'] = $px;
                } catch (Exception $e) {
                    // ignore
                }
            }

            if (!$lineInfo) {
                $this->loadView('errors/404', ['message' => 'Dây chuyền không tồn tại']);
                return;
            }

            // Ensure the requested dây chuyền belongs to the current user's phân xưởng
            // Debug: log user and line phân xưởng to help diagnose authorization issues
            try {
                error_log("TienDoController::show - maNV={$maNV} maPXUser={$maPXUser} line.MaPhanXuong=" . ($lineInfo['MaPhanXuong'] ?? 'NULL') . " maDayChuyen={$maDayChuyen}\n", 3, APP_PATH . '/logs/error.log');
                error_log('TienDoController::show - lineInfo: ' . json_encode($lineInfo, JSON_UNESCAPED_UNICODE) . "\n", 3, APP_PATH . '/logs/error.log');
            } catch (Exception $e) {}

            // Normalize both sides (trim + lowercase) to avoid format/case/whitespace mismatches
            $norm = function($s) { return mb_strtolower(trim((string)$s), 'UTF-8'); };

            if (!isset($lineInfo['MaPhanXuong']) || $norm($lineInfo['MaPhanXuong']) !== $norm($maPXUser)) {
                $msg = 'Bạn không có quyền xem dây chuyền này.';
                // Append debug-friendly info to help admin (non-sensitive internal use)
                $msg .= ' (Phân xưởng tài khoản: ' . ($maPXUser ?? 'NULL') . '; Phân xưởng dây chuyền: ' . ($lineInfo['MaPhanXuong'] ?? 'NULL') . ')';
                $this->loadView('errors/403', ['message' => $msg]);
                return;
            }

            // We have validated startDate and endDate above — use them
            $stats = $model->getStatistics($maDayChuyen, 30, $startDate, $endDate);
            if ($stats === null) {
                error_log("TienDoController::show - no stats for $maDayChuyen\n", 3, APP_PATH . '/logs/error.log');
                $this->loadView('xuongtruong/tien_do/detail', ['line' => $lineInfo, 'stats' => null]);
                return;
            }

            $this->loadView('xuongtruong/tien_do/detail', ['line' => $lineInfo, 'stats' => $stats]);
        } catch (Exception $e) {
            error_log("TienDoController::show error: " . $e->getMessage() . "\n", 3, APP_PATH . '/logs/error.log');
            $this->loadView('errors/500', ['message' => 'Lỗi khi lấy dữ liệu thống kê']);
        }
    }
}

?>
