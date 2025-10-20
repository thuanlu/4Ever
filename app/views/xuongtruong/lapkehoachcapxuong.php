<?php
ob_start();
?>
<div class="container mt-4">
    <h2><?= $pageTitle ?></h2>
    <?php if (!empty($kehoachs)): ?>
        <table class="table table-bordered mb-4">
            <thead>
                <tr>
                    <th>Mã kế hoạch</th>
                    <th>Tên kế hoạch</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Người lập</th>
                    <th>Đơn hàng</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kehoachs as $kh): ?>
                <tr>
                    <td><?= $kh['MaKeHoach'] ?></td>
                    <td><?= $kh['TenKeHoach'] ?></td>
                    <td><?= $kh['NgayBatDau'] ?></td>
                    <td><?= $kh['NgayKetThuc'] ?></td>
                    <td><?= $kh['NguoiLap'] ?></td>
                    <td><?= $kh['TenDonHang'] ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>xuongtruong/lapkehoachcapxuong/create/<?= $kh['MaKeHoach'] ?>" class="btn btn-primary btn-sm">Lập kế hoạch cấp xưởng</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">Không có kế hoạch tổng đã duyệt cho xưởng của bạn.</div>
    <?php endif; ?>

    <?php if (!empty($kehoach)): ?>
    <div class="card mt-4">
        <div class="card-header bg-info text-white">Lập kế hoạch cấp xưởng cho kế hoạch: <strong><?= $kehoach['TenKeHoach'] ?></strong></div>
        <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>xuongtruong/lapkehoachcapxuong/store">
                <input type="hidden" name="ma_kehoach" value="<?= $kehoach['MaKeHoach'] ?>">
                <div class="mb-3">
                    <label>Dây chuyền</label>
                    <select name="day_chuyen" class="form-control">
                        <!-- TODO: Load danh sách dây chuyền từ DB -->
                        <option value="DC01">DC01</option>
                        <option value="DC02">DC02</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Ca làm việc</label>
                    <select name="ca" class="form-control">
                        <option value="Sáng">Sáng</option>
                        <option value="Chiều">Chiều</option>
                        <option value="Tối">Tối</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Sản lượng mục tiêu</label>
                    <input type="number" name="san_luong" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Tổ trưởng phụ trách</label>
                    <select name="to_truong" class="form-control">
                        <!-- TODO: Load danh sách tổ trưởng từ DB -->
                        <option value="TT01">TT01</option>
                        <option value="TT02">TT02</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Công suất sơ bộ</label>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: 80%">Đủ</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Xác nhận</button>
                <a href="<?= BASE_URL ?>xuongtruong/lapkehoachcapxuong" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>