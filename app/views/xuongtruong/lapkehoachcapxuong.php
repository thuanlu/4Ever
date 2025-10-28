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
    // Dữ liệu mẫu, sẽ lấy từ backend
    const dayChuyenList = [
        { value: 'DC01', text: 'DC01' },
        { value: 'DC02', text: 'DC02' }
    ];
    const toTruongList = [
        { value: 'TT01', text: 'TT01' },
        { value: 'TT02', text: 'TT02' }
    ];
    const caList = ['Sáng', 'Tối'];

    let caIndex = 0;
    function addCa() {
        caIndex++;
        const caHtml = `<div class="card mb-2" id="ca-item-${caIndex}">
            <div class="card-body row align-items-end">
                <div class="col-md-3">
                    <label>Dây chuyền</label>
                    <select name="ca[${caIndex}][day_chuyen]" class="form-control">
                        ${dayChuyenList.map(dc => `<option value='${dc.value}'>${dc.text}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Ca làm việc</label>
                    <select name="ca[${caIndex}][ca]" class="form-control">
                        ${caList.map(ca => `<option value='${ca}'>${ca}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Sản lượng mục tiêu</label>
                    <input type="number" name="ca[${caIndex}][san_luong]" class="form-control" min="0" required oninput="updateProgress()">
                </div>
                <div class="col-md-3">
                    <label>Tổ trưởng phụ trách</label>
                    <select name="ca[${caIndex}][to_truong]" class="form-control">
                        ${toTruongList.map(tt => `<option value='${tt.value}'>${tt.text}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-danger" onclick="removeCa(${caIndex})"><i class="fa fa-trash"></i></button>
                </div>
            </div>
        </div>`;
        document.getElementById('ca-list').insertAdjacentHTML('beforeend', caHtml);
        updateProgress();
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
        // Giả sử mục tiêu tổng là 1000
        const goal = 1000;
        let percent = Math.min(100, Math.round(total / goal * 100));
        document.getElementById('progress-bar').style.width = percent + '%';
        document.getElementById('progress-bar').textContent = percent + '%';
        document.getElementById('progress-bar').className = 'progress-bar ' + (percent >= 100 ? 'bg-success' : 'bg-warning');
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