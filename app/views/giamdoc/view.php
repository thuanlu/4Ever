<?php
// Tệp: app/views/giamdoc/view.php
ob_start();
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center shadow-sm p-3 mb-3 rounded" 
         style="background-color: #fff3cd; border: 1px solid #ffecb5; color: #664d03;">
        <div>
            <i class="fas fa-info-circle me-2"></i>
            Bạn đang xem kế hoạch ở chế độ <strong>Phê duyệt</strong>.
        </div>
        <div>
            <span class="badge bg-warning text-dark border border-dark">Trạng thái: <?php echo $kehoach['TrangThai']; ?></span>
        </div>
    </div>

    <div class="card shadow border-warning">
        <div class="card-body">
            <?php 
            // Include form hiển thị chi tiết (Read-only)
            include APP_PATH . '/views/kehoachsanxuat/form.php'; 
            ?>
            
            <hr class="my-4 border-primary">
            
            <div class="d-flex justify-content-center gap-3 pb-3">
                
                <button type="button" class="btn btn-danger px-4" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="fas fa-times-circle me-2"></i> Từ chối
                </button>

                <button type="button" class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="fas fa-check-circle me-2"></i> Duyệt kế hoạch
                </button>
            </div>
            
            <div class="text-center">
                <a href="<?php echo BASE_URL; ?>giamdoc" class="text-decoration-none text-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách chờ
                </a>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Xác nhận Từ chối</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?php echo BASE_URL; ?>giamdoc/reject/<?php echo $kehoach['MaKeHoach']; ?>" method="POST">
          <div class="modal-body">
            <p>Bạn có chắc chắn muốn từ chối kế hoạch sản xuất này không?</p>
            <div class="mb-3">
                <label for="LyDoTuChoi" class="form-label fw-bold">Lý do từ chối <span class="text-danger">*</span>:</label>
                <textarea class="form-control" id="LyDoTuChoi" name="LyDoTuChoi" rows="3" placeholder="Nhập lý do hủy bỏ kế hoạch..." required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="submit" class="btn btn-danger">Xác nhận Từ chối</button>
          </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Phê duyệt Kế hoạch</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?php echo BASE_URL; ?>giamdoc/approve/<?php echo $kehoach['MaKeHoach']; ?>" method="POST">
          <div class="modal-body">
            <p>Xác nhận phê duyệt kế hoạch sản xuất: <strong><?php echo $kehoach['MaKeHoach']; ?></strong>?</p>
            <p class="text-muted small">Sau khi duyệt, kế hoạch sẽ được chuyển xuống xưởng để thực hiện.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
            <button type="submit" class="btn btn-success">Đồng ý Duyệt</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Ẩn nút quay lại cũ của form.php (nếu có)
        const oldBackBtn = document.querySelector('a[href*="kehoachsanxuat"][class*="btn-secondary"]');
        if(oldBackBtn) oldBackBtn.style.display = 'none';
        
        // Ẩn tiêu đề cũ của form.php
        const oldAlert = document.querySelector('.alert-info');
        if(oldAlert) oldAlert.style.display = 'none';
    });
</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>