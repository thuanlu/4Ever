<?php ob_start(); ?>
<div class="container mt-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Tạo phiếu yêu cầu xuất nguyên liệu</h4>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="<?php echo BASE_URL; ?>xuongtruong/dashboard">Về Dashboard</a>
      <a class="btn btn-outline-primary btn-sm" href="<?php echo BASE_URL; ?>yeucauxuat/list">Danh sách phiếu</a>
    </div>
  </div>

  <form id="frm" method="post" action="<?php echo BASE_URL; ?>yeucauxuat/save" class="row g-3">
    <input type="hidden" name="_csrf" value="<?php echo md5(session_id()); ?>">
    <input type="hidden" name="action" id="action" value="send">

    <div class="col-md-6">
      <label class="form-label">Chọn kế hoạch sản xuất</label>
      <select name="ma_kehoach" class="form-select" onchange="onPlanChange(this)" required>
        <option value="">-- Chọn kế hoạch --</option>
        <?php foreach ($plans as $p): ?>
          <option value="<?php echo htmlspecialchars($p['ma_kehoach']); ?>" <?php echo ($p['ma_kehoach'] === $selectedMaKH) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($p['ma_kehoach'] . ' - ' . $p['sanpham'] . ' (' . $p['soluong'] . ')'); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label">Ngày yêu cầu xuất</label>

      <?php
        // Ensure the date input uses a valid YYYY-MM-DD value. If oldDate is present but not in the
        // expected format, fall back to the system min date provided by the controller.
        $dateVal = $minDate;
        if (!empty($oldDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $oldDate)) {
            $dateVal = $oldDate;
        }
      ?>
      <input type="date" id="ngay_yeucau" name="ngay_yeucau" class="form-control" min="<?php echo $minDate; ?>" value="<?php echo htmlspecialchars($dateVal); ?>" required>

    </div>

    <div class="col-md-3">
      <label class="form-label">Ghi chú (nếu có)</label>

      <input type="text" name="ghichu" class="form-control" maxlength="255" placeholder="Ghi chú thêm" value="<?php echo htmlspecialchars($oldGhichu ?? ''); ?>">

    </div>

    <?php if ($selectedPlan): ?>
      <div class="col-12">
        <div class="alert alert-info">
          <strong>Thông tin kế hoạch:</strong>
          <div class="row mt-2">
            <div class="col-md-3"><small>Mã kế hoạch:</small><br><strong><?php echo htmlspecialchars($selectedPlan['ma_kehoach']); ?></strong></div>
            <div class="col-md-3"><small>Mã phân xưởng:</small><br><strong><?php echo htmlspecialchars($selectedPlan['ma_px'] ?? 'Không xác định'); ?></strong></div>
            <div class="col-md-3"><small>Sản phẩm:</small><br><strong><?php echo htmlspecialchars($selectedPlan['sanpham']); ?></strong></div>
            <div class="col-md-3"><small>Số lượng SP:</small><br><strong><?php echo (int)$selectedPlan['soluong']; ?></strong></div>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card">
          <div class="card-header bg-light"><strong>Nguyên liệu theo định mức</strong> <small class="text-muted">(Hệ thống tính tự động, tối đa +5%)</small></div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead>
                  <tr>
                    <th style="width: 20%">Mã NL</th>
                    <th style="width: 35%">Tên nguyên liệu</th>
                    <th style="width: 20%" class="text-end">Số lượng cần</th>
                    <th style="width: 25%" class="text-end">Tối đa cho phép (+5%)</th>
                  </tr>
                </thead>
                <tbody>
                <?php if (empty($materials)): ?>
                  <tr><td colspan="4" class="text-center text-muted py-3">Chưa có định mức nguyên liệu cho kế hoạch này</td></tr>
                <?php else: ?>
                  <?php foreach ($materials as $idx => $m): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($m['ma_nguyenlieu']); ?></td>
                      <td><?php echo htmlspecialchars($m['ten']); ?></td>
                      <td class="text-end">
                        <?php echo number_format($m['base'], 2); ?>
                        <input type="hidden" name="materials[<?php echo $idx; ?>][ma_nguyenlieu]" value="<?php echo htmlspecialchars($m['ma_nguyenlieu']); ?>">
                        <input type="hidden" name="materials[<?php echo $idx; ?>][ten]" value="<?php echo htmlspecialchars($m['ten']); ?>">
                        <input type="hidden" name="materials[<?php echo $idx; ?>][so_luong]" value="<?php echo htmlspecialchars($m['base']); ?>">
                        <input type="hidden" name="materials[<?php echo $idx; ?>][so_luong_max]" value="<?php echo htmlspecialchars($m['max']); ?>">
                      </td>
                      <td class="text-end text-muted"><?php echo number_format($m['max'], 2); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-warning mb-0">Vui lòng chọn kế hoạch để hệ thống tính toán nguyên liệu.</div>
      </div>
    <?php endif; ?>

    <div class="col-12 d-flex gap-2 justify-content-end">
      <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('frm').reset()">Làm mới</button>
      <button type="button" class="btn btn-warning" onclick="setActionAndSubmit('draft')">Lưu nháp</button>
      <button type="button" class="btn btn-primary" onclick="setActionAndSubmit('send')">Gửi yêu cầu</button>
    </div>
  </form>
  
  <?php // Embedded list ngay trong trang tạo phiếu ?>
  <div class="card mt-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
      <strong>Danh sách phiếu đã tạo</strong>

      <a href="<?php echo BASE_URL; ?>yeucauxuat/list" class="btn btn-primary">Mở trang quản lý</a>

    </div>
    <div class="card-body">
      <form class="row gy-2 gx-2 align-items-end mb-3" method="get" action="">
        <input type="hidden" name="ma_kehoach" value="<?php echo htmlspecialchars($selectedMaKH); ?>">
        <div class="col-md-3">
          <label class="form-label">Trạng thái</label>
          <select name="status" class="form-select">
            <option value="">Tất cả</option>
            <?php foreach (['Nháp','Chờ xử lý','Đã duyệt','Từ chối'] as $st): ?>
              <option value="<?php echo $st; ?>" <?php echo (($filterStatus ?? '')===$st)?'selected':''; ?>><?php echo $st; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Tìm kiếm</label>
          <input type="text" class="form-control" name="q" placeholder="Mã phiếu, mã kế hoạch, ghi chú" value="<?php echo htmlspecialchars($filterKeyword ?? ''); ?>">
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
          <?php if (empty($requests ?? [])): ?>
            <tr><td colspan="6" class="text-center text-muted">Chưa có phiếu</td></tr>
          <?php else: ?>
            <?php foreach ($requests as $r): ?>
              <tr>
                <td><?php echo htmlspecialchars($r['ma_phieu'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($r['ma_kehoach'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($r['ngay_yeucau'] ?? ''); ?></td>
                <td>
                  <?php $badge = ($r['trangthai']==='Nháp'?'secondary':($r['trangthai']==='Chờ xử lý'?'warning text-dark':($r['trangthai']==='Đã duyệt'?'success':'danger'))); ?>
                  <span class="badge bg-<?php echo $badge; ?>"><?php echo htmlspecialchars($r['trangthai'] ?? ''); ?></span>
                </td>
                <td><?php echo htmlspecialchars($r['ghichu'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($r['ngay_tao'] ?? ''); ?></td>
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