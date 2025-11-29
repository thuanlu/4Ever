<?php
ob_start();
?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-3">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard Kho
            <small class="text-muted">Tổng quan cho Nhân viên kho</small>
        </h2>
    </div>
</div>
<!-- Nội dung dashboard Kho riêng biệt ở đây -->
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
            <i class="fa-solid fa-triangle-exclamation"></i> CẢNH BÁO KHẨN CẤP
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p class="fw-bold text-danger">Phát hiện các vấn đề nghiêm trọng cần xử lý ngay:</p>
        <table class="table table-bordered table-striped">
            <thead class="table-danger">
                <tr>
                    <th>Mã</th>
                    <th>Tên Hàng Hóa</th>
                    <th>Tồn kho / Hạn dùng</th>
                    <th>Vấn đề</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($criticalAlerts as $alert): ?>
            <tr>
                <td class="fw-bold"><?= $alert['Ma'] ?></td>
                <td><?= $alert['Ten'] ?></td>
                <td class="text-center fw-bold"><?= $alert['SoLuongHoacHan'] ?></td>
                <td class="fw-bold text-danger text-uppercase"><?= $alert['TrangThai'] ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Đã hiểu</button>
        <a href="<?= BASE_URL ?>kho/tonkho" class="btn btn-danger">Xử lý ngay</a>
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
