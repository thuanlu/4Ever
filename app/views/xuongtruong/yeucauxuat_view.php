<?php ob_start(); ?>
<div class="container mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><?php echo htmlspecialchars($pageTitle ?? 'Chi tiết phiếu yêu cầu'); ?></h4>
        <div>
            <a class="btn btn-outline-secondary btn-sm" href="<?php echo BASE_URL; ?>yeucauxuat/list">Quay lại danh sách</a>
            <a class="btn btn-primary btn-sm" href="<?php echo BASE_URL; ?>yeucauxuat">Tạo phiếu mới</a>
        </div>
    </div>

    <?php if (!empty($request)): ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4"><strong>Mã phiếu:</strong> <?php echo htmlspecialchars($request['ma_phieu'] ?? ''); ?></div>
                    <div class="col-md-4"><strong>Mã kế hoạch:</strong> <?php echo htmlspecialchars($request['ma_kehoach'] ?? ''); ?></div>
                    <div class="col-md-4"><strong>Phân xưởng:</strong> <?php echo htmlspecialchars($request['ma_phanxuong'] ?? ''); ?></div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-4"><strong>Người lập:</strong> <?php echo htmlspecialchars($request['ma_nv'] ?? ''); ?></div>
                    <div class="col-md-4"><strong>Trạng thái:</strong> <?php echo htmlspecialchars($request['trang_thai'] ?? ''); ?></div>
                    <div class="col-md-4"><strong>Ngày tạo:</strong> <?php echo htmlspecialchars($request['ngay_lap'] ?? ''); ?></div>
                </div>
                <?php if (!empty($request['ghichu'])): ?>
                    <div class="row mt-2">
                        <div class="col-12"><strong>Ghi chú:</strong> <?php echo nl2br(htmlspecialchars($request['ghichu'])); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <h5>Danh sách nguyên liệu</h5>
        <?php if (!empty($lines)): ?>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Mã NL</th>
                            <th>Tên nguyên liệu</th>
                            <th>Số lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach ($lines as $ln): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($ln['ma_nguyenlieu'] ?? $ln['MaNguyenLieu'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($ln['ten'] ?? $ln['TenNguyenLieu'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($ln['so_luong'] ?? $ln['SoLuong'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-secondary">Không có nguyên liệu nào trong phiếu.</div>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-warning">Phiếu yêu cầu không tồn tại hoặc đã bị xóa.</div>
    <?php endif; ?>

</div>

<?php $content = ob_get_clean(); include APP_PATH . '/views/layouts/main.php'; ?>