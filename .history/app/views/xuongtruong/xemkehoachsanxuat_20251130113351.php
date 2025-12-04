<?php
ob_start();
?>
<div class="container mt-4">
    <h2><i class="fas me-2"></i>Xem Kế hoạch sản xuất</h2>
    <form method="get" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <label>Kỳ:</label>
                <select name="ky" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="week">Tuần</option>
                    <option value="month">Tháng</option>
                </select>
            </div>
            <div class="col-md-3">
                    <label>Mã kế hoạch:</label>
                    <input type="text" name="makehoach" class="form-control" placeholder="Mã kế hoạch">
            </div>
            <div class="col-md-3">
                <label>Mã đơn hàng:</label>
                    <input type="text" name="donhang" class="form-control" placeholder="Mã đơn hàng">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Lọc</button>
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
                <th>Mã đơn hàng</th>
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
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
        <?php if (!empty($chitietKeHoach)): ?>
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <strong>Chi tiết kế hoạch</strong>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Mã KH:</strong> <?php echo htmlspecialchars($chitietKeHoach['MaKeHoach'] ?? ''); ?></div>
                    <div class="col-md-6"><strong>Tên kế hoạch:</strong> <?php echo htmlspecialchars($chitietKeHoach['TenKeHoach'] ?? ''); ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Ngày bắt đầu:</strong> <?php echo htmlspecialchars($chitietKeHoach['NgayBatDau'] ?? ''); ?></div>
                    <div class="col-md-6"><strong>Ngày kết thúc:</strong> <?php echo htmlspecialchars($chitietKeHoach['NgayKetThuc'] ?? ''); ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Người lập:</strong> <?php echo htmlspecialchars($chitietKeHoach['NguoiLap'] ?? ''); ?></div>
                    <div class="col-md-6"><strong>Mã đơn hàng:</strong> <?php echo htmlspecialchars($chitietKeHoach['MaDonHang'] ?? ''); ?></div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6"><strong>Trạng thái:</strong> <?php echo htmlspecialchars($chitietKeHoach['TrangThai'] ?? ''); ?></div>
                </div>
                <!-- Có thể bổ sung thêm các thông tin chi tiết khác tại đây -->
            </div>
        </div>
        <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>