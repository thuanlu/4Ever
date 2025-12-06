<?php
/**
 * View: Nhập Kho Nguyên Liệu - Chi tiết đơn đặt
 */
ob_start();
$phieu = $chiTietPhieuNhap['phieu'];
$chiTiet = $chiTietPhieuNhap['chiTiet'];
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-invoice me-2 text-primary"></i>Chi Tiết Phiếu Nhập
            </h2>
            <p class="text-muted">Thông tin chi tiết phiếu nhập nguyên liệu</p>
        </div>
        <a href="<?= BASE_URL ?>nhapkhonguyenlieu" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay Lại
        </a>
    </div>

    <!-- Thông báo -->
    <div id="result-alert" class="alert" role="alert" style="display: none;">
        <i class="fas fa-info-circle me-2"></i>
        <span id="result-message"></span>
        <button type="button" class="btn-close" onclick="closeAlert()"></button>
    </div>

    <!-- Thông tin phiếu -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>Thông Tin Phiếu Nhập
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Mã Phiếu Nhập:</th>
                            <td><span class="badge bg-primary"><?= htmlspecialchars($phieu['MaPhieuNhap']) ?></span></td>
                        </tr>
                        <tr>
                            <th>Ngày Nhập:</th>
                            <td><?= date('d/m/Y H:i', strtotime($phieu['NgayNhap'])) ?></td>
                        </tr>
                        <tr>
                            <th>Nhân Viên:</th>
                            <td><?= htmlspecialchars($phieu['TenNhanVien'] ?? 'N/A') ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Nhà Cung Cấp:</th>
                            <td><span class="badge bg-info"><?= htmlspecialchars($phieu['TenNhaCungCap'] ?? 'N/A') ?></span></td>
                        </tr>
                        <tr>
                            <th>Tổng Giá Trị:</th>
                            <td><strong class="text-success"><?= number_format($phieu['TongGiaTri'], 0, ',', '.') ?> đ</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chi tiết nguyên liệu -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list-ul me-2"></i>Chi Tiết Nguyên Liệu
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>Mã NL</th>
                            <th>Tên Nguyên Liệu</th>
                            <th>Loại</th>
                            <th>Đơn Vị</th>
                            <th>Số Lượng Nhập</th>
                            <th>Đơn Giá</th>
                            <th>Thành Tiền</th>
                            <th>Tồn Kho Hiện Tại</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($chiTiet as $item): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary"><?= htmlspecialchars($item['MaNguyenLieu']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($item['TenNguyenLieu']) ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($item['LoaiNguyenLieu'] ?? 'N/A') ?></span>
                                </td>
                                <td><?= htmlspecialchars($item['DonViTinh'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="badge bg-info"><?= number_format($item['SoLuongNhap'], 2) ?></span>
                                </td>
                                <td><?= number_format($item['DonGia'], 0, ',', '.') ?> đ</td>
                                <td>
                                    <strong class="text-success"><?= number_format($item['ThanhTien'], 0, ',', '.') ?> đ</strong>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark"><?= number_format($item['SoLuongTonKho'], 2) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Form xác nhận/từ chối -->
    <?php if (!$daNhap): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-check-circle me-2"></i>Xác Nhận Nhập Kho
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-success btn-lg w-100" onclick="confirmImport()">
                            <i class="fas fa-check me-2"></i>Xác Nhận Nhập Kho
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-danger btn-lg w-100" onclick="showRejectForm()">
                            <i class="fas fa-times me-2"></i>Từ Chối
                        </button>
                    </div>
                </div>

                <!-- Form từ chối (ẩn mặc định) -->
                <div id="reject-form" class="mt-4" style="display: none;">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">Nhập Lý Do Từ Chối</h6>
                        </div>
                        <div class="card-body">
                            <form id="rejectForm" onsubmit="rejectImport(event)">
                                <input type="hidden" name="maPhieuNhap" value="<?= htmlspecialchars($phieu['MaPhieuNhap']) ?>">
                                <div class="mb-3">
                                    <label for="lyDo" class="form-label">Lý Do Từ Chối <span class="text-danger">*</span></label>
                                    <textarea class="form-control" 
                                              id="lyDo" 
                                              name="lyDo" 
                                              rows="4" 
                                              required
                                              placeholder="Nhập lý do từ chối nhập kho..."></textarea>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-times me-2"></i>Xác Nhận Từ Chối
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="hideRejectForm()">
                                        <i class="fas fa-arrow-left me-2"></i>Hủy
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Phiếu nhập này đã được nhập kho rồi!
        </div>
    <?php endif; ?>
</div>

<script>
function showAlert(message, type) {
    const alertDiv = document.getElementById('result-alert');
    const messageSpan = document.getElementById('result-message');
    
    alertDiv.className = 'alert alert-' + type;
    messageSpan.textContent = message;
    alertDiv.style.display = 'block';
    
    // Tự động ẩn sau 5 giây
    setTimeout(() => {
        alertDiv.style.display = 'none';
    }, 5000);
}

function closeAlert() {
    document.getElementById('result-alert').style.display = 'none';
}

function showRejectForm() {
    document.getElementById('reject-form').style.display = 'block';
}

function hideRejectForm() {
    document.getElementById('reject-form').style.display = 'none';
    document.getElementById('rejectForm').reset();
}

function confirmImport() {
    if (!confirm('Bạn có chắc chắn muốn xác nhận nhập kho phiếu nhập này?')) {
        return;
    }

    const maPhieuNhap = '<?= htmlspecialchars($phieu['MaPhieuNhap']) ?>';
    const formData = new FormData();
    formData.append('maPhieuNhap', maPhieuNhap);

    fetch('<?= BASE_URL ?>nhapkhonguyenlieu/confirm', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                throw new Error('Response is not JSON: ' + text.substring(0, 100));
            });
        }
        return response.json();
    })
    .then(data => {
        if (data && data.success) {
            showAlert('Nhập kho thành công!', 'success');
            setTimeout(() => {
                window.location.href = '<?= BASE_URL ?>nhapkhonguyenlieu';
            }, 2000);
        } else {
            const errorMsg = data && data.message ? data.message : 'Có lỗi xảy ra khi nhập kho!';
            showAlert(errorMsg, 'danger');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showAlert('Lỗi kết nối! Vui lòng kiểm tra console để xem chi tiết.', 'danger');
    });
}

function rejectImport(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const lyDo = formData.get('lyDo');
    
    if (!lyDo || lyDo.trim() === '') {
        showAlert('Vui lòng nhập lý do từ chối!', 'warning');
        return;
    }

    fetch('<?= BASE_URL ?>nhapkhonguyenlieu/reject', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                throw new Error('Response is not JSON: ' + text.substring(0, 100));
            });
        }
        return response.json();
    })
    .then(data => {
        if (data && data.success) {
            showAlert('Đã ghi nhận lý do từ chối!', 'info');
            hideRejectForm();
        } else {
            const errorMsg = data && data.message ? data.message : 'Có lỗi xảy ra!';
            showAlert(errorMsg, 'danger');
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showAlert('Lỗi kết nối! Vui lòng kiểm tra console để xem chi tiết.', 'danger');
    });
}
</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>