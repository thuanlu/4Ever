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
    <div class="pt-4 mt-2">
        <div class="card mb-4">
            <div class="card-header gradient-header">
                <h4 class="mb-0 fw-bold" style="letter-spacing:0.5px;">Lập Kế hoạch Cấp Xưởng</h4>
                <div class="mt-1" style="font-size:1.1rem; color:#fff;">Phân bổ sản lượng từ kế hoạch tổng xuống từng ca làm việc và Tổ trưởng phụ trách.</div>
            </div>
            <form method="POST" action="<?= BASE_URL ?>xuongtruong/lapkehoachcapxuong/store" id="form-lapkehoach" class="p-3">
                <input type="hidden" name="ma_kehoach" value="<?= $kehoach['MaKeHoach'] ?>">
                <div class="mb-3">
                    <h5 class="fw-bold mb-3" style="font-size:1.15rem;">Tham chiếu Kế hoạch Tổng</h5>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 rounded-3 p-3 h-100 bg-light">
                                <div class="fw-bold text-secondary mb-1">Mã Kế hoạch</div>
                                <div class="fs-5 fw-bold text-primary"><?= $kehoach['MaKeHoach'] ?? '' ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 rounded-3 p-3 h-100 bg-light">
                                <div class="fw-bold text-secondary mb-1">Tên Kế hoạch</div>
                                <div class="fs-5 fw-bold text-primary"><?= $kehoach['TenKeHoach'] ?? '' ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 rounded-3 p-3 h-100 bg-light">
                                <div class="fw-bold text-secondary mb-1">Sản lượng Tổng của phân xưởng</div>
                                <?php
                                // Tính tổng sản lượng của phân xưởng hiện tại
                                $sanLuongPhanXuong = 0;
                                if (!empty($kehoach['SanPhamList']) && is_array($kehoach['SanPhamList'])) {
                                    foreach ($kehoach['SanPhamList'] as $sp) {
                                        $sanLuongPhanXuong += (int)($sp['SanLuongMucTieu'] ?? 0);
                                    }
                                }
                                ?>
                                <div class="fs-5 fw-bold text-success"><?= number_format($sanLuongPhanXuong, 0, ',', '.') ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">Ngày bắt đầu</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                    <input type="date" class="form-control" name="ngay_bat_dau" value="<?= isset($kehoach['NgayBatDau']) ? date('Y-m-d', strtotime($kehoach['NgayBatDau'])) : '' ?>" required readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold">Ngày kết thúc</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                    <input type="date" class="form-control" name="ngay_ket_thuc" value="<?= isset($kehoach['NgayKetThuc']) ? date('Y-m-d', strtotime($kehoach['NgayKetThuc'])) : '' ?>" required readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <h5 class="fw-bold mb-3" style="font-size:1.15rem;">Lập kế hoạch cấp xưởng</h5>
                <div class="row g-3 mb-3">
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label fw-bold">Mã kế hoạch xưởng</label>
                            <?php
                                // Sinh mã kế hoạch xưởng tự động: CX-[ngày]-[random]
                                $autoMaKeHoachXuong = 'KCX-' . date('Ymd') . '-' . rand(100,999);
                            ?>
                            <input type="text" class="form-control" name="ma_kehoach_xuong" value="<?= htmlspecialchars($autoMaKeHoachXuong) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label fw-bold">Tên xưởng trưởng</label>
                            <?php
                                $tenXuongTruong = $_SESSION['user']['HoTen'] ?? '';
                            ?>
                            <input type="text" class="form-control" name="ten_xuong_truong" value="<?= htmlspecialchars($tenXuongTruong) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label fw-bold">Ngày lập kế hoạch cấp xưởng</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                <input type="date" class="form-control" name="ngay_lap" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label fw-bold mb-2">Danh sách sản phẩm</label>
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Mã sản phẩm</th>
                                            <th>Tên sản phẩm</th>
                                            <th>Số lượng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Nếu có nhiều sản phẩm, hiển thị tất cả
                                        if (!empty($kehoach['SanPhamList']) && is_array($kehoach['SanPhamList'])) {
                                            foreach ($kehoach['SanPhamList'] as $sp) {
                                                echo '<tr>';
                                                echo '<td>' . htmlspecialchars($sp['MaSanPham'] ?? '') . '</td>';
                                                echo '<td>' . htmlspecialchars($sp['TenSanPham'] ?? '') . '</td>';
                                                echo '<td>' . htmlspecialchars($sp['SanLuongMucTieu'] ?? $sp['SoLuong'] ?? '') . '</td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            // Nếu chỉ có một sản phẩm, hiển thị một dòng
                                            echo '<tr>';
                                            echo '<td>' . htmlspecialchars($kehoach['MaSanPham'] ?? '') . '</td>';
                                            echo '<td>' . htmlspecialchars($kehoach['TenSanPham'] ?? '') . '</td>';
                                            echo '<td>' . htmlspecialchars($kehoach['SoLuongSanPham'] ?? '') . '</td>';
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <div class="mb-3">
                    <label class="form-label fw-bold mb-2">Danh sách ca làm việc</label>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Dây chuyền</th>
                                    <th>Sản lượng mục tiêu</th>
                                    <th>Tổ trưởng phụ trách</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="ca-list">
                                <!-- Ca làm việc động -->
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary mt-2 px-4 py-2 fw-bold" style="width:fit-content;" onclick="addCa()"><i class="fa fa-plus"></i> Thêm ca làm việc</button>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Công suất sơ bộ</label>
                    <div class="progress rounded-3" style="height:32px;">
                        <div id="progress-bar" class="progress-bar bg-success" style="width: 0%; font-size:1.1rem; border-radius: 0.5rem;">0%</div>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end mt-3">
                    <button type="submit" class="btn btn-success px-4 py-2 fw-bold" style="font-size:1.1rem;">Xác nhận</button>
                    <a href="<?= BASE_URL ?>xuongtruong/lapkehoachcapxuong" class="btn btn-secondary px-4 py-2 fw-bold" style="font-size:1.1rem;">Hủy</a>
                </div>
            </form>
        </div>
    </div>
    
<?php if (!empty($kehoach)): ?>
    <script>
        // Dữ liệu lấy từ backend (PHP)
        // Lấy đúng mã phân xưởng của xưởng trưởng
        const phanXuongList = <?php echo json_encode($phanXuongList ?? []); ?>;
        const maXuongTruong = <?php echo json_encode($_SESSION['user']['MaNV'] ?? ''); ?>;
        let maPhanXuong = '';
        if (Array.isArray(phanXuongList) && maXuongTruong) {
            // So sánh kiểu dữ liệu, ép về string để tránh lỗi so sánh
            const found = phanXuongList.find(px => String(px.MaXuongTruong) === String(maXuongTruong));
            if (found) {
                maPhanXuong = found.MaPhanXuong;
            } else {
                // Nếu không tìm thấy, lấy phân xưởng đầu tiên làm mặc định
                if (phanXuongList.length > 0) {
                    maPhanXuong = phanXuongList[0].MaPhanXuong;
                }
            }
        }
        console.log('DEBUG MaXuongTruong:', maXuongTruong);
        console.log('DEBUG maPhanXuong:', maPhanXuong);
        console.log('DEBUG phanXuongList:', phanXuongList);
        const dayChuyenList = <?php echo json_encode($dayChuyenList ?? []); ?>;
        let filteredDayChuyenList = [];
            if (Array.isArray(dayChuyenList) && maPhanXuong) {
                filteredDayChuyenList = dayChuyenList.filter(dc => dc.MaPhanXuong == maPhanXuong);
            }
            // Debug: In ra danh sách dây chuyền lọc được
            console.log('maXuongTruong:', maXuongTruong);
            console.log('maPhanXuong:', maPhanXuong);
            console.log('filteredDayChuyenList:', filteredDayChuyenList);
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
                dayChuyenToTruongMap[dc.MaDayChuyen] = {
                    MaNV: dc.MaToTruong,
                    HoTen: hoTen
                };
            }
        });
        let caIndex = 0;

        function addCa() {
            caIndex++;
            // Lấy các dây chuyền đã chọn ở các ca trước
            let usedDayChuyen = [];
            document.querySelectorAll('[id^=daychuyen-]').forEach(sel => {
                if (sel.value) usedDayChuyen.push(sel.value);
            });
            let dcOptions = '';
            if (filteredDayChuyenList.length > 0) {
                dcOptions = filteredDayChuyenList
                    .filter(dc => !usedDayChuyen.includes(dc.MaDayChuyen))
                    .map(dc => `<option value='${dc.MaDayChuyen}'>${dc.TenDayChuyen}</option>`)
                    .join('');
            }
            if (!dcOptions) {
                dcOptions = `<option value="">Không có dây chuyền phù hợp</option>`;
            }
            const caHtml = `<tr id="ca-item-${caIndex}">
                <td>
                    <select name="ca[${caIndex}][day_chuyen]" class="form-control" onchange="updateToTruong(${caIndex}); updateDayChuyenOptions();" id="daychuyen-${caIndex}">
                        ${dcOptions}
                    </select>
                </td>
                <td>
                    <input type="number" name="ca[${caIndex}][san_luong]" class="form-control" min="0" required oninput="updateProgress(); this.setCustomValidity('');" oninvalid="this.setCustomValidity('Vui lòng nhập sản lượng mục tiêu')">
                </td>
                <td>
                    <input type="text" name="ca[${caIndex}][to_truong_name]" class="form-control" id="totruong-name-${caIndex}" readonly>
                    <input type="hidden" name="ca[${caIndex}][to_truong]" id="totruong-id-${caIndex}">
                </td>
                <td class="text-end align-middle">
                    <button type="button" class="btn btn-danger" onclick="removeCa(${caIndex}); updateDayChuyenOptions();"><i class="fa fa-trash"></i></button>
                </td>
            </tr>`;
            document.getElementById('ca-list').insertAdjacentHTML('beforeend', caHtml);
            updateToTruong(caIndex);
            updateProgress();
            updateDayChuyenOptions();
        }

        // Hàm cập nhật lại các option dây chuyền cho tất cả ca
        function updateDayChuyenOptions() {
            let selected = [];
            document.querySelectorAll('[id^=daychuyen-]').forEach(sel => {
                if (sel.value) selected.push(sel.value);
            });
            document.querySelectorAll('[id^=daychuyen-]').forEach(sel => {
                let idx = sel.id.split('-')[1];
                let currentValue = sel.value;
                let options = filteredDayChuyenList
                    .filter(dc => !selected.includes(dc.MaDayChuyen) || dc.MaDayChuyen === currentValue)
                    .map(dc => `<option value='${dc.MaDayChuyen}' ${dc.MaDayChuyen === currentValue ? 'selected' : ''}>${dc.TenDayChuyen}</option>`)
                    .join('');
                if (!options) options = `<option value="">Không có dây chuyền phù hợp</option>`;
                sel.innerHTML = options;
            });
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
            updateDayChuyenOptions();
        }

        function updateProgress() {
            let total = 0;
            document.querySelectorAll('#ca-list input[type=number]').forEach(input => {
                total += parseInt(input.value) || 0;
            });
            // Lấy sản lượng tổng của phân xưởng hiện tại từ PHP
            const goal = <?php
                $sanLuongPhanXuong = 0;
                if (!empty($kehoach['SanPhamList']) && is_array($kehoach['SanPhamList'])) {
                    foreach ($kehoach['SanPhamList'] as $sp) {
                        $sanLuongPhanXuong += (int)($sp['SanLuongMucTieu'] ?? 0);
                    }
                }
                echo (int)$sanLuongPhanXuong;
            ?>;
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
        window.onload = function() {
            addCa();
        };
    </script>
<?php endif; ?>


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