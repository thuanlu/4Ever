<?php
// views/totruong/phancalamviec.php
$pageTitle     = 'Phân công & Lập ca công việc';
$menuActive    = 'phanca';
$pageBodyClass = 'layout-with-sidebar page-shift-assign';

ob_start();
?>
<div class="container-fluid container-narrow py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="page-title mb-0">Thông tin phân công ca làm việc</h5>
  </div>

  <!-- Hàng input 1 -->
  <div class="row g-3 align-items-end">
    <div class="col-lg-2 col-md-3">
      <label class="form-label form-label-sm">Mã phiếu</label>
      <select id="ticketId" class="form-select" onchange="loadTicketData()">
        <!-- JS sẽ thay bằng danh sách thật từ API; để mẫu cho dễ test -->
        <option value="">-- Chọn --</option>
        <option value="P001">P001</option>
        <option value="P002">P002</option>
        <option value="P003">P003</option>
      </select>
    </div>

    <div class="col-lg-3 col-md-4">
      <label class="form-label form-label-sm">Công việc</label>
      <input type="text" id="workName" class="form-control" placeholder="Sản xuất giày/dép..." readonly>
    </div>

    <div class="col-lg-2 col-md-3">
      <label class="form-label form-label-sm">Số lượng</label>
      <input type="number" id="totalQty" class="form-control" readonly>
    </div>

    <div class="col-lg-1 col-md-2">
      <label class="form-label form-label-sm">Size</label>
      <input type="text" id="prodSize" class="form-control" readonly>
    </div>

    <div class="col-lg-2 col-md-3">
      <label class="form-label form-label-sm">Màu</label>
      <input type="text" id="prodColor" class="form-control" readonly>
    </div>

    <div class="col-lg-2 col-md-3">
      <label class="form-label form-label-sm">Ngày bắt đầu</label>
      <input type="date" id="startDate" class="form-control" onchange="calculateDays()">
    </div>

    <div class="col-lg-2 col-md-3">
      <label class="form-label form-label-sm">Ngày hoàn thành</label>
      <input type="date" id="endDate" class="form-control" onchange="calculateDays()">
    </div>

    <div class="col-lg-2 col-md-3">
      <label class="form-label form-label-sm">Chọn ca làm</label>
      <select id="shiftType" class="form-select" onchange="loadWorkers()">
        <option value="morning" selected>Ca Sáng</option>
        <option value="afternoon">Ca Tối</option>
        <option value="night">Ca Đêm</option>
      </select>
    </div>

    <div class="col-lg-2 col-md-3">
      <label class="form-label form-label-sm">Tỷ lệ (%)</label>
      <input type="number" id="percentage" class="form-control" value="50" min="0" max="100" oninput="calculateQuantity()">
    </div>

    <div class="col-lg-2 col-md-3">
      <label class="form-label form-label-sm">Số ngày</label>
      <div class="stat-tile"><div id="totalDays" class="stat-value">0</div></div>
    </div>
  </div>

  <!-- Hàng thống kê -->
  <div class="row g-3 mt-1">
    <div class="col-lg-3 col-md-6">
      <div class="stat-tile"><div class="stat-label">SL hoàn thành/ngày</div><div id="qtyPerDay" class="stat-value">0</div></div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="stat-tile"><div class="stat-label">Số lượng ca này</div><div id="shiftQty" class="stat-value">0</div></div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="stat-tile"><div class="stat-label">Số công nhân</div><div id="workerCount" class="stat-value">0</div></div>
    </div>
    <div class="col-lg-3 col-md-6">
      <div class="stat-tile"><div class="stat-label">Mỗi người làm</div><div id="qtyPerWorker" class="stat-value">0</div></div>
    </div>
  </div>

  <!-- Danh sách công nhân -->
  <div class="card mt-3">
    <div class="card-body">
      <h6 class="section-title">Danh sách công nhân ca <span id="shiftName">Sáng</span></h6>
      <div id="workerGrid"></div>
      <div class="d-flex gap-2 mt-3 justify-content-end">
        <button class="btn btn-primary" onclick="addToList()">Thêm vào danh sách</button>
        <button class="btn btn-success" onclick="saveAllAssignments()">Lưu tất cả</button>
      </div>
    </div>
  </div>

  <!-- Danh sách đã thiết lập -->
  <div class="card mt-3">
    <div class="card-body">
      <div class="list-title">Danh sách ca đã thiết lập</div>
      <div id="alertBox"></div>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead class="table-light">
            <tr>
              <th width="50">STT</th>
              <th>Mã phiếu</th>
              <th>Công việc</th>
              <th>Ca làm</th>
              <th>Ngày bắt đầu</th>
              <th>Ngày hoàn thành</th>
              <th>Tỷ lệ</th>
              <th>Số lượng</th>
              <th>Công nhân</th>
              <th width="80">Thao tác</th>
            </tr>
          </thead>
          <tbody id="assignmentTable">
            <tr><td colspan="10" class="text-center text-muted py-4">Chưa có ca làm việc nào được thiết lập</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>
