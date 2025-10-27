<?php
/**
 * Controller: Yêu cầu xuất nguyên liệu (Xưởng trưởng)
 */
require_once APP_PATH . '/controllers/BaseController.php';

class YeuCauXuatController extends BaseController {
    public function index() {
        $this->requireRole(['XT']);
        $model = $this->loadModel('YeuCauXuat');
        $model->ensureTables();

        $plans = $model->getPlans();
        $selectedMaKH = $_GET['ma_kehoach'] ?? ($plans[0]['ma_kehoach'] ?? '');
        $selectedPlan = $selectedMaKH ? $model->getPlan($selectedMaKH) : null;
        $materials = $selectedPlan ? $model->getMaterialsForPlan($selectedPlan['ma_kehoach'], (int)$selectedPlan['soluong']) : [];

        // Filters for embedded list
        $status = $_GET['status'] ?? '';
        $keyword = trim($_GET['q'] ?? '');
        $requests = $model->listRequests($status, $keyword);

        $minDate = (new DateTime('today', new DateTimeZone('Asia/Ho_Chi_Minh')))->format('Y-m-d');

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
            $this->redirect('yeucauxuat?ma_kehoach=' . urlencode($ma_kehoach));
        }

        try {
            $today = new DateTime('today', new DateTimeZone('Asia/Ho_Chi_Minh'));
            $dReq  = DateTime::createFromFormat('Y-m-d', $ngay_yeucau, new DateTimeZone('Asia/Ho_Chi_Minh'));
            if (!$dReq) throw new Exception('Định dạng ngày không hợp lệ');
            $dReq->setTime(0,0,0);
            if ($dReq < $today) {
                $_SESSION['error'] = 'Ngày yêu cầu không được là ngày trong quá khứ';
                $this->redirect('yeucauxuat?ma_kehoach=' . urlencode($ma_kehoach));
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Ngày yêu cầu không hợp lệ: ' . $e->getMessage();
            $this->redirect('yeucauxuat?ma_kehoach=' . urlencode($ma_kehoach));
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
            $_SESSION['error'] = 'Không thể lưu phiếu, vui lòng thử lại';
            $this->redirect('yeucauxuat?ma_kehoach=' . urlencode($ma_kehoach));
        }
    }

    public function list() {
        $this->requireRole(['XT']);
        $model = $this->loadModel('YeuCauXuat');
        $model->ensureTables();
        $status = $_GET['status'] ?? '';
        $keyword = trim($_GET['q'] ?? '');
        $rows = $model->listRequests($status, $keyword);
        $this->loadView('yeucauxuat/list', [
            'pageTitle' => 'Danh sách phiếu yêu cầu xuất',
            'rows' => $rows,
            'status' => $status,
            'keyword' => $keyword,
        ]);
    }

    /**
     * Tạo một phiếu mẫu tự động (không cần thao tác tay)
     * GET /yeucauxuat/demo?kh=KH001&status=send|draft
     */
    public function demo() {
        $this->requireRole(['XT']);
        $model = $this->loadModel('YeuCauXuat');
        $model->ensureTables();

        // Chọn kế hoạch: ưu tiên tham số ?kh=, nếu không có lấy kế hoạch mới nhất
        $plans = $model->getPlans();
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
        $ngay = (new DateTime('today', new DateTimeZone('Asia/Ho_Chi_Minh')))->format('Y-m-d');
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
}
?>
