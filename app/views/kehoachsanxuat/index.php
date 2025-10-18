<?php
ob_start();
?>
<div class="container mt-4">
    <h2>Danh sách kế hoạch sản xuất</h2>
    <a href="<?php echo BASE_URL; ?>kehoachsanxuat/create" class="btn btn-primary mb-3">Tạo kế hoạch mới</a>
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
                <td><?php echo htmlspecialchars($k['NguoiLap']); ?></td>
                <td><?php echo htmlspecialchars($k['TenDonHang']); ?></td>
                <td><?php echo htmlspecialchars($k['TrangThai']); ?></td>
                <td>
                    <a href="<?php echo BASE_URL; ?>kehoachsanxuat/edit/<?php echo $k['MaKeHoach']; ?>" class="btn btn-sm btn-warning">Sửa</a>
                    <a href="<?php echo BASE_URL; ?>kehoachsanxuat/delete/<?php echo $k['MaKeHoach']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa kế hoạch này?');">Xóa</a>
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
