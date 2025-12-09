<?php
/**
 * Controller: Yêu cầu xuất nguyên liệu (Xưởng trưởng)
 */
require_once APP_PATH . '/controllers/BaseController.php';

class YeuCauXuatController extends BaseController {
    public function index() {
        $this->requireRole(['XT']);
        $model = $this->loadModel('YeuCauXuat');
        // Xác định phân xưởng của xưởng trưởng đang đăng nhập (nếu có) để lọc kế hoạch
        $maPX = '';
        $maNV = $_SESSION['user_id'] ?? ($_SESSION['username'] ?? null);
        if ($maNV) {
            $maPX = $model->getPhanXuongForUser($maNV) ?? '';
        }

        $plans = $model->getPlans($maPX);
        $selectedMaKH = $_GET['ma_kehoach'] ?? ''; // Không tự động chọn kế hoạch đầu tiên
        // Ensure the selected plan is filtered by the user's MaPhanXuong for security
        $selectedPlan = $selectedMaKH ? $model->getPlan($selectedMaKH, $maPX) : null;
        $materials = $selectedPlan ? $model->getMaterialsForPlan($selectedPlan['ma_kehoach'], (int)$selectedPlan['soluong']) : [];

        // Preserve form values after redirect on validation errors
        $oldDate = $_GET['ngay_yeucau'] ?? '';
        $oldGhichu = $_GET['ghichu'] ?? '';

        // Filters for embedded list
        $status = $_GET['status'] ?? '';
        $keyword = trim($_GET['q'] ?? '');
        $requests = $model->listRequests($status, $keyword, $maPX);

    // Use system default timezone / server date for default date (format YYYY-MM-DD for HTML date input)
    $minDate = (new DateTime('today'))->format('Y-m-d');

        $this->loadView('xuongtruong/yeucauxuat_create', [
            'pageTitle' => 'Tạo phiếu yêu cầu xuất nguyên liệu',
            'plans' => $plans,
            'selectedPlan' => $selectedPlan,
            'materials' => $materials,
            'selectedMaKH' => $selectedMaKH,
            'requests' => $requests,
            'filterStatus' => $status,
            'filterKeyword' => $keyword,
            'minDate' => $minDate,
            'oldDate' => $oldDate,
            'oldGhichu' => $oldGhichu,
        ]);
    }

