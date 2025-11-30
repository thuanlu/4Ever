<?php
ob_start();
?>
<div class="container mt-4">
    <h2><i class="fas fa-tasks me-2"></i>Xem Kế hoạch sản xuất</h2>
    <form method="get" class="mb-3">
        <div class="filter-box shadow-sm p-3 mb-4 bg-white rounded">
    <div class="row g-3">

        <div class="col-md-3">
            <label class="form-label fw-bold">Kỳ:</label>
            <select class="form-select">
                <option>Tất cả</option>
                <option>Tuần</option>
                <option>Tháng</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">Sản phẩm:</label>
            <input type="text" class="form-control" placeholder="Tên sản phẩm">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">Đơn hàng:</label>
            <input type="text" class="form-control" placeholder="Mã đơn hàng">
        </div>

        <div class="col-md-3">
            <label class="form-label fw-bold">Trạng thái:</label>
            <select class="form-select">
                <option>Tất cả</option>
                <option>Đã duyệt</option>
                <option>Chờ duyệt</option>
            </select>
        </div>

        <div class="col-md-12 text-end">
            <button class="btn btn-primary px-4 mt-2">
                <i class="fa-solid fa-filter"></i> Lọc
            </button>
        </div>

    </div>
    </form>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Mã KH</th>
                <th>Tên kế hoạch</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Người lập</th>
                <th>Đơn hàng</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kehoachs as $k): ?>
            <tr>
                <td><?php echo htmlspecialchars($k['MaKeHoach']); ?></td>
                <td><?php echo htmlspecialchars($k['TenKeHoach']); ?></td>
                <td><?php echo htmlspecialchars($k['NgayBatDau']); ?></td>
                <td><?php echo htmlspecialchars($k['NgayKetThuc']); ?></td>
                <td><?php echo htmlspecialchars($k['NguoiLap'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($k['MaDonHang'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($k['TrangThai']); ?></td>
                <td>
                    <a href="?xem=<?php echo $k['MaKeHoach']; ?>" class="btn btn-sm btn-info">Xem</a>
                    <a href="?pdf=<?php echo $k['MaKeHoach']; ?>" class="btn btn-sm btn-secondary">Xuất PDF</a>
                    <a href="?pin=<?php echo $k['MaKeHoach']; ?>" class="btn btn-sm btn-warning">Ghim quan trọng</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>