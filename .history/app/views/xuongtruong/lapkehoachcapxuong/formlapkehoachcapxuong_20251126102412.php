<?php
// Đảm bảo các biến cần thiết (như $kehoach, $dayChuyenList) đã được định nghĩa
// Logic kiểm tra session/ob_start/include layout nên nằm ở Controller chính
if (empty($kehoach)) {
    // Nếu không có kế hoạch tổng được chọn, không hiển thị form
    // Trong môi trường MVC, Controller sẽ quyết định điều này.
    return;
}
?>

<div class="container mt-4">
    <div class="border-top pt-4 mt-2">
        <div class="card shadow-lg rounded-4 p-4 w-100" style="background: #fff;">
            <div class="card-header gradient-header rounded-4 mb-4" style="padding: 2rem 2rem;">
                <h2 class="fw-bold mb-0 text-white" style="font-size:2.2rem;">Lập Kế hoạch Cấp Xưởng</h2>
                <div class="mt-1 text-white" style="font-size:1.1rem;">Phân bổ sản lượng từ kế hoạch tổng xuống từng ca làm việc và Tổ trưởng phụ trách.</div>
            </div>
            <form method="POST" action="<?= BASE_URL ?>xuongtruong/lapkehoachcapxuong/store" id="form-lapkehoach">
                <input type="hidden" name="ma_kehoach" value="<?= $kehoach['MaKeHoach'] ?>">
                
                <div class="mb-3 fw-bold" style="font-size:1.1rem;">Tham chiếu Kế hoạch Tổng</div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="bg-light rounded-3 p-3" style="border:1px solid #eee;">
                            <div class="form-label fw-bold mb-1" style="font-size:1rem;">Mã Kế hoạch:</div>
                            <div class="fw-normal" style="font-size:1.1rem;"><?= $kehoach['MaKeHoach'] ?? '' ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light rounded-3 p-3" style="border:1px solid #eee;">
                            <div class="form-label fw-bold mb-1" style="font-size:1rem;">Tên Kế hoạch:</div>
                            <div class="fw-normal" style="font-size:1.1rem;"><?= $kehoach['TenKeHoach'] ?? '' ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="bg-light rounded-3 p-3" style="border:1px solid #eee;">
                            <div class="form-label fw-bold mb-1" style="font-size:1rem;">Sản lượng Tổng:</div>
                            <div class="fw-bold text-primary" style="font-size:1.2rem;"><?= number_format($kehoach['SanLuongTong'] ?? $kehoach['SanLuong'] ?? 0, 0, ',', '.') ?></div>
                        </div>
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
</div>

<script>
    // Dữ liệu lấy từ backend (PHP)
    const dayChuyenList = <?php echo json_encode($dayChuyenList ?? []); ?>;
    const goal = <?= (int)($kehoach['SanLuongTong'] ?? $kehoach['SanLuong'] ?? 0) ?>;
    
    // Map dây chuyền -> tổ trưởng (Giữ nguyên logic từ code gốc)
    const dayChuyenToTruongMap = {};
    dayChuyenList.forEach(dc => {
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

    function updateProgress() {
        let total = 0;
        document.querySelectorAll('#ca-list input[type=number]').forEach(input => {
            total += parseInt(input.value) || 0;
        });
        
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

    function updateToTruong(idx) {
        const dcSelect = document.getElementById('daychuyen-' + idx);
        if (!dcSelect) return;
        const selectedDC = dcSelect.value;
        const toTruong = dayChuyenToTruongMap[selectedDC];
        document.getElementById('totruong-name-' + idx).value = toTruong && toTruong.HoTen ? toTruong.HoTen : 'Không có tổ trưởng';
        document.getElementById('totruong-id-' + idx).value = toTruong && toTruong.MaNV ? toTruong.MaNV : '';
    }

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

    function removeCa(idx) {
        const el = document.getElementById('ca-item-' + idx);
        if (el) el.remove();
        updateProgress();
    }

    // Tự động thêm 1 ca khi mở form
    window.addEventListener('load', function() {
        if (document.getElementById('form-lapkehoach')) {
            addCa();
        }
    });
</script>