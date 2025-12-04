<?php
ob_start();
?>
<div class="container mt-4">
    <!-- <h2 class="text-primary fw-bold">Lập kế hoạch cấp xưởng</h2> -->
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
                        <a href="?kehoach=<?= $kh['MaKeHoach'] ?>" class="btn btn-primary btn-sm">Lập kế hoạch cấp xưởng</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">Không có kế hoạch tổng đã duyệt cho xưởng của bạn.</div>
    <?php endif; ?>

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
            <div class="border-top pt-4 mt-2">
                <form method="POST" action="<?= BASE_URL ?>xuongtruong/lapkehoachcapxuong/store" id="form-lapkehoach">
                    <input type="hidden" name="ma_kehoach" value="<?= $kehoach['MaKeHoach'] ?>">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="ngay_lap" class="form-label fw-semibold">Ngày lập kế hoạch cấp xưởng</label>
                            <input type="date" class="form-control" name="ngay_lap" id="ngay_lap" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="ngay_bat_dau" class="form-label fw-semibold">Ngày bắt đầu</label>
                            <input type="date" class="form-control" name="ngay_bat_dau" id="ngay_bat_dau" value="<?= isset($kehoach['NgayBatDau']) ? date('Y-m-d', strtotime($kehoach['NgayBatDau'])) : '' ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="ngay_ket_thuc" class="form-label fw-semibold">Ngày kết thúc</label>
                            <input type="date" class="form-control" name="ngay_ket_thuc" id="ngay_ket_thuc" value="<?= isset($kehoach['NgayKetThuc']) ? date('Y-m-d', strtotime($kehoach['NgayKetThuc'])) : '' ?>" readonly>
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
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>