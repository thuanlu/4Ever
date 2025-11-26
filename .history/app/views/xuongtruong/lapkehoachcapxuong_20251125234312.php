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
                    <div class="col-md-4">
                        <div class="bg-light rounded p-2 mb-2 border">
                            <span class="text-secondary">Công suất sơ bộ:</span><br>
                            <input type="number" class="form-control" name="cong_suat_du_kien" value="<?= htmlspecialchars($_POST['cong_suat_du_kien'] ?? '') ?>" required>
                        </div>
                    </div>
                </div>   
                <?php
                // Hiển thị tất cả sản phẩm của kế hoạch
                if (!empty($kehoach['DanhSachSanPham']) && is_array($kehoach['DanhSachSanPham'])) {
                    foreach ($kehoach['DanhSachSanPham'] as $sp) {
                ?>
                    <div class="col-md-4">
                        <div class="bg-light rounded p-2 mb-2 border">
                            <span class="text-secondary">Ngày lập kế hoạch cấp xưởng:</span><br>
                            <span class="fw-semibold"> <?= $kehoach['NgayLap'] ?? date('Y-m-d') ?> </span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light rounded p-2 mb-2 border">
                            <span class="text-secondary">Ngày bắt đầu:</span><br>
                            <input type="date" class="form-control" name="ngay_bat_dau" value="<?= htmlspecialchars($kehoach['NgayBatDau'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light rounded p-2 mb-2 border">
                            <span class="text-secondary">Ngày kết thúc:</span><br>
                            <input type="date" class="form-control" name="ngay_ket_thuc" value="<?= htmlspecialchars($kehoach['NgayKetThuc'] ?? '') ?>" required>
                        </div>
                    </div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <div class="bg-light rounded p-2 mb-2 border">
                            <span class="text-secondary">Mã sản phẩm:</span><br>
                            <span class="fw-semibold"> <?= $kehoach['MaSanPham'] ?? '' ?> </span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light rounded p-2 mb-2 border">
                            <span class="text-secondary">Tên sản phẩm:</span><br>
                            <span class="fw-semibold"> <?= $kehoach['TenSanPham'] ?? '' ?> </span>
                        </div>
                    </div>
                </div>
                <?php
                    }
                }
                ?>
            </div>
            <div class="border-top pt-4 mt-2">
                <div class="fw-bold mb-2">Kế hoạch cấp xưởng</div>
                <form method="POST" action="<?= BASE_URL ?>xuongtruong/lapkehoachcapxuong/store" id="form-lapkehoach">
                    <input type="hidden" name="ma_kehoach" value="<?= $kehoach['MaKeHoach'] ?>">
                    <?php
                    // Lấy thông tin xưởng trưởng từ session
                    $maXuongTruong = $_SESSION['user']['MaNV'] ?? '';
                    $tenXuongTruong = $_SESSION['user']['HoTen'] ?? '';
                    // Lấy mã phân xưởng từ DB nếu có
                    $maPhanXuong = '';
                    if (!empty($_SESSION['user']['MaNV'])) {
                            $maNV = $_SESSION['user']['MaNV'];
                            $stmt = $db->prepare('SELECT BoPhan FROM nhanvien WHERE MaNV = ? LIMIT 1');
                            $stmt->execute([$maNV]);
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            if ($row && !empty($row['BoPhan'])) {
                                $stmt2 = $db->prepare('SELECT MaPhanXuong FROM phanxuong WHERE TenPhanXuong = ? LIMIT 1');
                                $stmt2->execute([$row['BoPhan']]);
                                $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                            if ($row2 && !empty($row2['MaPhanXuong'])) {
                                $maPhanXuong = $row2['MaPhanXuong'];
                            }
                        }
                    }
                    ?>
                    
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <div class="bg-light rounded p-2 mb-2 border">
                                <span class="text-secondary">Mã xưởng trưởng:</span><br>
                                <span class="fw-semibold"> <?= htmlspecialchars($maXuongTruong) ?> </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded p-2 mb-2 border">
                                <span class="text-secondary">Tên xưởng trưởng:</span><br>
                                <span class="fw-semibold"> <?= htmlspecialchars($tenXuongTruong) ?> </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded p-2 mb-2 border">
                                <span class="text-secondary">Mã phân xưởng:</span><br>
                                <span class="fw-semibold"> <?= htmlspecialchars($maPhanXuong) ?> </span>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <div class="bg-light rounded p-2 mb-2 border">
                                <span class="text-secondary">Ngày lập kế hoạch cấp xưởng:</span><br>
                                <span class="fw-semibold"> <?= date('Y-m-d') ?> </span>
                                <input type="hidden" name="ngay_lap" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded p-2 mb-2 border">
                                <span class="text-secondary">Ngày bắt đầu:</span><br>
                                <span class="fw-semibold"> <?= isset($kehoach['NgayBatDau']) ? date('Y-m-d', strtotime($kehoach['NgayBatDau'])) : '' ?> </span>
                                <input type="hidden" name="ngay_bat_dau" value="<?= isset($kehoach['NgayBatDau']) ? date('Y-m-d', strtotime($kehoach['NgayBatDau'])) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light rounded p-2 mb-2 border">
                                <span class="text-secondary">Ngày kết thúc:</span><br>
                                <span class="fw-semibold"> <?= isset($kehoach['NgayKetThuc']) ? date('Y-m-d', strtotime($kehoach['NgayKetThuc'])) : '' ?> </span>
                                <input type="hidden" name="ngay_ket_thuc" value="<?= isset($kehoach['NgayKetThuc']) ? date('Y-m-d', strtotime($kehoach['NgayKetThuc'])) : '' ?>">
                            </div>
                        </div>
                    </div>
                    <div id="ca-list">
                        <!-- Ca làm việc động -->
                    </div>
                    <button type="button" class="btn btn-outline-primary mb-3" onclick="addCa()"><i class="fa fa-plus"></i> Thêm ca làm việc</button>
                    <div class="mb-3">
                        <label>Công suất sơ bộ</label>
                        <div class="progress">
                            <div id="progress-bar" class="progress-bar bg-success" style="width: 0%">0%</div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Xác nhận</button>
                    <a href="<?= BASE_URL ?>xuongtruong/lapkehoachcapxuong" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>
    </div>
