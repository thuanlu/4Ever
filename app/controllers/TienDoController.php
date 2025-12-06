<?php
class TienDoController extends BaseController {
    public function index() {
        $this->requireAuth();
        // Allow xưởng trưởng and related roles
        $this->requireRole(['xuong_truong','ban_giam_doc','nhan_vien_ke_hoach']);

        $model = $this->loadModel('TienDoModel');

        // Determine the current user's phân xưởng (xưởng trưởng -> MaPhanXuong)
        $ycModel = $this->loadModel('YeuCauXuat');
        $maNV = $_SESSION['user']['MaNV'] ?? null;
        $maPX = $maNV ? $ycModel->getPhanXuongForUser($maNV) : null;

        if (!$maPX) {
            // If we cannot determine the phân xưởng for this user, show an instructive error
            $this->loadView('xuongtruong/tien_do/index', ['lines' => [], 'error' => 'Không xác định phân xưởng cho tài khoản hiện tại. Vui lòng liên hệ quản trị.']);
            return;
        }

        // Only fetch dây chuyền that belong to this phân xưởng
        $lines = $model->getLinesByPhanXuong($maPX);

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
                    // placeholder entry: do NOT invent a TenDayChuyen from the label.
                    // Leave TenDayChuyen empty so the UI shows the database name when available,
                    // otherwise render a clear "Chưa cấu hình" state.
                    $final[] = [
                        'MaDayChuyen'   => '',
                        'TenDayChuyen'  => '',
                        'MaPhanXuong'   => '',
                        'TrangThai'     => 'Chưa cấu hình',
                        'SoLuongCongNhan' => null,
                        'is_placeholder' => true,
                        'preferredLabel' => $p['label'],
                    ];
                }
        }

        // If some preferred slots are placeholders, try to fill them with any remaining real lines
        $unusedRows = [];
        foreach ($all as $i => $row) {
            if (!in_array($i, $usedIndexes)) $unusedRows[] = $row;
        }
        if (!empty($unusedRows)) {
            foreach ($final as $k => $entry) {
                if (count($unusedRows) === 0) break;
                if (!empty($entry['is_placeholder'])) {
                    $r = array_shift($unusedRows);
                    $r['is_placeholder'] = false;
                    $final[$k] = $r;
                }
            }
        }

        // Ensure exactly 6 entries (we already have 6 preferred entries)
            // Ensure view receives the full DB list (show all lines for the phân xưởng)
            $viewData = ['lines' => $all, 'resolvedMaNV' => $maNV, 'resolvedMaPX' => $maPX];
        // If debug flag present, also include raw fetched DB rows for inspection
        if (isset($_GET['_dbg']) && $_GET['_dbg'] == '1') {
            $viewData['rawLines'] = $lines;
        }
        $this->loadView('xuongtruong/tien_do/index', $viewData);
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
            $maNV = $_SESSION['user']['MaNV'] ?? null;
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

    /**
     * API: trả về 6 dây chuyền canonical (Cắt, May, Gấp đế, Hoàn thiện, Kiểm tra, Đóng gói)
     * cho phân xưởng của người đang đăng nhập. Trả về placeholder entries nếu thiếu.
     * GET /xuongtruong/tien-do/api-canonical-lines
     */
    public function apiCanonicalLines() {
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
            $all = $model->getLinesByPhanXuong($maPX);

            // canonical ordering
            $preferred = [
                ['label' => 'Cắt', 'keyword' => 'cắt'],
                ['label' => 'May', 'keyword' => 'may'],
                ['label' => 'Gấp đế', 'keyword' => 'đế'],
                ['label' => 'Hoàn thiện', 'keyword' => 'hoàn'],
                ['label' => 'Kiểm tra', 'keyword' => 'kiểm'],
                ['label' => 'Đóng gói', 'keyword' => 'đóng']
            ];

            $usedIndexes = [];
            $final = [];
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
                    // placeholder: do not invent a DB name here
                    $final[] = [
                        'MaDayChuyen' => '',
                        'TenDayChuyen' => '',
                        'MaPhanXuong' => $maPX,
                        'TrangThai' => 'Chưa cấu hình',
                        'SoLuongCongNhan' => null,
                        'is_placeholder' => true,
                        'preferredLabel' => $p['label'],
                    ];
                }
            }

            echo json_encode(['MaPhanXuong' => $maPX, 'lines' => $final], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Lỗi nội bộ', 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: trả về tên các dây chuyền cho MaNV của xưởng trưởng (hoặc MaNV được truyền vào)
     * GET /xuongtruong/tien-do/api-lines-by-user?manv=XT01
     */
    public function apiLinesByUser() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $this->requireAuth();
            $this->requireRole(['xuong_truong','ban_giam_doc','nhan_vien_ke_hoach']);

            // allow passing manv as query param; if omitted, use current session
            $maNV = isset($_GET['manv']) && trim($_GET['manv']) !== '' ? trim($_GET['manv']) : ($_SESSION['user_id'] ?? ($_SESSION['username'] ?? null));
            if (!$maNV) {
                http_response_code(400);
                echo json_encode(['error' => 'Thiếu tham số manv hoặc chưa đăng nhập']);
                return;
            }

            $ycModel = $this->loadModel('YeuCauXuat');
            $maPX = $ycModel->getPhanXuongForUser($maNV);
            if (!$maPX) {
                echo json_encode(['error' => 'Không xác định phân xưởng cho MaNV được cung cấp', 'MaNV' => $maNV], JSON_UNESCAPED_UNICODE);
                return;
            }

            $model = $this->loadModel('TienDoModel');
            $lines = $model->getLinesByPhanXuong($maPX);

            // extract names
            $names = [];
            foreach ($lines as $r) {
                if (!empty($r['TenDayChuyen'])) $names[] = $r['TenDayChuyen'];
            }

            echo json_encode(['MaNV' => $maNV, 'MaPhanXuong' => $maPX, 'names' => $names, 'lines' => $lines], JSON_UNESCAPED_UNICODE);
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
        $maNV = $_SESSION['user']['MaNV'] ?? null;
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
                $this->loadView('xuongtruong/tien_do/detail', ['line' => $lineInfo, 'stats' => null]);
                return;
            }

            $this->loadView('xuongtruong/tien_do/detail', ['line' => $lineInfo, 'stats' => $stats]);
        } catch (Exception $e) {
            $this->loadView('errors/500', ['message' => 'Lỗi khi lấy dữ liệu thống kê']);
        }
    }
}

?>
