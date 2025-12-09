<?php
ob_start();
?>
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-3">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </h2>
    </div>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>

<?php if (!empty($criticalAlerts)): ?>
    <?php
    // Xử lý tách mảng ngay tại View dựa vào cột 'TrangThai' đã định nghĩa ở Model
    $dsNguyenLieu = [];
    $dsSanPham = [];

    foreach ($criticalAlerts as $alert) {
        if ($alert['TrangThai'] === 'HẾT HÀNG') {
            $dsNguyenLieu[] = $alert;
        } else {
            $dsSanPham[] = $alert;
        }
    }
    ?>

<div class="modal fade" id="criticalModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-danger" style="border-width: 3px;">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">
            <i class="fas fa-triangle-exclamation"></i> CẢNH BÁO KHẨN CẤP
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
        <p class="fw-bold text-danger text-uppercase text-center me-4">Phát hiện các vấn đề nghiêm trọng cần xử lý ngay</p>
        <?php if (!empty($dsNguyenLieu)): ?>
            <h6 class="fw-bold text-danger border-bottom border-danger pb-2 mb-3">
                <i class="fas fa-box-open me-2"></i>NGUYÊN LIỆU
            </h6>
            <table class="table table-bordered table-striped mb-4">
                <thead class="table-danger">
                    <tr>
                        <th style="width: 20%">Mã NL</th>
                        <th style="width: 40%">Tên Nguyên Liệu</th>
                        <th style="width: 25%">Tồn kho hiện tại</th>
                        <th style="width: 15%">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($dsNguyenLieu as $nl): ?>
                <tr>
                    <td class="fw-bold"><?= $nl['Ma'] ?></td>
                    <td><?= $nl['Ten'] ?></td>
                    <td class="text-center fw-bold text-danger"><?= $nl['SoLuongHoacHan'] ?></td> <td class="fw-bold text-danger text-uppercase small"><?= $nl['TrangThai'] ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (!empty($dsSanPham)): ?>
            <h6 class="fw-bold text-danger border-bottom border-danger pb-2 mb-3 mt-4">
                <i class="fas fa-calendar-times me-2"></i>SẢN PHẨM
            </h6>
            <table class="table table-bordered table-striped">
                <thead class="table-danger">
                    <tr>
                        <th style="width: 20%">Mã Lô</th>
                        <th style="width: 40%">Tên Sản Phẩm</th>
                        <th style="width: 25%">Ngày hết hạn</th>
                        <th style="width: 15%">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($dsSanPham as $sp): ?>
                <tr>
                    <td class="fw-bold"><?= $sp['Ma'] ?></td>
                    <td><?= $sp['Ten'] ?></td>
                    <td class="text-center fw-bold text-danger"><?= $sp['SoLuongHoacHan'] ?></td> <td class="fw-bold text-danger text-uppercase small"><?= $sp['TrangThai'] ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

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