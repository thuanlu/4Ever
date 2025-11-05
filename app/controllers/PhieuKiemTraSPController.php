<?php
/**
 * PhieuKiemTraSPController
 * Controller cho chức năng tạo và quản lý phiếu kiểm tra lô/sản phẩm
 * Tuân thủ mô hình MVC thuần, PHP 8+
 */
require_once APP_PATH . '/controllers/BaseController.php';

class PhieuKiemTraSPController extends BaseController
{
    /** Hiển thị form tạo phiếu */
    public function create()
    {
        $this->requireRole(['XT']); // Xưởng trưởng
        $model = $this->loadModel('PhieuKiemTraSP');
        // Determine the current user's phân xưởng (if any) so we only show relevant plans/lots
        $maNV = $_SESSION['user_id'] ?? ($_SESSION['username'] ?? null);
        $maPX = '';
        if ($maNV) {
            $ycModel = $this->loadModel('YeuCauXuat');
            try {
                $maPX = $ycModel->getPhanXuongForUser($maNV) ?? '';
            } catch (Throwable $e) {
                // ignore and fallback to showing all
                $maPX = '';
            }
        }

        $plans = $model->getPlans($maPX);
        $lots = $model->getLots($maPX);

    // Also load recent tickets to show under the form
    $tickets = $model->listTickets();

        // Default date = system date in Y-m-d (suitable for input[type=date])
        $minDate = (new DateTime('today'))->format('Y-m-d');

        $this->loadView('xuongtruong/phieu_kiem_tra_sp/create', [
            'plans' => $plans,
            'lots' => $lots,
            'minDate' => $minDate,
            'tickets' => $tickets,
        ]);
    }

    /** Xử lý lưu phiếu mới (POST) */
    public function store()
    {
        $this->requireRole(['XT']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('phieu-kiem-tra/create');
        }

        $maKeHoach = trim($_POST['ma_kehoach'] ?? '');
        $maLoHang = trim($_POST['ma_lohang'] ?? '');
        $ngay = trim($_POST['ngay_kiemtra'] ?? '');
        $action = trim($_POST['action'] ?? 'send');

        // Validation: ngày không rỗng
        if ($ngay === '') {
            $_SESSION['error'] = 'Ngày kiểm tra không được để trống.';
            $this->redirect('phieu-kiem-tra/create');
        }

        // Ngày phải >= ngày hiện tại
        $today = new DateTime('today');
        $dReq = DateTime::createFromFormat('Y-m-d', $ngay);
        if (!$dReq) {
            $_SESSION['error'] = 'Định dạng ngày không hợp lệ.';
            $this->redirect('phieu-kiem-tra/create');
        }
        $dReq->setTime(0,0,0);
        if ($dReq < $today) {
            $_SESSION['error'] = 'Ngày yêu cầu không được là ngày trong quá khứ.';
            $this->redirect('phieu-kiem-tra/create');
        }

    // Determine status
        $status = ($action === 'draft') ? 'Nháp' : 'Chờ xử lý';

        // MaNV lấy từ session
        $maNV = $_SESSION['user_id'] ?? ($_SESSION['username'] ?? null);
        if (!$maNV) {
            $_SESSION['error'] = 'Không xác định người dùng. Vui lòng đăng nhập lại.';
            $this->redirect('phieu-kiem-tra/create');
        }

        // Server-side validation: MaLoHang phải được chọn và thuộc phân xưởng/kế hoạch hợp lệ
        if ($maLoHang === '') {
            $_SESSION['error'] = 'Bạn phải chọn một lô hàng để kiểm tra.';
            $this->redirect('phieu-kiem-tra/create');
        }

        try {
            $model = $this->loadModel('PhieuKiemTraSP');

            // Verify the selected lot is allowed for this user's phân xưởng (defensive check)
            $maPX = '';
            try {
                $ycModel = $this->loadModel('YeuCauXuat');
                $maPX = $ycModel->getPhanXuongForUser($maNV) ?? '';
            } catch (Throwable $e) {
                // ignore
            }

            $allowedLots = $model->getLots($maPX);
            $found = false;
            foreach ($allowedLots as $l) {
                if (($l['MaLoHang'] ?? $l['MaLoHang']) === $maLoHang) { $found = true; break; }
            }
            if (!$found) {
                $_SESSION['error'] = 'Lô hàng được chọn không hợp lệ hoặc không thuộc phân xưởng của bạn.';
                $this->redirect('phieu-kiem-tra/create');
            }

            $maPhieu = $model->createTicket($maLoHang, $ngay, $maNV, $status);

            // Ghi flash và redirect
            $_SESSION['success'] = 'Tạo phiếu thành công: ' . $maPhieu;
            $this->redirect('phieu-kiem-tra/index');
        } catch (Throwable $e) {
            error_log('[PhieuKiemTraSPController.store] ' . $e->getMessage());
            // For now show a helpful error to developer; in production you may keep a generic message
            $_SESSION['error'] = 'Không thể lưu phiếu, vui lòng thử lại. Lỗi: ' . $e->getMessage();
            $this->redirect('phieu-kiem-tra/create');
        }
    }

    /** Hiển thị danh sách phiếu */
    public function index()
    {
        $this->requireRole(['XT','QC']);
        $model = $this->loadModel('PhieuKiemTraSP');
        $rows = $model->listTickets();
        $this->loadView('xuongtruong/phieu_kiem_tra_sp/list', ['rows' => $rows]);
    }

    /**
     * Hiển thị chi tiết phiếu kiểm tra
     * URL: /phieu-kiem-tra/view/{MaPhieu} or /phieu-kiem-tra/view?ma=...
     */
    public function view($ma = null)
    {
        $this->requireRole(['XT','QC']);
        $id = $ma ?? ($_GET['ma'] ?? null);
        if (!$id) {
            $_SESSION['error'] = 'Thiếu mã phiếu cần xem.';
            $this->redirect('phieu-kiem-tra/index');
        }

        $model = $this->loadModel('PhieuKiemTraSP');
        try {
            $data = $model->getTicketDetails($id);
            if (!$data) {
                $_SESSION['error'] = 'Không tìm thấy phiếu: ' . htmlspecialchars($id);
                $this->redirect('phieu-kiem-tra/index');
            }
            $this->loadView('xuongtruong/phieu_kiem_tra_sp/view', [
                'pageTitle' => 'Chi tiết phiếu ' . $id,
                'ticket' => $data,
            ]);
        } catch (Throwable $e) {
            error_log('[PhieuKiemTraSPController.view] ' . $e->getMessage());
            $_SESSION['error'] = 'Lỗi khi lấy chi tiết phiếu: ' . $e->getMessage();
            $this->redirect('phieu-kiem-tra/index');
        }
    }
}