<script>
    // Dữ liệu lấy từ backend (PHP)
    const dayChuyenList = <?php echo json_encode($dayChuyenList ?? []); ?>;
    // Map dây chuyền -> tổ trưởng
    const dayChuyenToTruongMap = {};
    dayChuyenList.forEach(dc => {
        // Nếu controller chưa truyền HoTenToTruong, thử lấy từ toTruongList
        if (dc.MaToTruong) {
            let hoTen = dc.HoTenToTruong;
            if (!hoTen && typeof toTruongList !== 'undefined') {
                const found = toTruongList.find(tt => tt.MaNV === dc.MaToTruong);
                hoTen = found ? found.HoTen : '';
            }
            dayChuyenToTruongMap[dc.MaDayChuyen] = { MaNV: dc.MaToTruong, HoTen: hoTen };
        }
    });
    let caIndex = 0;
    function addCa() {
        caIndex++;
        const dcOptions = dayChuyenList.map(dc => `<option value='${dc.MaDayChuyen}'>${dc.TenDayChuyen}</option>`).join('');
        const caHtml = `<div class="card mb-2" id="ca-item-${caIndex}">
            <div class="card-body row align-items-end">
                <div class="col-md-4">
                    <label>Dây chuyền</label>
                    <select name="ca[${caIndex}][day_chuyen]" class="form-control" onchange="updateToTruong(${caIndex})" id="daychuyen-${caIndex}">
                        ${dcOptions}
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Sản lượng mục tiêu</label>
                    <input type="number" name="ca[${caIndex}][san_luong]" class="form-control" min="0" required oninput="updateProgress(); this.setCustomValidity('');" oninvalid="this.setCustomValidity('Vui lòng nhập sản lượng mục tiêu')">
                </div>
                <div class="col-md-3">
                    <label>Tổ trưởng phụ trách</label>
                    <input type="text" name="ca[${caIndex}][to_truong_name]" class="form-control" id="totruong-name-${caIndex}" readonly>
                    <input type="hidden" name="ca[${caIndex}][to_truong]" id="totruong-id-${caIndex}">
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-danger" onclick="removeCa(${caIndex})"><i class="fa fa-trash"></i></button>
                </div>
            </div>
        </div>`;
        document.getElementById('ca-list').insertAdjacentHTML('beforeend', caHtml);
        updateToTruong(caIndex);
        updateProgress();
    }

    function updateToTruong(idx) {
        const dcSelect = document.getElementById('daychuyen-' + idx);
        const selectedDC = dcSelect.value;
        const toTruong = dayChuyenToTruongMap[selectedDC];
        document.getElementById('totruong-name-' + idx).value = toTruong && toTruong.HoTen ? toTruong.HoTen : 'Không có tổ trưởng';
        document.getElementById('totruong-id-' + idx).value = toTruong && toTruong.MaNV ? toTruong.MaNV : '';
    }
    function removeCa(idx) {
        const el = document.getElementById('ca-item-' + idx);
        if (el) el.remove();
        updateProgress();
    }
    function updateProgress() {
        let total = 0;
        document.querySelectorAll('#ca-list input[type=number]').forEach(input => {
            total += parseInt(input.value) || 0;
        });
        // Lấy sản lượng tổng từ PHP
        const goal = <?= (int)($kehoach['SanLuongTong'] ?? $kehoach['SanLuong'] ?? 0) ?>;
        let percent = goal > 0 ? Math.round(total / goal * 100) : 0;
        percent = Math.min(100, percent);
        let bar = document.getElementById('progress-bar');
        let submitBtn = document.querySelector('button[type=submit]');
        if (total > goal) {
            bar.className = 'progress-bar bg-danger';
            bar.textContent = percent + '% (Vượt tổng)';
            if (submitBtn) submitBtn.disabled = true;
        } else if (total === goal) {
            bar.className = 'progress-bar bg-success';
            bar.textContent = percent + '%';
            if (submitBtn) submitBtn.disabled = false;
        } else {
            bar.className = 'progress-bar bg-warning';
            bar.textContent = percent + '%';
            if (submitBtn) submitBtn.disabled = false;
        }
        bar.style.width = percent + '%';
    }
    // Tự động thêm 1 ca khi mở form
    window.onload = function() { addCa(); };
    </script>
    // ...existing code...

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
            <?php if (!empty($_GET['edit_khcx'])): ?>
            <?php $khcxEdit = null; foreach ($kehoachCapXuongs as $item) { if ($item['MaKHCapXuong'] == $_GET['edit_khcx']) { $khcxEdit = $item; break; } } ?>
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
            <?php $khcxView = null; foreach ($kehoachCapXuongs as $item) { if ($item['MaKHCapXuong'] == $_GET['view_khcx']) { $khcxView = $item; break; } } ?>
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
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';