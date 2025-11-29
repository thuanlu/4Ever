<?php ob_start(); ?>

<div class="container mt-4 mb-5">
    <h3 class="text-center mb-4 text-uppercase fw-bold">Phiếu Xuất Nguyên Liệu</h3>

    <form action="<?= BASE_URL ?>kho/xuatnguyenlieu/store" method="POST">
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">1. Thông tin chung</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mã phiếu yêu cầu</label>
                        <input type="text" class="form-control bg-light" name="MaPhieuYC" value="<?= $request['MaPhieuYC'] ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Người yêu cầu</label>
                        <input type="text" class="form-control bg-light" value="<?= $request['NguoiYeuCau'] ?>" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Ngày yêu cầu xuất</label>
                        <input type="text" class="form-control bg-light" value="<?= date('d/m/Y', strtotime($request['NgayLap'])) ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Ngày thực xuất <span class="text-danger">*</span></label>
                        <input type="date" class="form-control bg-light" name="NgayLap" value="<?= date('Y-m-d') ?>" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Kho xuất</label>
                        <input type="text" class="form-control bg-light" value="Kho Nguyên Liệu" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Phân xưởng nhận</label>
                        <input type="text" class="form-control bg-light" value="<?= $request['TenPhanXuong'] ?>" readonly>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">2. Thông tin chi tiết</h5>
            </div>
            <br>
            <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0 align-middle">
                            <thead class="table-info text-center">
                                <tr>
                                    <th rowspan="2" style="width: 50px;">STT</th>
                                    <th rowspan="2" style="width: 280px;">Nguyên liệu</th>
                                    <th rowspan="2" style="width: 100px;">Mã số</th>
                                    <th rowspan="2" style="width: 80px;">DVT</th>
                                    <th colspan="2">Số lượng</th>
                                    <th rowspan="2" style="width: 150px;">Tồn kho</th>
                                    <!-- <th rowspan="2">Ghi chú</th> -->
                                </tr>
                                <tr>
                                    <th style="width: 120px;">Yêu cầu</th>
                                    <th style="width: 120px;">Thực xuất</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $stt = 1; ?>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="text-center"><?= $stt++ ?></td>
                                    
                                    <td>
                                        <strong><?= htmlspecialchars($item['TenNguyenLieu']) ?></strong>
                                        <input type="hidden" name="items[<?= $item['MaNguyenLieu'] ?>][id]" value="<?= $item['MaNguyenLieu'] ?>">
                                    </td>
                                    <td class="text-center">
                                        <?= htmlspecialchars($item['MaNguyenLieu']) ?>
                                    </td>
                                    
                                    <td class="text-center"><?= htmlspecialchars($item['DonViTinh']) ?></td>
                                    
                                    <td class="text-center">
                                        <input type="text" class="form-control-plaintext text-center fw-bold" value="<?= $item['SoLuong'] ?>" readonly>
                                        <input type="hidden" name="items[<?= $item['MaNguyenLieu'] ?>][sl_yeu_cau]" value="<?= $item['SoLuong'] ?>" readonly>
                                    </td>
                                    
                                    <!-- <td>
                                        <input type="number" 
                                            name="items[<?= $item['MaNguyenLieu'] ?>][sl_thuc_xuat]" 
                                            class="form-control text-center fw-bold text-primary" 
                                            value="<?= $item['SoLuong'] ?>" 
                                            min="0" 
                                            max="<?= $item['SoLuongTonKho'] ?>" 
                                            step="0.01">
                                    </td> -->
                                    <?php
                                        // đảm bảo value ban đầu không vượt quá tồn kho — tránh invalid HTML
                                        $initial_val = min(floatval($item['SoLuong']), floatval($item['SoLuongTonKho']));
                                    ?>
                                    <td>
                                        <input type="number"
                                            name="items[<?= $item['MaNguyenLieu'] ?>][sl_thuc_xuat]"
                                            class="form-control text-center fw-bold text-primary"
                                            value="<?= $initial_val ?>"
                                            min="0"
                                            max="<?= htmlspecialchars($item['SoLuongTonKho']) ?>"
                                            step="0.01"
                                            required>
                                        <!-- giữ lại số yêu cầu để server biết -->
                                        <input type="hidden" name="items[<?= $item['MaNguyenLieu'] ?>][sl_yeu_cau]" value="<?= htmlspecialchars($item['SoLuong']) ?>">
                                    </td>


                                    <td class="text-center">
                                        <span class="badge <?= ($item['SoLuongTonKho'] < $item['SoLuong']) ? 'bg-danger' : 'bg-success' ?>">
                                            <?= number_format($item['SoLuongTonKho'], 2) ?>
                                        </span>
                                    </td>

                                    <!-- <td>
                                        <input type="text" name="items[<?= $item['MaNguyenLieu'] ?>][ghi_chu]" class="form-control" placeholder="">
                                    </td> -->
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-end py-5">
                <a href="<?= BASE_URL ?>kho/xuatnguyenlieu" class="btn btn-secondary me-2" style="min-width: 120px;">Quay lại</a>
                <button type="submit" class="btn btn-success fw-bold" style="min-width: 150px;">Xác nhận Xuất</button>
            </div>
        </div>

    </form>
</div>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>

<?php if (!empty($criticalAlerts)): ?>
<div class="modal fade" id="criticalModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-danger" style="border-width: 3px;">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">
            <i class="fa-solid fa-triangle-exclamation"></i>CẢNH BÁO TỒN KHO
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p class="fw-bold text-danger">Một số nguyên liệu trong phiếu yêu cầu đang có tồn kho thấp hơn số lượng yêu cầu:</p>
        <table class="table table-bordered table-striped">
            <thead class="table-danger">
                <tr>
                    <th>Mã</th>
                    <th>Tên Hàng Hóa</th>
                    <th>Số lượng yêu cầu</th>
                    <th>Tồn kho</th>
                    <th>Vấn đề</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($criticalAlerts as $alert): ?>
            <tr>
                <td class="fw-bold"><?= htmlspecialchars($alert['Ma']) ?></td>
                <td><?= htmlspecialchars($alert['Ten']) ?></td>
                <td class="text-center fw-bold"><?= htmlspecialchars($alert['SoLuong']) ?></td>
                <td class="text-center fw-bold"><?= htmlspecialchars($alert['SoLuongHoacHan']) ?></td>
                <td class="fw-bold text-danger text-uppercase"><?= htmlspecialchars($alert['TrangThai']) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
      </div>

      <div class="modal-footer">
        <button class="btn btn-danger" data-bs-dismiss="modal">Đã hiểu</button>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var myModal = new bootstrap.Modal(document.getElementById('criticalModal'));
    myModal.show();
});
</script>
<?php endif; ?>