    public function save() {
        $this->requireRole(['XT']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('yeucauxuat');
        }

        // CSRF đơn giản
        if (!isset($_POST['_csrf']) || $_POST['_csrf'] !== md5(session_id())) {
            $_SESSION['error'] = 'Phiên làm việc không hợp lệ. Vui lòng thử lại.';
            $this->redirect('yeucauxuat');
        }

        $ma_kehoach = trim($_POST['ma_kehoach'] ?? '');
        $ngay_yeucau = trim($_POST['ngay_yeucau'] ?? '');
        $ghichu      = trim($_POST['ghichu'] ?? '');
        $action      = trim($_POST['action'] ?? 'send');
        $materials   = $_POST['materials'] ?? [];

        if ($ma_kehoach === '' || $ngay_yeucau === '' || empty($materials)) {
            $_SESSION['error'] = 'Thiếu dữ liệu yêu cầu. Vui lòng kiểm tra lại.';
            $qs = 'yeucauxuat?ma_kehoach=' . urlencode($ma_kehoach) . '&ngay_yeucau=' . urlencode($ngay_yeucau) . '&ghichu=' . urlencode($ghichu);
            $this->redirect($qs);
        }

        try {
            $today = new DateTime('today');
            $dReq  = DateTime::createFromFormat('Y-m-d', $ngay_yeucau, new DateTimeZone('Asia/Ho_Chi_Minh'));
            if (!$dReq) throw new Exception('Định dạng ngày không hợp lệ');
            $dReq->setTime(0,0,0);
            if ($dReq < $today) {
                $_SESSION['error'] = 'Ngày yêu cầu không được là ngày trong quá khứ';
                $this->redirect('yeucauxuat?ma_kehoach=' . urlencode($ma_kehoach));
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Ngày yêu cầu không hợp lệ: ' . $e->getMessage();
            $qs = 'yeucauxuat?ma_kehoach=' . urlencode($ma_kehoach) . '&ngay_yeucau=' . urlencode($ngay_yeucau) . '&ghichu=' . urlencode($ghichu);
            $this->redirect($qs);
        }

        $status = ($action === 'draft') ? 'Nháp' : 'Chờ xử lý';

        try {
            $model = $this->loadModel('YeuCauXuat');
            $ma_phieu = $model->saveRequest($ma_kehoach, $ngay_yeucau, $ghichu, $materials, $status);
            if ($status === 'Nháp') {
                $_SESSION['success'] = 'Phiếu đã được lưu nháp thành công';
            } else {
                $_SESSION['success'] = 'Tạo phiếu yêu cầu thành công';
            }
            $this->redirect('yeucauxuat?ma_kehoach=' . urlencode($ma_kehoach));
        } catch (Throwable $e) {
            error_log('[YeuCauXuatController.save] ' . $e->getMessage());
            // Truyền thông báo lỗi chi tiết để dễ debug trong môi trường dev
            $_SESSION['error'] = 'Không thể lưu phiếu: ' . $e->getMessage();
            $qs = 'yeucauxuat?ma_kehoach=' . urlencode($ma_kehoach) . '&ngay_yeucau=' . urlencode($ngay_yeucau) . '&ghichu=' . urlencode($ghichu);
            $this->redirect($qs);
        }
    }

    public function list() {
        $this->requireRole(['XT']);
        $model = $this->loadModel('YeuCauXuat');
        // Xác định phân xưởng của xưởng trưởng đang đăng nhập để lọc phiếu
        $maPX = '';
        $maNV = $_SESSION['user_id'] ?? ($_SESSION['username'] ?? null);
        if ($maNV) {
            $maPX = $model->getPhanXuongForUser($maNV) ?? '';
        }
        // Không gọi ensureTables() ở đây để sử dụng dữ liệu thực từ database (không tự tạo/seed)
        $status = $_GET['status'] ?? '';
        $keyword = trim($_GET['q'] ?? '');
        $rows = $model->listRequests($status, $keyword, $maPX);
        // The view file has been moved under xuongtruong namespace
        $this->loadView('xuongtruong/yeucauxuat_list', [
            'pageTitle' => 'Danh sách phiếu yêu cầu xuất',
            'rows' => $rows,
            'status' => $status,
            'keyword' => $keyword,
        ]);
    }

    /**
     * API: trả về danh sách kế hoạch sản xuất dạng JSON
     * GET /yeucauxuat/api/plans
     */
    public function apiPlans() {
        $this->requireRole(['XT']);
        $model = $this->loadModel('YeuCauXuat');
        $maPX = '';
        $maNV = $_SESSION['user_id'] ?? ($_SESSION['username'] ?? null);
        if ($maNV) $maPX = $model->getPhanXuongForUser($maNV) ?? '';
        $plans = $model->getPlans($maPX);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'data' => $plans
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Tạo một phiếu mẫu tự động (không cần thao tác tay)
     * GET /yeucauxuat/demo?kh=KH001&status=send|draft
     */
    public function demo() {
        $this->requireRole(['XT']);
        $model = $this->loadModel('YeuCauXuat');
        // Không gọi ensureTables() nữa — model không tự tạo/seed bảng. Demo sẽ chỉ tạo phiếu mẫu nếu dữ liệu kế hoạch tồn tại trong DB.

        // Chọn kế hoạch: ưu tiên tham số ?kh=, nếu không có lấy kế hoạch mới nhất
        $maPX = '';
        $maNV = $_SESSION['user_id'] ?? ($_SESSION['username'] ?? null);
        if ($maNV) $maPX = $model->getPhanXuongForUser($maNV) ?? '';
        $plans = $model->getPlans($maPX);
        if (empty($plans)) {
            $_SESSION['error'] = 'Chưa có kế hoạch sản xuất để tạo phiếu mẫu.';
            $this->redirect('yeucauxuat');
        }

        $kh = $_GET['kh'] ?? $plans[0]['ma_kehoach'];
        $selected = null;
        foreach ($plans as $p) { if ($p['ma_kehoach'] === $kh) { $selected = $p; break; } }
        if (!$selected) { $selected = $plans[0]; }

        // Lấy materials theo định mức cho toàn bộ sản lượng kế hoạch
        $materials = $model->getMaterialsForPlan($selected['ma_kehoach'], (int)$selected['soluong']);
        // Chuẩn hóa mảng theo định dạng saveRequest
        $materialsPayload = [];
        foreach ($materials as $m) {
            $materialsPayload[] = [
                'ma_nguyenlieu' => $m['ma_nguyenlieu'],
                'ten' => $m['ten'],
                'so_luong' => $m['base'],
                'so_luong_max' => $m['max'],
            ];
        }

        $status = (($_GET['status'] ?? 'send') === 'draft') ? 'Nháp' : 'Chờ xử lý';
    $ngay = (new DateTime('today'))->format('Y-m-d');
        $ghichu = 'Phiếu mẫu tạo tự động từ demo';

        try {
            $maPhieu = $model->saveRequest($selected['ma_kehoach'], $ngay, $ghichu, $materialsPayload, $status);
            $_SESSION['success'] = 'Đã tạo phiếu mẫu: ' . $maPhieu . ' (' . $status . ')';
            $this->redirect('yeucauxuat/list');
        } catch (Throwable $e) {
            error_log('[YeuCauXuatController.demo] ' . $e->getMessage());
            $_SESSION['error'] = 'Không thể tạo phiếu mẫu: ' . $e->getMessage();
            $this->redirect('yeucauxuat');
        }
    }

    /**
     * Hiển thị chi tiết một phiếu yêu cầu (truy cập từ nút Xem trong danh sách)
     * URL: /yeucauxuat/view/{MaPhieu}
     */
    public function view($ma = null) {
        $this->requireRole(['XT']);
        $id = $ma ?? ($_GET['ma'] ?? null);
        if (!$id) {
            $_SESSION['error'] = 'Thiếu tham số mã phiếu';
            $this->redirect('yeucauxuat/list');
        }

        $model = $this->loadModel('YeuCauXuat');
        try {
            $data = $model->getRequestDetails($id);
            if (!$data) {
                $_SESSION['error'] = 'Không tìm thấy phiếu yêu cầu: ' . htmlspecialchars($id);
                $this->redirect('yeucauxuat/list');
            }

            $this->loadView('xuongtruong/yeucauxuat_view', [
                'pageTitle' => 'Chi tiết phiếu yêu cầu ' . $id,
                'request' => $data['header'],
                'lines' => $data['lines'],
            ]);
        } catch (Throwable $e) {
            error_log('[YeuCauXuatController.view] ' . $e->getMessage());
            $_SESSION['error'] = 'Lỗi khi lấy chi tiết phiếu: ' . $e->getMessage();
            $this->redirect('yeucauxuat/list');
        }
    }
}
?>