<?php
// Bảng dưới cùng: Danh sách kế hoạch cấp xưởng đã lập
?>
<div class="card mt-4 mb-4 w-100">
    <div class="card-header gradient-header">
        <h4 class="mb-0 fw-bold" style="letter-spacing:0.5px;">Danh sách kế hoạch cấp xưởng đã lập</h4>
    </div>
    <div class="card-body pb-2 w-100">
        <?php if (!empty($kehoachCapXuongs)): ?>
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Mã KH cấp xưởng</th>
                            <th>Mã kế hoạch tổng</th>
                            <th>Mã phân xưởng</th>
                            <th>Ngày lập</th>
                            <th>Số lượng</th>
                            <th>Công suất dự kiến</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kehoachCapXuongs as $khcx): ?>
                            <tr>
                                <td><?= htmlspecialchars($khcx['MaKHCapXuong'] ?? '') ?></td>
                                <td><?= htmlspecialchars($khcx['MaKeHoach'] ?? '') ?></td>
                                <td><?= htmlspecialchars($khcx['MaPhanXuong'] ?? '') ?></td>
                                <td><?= htmlspecialchars($khcx['NgayLap'] ?? '') ?></td>
                                <td><?= htmlspecialchars($khcx['SoLuong'] ?? '') ?></td>
                                <td><?= htmlspecialchars($khcx['CongSuatDuKien'] ?? '') ?></td>
                                <td><?= htmlspecialchars($khcx['TrangThai'] ?? '') ?></td>
                                <td>
                                    <a href="?edit_khcx=<?= urlencode($khcx['MaKHCapXuong']) ?>" class="btn btn-sm btn-warning">Sửa</a>
                                    <a href="?view_khcx=<?= urlencode($khcx['MaKHCapXuong']) ?>" class="btn btn-sm btn-info">Xem</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>