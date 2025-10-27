<?php
ob_start();
?>
<div class="container mt-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Danh sách phiếu yêu cầu xuất</h4>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="<?php echo BASE_URL; ?>xuongtruong/dashboard">Về Dashboard</a>
      <a class="btn btn-primary btn-sm" href="<?php echo BASE_URL; ?>yeucauxuat">Tạo phiếu</a>
    </div>
  </div>

  <form class="row g-2 mb-3" method="get" action="">
    <div class="col-auto">
      <select name="status" class="form-select form-select-sm">
        <option value="">-- Tất cả trạng thái --</option>
        <?php foreach (['Nháp','Chờ xử lý','Đã duyệt','Từ chối'] as $st): ?>
          <option value="<?php echo $st; ?>" <?php echo ($status ?? '')===$st?'selected':''; ?>><?php echo $st; ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-auto">
      <input type="text" class="form-control form-control-sm" name="q" placeholder="Tìm mã phiếu/Mã KH/Ghi chú" value="<?php echo htmlspecialchars($keyword ?? ''); ?>">
    </div>
    <div class="col-auto">
      <button class="btn btn-outline-primary btn-sm" type="submit">Lọc</button>
    </div>
  </form>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead>
          <tr>
            <th>Mã phiếu</th>
            <th>Mã kế hoạch</th>
            <th>Ngày yêu cầu</th>
            <th>Trạng thái</th>
            <th>Ghi chú</th>
            <th>Ngày tạo</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($rows)): ?>
            <tr><td colspan="6" class="text-center text-muted">Chưa có phiếu nào</td></tr>
          <?php else: ?>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td><?php echo htmlspecialchars($r['ma_phieu']); ?></td>
                <td><?php echo htmlspecialchars($r['ma_kehoach']); ?></td>
                <td><?php echo htmlspecialchars($r['ngay_yeucau']); ?></td>
                <td><?php echo htmlspecialchars($r['trangthai']); ?></td>
                <td><?php echo htmlspecialchars($r['ghichu'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($r['ngay_tao']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
