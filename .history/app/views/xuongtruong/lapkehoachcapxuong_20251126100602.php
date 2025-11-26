<?php
ob_start();
// Kiểm tra session đăng nhập, nếu chưa có thì chuyển hướng về trang login
if (empty($_SESSION['user'])) {
    header('Location: /4Ever/login');
    exit;
}
?>

<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header gradient-header">
            <h4 class="mb-0 fw-bold" style="letter-spacing:0.5px;">Danh sách kế hoạch</h4>
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
    <div class="border-top pt-4 mt-2">
        <div id="form-kehoach-capxuong" class="card shadow-lg rounded-4 p-4 w-100 d-none" style="background: #fff;">
            <div class="card-header gradient-header rounded-4 mb-4" style="padding: 2rem 2rem;">
                <h2 class="fw-bold mb-0 text-white" style="font-size:2.2rem;">Lập Kế hoạch Cấp Xưởng</h2>
                <div class="mt-1 text-white" style="font-size:1.1rem;">Phân bổ sản lượng từ kế hoạch tổng xuống từng ca làm việc và Tổ trưởng phụ trách.</div>
            </div>
            <form method="POST" action="<?= BASE_URL ?>xuongtruong/lapkehoachcapxuong/store" id="form-lapkehoach">
                <input type="hidden" name="ma_kehoach" value="<?= $kehoach['MaKeHoach'] ?>">
                <div class="mb-3 fw-bold" style="font-size:1.1rem;">Tham chiếu Kế hoạch Tổng</div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Mã Kế hoạch:</label>
                        <input type="text" class="form-control rounded-3" value="<?= $kehoach['MaKeHoach'] ?? '' ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tên Kế hoạch:</label>
                        <input type="text" class="form-control rounded-3" value="<?= $kehoach['TenKeHoach'] ?? '' ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sản lượng Tổng:</label>
                        <input type="text" class="form-control rounded-3 fw-bold" value="<?= number_format($kehoach['SanLuongTong'] ?? $kehoach['SanLuong'] ?? 0, 0, ',', '.') ?>" readonly>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Ngày lập kế hoạch cấp xưởng</label>
                        <input type="date" class="form-control rounded-3" name="ngay_lap" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ngày bắt đầu</label>
                        <input type="date" class="form-control rounded-3" name="ngay_bat_dau" value="<?= isset($kehoach['NgayBatDau']) ? date('Y-m-d', strtotime($kehoach['NgayBatDau'])) : '' ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ngày kết thúc</label>
                        <input type="date" class="form-control rounded-3" name="ngay_ket_thuc" value="<?= isset($kehoach['NgayKetThuc']) ? date('Y-m-d', strtotime($kehoach['NgayKetThuc'])) : '' ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="card rounded-4 p-3 shadow-sm border-0" style="background:#f8f9fa;">
                        <div class="row g-3 align-items-end" id="ca-list">
                            <!-- Ca làm việc động -->
                        </div>
                        <button type="button" class="btn btn-outline-primary mt-2" style="width:fit-content;" onclick="addCa()"><i class="fa fa-plus"></i> Thêm ca làm việc</button>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Công suất sơ bộ</label>
                    <div class="progress" style="height:32px;">
                        <div id="progress-bar" class="progress-bar bg-success" style="width: 0%; font-size:1.1rem;">0%</div>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-end mt-3">
                    <button type="submit" class="btn btn-success px-4 py-2 fw-bold" style="font-size:1.1rem;">Xác nhận</button>
                    <a href="<?= BASE_URL ?>xuongtruong/lapkehoachcapxuong" class="btn btn-secondary px-4 py-2 fw-bold" style="font-size:1.1rem;">Hủy</a>
                </div>
            </form>
        </div>
    </div>
<script>
    // Chỉ hiện form khi bấm nút thao tác
    document.addEventListener('DOMContentLoaded', function() {
        // Ẩn form khi load
        var formDiv = document.getElementById('form-kehoach-capxuong');
        if (formDiv) formDiv.classList.add('d-none');

        // Gắn sự kiện cho các nút thao tác
        document.querySelectorAll('a.btn-primary.btn-sm').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                // Lấy mã kế hoạch từ href
                var url = new URL(btn.href, window.location.origin);
                var maKeHoach = url.searchParams.get('kehoach');
                // Chuyển hướng trang với tham số mã kế hoạch
                window.location.search = '?kehoach=' + maKeHoach;
            });
        });
    });
    // ...existing JS code cho addCa, updateToTruong, removeCa, updateProgress ...
</script>


<!-- Danh sách kế hoạch cấp xưởng đã lập -->
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
                    if ($item['MaKHCapXuong'] == $_GET['edit_khcx']) {
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
                    if ($item['MaKHCapXuong'] == $_GET['view_khcx']) {
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
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>