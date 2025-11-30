<?php
/**
 * View: Nhập Kho Nguyên Liệu - Danh sách đơn đặt
 */
ob_start();
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-arrow-down me-2 text-primary"></i>Nhập Kho Nguyên Liệu
            </h2>
            <p class="text-muted">Danh sách các phiếu nhập nguyên liệu cần nhập vào kho</p>
        </div>
    </div>

    <!-- Danh sách đơn đặt -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Danh sách Phiếu Nhập Cần Nhập Kho
            </h5>
        </div>
        <div class="card-body">
            <?php if (empty($danhSachPhieuNhap)): ?>
                <!-- Không có dữ liệu -->
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không có phiếu nhập nào cần nhập kho</h5>
                    <p class="text-muted">Tất cả phiếu nhập đã được nhập kho</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th>Mã Phiếu Nhập</th>
                                <th>Ngày Nhập</th>
                                <th>Nhà Cung Cấp</th>
                                <th>Nhân Viên</th>
                                <th>Tổng Giá Trị</th>
                                <th>Trạng Thái</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($danhSachPhieuNhap as $phieuNhap): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-primary"><?= htmlspecialchars($phieuNhap['MaPhieuNhap']) ?></span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($phieuNhap['NgayNhap'])) ?></td>
                                    <td>
                                        <span class="badge bg-info"><?= htmlspecialchars($phieuNhap['TenNhaCungCap'] ?? 'N/A') ?></span>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($phieuNhap['TenNhanVien'] ?? 'N/A') ?>
                                    </td>
                                    <td>
                                        <strong class="text-success"><?= number_format($phieuNhap['TongGiaTri'], 0, ',', '.') ?> đ</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark"><?= htmlspecialchars($phieuNhap['TrangThaiNhap'] ?? 'Chưa nhập') ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>nhapkhonguyenlieu/detail?maPhieuNhap=<?= urlencode($phieuNhap['MaPhieuNhap']) ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i>Xem Chi Tiết
                                        </a>
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

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>

