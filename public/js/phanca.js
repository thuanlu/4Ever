// public/js/phanca.js
// public/js/phanca.js
(function () {
  // 1) Stub để tránh lỗi undefined khi HTML gọi inline
  window.calculateDays = function () { };
  window.calculateQuantity = function () { };

  // 2) Chỉ chạy JS ở trang phân ca
  if (!document.body.classList.contains('page-shift-assign')) return;

  // 3) Config API (KHAI BÁO MỘT LẦN DUY NHẤT)
  const BASE_URL = (window.BASE_URL || '/4Ever/').replace(/\/+$/, '/');
  const API = BASE_URL + 'public/api_phanca.php';

  // ====== Helpers ======
  const $ = (id) => document.getElementById(id);

  const el = {
    // Inputs chính
    ticketId: $('ticketId'),
    workName: $('workName'),
    totalQty: $('totalQty'),
    prodSize: $('prodSize'),
    prodColor: $('prodColor'),
    startDate: $('startDate'),
    endDate: $('endDate'),
    shiftType: $('shiftType'),
    percentage: $('percentage'),

    // Thống kê
    totalDays: $('totalDays'),
    qtyPerDay: $('qtyPerDay'),
    shiftQty: $('shiftQty'),
    workerCount: $('workerCount'),
    qtyPerWorker: $('qtyPerWorker'),
    shiftName: $('shiftName'),

    // Danh sách
    workerGrid: $('workerGrid'),
    assignmentTable: $('assignmentTable'),
    alertBox: $('alertBox'),
  };

  let currentWorkers = [];
  let selectedWorkers = new Set();
  let currentTicket = null; // lưu tạm dữ liệu phiếu đang chọn (kèm MaPhanXuong để lọc CN)

  function showAlert(type, msg) {
    if (!el.alertBox) return;
    el.alertBox.innerHTML = `<div class="alert alert-${type} py-2 px-3">${msg}</div>`;
    setTimeout(() => (el.alertBox.innerHTML = ''), 4500);
  }

  async function jget(url) {
    const r = await fetch(url, { credentials: 'same-origin' });
    if (!r.ok) throw new Error('HTTP ' + r.status);
    // nếu server trả HTML 404 thì parse json sẽ lỗi; bọc try-catch
    const text = await r.text();
    try { return JSON.parse(text); } catch (e) {
      throw new Error('Phản hồi không phải JSON: ' + text.slice(0, 120));
    }
  }

  async function jpost(url, body) {
    const r = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(body || {})
    });
    if (!r.ok) throw new Error('HTTP ' + r.status);
    const text = await r.text();
    try { return JSON.parse(text); } catch (e) {
      throw new Error('Phản hồi không phải JSON: ' + text.slice(0, 120));
    }
  }

  function daysDiff(a, b) {
    if (!a || !b) return 0;
    const d1 = new Date(a), d2 = new Date(b);
    const diff = Math.round((d2 - d1) / (1000 * 60 * 60 * 24)) + 1;
    return diff > 0 ? diff : 0;
  }

  function calculateQuantity() {
    const total = Number(el.totalQty.value || 0);
    const pct = Math.min(100, Math.max(0, Number(el.percentage.value || 0)));
    const days = Number(el.totalDays.textContent || 0);

    const qtyForShift = Math.round(total * pct / 100);
    const perDay = days > 0 ? Math.ceil(qtyForShift / days) : 0;
    const nWorkers = Number(el.workerCount.textContent || 0);
    const perWorker = nWorkers > 0 ? Math.ceil(qtyForShift / nWorkers) : 0;

    el.shiftQty.textContent = String(qtyForShift);
    el.qtyPerDay.textContent = String(perDay);
    el.qtyPerWorker.textContent = String(perWorker);
  }

  function calculateDays() {
    el.totalDays.textContent = String(daysDiff(el.startDate.value, el.endDate.value));
    calculateQuantity();
  }

  // ====== Render workers ======
  function renderWorkers(list) {
    currentWorkers = list || [];
    selectedWorkers.clear();

    if (currentWorkers.length === 0) {
      el.workerGrid.innerHTML = `<div class="text-muted">Không có công nhân cho ca này.</div>`;
    } else {
      el.workerGrid.innerHTML = currentWorkers.map(w => {
        const id = w.MaNV ?? w.id ?? w.code ?? '';
        const name = w.HoTen ?? w.fullname ?? w.name ?? '';
        const bp = w.BoPhan ? ` • ${w.BoPhan}` : '';
        return `
          <label class="form-check d-inline-flex align-items-center p-2 border rounded me-2 mb-2" style="min-width:240px">
            <input type="checkbox" class="form-check-input me-2" value="${id}" onchange="onWorkerCheck(this)">
            <span class="small"><b>${id}</b> - ${name}${bp}</span>
          </label>
        `;
      }).join('');
    }

    el.workerCount.textContent = String(currentWorkers.length);
    el.shiftName.textContent = el.shiftType.selectedOptions[0]?.textContent || '';
    calculateQuantity();
  }

  // cho HTML gọi
  window.onWorkerCheck = function (cb) {
    if (cb.checked) selectedWorkers.add(cb.value);
    else selectedWorkers.delete(cb.value);
    calculateQuantity();
  };

  // ====== Load ticket options (optional) ======
  async function loadTicketOptions() {
    // Nếu bạn đã có API này, bỏ comment đoạn dưới.
    // try {
    //   const ret = await jget(`${API}?action=ticket_options`);
    //   if (ret.ok && Array.isArray(ret.items)) {
    //     el.ticketId.innerHTML = `<option value="">-- Chọn --</option>` +
    //       ret.items.map(x => `<option value="${x.id}">${x.id}</option>`).join('');
    //   }
    // } catch(e) { /* bỏ qua nếu chưa có API */ }
  }

  // ====== Load ticket data when select changed ======
  async function loadTicketData() {
    const ticket = el.ticketId.value;
    if (!ticket) {
      currentTicket = null;
      // clear các field
      el.workName.value = '';
      el.totalQty.value = '';
      el.prodSize.value = '';
      el.prodColor.value = '';
      el.startDate.value = '';
      el.endDate.value = '';
      el.totalDays.textContent = '0';
      renderWorkers([]);
      calculateQuantity();
      return;
    }

    try {
      const ret = await jget(`${API}?action=get_ticket&ticketId=${encodeURIComponent(ticket)}`);
      if (!ret.ok) return showAlert('danger', ret.message || 'Không lấy được phiếu');

      // t: { work_name, total_qty, start_date, end_date, size, color, workshop }
      const t = ret.ticket || {};
      currentTicket = t;

      // Công việc hiển thị đẹp: "Sản xuất [Tên SP]" (nếu có product_type)
      const pvType = t.product_type ? (t.product_type + ' ') : '';
      const pvName = t.product_name || (t.work_name || '');
      el.workName.value = (pvType + pvName).trim();
      el.totalQty.value = t.total_qty || 0;
      el.prodSize.value = t.size || '';
      el.prodColor.value = t.color || '';

      if (t.start_date) el.startDate.value = t.start_date;
      if (t.end_date) el.endDate.value = t.end_date;

      calculateDays();

      // Lấy công nhân theo ca + phân xưởng (nếu server trả về)
      await loadWorkers(t.workshop || '');
      await loadAssignmentTable();
    } catch (e) {
      showAlert('danger', 'Lỗi tải phiếu: ' + e.message);
    }
  }
  window.loadTicketData = loadTicketData;

  // ====== Load workers by shift ======
  async function loadWorkers(px = '') {
    const shift = el.shiftType.value || 'morning';
    try {
      // server sẽ trả tối đa 20 người/ca theo mapping NhanVien_CaLam
      const ret = await jget(`${API}?action=get_workers&shift=${encodeURIComponent(shift)}&px=${encodeURIComponent(px)}`);
      if (!ret.ok) return showAlert('danger', ret.message || 'Không tải được công nhân');
      renderWorkers(ret.workers || []);
    } catch (e) {
      showAlert('danger', 'Lỗi tải công nhân: ' + e.message);
    }
  }
  window.loadWorkers = loadWorkers;

  // ====== Bảng đã thiết lập ======
  async function loadAssignmentTable() {
    try {
      const ret = await jget(`${API}?action=list`);
      if (!ret.ok) return;

      const items = ret.items || [];
      if (items.length === 0) {
        el.assignmentTable.innerHTML = `<tr><td colspan="10" class="text-center text-muted py-4">Chưa có ca làm việc nào được thiết lập</td></tr>`;
        return;
      }

      el.assignmentTable.innerHTML = items.map((it, idx) => `
        <tr>
          <td>${idx + 1}</td>
          <td>${it.ticket_id || ''}</td>
          <td>${it.work_name || ''}</td>
          <td>${it.shift_type || ''}</td>
          <td>${it.start_date || ''}</td>
          <td>${it.end_date || ''}</td>
          <td>${it.percentage || 0}%</td>
          <td>${it.quantity || 0}</td>
          <td>${(it.workers || '').toString()}</td>
          <td><button class="btn btn-sm btn-outline-danger" onclick="removeAssignment(${Number(it.id)})">Xóa</button></td>
        </tr>
      `).join('');
    } catch (e) {
      showAlert('danger', 'Lỗi tải danh sách: ' + e.message);
    }
  }

  window.removeAssignment = async function (id) {
    if (!id) return;
    try {
      const ret = await jpost(`${API}?action=remove`, { id });
      if (!ret.ok) return showAlert('danger', ret.message || 'Xoá thất bại');
      showAlert('success', 'Đã xoá');
      loadAssignmentTable();
    } catch (e) { showAlert('danger', 'Lỗi xoá: ' + e.message); }
  };

  // ====== Thêm/Lưu ======
  window.addToList = function () {
    if (selectedWorkers.size === 0) return showAlert('warning', 'Chưa chọn công nhân');
    showAlert('success', `Đã chọn ${selectedWorkers.size} công nhân (chưa lưu)`);
  };

  window.saveAllAssignments = async function () {
    if (!el.ticketId.value) return showAlert('warning', 'Chưa chọn mã phiếu');
    const payload = {
      ticketId: el.ticketId.value,
      shiftType: el.shiftType.value,
      startDate: el.startDate.value,
      endDate: el.endDate.value,
      percentage: Number(el.percentage.value || 0),
      quantity: Number(el.shiftQty.textContent || 0),
      workerIds: Array.from(selectedWorkers)
    };

    try {
      const ret = await jpost(`${API}?action=save`, payload);
      if (!ret.ok) return showAlert('danger', ret.message || 'Lưu thất bại');
      showAlert('success', 'Đã lưu phân công');
      selectedWorkers.clear();
      await loadAssignmentTable();
    } catch (e) {
      showAlert('danger', 'Lỗi lưu: ' + e.message);
    }
  };

  // ====== Events ======
  el.startDate?.addEventListener('change', calculateDays);
  el.endDate?.addEventListener('change', calculateDays);
  el.percentage?.addEventListener('input', calculateQuantity);
  el.shiftType?.addEventListener('change', () => loadWorkers(currentTicket?.workshop || ''));

  // ====== Init ======
  document.addEventListener('DOMContentLoaded', async () => {
    try { await loadTicketOptions(); } catch (e) { }
    // Nếu trong select đã có sẵn P00x thì load luôn
    if (el.ticketId.value) await loadTicketData();
  });
  window.calculateDays = calculateDays;
  window.calculateQuantity = calculateQuantity;
})();
