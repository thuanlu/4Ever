<?php
// Bảng trên cùng: Danh sách kế hoạch
?>
<div class="card mb-4">
    <div class="card-header gradient-header">
        <h4 class="mb-0 fw-bold" style="letter-spacing:0.5px;">Danh sách kế hoạch</h4>
    </div>
    <div class="card-body pb-2">
        <form class="row g-2 align-items-center mb-3" method="get">
            <div class="col-auto flex-grow-1">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm mã kế hoạch, tên kế hoạch, đơn hàng..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Tìm kiếm</button>
            </div>
        </form>
        <?php if (!empty($kehoachs)): ?>
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Mã kế hoạch</th>
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
                        <?php foreach ($kehoachs as $kh): ?>
                            <tr>
                                <td><?= htmlspecialchars($kh['MaKeHoach']) ?></td>
                                <td><?= htmlspecialchars($kh['TenKeHoach']) ?></td>
                                <td><?= htmlspecialchars($kh['NgayBatDau']) ?></td>
                                <td><?= htmlspecialchars($kh['NgayKetThuc']) ?></td>
                                <td><?= htmlspecialchars($kh['NguoiLap']) ?></td>
                                <td><?= htmlspecialchars($kh['TenDonHang']) ?></td>
                                <td>
                                    <?php
                                    $trangThai = $kh['TrangThai'] ?? 'Đã duyệt';
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
                                    <a href="?kehoach=<?= urlencode($kh['MaKeHoach']) ?>" class="btn btn-primary btn-sm">Lập kế hoạch cấp xưởng</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning mt-3">Không có kế hoạch tổng đã duyệt cho xưởng của bạn.</div>
        <?php endif; ?>
    </div>
</div>