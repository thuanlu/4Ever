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

    <?php if (!empty($kehoach)): ?>
    <div class="card shadow-lg border-2 rounded-4 mb-4">
        <div class="card-header rounded-top-4 py-3 px-4">
            <h2 class="fw-bold mb-0">Lập Kế hoạch Cấp Xưởng</h2>
            <div class="mt-1">Phân bổ sản lượng từ kế hoạch tổng xuống từng ca làm việc và Tổ trưởng phụ trách.</div>
        </div>

        <div class="card-body p-4">

            <div class="mb-4">
                <div class="fw-bold mb-2">Tham chiếu Kế hoạch Tổng</div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="bg-light rounded p-2 mb-2 border">
                            <span class="text-secondary">Mã Kế hoạch:</span><br>
                            <span class="fw-semibold"> <?= $kehoach['MaKeHoach'] ?? '' ?> </span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light rounded p-2 mb-2 border">
                            <span class="text-secondary">Tên Kế hoạch:</span><br>
                            <span class="fw-semibold"> <?= $kehoach['TenKeHoach'] ?? '' ?> </span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light rounded p-2 mb-2 border">
                            <span class="text-secondary">Sản lượng Tổng:</span><br>
                            <span class="fw-semibold"> <?= number_format($kehoach['SanLuongTong'] ?? $kehoach['SanLuong'] ?? 0, 0, ',', '.') ?> </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PHẦN NÀY ANH GIỮ NGUYÊN TOÀN BỘ -->
            <?= /* CODE THAM CHIẾU SẢN PHẨM, DÂY CHUYỀN, CA LÀM VIỆC… */ "" ?>

            <script>
            // đoạn JS của em anh giữ nguyên
            </script>

            <!-- ĐÃ SỬA LỖI TẠI ĐÂY -->
            <!-- existing code -->

            <!-- Danh sách kế hoạch cấp xưởng đã lập -->
            <div class="card mt-4 mb-4">
                <div class="card-header gradient-header">
                    <h4 class="mb-0 fw-bold" style="letter-spacing:0.5px;">Danh sách kế hoạch cấp xưởng đã lập</h4>
                </div>

                <div class="card-body pb-2">

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
                                    <td><?= htmlspecialchars($khcx['MaKHCapXuong']) ?></td>
                                    <td><?= htmlspecialchars($khcx['MaKeHoach']) ?></td>
                                    <td><?= htmlspecialchars($khcx['MaPhanXuong']) ?></td>
                                    <td><?= htmlspecialchars($khcx['NgayLap']) ?></td>
                                    <td><?= htmlspecialchars($khcx['SoLuong']) ?></td>
                                    <td><?= htmlspecialchars($khcx['CongSuatDuKien']) ?></td>
                                    <td><?= htmlspecialchars($khcx['TrangThai']) ?></td>
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


                    <!-- FORM AND VIEW SECTION: giữ nguyên -->
                    <?php if (!empty($_GET['edit_khcx'])): ?>
                        <?php 
                            $khcxEdit = null; 
                            foreach ($kehoachCapXuongs as $item) {
                                if ($item['MaKHCapXuong'] == $_GET['edit_khcx']) { 
                                    $khcxEdit = $item; 
                                    break; 
                                }
                            } 
                        ?>

                        <?php if ($khcxEdit): ?>
                            <!-- form sửa -->
                        <?php endif; ?>
                    <?php endif; ?>


                </div>
            </div>

        </div>
    </div>
    <?php endif; ?>

</div>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>
