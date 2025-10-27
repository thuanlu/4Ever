<?php
// View tạo phiếu yêu cầu xuất nguyên liệu (bản gốc)
// Nếu dùng lại cho các controller khác, chỉ cần include file này
ob_start();
?>
<div class="container mt-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Tạo phiếu yêu cầu xuất nguyên liệu</h4>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="<?php echo BASE_URL; ?>xuongtruong/dashboard">Về Dashboard</a>
      <a class="btn btn-outline-primary btn-sm" href="<?php echo BASE_URL; ?>yeucauxuat/list">Danh sách phiếu</a>
    </div>
  </div>
  <!-- ...rest of form... -->
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
