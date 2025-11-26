<?php
// Đảm bảo các biến cần thiết (như $kehoachCapXuongs, $_GET['edit_khcx'], $_GET['view_khcx']) đã được định nghĩa
// Logic kiểm tra session/ob_start/include layout nên nằm ở Controller chính
?>
<div class="container mt-4">
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
                
                <?php if (!empty($_GET['edit_khcx'])): ?>
                    <?php $khcxEdit = null;
                    foreach ($kehoachCapXuongs as $item) {
                        if (($item['MaKHCapXuong'] ?? '') == $_GET['edit_khcx']) {
                            $khcxEdit = $item;
                            break;
                        }
                    } ?>
                    <?php if ($khcxEdit): ?>
                        <div class="mt-4 p-4 border rounded bg-light">
                            <h5 class="fw-bold mb-3">Sửa kế hoạch cấp xưởng</h5>
                            <form method="post" action="/4Ever/xuongtruong/suakehoachcapxuong">
                                <input type="hidden" name="makhcx" value="<?= htmlspecialchars($khcxEdit['MaKHCapXuong']) ?>">
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <label>Mã kế hoạch tổng</label>
                                        <input type="text" class="form-control" name="ma_kehoach" value="<?= htmlspecialchars($khcxEdit['MaKeHoach']) ?>" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Mã phân xưởng</label>
                                        <input type="text" class="form-control" name="ma_phan_xuong" value="<?= htmlspecialchars($khcxEdit['MaPhanXuong']) ?>" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Ngày lập</label>
                                        <input type="text" class="form-control" name="ngay_lap" value="<?= htmlspecialchars($khcxEdit['NgayLap']) ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <label>Số lượng</label>
                                        <input type="number" class="form-control" name="so_luong" value="<?= htmlspecialchars($khcxEdit['SoLuong']) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Công suất dự kiến</label>
                                        <input type="number" class="form-control" name="cong_suat_du_kien" value="<?= htmlspecialchars($khcxEdit['CongSuatDuKien']) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Trạng thái</label>
                                        <input type="text" class="form-control" name="trang_thai" value="<?= htmlspecialchars($khcxEdit['TrangThai']) ?>">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (!empty($_GET['view_khcx'])): ?>
                    <?php $khcxView = null;
                    foreach ($kehoachCapXuongs as $item) {
                        if (($item['MaKHCapXuong'] ?? '') == $_GET['view_khcx']) {
                            $khcxView = $item;
                            break;
                        }
                    } ?>
                    <?php if ($khcxView): ?>
                        <div class="mt-4 p-4 border rounded bg-light">
                            <h5 class="fw-bold mb-3">Chi tiết kế hoạch cấp xưởng</h5>
                            <div><b>Mã KH cấp xưởng:</b> <?= htmlspecialchars($khcxView['MaKHCapXuong']) ?></div>
                            <div><b>Mã kế hoạch tổng:</b> <?= htmlspecialchars($khcxView['MaKeHoach']) ?></div>
                            <div><b>Mã phân xưởng:</b> <?= htmlspecialchars($khcxView['MaPhanXuong']) ?></div>
                            <div><b>Ngày lập:</b> <?= htmlspecialchars($khcxView['NgayLap']) ?></div>
                            <div><b>Số lượng:</b> <?= htmlspecialchars($khcxView['SoLuong']) ?></div>
                            <div><b>Công suất dự kiến:</b> <?= htmlspecialchars($khcxView['CongSuatDuKien']) ?></div>
                            <div><b>Trạng thái:</b> <?= htmlspecialchars($khcxView['TrangThai']) ?></div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</div>