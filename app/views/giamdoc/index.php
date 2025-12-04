<?php 
// Tệp: app/views/giamdoc/index.php
$pageTitle = "Duyệt Kế hoạch Sản xuất"; 
ob_start();
?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary"><i class="fas fa-check-double me-2"></i>Duyệt Kế hoạch Sản xuất</h2>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Danh sách Kế hoạch đang Chờ duyệt</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã KH</th>
                            <th>Tên kế hoạch</th>
                            <th>Ngày bắt đầu</th>
                            <th>Đơn hàng</th>
                            <th>Người lập</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($kehoachs)): ?>
                            <?php foreach ($kehoachs as $k): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($k['MaKeHoach']); ?></strong></td>
                                <td><?php echo htmlspecialchars($k['TenKeHoach']); ?></td>
                                <td><?php echo htmlspecialchars($k['NgayBatDau']); ?></td>
                                <td><?php echo htmlspecialchars($k['TenDonHang']); ?></td>
                                <td><?php echo htmlspecialchars($k['NguoiLap']); ?></td>
                                <td class="text-center">
                                    <a href="<?php echo BASE_URL; ?>giamdoc/view/<?php echo $k['MaKeHoach']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-search-plus me-1"></i> Xem & Duyệt
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                                    Hiện không có kế hoạch nào cần duyệt.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>