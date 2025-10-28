<?php ob_start(); ?>
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Thông tin Phiếu Yêu cầu Kiểm Định</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4"><strong>Mã Phiếu:</strong> <?= htmlspecialchars($phieu['MaPhieuKT']) ?></div>
            <div class="col-md-4"><strong>Mã Lô Hàng:</strong> <?= htmlspecialchars($phieu['MaLoHang']) ?></div>
            <div class="col-md-4"><strong>Tên Sản Phẩm:</strong> <?= htmlspecialchars($phieu['TenSanPham']) ?></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4"><strong>Người Yêu Cầu:</strong> <?= htmlspecialchars($phieu['NguoiYeuCau']) ?></div>
            <div class="col-md-4"><strong>Ngày Yêu Cầu Kiểm Tra:</strong> <?= htmlspecialchars(date('d/m/Y', strtotime($phieu['NgayKiemTra']))) ?></div>
            <div class="col-md-4"><strong>Trạng Thái:</strong> <?= htmlspecialchars($phieu['TrangThai']) ?></div>
        </div>
    </div>
</div>

<div class="card mb-4"></div>
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Ghi Nhận Kết Quả Kiểm Định</h5>
    </div>
    <div class="card-body">
        <!-- <form method="post"> -->
        <form method="post" action="<?= BASE_URL ?>qc/save">
            <input type="hidden" name="MaPhieuKT" value="<?= htmlspecialchars($phieu['MaPhieuKT']) ?>">
            <input type="hidden" name="MaLoHang" value="<?= htmlspecialchars($phieu['MaLoHang']) ?>">
        
            <div class="mb-3">
                <label class="form-label fw-bold">Kết quả Kiểm tra <span class="text-danger">*</span></label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="KetQua" value="Đạt" id="dat"
                        <?= ($phieu['KetQua'] == 'Đạt') ? 'checked' : '' ?>>
                    <label class="form-check-label text-success" for="dat">Đạt</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="KetQua" value="Không đạt" id="khongdat"
                        <?= ($phieu['KetQua'] == 'Không đạt') ? 'checked' : '' ?>>
                    <label class="form-check-label text-danger" for="khongdat">Không đạt</label>
                </div>
            </div>

            <div id="nguyenNhanBox" class="mb-3 <?= ($phieu['KetQua'] != 'Không đạt') ? 'd-none' : '' ?>">
                <label class="form-label fw-bold text-danger">Nguyên nhân Lỗi</label>
                <textarea name="GhiChu" class="form-control" rows="3"
                    placeholder="Nhập nguyên nhân lỗi chi tiết (VD: lỗi đường may, kích thước, màu,...)"><?=
                    trim(isset($phieu['GhiChu']) ? htmlspecialchars($phieu['GhiChu']) : '')
                ?></textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Ngày Kiểm tra <span class="text-danger">*</span></label>
                    <input type="date" name="NgayKiemTra" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Thời gian Kiểm tra <span class="text-danger">*</span></label>
                    <input type="time" name="GioKiemTra" class="form-control" value="<?= date('H:i') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Người Kiểm tra</label>
                    <input type="text" class="form-control" 
                        value="<?= htmlspecialchars($_SESSION['full_name'] ?? 'Chưa đăng nhập') ?>" readonly>
                </div>
            </div>

            <div class="text-end">
                <a href="<?= BASE_URL ?>qc/ketquakiemdinh" class="btn btn-secondary">Hủy</a>
                <button type="submit" class="btn btn-primary">Tạo Phiếu Kết Quả Kiểm Định</button>
            </div>
            
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('input[name="KetQua"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('nguyenNhanBox').classList.toggle('d-none', this.value !== 'Không đạt');
        });
    });
</script>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>
