<?php
ob_start();
?>
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header gradient-header">
            <h4 class="mb-0 fw-bold" style="letter-spacing:0.5px;">Xem Kế hoạch sản xuất</h4>
        </div>
        <style>
            .gradient-header {
                background: linear-gradient(90deg, #7b8cff 0%, #7b5fd4 100%);
                color: #fff;
                border-radius: 12px 12px 0 0;
                font-weight: bold;
                padding: 1.1rem 1.5rem;
            }
        </style>
        <div class="card-body pb-2">
            <form method="get" class="row g-2 align-items-center mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Kỳ:</label>
                    <select name="ky" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="week">Tuần</option>
                        <option value="month">Tháng</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Mã kế hoạch:</label>
                    <input type="text" name="makehoach" class="form-control" placeholder="Mã kế hoạch">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Mã đơn hàng:</label>
                    <input type="text" name="donhang" class="form-control" placeholder="Mã đơn hàng">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary w-100 fw-bold">Lọc</button>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
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
                            <td><?= htmlspecialchars($k['MaKeHoach']) ?></td>
                            <td><?= htmlspecialchars($k['TenKeHoach']) ?></td>
                            <td><?= htmlspecialchars($k['NgayBatDau']) ?></td>
                            <td><?= htmlspecialchars($k['NgayKetThuc']) ?></td>
                            <td><?= htmlspecialchars($k['NguoiLap'] ?? '') ?></td>
                            <td><?= htmlspecialchars($k['MaDonHang'] ?? '') ?></td>
                            <td>
                                <?php
                                $trangThai = $k['TrangThai'] ?? 'Đã duyệt';
                                if ($trangThai === 'Đã duyệt' || $trangThai === 'daduyet') {
                                    echo '<span class="badge bg-success">Đã duyệt</span>';
                                } elseif ($trangThai === 'Chờ duyệt' || $trangThai === 'choduyet') {
                                    echo '<span class="badge bg-warning text-dark">Chờ duyệt</span>';
                                } else {
                                    echo '<span class="badge bg-secondary">' . htmlspecialchars($trangThai) . '</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="?xem=<?= $k['MaKeHoach'] ?>" class="btn btn-sm btn-info">Xem</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php if (!empty($kehoach) && !empty($chitietkehoach)): ?>
        <?php include APP_PATH . '/views/xuongtruong/xemchitietkehoachsanxuat.php'; ?>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>