<?php ob_start(); ?>
<div class="container mt-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><?php echo htmlspecialchars($pageTitle ?? 'Chi tiết phiếu kiểm tra'); ?></h4>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="<?php echo BASE_URL; ?>phieu-kiem-tra/index">Quay lại</a>
      <a class="btn btn-primary btn-sm" href="<?php echo BASE_URL; ?>phieu-kiem-tra/create">Tạo phiếu mới</a>
    </div>
  </div>

  <?php if (!empty($ticket)): ?>
    <div class="card mb-3">
      <div class="card-body">
        <div class="row">
          <div class="col-md-4"><strong>Mã phiếu:</strong> <?php echo htmlspecialchars($ticket['MaPhieuKT'] ?? ''); ?></div>
          <div class="col-md-4"><strong>Mã lô:</strong> <?php echo htmlspecialchars($ticket['MaLoHang'] ?? ''); ?></div>
          <div class="col-md-4"><strong>Sản phẩm (lô):</strong>
            <?php echo htmlspecialchars($ticket['MaSanPham'] ?? ''); ?>
            <?php if (!empty($ticket['TenSanPham'])): ?>
              - <?php echo htmlspecialchars($ticket['TenSanPham']); ?>
            <?php endif; ?>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-md-4"><strong>Ngày kiểm tra:</strong> <?php echo htmlspecialchars($ticket['ngay_kiemtra'] ?? ''); ?></div>
          <div class="col-md-4"><strong>Người lập:</strong> <?php echo htmlspecialchars($ticket['MaNV'] ?? ''); ?></div>
          <div class="col-md-4"><strong>Trạng thái:</strong> <?php echo htmlspecialchars($ticket['TrangThai'] ?? ''); ?></div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-warning">Phiếu không tồn tại.</div>
  <?php endif; ?>

</div>
<?php $content = ob_get_clean(); include APP_PATH . '/views/layouts/main.php'; ?>