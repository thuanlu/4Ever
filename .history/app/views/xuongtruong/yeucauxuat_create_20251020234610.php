<?php
// View chuyển từ yeucauxuat/create sang xuongtruong/yeucauxuat_create
include APP_PATH . '/views/yeucauxuat/create.php';
          <button type="button" class="btn btn-warning" onclick="setActionAndSubmit('draft')">Lưu nháp</button>
          <button type="button" class="btn btn-primary" onclick="setActionAndSubmit('send')">Gửi yêu cầu</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Embedded list -->
  <div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
      <strong>Danh sách phiếu đã tạo</strong>
      <a href="<?php echo BASE_URL; ?>yeucauxuat/list" class="btn btn-sm btn-outline-primary">Mở trang quản lý</a>
    </div>
    <div class="card-body">
      <form class="row gy-2 gx-2 align-items-end mb-3" method="get" action="">
        <input type="hidden" name="ma_kehoach" value="<?php echo htmlspecialchars($selectedMaKH); ?>">
        <div class="col-md-3">
          <label class="form-label">Trạng thái</label>
          <select name="status" class="form-select">
            <option value="">Tất cả</option>
            <?php foreach (['Nháp','Chờ xử lý','Đã duyệt','Từ chối'] as $st): ?>
              <option value="<?php echo $st; ?>" <?php echo ($filterStatus===$st)?'selected':''; ?>><?php echo $st; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Tìm kiếm</label>
          <input type="text" class="form-control" name="q" placeholder="Mã phiếu, mã kế hoạch, ghi chú" value="<?php echo htmlspecialchars($filterKeyword); ?>">
        </div>
        <div class="col-md-2">
          <button class="btn btn-outline-primary w-100">Lọc</button>
        </div>
      </form>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
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
          <?php if (empty($requests)): ?>
            <tr><td colspan="6" class="text-center text-muted">Chưa có phiếu</td></tr>
          <?php else: ?>
            <?php foreach ($requests as $r): ?>
              <tr>
                <td><?php echo htmlspecialchars($r['ma_phieu']); ?></td>
                <td><?php echo htmlspecialchars($r['ma_kehoach']); ?></td>
                <td><?php echo htmlspecialchars($r['ngay_yeucau']); ?></td>
                <td><span class="badge bg-<?php echo ($r['trangthai']==='Nháp'?'secondary':($r['trangthai']==='Chờ xử lý'?'warning text-dark':($r['trangthai']==='Đã duyệt'?'success':'danger'))); ?>"><?php echo htmlspecialchars($r['trangthai']); ?></span></td>
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

</div>

<script>
  function validateDateNotPast(input) {
    const v = input.value; if (!v) return true;
    const today = new Date(); today.setHours(0,0,0,0);
    const d = new Date(v + 'T00:00:00');
    if (d < today) { alert('Ngày yêu cầu không được là ngày trong quá khứ'); input.focus(); return false; }
    return true;
  }
  function onPlanChange(sel) {
    const url = new URL(window.location.href);
    url.searchParams.set('ma_kehoach', sel.value);
    window.location.href = url.toString();
  }
  function setActionAndSubmit(act) {
    const dateInput = document.getElementById('ngay_yeucau');
    if (!validateDateNotPast(dateInput)) return;
    document.getElementById('action').value = act;
    document.getElementById('frm').submit();
  }
</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>
