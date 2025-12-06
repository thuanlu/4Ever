<?php ob_start(); ?>
<div class="container mt-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Tạo phiếu yêu cầu kiểm tra lô/sản phẩm</h4>
    <div>
      <a class="btn btn-outline-secondary btn-sm" href="<?php echo BASE_URL; ?>xuongtruong/dashboard">Về Dashboard</a>
      <a class="btn btn-outline-primary btn-sm" href="<?php echo BASE_URL; ?>phieu-kiem-tra/index">Danh sách phiếu</a>
    </div>
  </div>

  <?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
  <?php endif; ?>
  <?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
  <?php endif; ?>

  <form method="post" action="<?php echo BASE_URL; ?>phieu-kiem-tra/store" class="row g-3">
    <input type="hidden" name="_csrf" value="<?php echo md5(session_id()); ?>">

    <div class="col-12">
      <label class="form-label">Chọn kế hoạch sản xuất</label>
      <select id="select-kehoach" name="ma_kehoach" class="form-select">
        <option value="">-- Chọn kế hoạch --</option>
        <?php foreach (($plans ?? []) as $p): ?>
          <option value="<?php echo htmlspecialchars($p['MaKeHoach']); ?>"
                  data-mapx="<?php echo htmlspecialchars($p['MaPhanXuong'] ?? ''); ?>"
                  data-products="<?php echo htmlspecialchars($p['MaSanPhamList'] ?? ''); ?>"
                  data-products-named="<?php echo htmlspecialchars($p['MaSanPhamNamedList'] ?? ''); ?>"
                  data-start="<?php echo htmlspecialchars($p['NgayBatDau'] ?? ''); ?>"
                  data-end="<?php echo htmlspecialchars($p['NgayKetThuc'] ?? ''); ?>"
                  data-ten="<?php echo htmlspecialchars($p['TenKeHoach'] ?? ''); ?>"
          ><?php echo htmlspecialchars($p['MaKeHoach'] . ' - ' . $p['TenKeHoach']); ?></option>
        <?php endforeach; ?>
      </select>

      <div id="plan-info" class="mt-3" style="display:none;">
        <div class="card shadow-sm">
          <div class="card-body p-3">
            <h6 class="card-title mb-2">Thông tin kế hoạch</h6>
            <div class="row align-items-center gx-2 gy-1">
              <div class="col-md-6">
                <div class="fw-semibold">Mã kế hoạch</div>
                <div id="pi-ma" class="text-muted"></div>
              </div>
              <div class="col-md-6">
                <div class="fw-semibold">Mã phân xưởng</div>
                <div id="pi-px" class="text-muted">
                  <?php if (!empty($maPX ?? '')): ?>
                    <span class="badge bg-info text-dark"><?php echo htmlspecialchars($maPX); ?></span>
                  <?php endif; ?>
                </div>
              </div>
              <div class="col-12 mt-2">
                <div class="fw-semibold">Sản phẩm</div>
                <div id="pi-sp" class="text-muted"></div>
              </div>
              <div class="col-md-6 mt-2">
                <div class="fw-semibold">Ngày bắt đầu</div>
                <div id="pi-start" class="text-muted"></div>
              </div>
              <div class="col-md-6 mt-2">
                <div class="fw-semibold">Ngày kết thúc</div>
                <div id="pi-end" class="text-muted"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <label class="form-label">Mã lô / Sản phẩm</label>
      <select id="select-lohang" name="ma_lohang" class="form-select" aria-label="Chọn lô hàng">
        <option value="">-- Chọn lô --</option>
        <?php foreach (($lots ?? []) as $l): ?>
          <option value="<?php echo htmlspecialchars($l['MaLoHang']); ?>" data-product="<?php echo htmlspecialchars($l['MaSanPham']); ?>"><?php echo htmlspecialchars($l['MaLoHang'] . ' - ' . $l['MaSanPham']); ?></option>
        <?php endforeach; ?>
      </select>
      <small id="no-lohang" class="form-text text-muted" style="display:none;color:#b00;">Không có lô phù hợp với kế hoạch này.</small>
    </div>

    <div class="col-md-3">
      <label class="form-label">Ngày yêu cầu</label>
      <input type="date" id="ngay_kiemtra" name="ngay_kiemtra" class="form-control" value="<?php echo htmlspecialchars($minDate ?? date('Y-m-d')); ?>" required>
      <small id="date-error" class="form-text text-danger" style="display:none;">Ngày không được nhỏ hơn ngày hôm nay</small>
    </div>

    <div class="col-12 d-flex gap-2 justify-content-end">
      <button type="submit" name="action" value="draft" class="btn btn-warning">Lưu nháp</button>
      <button type="submit" name="action" value="send" class="btn btn-primary">Gửi yêu cầu</button>
      <a href="<?php echo BASE_URL; ?>" class="btn btn-outline-secondary">Hủy</a>
    </div>
  </form>

    </form>

    <hr class="my-4" />
    <h5>Danh sách phiếu yêu cầu</h5>
    <?php if (!empty($tickets)): ?>
      <div class="table-responsive mt-2">
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th>Mã phiếu</th>
              <th>Mã lô</th>
              <th>Ngày kiểm tra</th>
              <th>Trạng thái</th>
              <th>Người tạo</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($tickets as $t): ?>
              <?php
                $trangThai = $t['TrangThai'] ?? '';
                $badgeClass = 'secondary';
                
                // Kiểm tra trạng thái có chứa "Đạt" → màu xanh
                if (stripos($trangThai, 'Đạt') !== false || stripos($trangThai, 'đạt') !== false) {
                    $badgeClass = 'success';
                }
                // Kiểm tra trạng thái có chứa "Không đạt" → màu đỏ
                elseif (stripos($trangThai, 'Không đạt') !== false || stripos($trangThai, 'không đạt') !== false) {
                    $badgeClass = 'danger';
                }
                // Các trạng thái khác
                elseif ($trangThai === 'Chờ xử lý' || $trangThai === 'Chờ kiểm tra') {
                    $badgeClass = 'warning text-dark';
                }
                elseif ($trangThai === 'Đã duyệt') {
                    $badgeClass = 'success';
                }
                elseif ($trangThai === 'Nháp') {
                    $badgeClass = 'secondary';
                }
              ?>
              <tr>
                <td><?php echo htmlspecialchars($t['MaPhieuKT'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($t['MaLoHang'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($t['ngay_kiemtra'] ?? ''); ?></td>
                <td><span class="badge bg-<?php echo $badgeClass; ?>"><?php echo htmlspecialchars($trangThai); ?></span></td>
                <td><?php echo htmlspecialchars($t['MaNV'] ?? ''); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="alert alert-secondary mt-2">Chưa có phiếu nào được tạo.</div>
    <?php endif; ?>

  </div>

<script>
  (function(){
    const selPlan = document.getElementById('select-kehoach');
    const selLot = document.getElementById('select-lohang');
    const info = document.getElementById('plan-info');
    const dateInput = document.getElementById('ngay_kiemtra');
    const dateError = document.getElementById('date-error');
    const form = document.querySelector('form');
    
    // Set minimum date to today
    const today = new Date();
    const todayStr = today.getFullYear() + '-' + 
                     String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                     String(today.getDate()).padStart(2, '0');
    if (dateInput) {
      dateInput.setAttribute('min', todayStr);
    }

    // Real-time date validation
    function validateDate() {
      if (!dateInput || !dateInput.value) return true;
      const selectedDate = new Date(dateInput.value + 'T00:00:00');
      const todayDate = new Date();
      todayDate.setHours(0, 0, 0, 0);
      
      if (selectedDate < todayDate) {
        dateInput.classList.add('is-invalid');
        if (dateError) dateError.style.display = '';
        return false;
      } else {
        dateInput.classList.remove('is-invalid');
        if (dateError) dateError.style.display = 'none';
        return true;
      }
    }

    // Validate on input change
    dateInput?.addEventListener('change', validateDate);
    dateInput?.addEventListener('input', validateDate);

    // Validate on form submission
    form?.addEventListener('submit', function(e) {
      if (!validateDate()) {
        e.preventDefault();
        dateInput?.focus();
        if (dateError) dateError.style.display = '';
      }
    });

    const pi = {
      ma: document.getElementById('pi-ma'),
      px: document.getElementById('pi-px'),
      sp: document.getElementById('pi-sp'),
      start: document.getElementById('pi-start'),
      end: document.getElementById('pi-end')
    };

    function clearInfo(){
      if (!info) return;
      info.style.display = 'none';
      pi.ma.textContent = '';
      pi.px.textContent = '';
      pi.sp.textContent = '';
      pi.start.textContent = '';
      pi.end.textContent = '';
    }

    function filterLotsByProducts(productList) {
      const allowed = new Set((productList || '').split(',').map(s=>s.trim()).filter(Boolean));
      for (const opt of selLot.options) {
        const prod = opt.getAttribute('data-product') || '';
        if (!allowed.size) {
          opt.style.display = ''; // show all
        } else {
          opt.style.display = allowed.has(prod) ? '' : 'none';
        }
      }
      // If currently selected option is hidden, reset selection
      if (selLot.selectedIndex >= 0) {
        const cur = selLot.options[selLot.selectedIndex];
        if (cur && cur.style.display === 'none') selLot.selectedIndex = 0;
      }
      // show/no-show message
      const anyVisible = Array.from(selLot.options).some(o => o.value && o.style.display !== 'none');
      const noL = document.getElementById('no-lohang');
      if (noL) noL.style.display = anyVisible ? 'none' : '';
    }

    selPlan?.addEventListener('change', function(){
      const opt = selPlan.options[selPlan.selectedIndex];
      if (!opt || !opt.value) { clearInfo(); filterLotsByProducts(''); return; }
      const ma = opt.value;
      const px = opt.getAttribute('data-mapx') || '';
      const prods = opt.getAttribute('data-products') || '';
      const productsNamed = opt.getAttribute('data-products-named') || '';
      const start = opt.getAttribute('data-start') || '';
      const end = opt.getAttribute('data-end') || '';
      const ten = opt.getAttribute('data-ten') || '';

      pi.ma.textContent = ma;
      if (px) {
        pi.px.innerHTML = '<span class="badge bg-info text-dark">' + px + '</span>';
      } else {
        pi.px.innerHTML = '<span class="text-muted small">Chưa xác định</span>';
      }
      // show names if available
      if (productsNamed) {
        const parts = productsNamed.split(',').map(s => (s.split(':')[1] || s.split(':')[0]).trim());
        pi.sp.textContent = parts.join(', ');
      } else {
        pi.sp.textContent = ten || prods || (opt.text || '');
      }
      pi.start.textContent = start;
      pi.end.textContent = end;
      info.style.display = '';

      filterLotsByProducts(prods);
    });

    // init
    if (selPlan) selPlan.dispatchEvent(new Event('change'));
  })();
</script>

<?php $content = ob_get_clean(); include APP_PATH . '/views/layouts/main.php'; ?>
