<?php
/**
 * View: Chi tiết phiếu nhập nguyên vật liệu
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
                                <i class="fas fa-file-invoice"></i> Chi tiết Phiếu Nhập: <?php echo htmlspecialchars($phieuNhap['MaPhieuNhap']); ?>
                            </h3>
                        </div>
                        <div class="col-auto">
                            <a href="<?php echo BASE_URL; ?>/phieu-nhap" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Thông tin cơ bản -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Thông tin phiếu nhập</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Mã phiếu:</strong></div>
                                        <div class="col-8"><?php echo htmlspecialchars($phieuNhap['MaPhieuNhap']); ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Ngày nhập:</strong></div>
                                        <div class="col-8"><?php echo date('d/m/Y H:i', strtotime($phieuNhap['NgayNhap'])); ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Người lập:</strong></div>
                                        <div class="col-8"><?php echo htmlspecialchars($phieuNhap['NguoiLap'] ?? 'N/A'); ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Tổng giá trị:</strong></div>
                                        <div class="col-8">
                                            <span class="badge bg-success fs-6">
                                                <?php echo number_format($phieuNhap['TongGiaTri'], 0, ',', '.'); ?> VNĐ
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Thông tin nhà cung cấp</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Tên NCC:</strong></div>
                                        <div class="col-8"><?php echo htmlspecialchars($phieuNhap['TenNhaCungCap']); ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Địa chỉ:</strong></div>
                                        <div class="col-8"><?php echo htmlspecialchars($phieuNhap['DiaChi']); ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>SĐT:</strong></div>
                                        <div class="col-8"><?php echo htmlspecialchars($phieuNhap['SoDienThoai']); ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Email:</strong></div>
                                        <div class="col-8"><?php echo htmlspecialchars($phieuNhap['Email']); ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chi tiết nguyên vật liệu -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Chi tiết nguyên vật liệu</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($chiTietNVL)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>Mã NVL</th>
                                                        <th>Tên nguyên vật liệu</th>
                                                        <th>Đơn vị tính</th>
                                                        <th>Số lượng nhập</th>
                                                        <th>Đơn giá</th>
                                                        <th>Thành tiền</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $tongThanhTien = 0;
                                                    foreach ($chiTietNVL as $item): 
                                                        $thanhTien = $item['SoLuongNhap'] * $item['DonGia'];
                                                        $tongThanhTien += $thanhTien;
                                                    ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($item['MaNguyenLieu']); ?></td>
                                                            <td><?php echo htmlspecialchars($item['TenNguyenLieu']); ?></td>
                                                            <td><?php echo htmlspecialchars($item['DonViTinh']); ?></td>
                                                            <td class="text-end"><?php echo number_format($item['SoLuongNhap'], 2, ',', '.'); ?></td>
                                                            <td class="text-end"><?php echo number_format($item['DonGia'], 0, ',', '.'); ?> VNĐ</td>
                                                            <td class="text-end">
                                                                <strong><?php echo number_format($thanhTien, 0, ',', '.'); ?> VNĐ</strong>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="5" class="text-end"><strong>Tổng cộng:</strong></td>
                                                        <td class="text-end">
                                                            <strong class="text-primary fs-5">
                                                                <?php echo number_format($tongThanhTien, 0, ',', '.'); ?> VNĐ
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>Không có chi tiết nguyên vật liệu</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thống kê -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4><?php echo count($chiTietNVL); ?></h4>
                                    <p class="mb-0">Loại NVL</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4><?php echo number_format(array_sum(array_column($chiTietNVL, 'SoLuongNhap')), 0, ',', '.'); ?></h4>
                                    <p class="mb-0">Tổng số lượng</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4><?php echo number_format($phieuNhap['TongGiaTri'], 0, ',', '.'); ?></h4>
                                    <p class="mb-0">Tổng giá trị (VNĐ)</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4><?php echo count($chiTietNVL) > 0 ? number_format($phieuNhap['TongGiaTri'] / count($chiTietNVL), 0, ',', '.') : 0; ?></h4>
                                    <p class="mb-0">Giá trị TB/NVL (VNĐ)</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row mt-4">
                        <div class="col-12 text-end">
                            <button class="btn btn-outline-primary me-2" onclick="window.print()">
                                <i class="fas fa-print"></i> In phiếu
                            </button>
                            <button class="btn btn-outline-success me-2" onclick="exportToPDF()">
                                <i class="fas fa-file-pdf"></i> Xuất PDF
                            </button>
                            <button class="btn btn-outline-info" onclick="exportToExcel()">
                                <i class="fas fa-file-excel"></i> Xuất Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportToPDF() {
    // Tạo PDF từ nội dung hiện tại
    window.print();
}

function exportToExcel() {
    // Tạo Excel từ dữ liệu
    const table = document.querySelector('table');
    const rows = table.querySelectorAll('tr');
    let csv = '';
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td, th');
        const rowData = Array.from(cells).map(cell => {
            return '"' + cell.textContent.replace(/"/g, '""') + '"';
        });
        csv += rowData.join(',') + '\n';
    });
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'phieu_nhap_<?php echo $phieuNhap['MaPhieuNhap']; ?>.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Ẩn các nút action khi in
window.addEventListener('beforeprint', function() {
    document.querySelectorAll('.btn').forEach(btn => {
        btn.style.display = 'none';
    });
});

window.addEventListener('afterprint', function() {
    document.querySelectorAll('.btn').forEach(btn => {
        btn.style.display = '';
    });
});
</script>

<style>
@media print {
    .btn {
        display: none !important;
    }
    
    .card-header {
        background-color: #f8f9fa !important;
        border-bottom: 2px solid #dee2e6 !important;
    }
    
    .table {
        border-collapse: collapse !important;
    }
    
    .table th,
    .table td {
        border: 1px solid #dee2e6 !important;
    }
}
</style>

<?php
// Kết thúc output buffering và lưu content
$content = ob_get_clean();

// Include layout với content
require_once APP_PATH . '/views/layouts/main.php';
?>
