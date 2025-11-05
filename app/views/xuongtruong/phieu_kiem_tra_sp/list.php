<?php ob_start(); ?>
<div class="container mt-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Danh sách phiếu yêu cầu kiểm tra</h4>
    <div>
      <a class="btn btn-outline-primary btn-sm" href="<?php echo BASE_URL; ?>phieu-kiem-tra/create">Tạo phiếu mới</a>
    </div>
  </div>

  <?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
  <?php endif; ?>
  <?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
  <?php endif; ?>

  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr>
          <th>Mã phiếu</th>
          <th>Mã lô</th>
          <th>Ngày kiểm tra</th>
          <th>Trạng thái</th>
          <th>Người lập</th>
          <th>Xem</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($rows ?? [])): ?>
        <tr><td colspan="6" class="text-center text-muted">Chưa có phiếu</td></tr>
      <?php else: ?>
        <?php foreach ($rows as $r): ?>
          <?php
            $badgeClass = 'secondary';
            if (($r['TrangThai'] ?? '') === 'Chờ xử lý') $badgeClass = 'warning text-dark';
            if (($r['TrangThai'] ?? '') === 'Đã duyệt') $badgeClass = 'success';
            if (($r['TrangThai'] ?? '') === 'Nháp') $badgeClass = 'secondary';
          ?>
          <tr>
            <td><?php echo htmlspecialchars($r['MaPhieuKT'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($r['MaLoHang'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($r['ngay_kiemtra'] ?? ''); ?></td>
            <td><span class="badge bg-<?php echo $badgeClass; ?>"><?php echo htmlspecialchars($r['TrangThai'] ?? ''); ?></span></td>
            <td><?php echo htmlspecialchars($r['MaNV'] ?? ''); ?></td>
            <td>
              <a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_URL; ?>phieu-kiem-tra/view?ma=<?php echo urlencode($r['MaPhieuKT'] ?? ''); ?>">Xem</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $content = ob_get_clean(); include APP_PATH . '/views/layouts/main.php'; ?>
