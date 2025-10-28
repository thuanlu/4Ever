<?php
/**
 * View: Nhập Kho Thành Phẩm
 * Hiển thị danh sách lô hàng cần nhập và form nhập kho
 */
ob_start();
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-warehouse me-2 text-primary"></i>Nhập Kho Thành Phẩm
            </h2>
            <p class="text-muted">Danh sách lô hàng đã được QC duyệt và cần nhập vào kho thành phẩm</p>
        </div>
    </div>

    <!-- Thông báo kết quả -->
    <div id="result-alert" class="alert" role="alert" style="display: none;">
        <i class="fas fa-info-circle me-2"></i>
        <span id="result-message"></span>
        <button type="button" class="btn-close" onclick="closeAlert()"></button>
    </div>

    <!-- Form tìm kiếm -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tìm kiếm theo mã lô hàng</label>
                    <input type="text" class="form-control" id="search-input" placeholder="Nhập mã lô hàng...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Lọc theo sản phẩm</label>
                    <select class="form-select" id="filter-sanpham">
                        <option value="">Tất cả sản phẩm</option>
                        <?php 
                        $uniqueProducts = [];
                        foreach ($loHangs as $lh) {
                            if (!in_array($lh['TenSanPham'], $uniqueProducts)) {
                                $uniqueProducts[] = $lh['TenSanPham'];
                                echo "<option value='{$lh['TenSanPham']}'>{$lh['TenSanPham']}</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Thao tác</label><br>
                    <button type="button" class="btn btn-secondary" onclick="resetFilter()">
                        <i class="fas fa-redo me-1"></i>Làm mới
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách lô hàng -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Danh sách Lô hàng cần nhập kho
                    </h5>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" onclick="confirmImportSelected()">
                        <i class="fas fa-check-double me-2"></i>Xác Nhận Nhập Kho Đã Chọn
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($loHangs)): ?>
                <!-- Không có dữ liệu -->
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không có lô hàng nào cần nhập kho</h5>
                    <p class="text-muted">Tất cả lô hàng đã được nhập kho hoặc chưa được QC duyệt</p>
                </div>
            <?php else: ?>
                <!-- Bảng danh sách -->
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="table-lohang">
                        <thead class="table-light">
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="select-all" onclick="toggleSelectAll()">
                                </th>
                                <th>Mã Lô Hàng</th>
                                <th>Tên Sản Phẩm</th>
                                <th>Size / Màu</th>
                                <th>Số Lượng</th>
                                <th>Trạng Thái QC</th>
                                <th>Trạng Thái Kho</th>
                                <th width="150">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loHangs as $lh): ?>
                            <tr data-lohang="<?php echo htmlspecialchars($lh['MaLoHang']); ?>">
                                <td>
                                    <input type="checkbox" 
                                           class="checkbox-lohang" 
                                           value="<?php echo htmlspecialchars($lh['MaLoHang']); ?>">
                                </td>
                                <td class="fw-bold text-primary">
                                    <?php echo htmlspecialchars($lh['MaLoHang']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($lh['TenSanPham']); ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo htmlspecialchars($lh['Size']); ?></span>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($lh['Mau']); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-primary fs-6">
                                        <?php echo number_format($lh['SoLuong']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success"><?php echo htmlspecialchars($lh['TrangThaiQC']); ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $trangThaiKho = $lh['TrangThaiKho'] ?? 'Chưa nhập kho';
                                    if ($trangThaiKho === 'Đã nhập kho') {
                                        echo '<span class="badge bg-success">Đã nhập kho</span>';
                                    } else {
                                        echo '<span class="badge bg-warning text-dark">Chưa nhập kho</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if (!isset($lh['TrangThaiKho']) || $lh['TrangThaiKho'] !== 'Đã nhập kho'): ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-primary"
                                            onclick="confirmImportSingle('<?php echo htmlspecialchars($lh['MaLoHang']); ?>')">
                                        <i class="fas fa-arrow-down me-1"></i>Nhập Kho
                                    </button>
                                    <?php else: ?>
                                    <span class="text-muted">Đã nhập kho</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Thống kê -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-boxes me-2"></i>Tổng Lô Hàng
                    </h5>
                    <h2 class="mb-0" id="tong-lo-hang"><?php echo count($loHangs); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-check-circle me-2"></i>Đã Nhập Kho
                    </h5>
                    <h2 class="mb-0" id="da-nhap-kho">
                        <?php 
                        $count = 0;
                        foreach ($loHangs as $lh) {
                            if (isset($lh['TrangThaiKho']) && $lh['TrangThaiKho'] === 'Đã nhập kho') {
                                $count++;
                            }
                        }
                        echo $count;
                        ?>
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-clock me-2"></i>Chờ Nhập Kho
                    </h5>
                    <h2 class="mb-0" id="cho-nhap-kho">
                        <?php 
                        $count = 0;
                        foreach ($loHangs as $lh) {
                            if (!isset($lh['TrangThaiKho']) || $lh['TrangThaiKho'] !== 'Đã nhập kho') {
                                $count++;
                            }
                        }
                        echo $count;
                        ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-question-circle me-2"></i>Xác Nhận Nhập Kho
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn nhập kho lô hàng này?</p>
                <div id="modal-lohang-info"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="btn-confirm-import">
                    <i class="fas fa-check me-2"></i>Xác Nhận
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ==========================================
// JavaScript Functions
// ==========================================

let selectedLoHang = null;

/**
 * Hiển thị thông báo
 */
function showAlert(message, type = 'success') {
    const alertDiv = document.getElementById('result-alert');
    const messageSpan = document.getElementById('result-message');
    
    // Xóa class cũ và thêm class mới
    alertDiv.className = 'alert alert-' + type;
    messageSpan.textContent = message;
    alertDiv.style.display = 'block';
    
    // Tự động ẩn sau 5 giây
    setTimeout(() => {
        closeAlert();
    }, 5000);
}

/**
 * Đóng thông báo
 */
function closeAlert() {
    document.getElementById('result-alert').style.display = 'none';
}

/**
 * Chọn/bỏ chọn tất cả
 */
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.checkbox-lohang');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
}

/**
 * Nhập kho một lô hàng
 */
function confirmImportSingle(maLoHang) {
    selectedLoHang = maLoHang;
    
    // Hiển thị modal xác nhận
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    document.getElementById('modal-lohang-info').innerHTML = 
        `<p class="fw-bold">Mã Lô Hàng: ${maLoHang}</p>`;
    
    // Xử lý khi nhấn nút xác nhận
    document.getElementById('btn-confirm-import').onclick = function() {
        performImport([maLoHang]);
        modal.hide();
    };
    
    modal.show();
}

/**
 * Nhập kho các lô hàng đã chọn
 */
function confirmImportSelected() {
    const checkboxes = document.querySelectorAll('.checkbox-lohang:checked');
    
    if (checkboxes.length === 0) {
        showAlert('Vui lòng chọn ít nhất một lô hàng!', 'warning');
        return;
    }
    
    const danhSachLoHang = [];
    checkboxes.forEach(checkbox => {
        danhSachLoHang.push(checkbox.value);
    });
    
    // Hiển thị modal xác nhận
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    document.getElementById('modal-lohang-info').innerHTML = 
        `<p class="fw-bold">Bạn sẽ nhập kho ${danhSachLoHang.length} lô hàng:</p>
         <ul>
             ${danhSachLoHang.map(lh => `<li>${lh}</li>`).join('')}
         </ul>`;
    
    // Xử lý khi nhấn nút xác nhận
    document.getElementById('btn-confirm-import').onclick = function() {
        performImport(danhSachLoHang);
        modal.hide();
    };
    
    modal.show();
}

/**
 * Thực hiện nhập kho
 */
function performImport(danhSachLoHang) {
    // Disable nút để tránh double click
    const buttons = document.querySelectorAll('button');
    buttons.forEach(btn => btn.disabled = true);
    
    // Hiển thị loading
    showAlert('Đang xử lý nhập kho...', 'info');
    
    const formData = new FormData();
    danhSachLoHang.forEach(lh => {
        formData.append('danhSachLoHang[]', lh);
    });
    
    fetch('<?php echo BASE_URL; ?>nhapkho/confirm-multi', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        buttons.forEach(btn => btn.disabled = false);
        
        if (data.success) {
            showAlert(
                `Nhập kho thành công! Đã nhập ${data.successCount} lô hàng.`, 
                'success'
            );
            
            // Reload trang sau 2 giây
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showAlert(
                `Có lỗi xảy ra: ${data.message}`, 
                'danger'
            );
        }
    })
    .catch(error => {
        buttons.forEach(btn => btn.disabled = false);
        showAlert('Lỗi kết nối! Vui lòng thử lại sau.', 'danger');
        console.error('Error:', error);
    });
}

/**
 * Tìm kiếm trong bảng
 */
document.getElementById('search-input').addEventListener('keyup', function(e) {
    const value = this.value.toLowerCase();
    const table = document.getElementById('table-lohang');
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(value) ? '' : 'none';
    });
});

/**
 * Lọc theo sản phẩm
 */
document.getElementById('filter-sanpham').addEventListener('change', function() {
    const value = this.value;
    const table = document.getElementById('table-lohang');
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (!value) {
            row.style.display = '';
        } else {
            const text = row.textContent;
            row.style.display = text.includes(value) ? '' : 'none';
        }
    });
});

/**
 * Làm mới bộ lọc
 */
function resetFilter() {
    document.getElementById('search-input').value = '';
    document.getElementById('filter-sanpham').value = '';
    
    const table = document.getElementById('table-lohang');
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        row.style.display = '';
    });
}
</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>

