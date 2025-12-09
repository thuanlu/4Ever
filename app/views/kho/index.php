<?php ob_start(); ?>

<div class="container mt-4 mb-5">

    <?php if (!empty($msg)): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> <strong><?= $msg ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-uppercase"> Danh sách yêu cầu chờ duyệt</h5>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead class="table-info">
                        <tr>
                            <th class="text-center">Mã PYC</th>
                            <th class="text-center">Người yêu cầu</th>
                            <th class="text-center">Phân xưởng</th>
                            <th class="text-center">Ngày yêu cầu</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center" style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($requests)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">Không có yêu cầu nào đang chờ.</td></tr>
                        <?php else: ?>
                            <?php foreach ($requests as $r): ?> 
                            <tr class="<?= (isset($_GET['ma_yc']) && $_GET['ma_yc'] == $r['MaPhieuYC']) ? 'table-warning border border-primary' : '' ?>">
                                <td class="text-center fw-bold "><?= htmlspecialchars($r['MaPhieuYC']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($r['NguoiYeuCau']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($r['TenPhanXuong']) ?></td>
                                <td class="text-center"><?= date('d/m/Y', strtotime($r['NgayLap'])) ?></td>
                                <td class="text-center"><span class="badge bg-warning text-dark"><?= htmlspecialchars($r['TrangThai']) ?></span></td>
                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>kho/xuatnguyenlieu?ma_yc=<?= $r['MaPhieuYC'] ?>" 
                                       class="btn btn-outline-primary fw-bold btn-sm">
                                        Lập phiếu xuất <i class="fas"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <?php if (!empty($request)): ?>
    
    <div id="processingForm" class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-uppercase">
                Phiếu Xuất Nguyên Liệu <?= $request['MaPhieuYC'] ?>
            </h5>
        </div>

        <div class="card-body">
            <form action="<?= BASE_URL ?>kho/xuatnguyenlieu/store" method="POST">
                
                <h6 class="text-primary fw-bold mb-3">1. Thông tin chung</h6>
                  <div class="border rounded p-3 mb-4">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Mã phiếu yêu cầu</label>
                        <input type="text" class="form-control bg-light" name="MaPhieuYC" value="<?= $request['MaPhieuYC'] ?>" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Người yêu cầu</label>
                        <input type="text" class="form-control bg-light" value="<?= $request['NguoiYeuCau'] ?>" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Ngày yêu cầu xuất</label>
                        <input type="text" class="form-control bg-light" value="<?= date('d/m/Y', strtotime($request['NgayLap'])) ?>" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Ngày thực xuất <span class="text-danger">*</span></label>
                        <input type="date" class="form-control bg-light" name="NgayLap" value="<?= date('Y-m-d') ?>" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Kho xuất</label>
                        <input type="text" class="form-control bg-light" value="Kho Nguyên Liệu" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Phân xưởng nhận</label>
                        <input type="text" class="form-control bg-light" value="<?= $request['TenPhanXuong'] ?>" readonly>
                    </div>

                </div>
            </div>

                <h6 class="text-primary fw-bold mb-3"> 2. Chi tiết nguyên liệu</h6>
                <div class="border rounded p-3 mb-4">
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

                                <td class="text-center"><?= htmlspecialchars($item['MaNguyenLieu']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($item['DonViTinh']) ?></td>

                                <td class="text-center">
                                    <input type="text" class="form-control-plaintext text-center fw-bold" value="<?= $item['SoLuong'] ?>" readonly>
                                    <input type="hidden" name="items[<?= $item['MaNguyenLieu'] ?>][sl_yeu_cau]" value="<?= $item['SoLuong'] ?>">
                                </td>

                                <?php $initial_val = min(floatval($item['SoLuong']), floatval($item['SoLuongTonKho'])); ?>

                                <td>
                                    <input type="number"
                                        name="items[<?= $item['MaNguyenLieu'] ?>][sl_thuc_xuat]"
                                        class="form-control text-center fw-bold text-primary"
                                        value="<?= $initial_val ?>"
                                        min="0"
                                        max="<?= htmlspecialchars($item['SoLuongTonKho']) ?>"
                                        step="0.01"
                                        required readonly>
                                </td>

                                <td class="text-center">
                                    <span class="badge <?= ($item['SoLuongTonKho'] < $item['SoLuong']) ? 'bg-danger' : 'bg-success' ?>">
                                        <?= number_format($item['SoLuongTonKho'], 2) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
            </div>

                <div class="d-flex justify-content-end pt-3 border-top">
                    <a href="<?= BASE_URL ?>kho/xuatnguyenlieu" class="btn btn-secondary me-2" style="min-width: 120px;">Hủy bỏ</a>
                    <button type="submit" class="btn btn-success fw-bold" style="min-width: 150px;">Xác nhận Xuất</button>
                </div>

            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("processingForm").scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    </script>
    <?php endif; ?>


    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-uppercase">Lịch sử phiếu xuất nguyên liệu</h5>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead class="table-info text-center sticky-top">
                        <tr>
                            <th style="width: 100px;">Mã PX</th>
                            <th>Mã PYC</th>
                            <th>Ngày lập</th>
                            <th>Phân xưởng</th>
                            <th>Người xuất</th>
                            <th>Trạng thái</th>
                            <!-- <th style="width: 100px;">Chi tiết</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($history)): ?>
                            <?php foreach ($history as $h): ?>
                            <tr>
                                <td class="text-center fw-bold"><?= $h['MaPX'] ?></td>
                                <td class="text-center"><?= $h['MaPhieuYC'] ?></td>
                                <td class="text-center"><?= date('d/m/Y', strtotime($h['NgayLap'])) ?></td>
                                <td class="text-center"><?= $h['TenPhanXuong'] ?></td>
                                <td class="text-center"><?= $h['NguoiXuat'] ?></td>
                                <td class="text-center"><span class="badge bg-success"><?= $h['TrangThaiPX'] ?></span></td>
                                <!-- <td class="text-center">
                                    <button class="btn btn-outline-primary btn-sm fw-bold view-btn" data-id="<?= $h['MaPX'] ?>">
                                        Xem
                                    </button>
                                </td> -->
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center">Chưa có phiếu xuất nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>