<?php ob_start(); ?>
<div class="container mt-3">
  <?php if (!empty($ticket)): ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h4 class="mb-1"><?php echo htmlspecialchars($pageTitle ?? 'Chi tiết phiếu kiểm tra lô/sản phẩm'); ?></h4>
        <div class="text-muted small">
          Mã phiếu: <span class="fw-semibold"><?php echo htmlspecialchars($ticket['MaPhieuKT'] ?? ''); ?></span>
        </div>
      </div>
      <div>
        <a class="btn btn-outline-secondary btn-sm me-2" href="<?php echo BASE_URL; ?>phieu-kiem-tra/index">
          <i class="fas fa-arrow-left me-1"></i>Quay lại danh sách
        </a>
        <a class="btn btn-primary btn-sm" href="<?php echo BASE_URL; ?>phieu-kiem-tra/create">
          <i class="fas fa-plus me-1"></i>Tạo phiếu mới
        </a>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <div class="row">
          <div class="col-md-4 mb-3 mb-md-0">
            <small class="text-muted text-uppercase d-block mb-1">Mã phiếu</small>
            <span class="fw-semibold"><?php echo htmlspecialchars($ticket['MaPhieuKT'] ?? ''); ?></span>
          </div>
          <div class="col-md-4 mb-3 mb-md-0">
            <small class="text-muted text-uppercase d-block mb-1">Mã lô</small>
            <span class="fw-semibold"><?php echo htmlspecialchars($ticket['MaLoHang'] ?? ''); ?></span>
          </div>
          <div class="col-md-4">
            <small class="text-muted text-uppercase d-block mb-1">Sản phẩm (lô)</small>
            <span class="fw-semibold">
              <?php echo htmlspecialchars($ticket['MaSanPham'] ?? ''); ?>
              <?php if (!empty($ticket['TenSanPham'])): ?>
                - <?php echo htmlspecialchars($ticket['TenSanPham']); ?>
              <?php endif; ?>
            </span>
          </div>
        </div>

        <hr class="my-3">

        <div class="row">
          <div class="col-md-4 mb-3 mb-md-0">
            <small class="text-muted text-uppercase d-block mb-1">Ngày kiểm tra</small>
            <span class="fw-semibold"><?php echo htmlspecialchars($ticket['ngay_kiemtra'] ?? ''); ?></span>
          </div>
          <div class="col-md-4 mb-3 mb-md-0">
            <small class="text-muted text-uppercase d-block mb-1">Người lập</small>
            <span class="fw-semibold"><?php echo htmlspecialchars($ticket['MaNV'] ?? ''); ?></span>
          </div>
          <div class="col-md-4">
            <small class="text-muted text-uppercase d-block mb-1">Trạng thái</small>
            <?php
              $status = $ticket['TrangThai'] ?? '';
              $badge = 'secondary';
              
              // Kiểm tra trạng thái có chứa "Đạt" → màu xanh
              if (stripos($status, 'Đạt') !== false || stripos($status, 'đạt') !== false) {
                  $badge = 'success';
              }
              // Kiểm tra trạng thái có chứa "Không đạt" → màu đỏ
              elseif (stripos($status, 'Không đạt') !== false || stripos($status, 'không đạt') !== false) {
                  $badge = 'danger';
              }
              // Các trạng thái khác
              elseif ($status === 'Nháp') {
                  $badge = 'warning text-dark';
              } elseif ($status === 'Chờ xử lý' || $status === 'Chờ duyệt' || $status === 'Chờ kiểm tra') {
                  $badge = 'info text-dark';
              } elseif ($status === 'Hoàn thành' || $status === 'Đã kiểm tra') {
                  $badge = 'success';
              }
            ?>
            <span class="badge bg-<?php echo $badge; ?>">
              <?php echo htmlspecialchars($status); ?>
            </span>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">Chi tiết phiếu kiểm tra lô/sản phẩm</h4>
      <a class="btn btn-outline-secondary btn-sm" href="<?php echo BASE_URL; ?>phieu-kiem-tra/index">
        <i class="fas fa-arrow-left me-1"></i>Quay lại danh sách
      </a>
    </div>
    <div class="alert alert-warning mb-0">
      Phiếu không tồn tại hoặc đã bị xóa.
    </div>
  <?php endif; ?>
</div>
<?php $content = ob_get_clean(); include APP_PATH . '/views/layouts/main.php'; ?>