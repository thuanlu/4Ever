<?php
// Phần giữa: Form Lập Kế hoạch Cấp Xưởng
?>
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
<?php if (!empty($kehoach)): ?>
<script>
// ...JS cho ca làm việc, tổ trưởng, công suất...
</script>
<?php endif; ?>