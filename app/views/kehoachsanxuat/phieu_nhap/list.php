<?php
/**
 * View: Danh sách phiếu nhập nguyên vật liệu
 * Hệ thống quản lý sản xuất nhà máy 4Ever
 */

// Bắt đầu output buffering để capture content
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="card-title">
                                <i class="fas fa-list"></i> Danh sách Phiếu Nhập Nguyên Vật Liệu
                            </h3>
                        </div>
                        <div class="col-auto">
                            <a href="<?php echo BASE_URL; ?>/phieu-nhap/create" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tạo phiếu nhập mới
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo htmlspecialchars($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo htmlspecialchars($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <!-- Bộ lọc -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="Chờ duyệt">Chờ duyệt</option>
                                <option value="Đã duyệt">Đã duyệt</option>
                                <option value="Đã hủy">Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="dateFrom" placeholder="Từ ngày">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control" id="dateTo" placeholder="Đến ngày">
                        </div>
                    </div>

                    <!-- Bảng danh sách -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="phieuNhapTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Mã phiếu</th>
                                    <th>Ngày nhập</th>
                                    <th>Nhà cung cấp</th>
                                    <th>Tổng giá trị</th>
                                    <th>Người lập</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($phieuNhap)): ?>
                                    <?php foreach ($phieuNhap as $pn): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($pn['MaPhieuNhap']); ?></strong>
                                            </td>
                                            <td>
                                                <?php echo date('d/m/Y', strtotime($pn['NgayNhap'])); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($pn['TenNhaCungCap']); ?>
                                            </td>
                                            <td class="text-end">
                                                <strong><?php echo number_format($pn['TongGiaTri'], 0, ',', '.'); ?> VNĐ</strong>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($pn['NguoiLap'] ?? 'N/A'); ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                switch ($pn['TrangThai']) {
                                                    case 'Chờ duyệt':
                                                        $statusClass = 'badge bg-warning';
                                                        break;
                                                    case 'Đã duyệt':
                                                        $statusClass = 'badge bg-success';
                                                        break;
                                                    case 'Đã hủy':
                                                        $statusClass = 'badge bg-danger';
                                                        break;
                                                    default:
                                                        $statusClass = 'badge bg-secondary';
                                                }
                                                ?>
                                                <span class="<?php echo $statusClass; ?>">
                                                    <?php echo htmlspecialchars($pn['TrangThai']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?php echo BASE_URL; ?>/phieu-nhap/show/<?php echo $pn['MaPhieuNhap']; ?>" 
                                                       class="btn btn-sm btn-info" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($pn['TrangThai'] === 'Chờ duyệt'): ?>
                                                        <button class="btn btn-sm btn-success" 
                                                                onclick="duyetPhieuNhap('<?php echo $pn['MaPhieuNhap']; ?>')" 
                                                                title="Duyệt phiếu">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" 
                                                                onclick="huyPhieuNhap('<?php echo $pn['MaPhieuNhap']; ?>')" 
                                                                title="Hủy phiếu">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                            Chưa có phiếu nhập nào
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Phân trang -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Phân trang">
                            <ul class="pagination justify-content-center">
                                <?php if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                    <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>

                    <!-- Thống kê -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4><?php echo $total; ?></h4>
                                            <p class="mb-0">Tổng phiếu nhập</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-file-invoice fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4><?php echo count(array_filter($phieuNhap, function($pn) { return $pn['TrangThai'] === 'Chờ duyệt'; })); ?></h4>
                                            <p class="mb-0">Chờ duyệt</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4><?php echo count(array_filter($phieuNhap, function($pn) { return $pn['TrangThai'] === 'Đã duyệt'; })); ?></h4>
                                            <p class="mb-0">Đã duyệt</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4><?php echo number_format(array_sum(array_column($phieuNhap, 'TongGiaTri')), 0, ',', '.'); ?></h4>
                                            <p class="mb-0">Tổng giá trị (VNĐ)</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-dollar-sign fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal xác nhận -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmBtn">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const dateFrom = document.getElementById('dateFrom');
    const dateTo = document.getElementById('dateTo');
    const table = document.getElementById('phieuNhapTable');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const confirmMessage = document.getElementById('confirmMessage');
    const confirmBtn = document.getElementById('confirmBtn');
    
    let currentAction = '';
    let currentMaPhieu = '';
    
    // Tìm kiếm và lọc
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const fromDate = dateFrom.value;
        const toDate = dateTo.value;
        
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length < 7) return; // Skip empty rows
            
            const maPhieu = cells[0].textContent.toLowerCase();
            const nhaCungCap = cells[2].textContent.toLowerCase();
            const nguoiLap = cells[4].textContent.toLowerCase();
            const trangThai = cells[5].textContent.trim();
            const ngayNhap = cells[1].textContent;
            
            let showRow = true;
            
            // Tìm kiếm
            if (searchTerm && !maPhieu.includes(searchTerm) && !nhaCungCap.includes(searchTerm) && !nguoiLap.includes(searchTerm)) {
                showRow = false;
            }
            
            // Lọc theo trạng thái
            if (statusValue && trangThai !== statusValue) {
                showRow = false;
            }
            
            // Lọc theo ngày
            if (fromDate || toDate) {
                const rowDate = new Date(ngayNhap.split('/').reverse().join('-'));
                if (fromDate && rowDate < new Date(fromDate)) {
                    showRow = false;
                }
                if (toDate && rowDate > new Date(toDate)) {
                    showRow = false;
                }
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }
    
    // Event listeners
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    dateFrom.addEventListener('change', filterTable);
    dateTo.addEventListener('change', filterTable);
    
    // Duyệt phiếu nhập
    window.duyetPhieuNhap = function(maPhieu) {
        currentAction = 'duyet';
        currentMaPhieu = maPhieu;
        confirmMessage.textContent = `Bạn có chắc chắn muốn duyệt phiếu nhập ${maPhieu}?`;
        confirmBtn.className = 'btn btn-success';
        confirmBtn.textContent = 'Duyệt';
        confirmModal.show();
    };
    
    // Hủy phiếu nhập
    window.huyPhieuNhap = function(maPhieu) {
        currentAction = 'huy';
        currentMaPhieu = maPhieu;
        confirmMessage.textContent = `Bạn có chắc chắn muốn hủy phiếu nhập ${maPhieu}?`;
        confirmBtn.className = 'btn btn-danger';
        confirmBtn.textContent = 'Hủy';
        confirmModal.show();
    };
    
    // Xử lý xác nhận
    confirmBtn.addEventListener('click', function() {
        if (currentAction === 'duyet') {
            // Gọi API duyệt phiếu
            fetch(`<?php echo BASE_URL; ?>/phieu-nhap/duyet/${currentMaPhieu}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Lỗi: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Lỗi khi duyệt phiếu nhập');
            });
        } else if (currentAction === 'huy') {
            // Gọi API hủy phiếu
            fetch(`<?php echo BASE_URL; ?>/phieu-nhap/huy/${currentMaPhieu}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Lỗi: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Lỗi khi hủy phiếu nhập');
            });
        }
        
        confirmModal.hide();
    });
});
</script>

<?php
// Kết thúc output buffering và lưu content
$content = ob_get_clean();

// Include layout với content
require_once APP_PATH . '/views/layouts/main.php';
?>
