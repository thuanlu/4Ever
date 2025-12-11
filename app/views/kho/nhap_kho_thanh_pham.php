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

    <!-- Gộp chung 1 form -->
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
                                <th>Trạng Thái Kho</th>
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
                                    <?php 
                                    $trangThaiKho = $lh['TrangThaiKho'] ?? 'Chưa nhập kho';
                                    if ($trangThaiKho === 'Đã nhập kho') {
                                        echo '<span class="badge bg-success">Đã nhập kho</span>';
                                    } else {
                                        echo '<span class="badge bg-warning text-dark">Chưa nhập kho</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Danh sách thành phẩm có trong kho -->
        <div class="card-header border-top">
            <h5 class="mb-0">
                <i class="fas fa-boxes me-2"></i>Danh Sách Các Thành Phẩm Có Trong Kho
            </h5>
        </div>
        <div class="card-body" id="thanh-pham-trong-kho-container">
            <?php if (empty($thanhPhamTrongKho)): ?>
                <!-- Không có dữ liệu -->
                <div class="text-center py-5" id="empty-message">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có thành phẩm nào trong kho</h5>
                    <p class="text-muted">Các thành phẩm sẽ xuất hiện ở đây sau khi được nhập kho</p>
                </div>
            <?php else: ?>
                <!-- Bảng danh sách thành phẩm -->
                <div class="table-responsive" id="thanh-pham-table-wrapper">
                    <table class="table table-hover table-bordered" id="table-thanh-pham">
                        <thead class="table-light">
                            <tr>
                                <th>Mã Sản Phẩm</th>
                                <th>Tên Sản Phẩm</th>
                                <th>Size / Màu</th>
                                <th>Số Lượng Tồn Kho</th>
                                <th>Vị Trí Kho</th>
                                <th>Giá Xuất</th>
                                <th>Ngày Cập Nhật</th>
                            </tr>
                        </thead>
                        <tbody id="thanh-pham-tbody">
                            <?php foreach ($thanhPhamTrongKho as $tp): ?>
                            <tr>
                                <td class="fw-bold text-primary">
                                    <?php echo htmlspecialchars($tp['MaSanPham']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($tp['TenSanPham']); ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo htmlspecialchars($tp['Size']); ?></span>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($tp['Mau']); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-success fs-6">
                                        <?php echo number_format($tp['SoLuongHienTai']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?php echo htmlspecialchars($tp['ViTriKho'] ?? 'Kho A'); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <?php echo number_format($tp['GiaXuat'] ?? 0, 0, ',', '.'); ?> đ
                                </td>
                                <td>
                                    <?php 
                                    if (!empty($tp['NgayCapNhat'])) {
                                        $date = new DateTime($tp['NgayCapNhat']);
                                        echo $date->format('d/m/Y H:i');
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
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

// (Đã xóa chức năng nhập kho từng lô riêng lẻ - chỉ còn nhập hàng loạt)

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
 * Cập nhật lại danh sách thành phẩm trong kho
 */
function updateThanhPhamTrongKho() {
    fetch('<?php echo BASE_URL; ?>nhapkho/get-thanh-pham-trong-kho', {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data && data.success && data.data) {
            const container = document.getElementById('thanh-pham-trong-kho-container');
            const tbody = document.getElementById('thanh-pham-tbody');
            const emptyMessage = document.getElementById('empty-message');
            const tableWrapper = document.getElementById('thanh-pham-table-wrapper');
            
            if (data.data.length === 0) {
                // Không có dữ liệu
                if (tbody) {
                    tbody.innerHTML = '';
                }
                if (tableWrapper) {
                    tableWrapper.style.display = 'none';
                }
                if (!emptyMessage) {
                    container.innerHTML = `
                        <div class="text-center py-5" id="empty-message">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Chưa có thành phẩm nào trong kho</h5>
                            <p class="text-muted">Các thành phẩm sẽ xuất hiện ở đây sau khi được nhập kho</p>
                        </div>
                    `;
                }
            } else {
                // Có dữ liệu - cập nhật bảng
                if (emptyMessage) {
                    emptyMessage.remove();
                }
                if (!tableWrapper) {
                    container.innerHTML = `
                        <div class="table-responsive" id="thanh-pham-table-wrapper">
                            <table class="table table-hover table-bordered" id="table-thanh-pham">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã Sản Phẩm</th>
                                        <th>Tên Sản Phẩm</th>
                                        <th>Size / Màu</th>
                                        <th>Số Lượng Tồn Kho</th>
                                        <th>Vị Trí Kho</th>
                                        <th>Giá Xuất</th>
                                        <th>Ngày Cập Nhật</th>
                                    </tr>
                                </thead>
                                <tbody id="thanh-pham-tbody"></tbody>
                            </table>
                        </div>
                    `;
                }
                
                const newTbody = document.getElementById('thanh-pham-tbody');
                newTbody.innerHTML = data.data.map(tp => {
                    const ngayCapNhat = tp.NgayCapNhat ? 
                        new Date(tp.NgayCapNhat).toLocaleString('vi-VN', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : '-';
                    
                    return `
                        <tr>
                            <td class="fw-bold text-primary">${escapeHtml(tp.MaSanPham)}</td>
                            <td>${escapeHtml(tp.TenSanPham)}</td>
                            <td>
                                <span class="badge bg-info">${escapeHtml(tp.Size)}</span>
                                <span class="badge bg-secondary">${escapeHtml(tp.Mau)}</span>
                            </td>
                            <td>
                                <span class="badge bg-success fs-6">
                                    ${parseInt(tp.SoLuongHienTai).toLocaleString('vi-VN')}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    ${escapeHtml(tp.ViTriKho || 'Kho A')}
                                </span>
                            </td>
                            <td class="text-end">
                                ${parseInt(tp.GiaXuat || 0).toLocaleString('vi-VN')} đ
                            </td>
                            <td>${ngayCapNhat}</td>
                        </tr>
                    `;
                }).join('');
            }
        }
    })
    .catch(error => {
        console.error('Error updating thanh pham trong kho:', error);
    });
}

/**
 * Escape HTML để tránh XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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
    
    // Debug log
    console.log('Sending lots:', danhSachLoHang);
    
    // Tạo AbortController để có thể timeout
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 giây timeout
    
    fetch('<?php echo BASE_URL; ?>nhapkho/confirm-multi', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
        signal: controller.signal,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        clearTimeout(timeoutId);
        
        // Kiểm tra status code
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Kiểm tra content type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('Response is not JSON:', text);
                throw new Error('Response is not JSON: ' + text.substring(0, 100));
            });
        }
        
        return response.json();
    })
    .then(data => {
        clearTimeout(timeoutId);
        buttons.forEach(btn => btn.disabled = false);
        
        console.log('Response data:', data);
        
        if (data && data.success) {
            const successMsg = data.successCount > 0 
                ? `Nhập kho thành công! Đã nhập ${data.successCount} lô hàng.` 
                : 'Nhập kho thành công!';
            showAlert(successMsg, 'success');
            
            // Cập nhật lại danh sách thành phẩm trong kho ngay lập tức
            updateThanhPhamTrongKho();
            
            // Reload trang sau 2 giây để cập nhật danh sách lô hàng
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            const errorMsg = data && data.message ? data.message : 'Có lỗi xảy ra khi nhập kho!';
            showAlert(errorMsg, 'danger');
        }
    })
    .catch(error => {
        clearTimeout(timeoutId);
        buttons.forEach(btn => btn.disabled = false);
        
        if (error.name === 'AbortError') {
            console.error('Request timeout');
            showAlert('Request timeout! Vui lòng thử lại.', 'danger');
        } else {
            console.error('Fetch error:', error);
            showAlert('Lỗi kết nối! Vui lòng kiểm tra console để xem chi tiết.', 'danger');
        }
    });
}


</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>

