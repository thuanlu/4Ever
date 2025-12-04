<?php
// Form nhập/sửa nhân viên, dùng cho cả thêm và sửa
// Các biến: $nv (nếu sửa), hoặc để trống nếu thêm mới
?>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-bold">Mã nhân viên <span class="text-danger">*</span></label>
        <input type="text" name="manv" class="form-control" value="<?= htmlspecialchars($nv['manv'] ?? '') ?>" required pattern="^[A-Za-z0-9]+$" maxlength="20">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Họ đệm <span class="text-danger">*</span></label>
        <input type="text" name="hodem" class="form-control" value="<?= htmlspecialchars($nv['hodem'] ?? '') ?>" required pattern="^[A-Za-zÀ-ỹ\s]+$" maxlength="50">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Tên <span class="text-danger">*</span></label>
        <input type="text" name="ten" class="form-control" value="<?= htmlspecialchars($nv['ten'] ?? '') ?>" required pattern="^[A-Za-zÀ-ỹ\s]+$" maxlength="30">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
        <input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars($nv['sdt'] ?? '') ?>" required pattern="^0[0-9]{9}$" maxlength="10">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Giới tính <span class="text-danger">*</span></label>
        <select name="gioitinh" class="form-select" required>
            <option value="">--Chọn--</option>
            <option value="Nam" <?= (isset($nv['gioitinh']) && $nv['gioitinh'] == 'Nam') ? 'selected' : '' ?>>Nam</option>
            <option value="Nữ" <?= (isset($nv['gioitinh']) && $nv['gioitinh'] == 'Nữ') ? 'selected' : '' ?>>Nữ</option>
            <option value="Khác" <?= (isset($nv['gioitinh']) && $nv['gioitinh'] == 'Khác') ? 'selected' : '' ?>>Khác</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Năm sinh <span class="text-danger">*</span></label>
        <input type="number" name="namsinh" class="form-control" value="<?= htmlspecialchars($nv['namsinh'] ?? '') ?>" required min="<?= date('Y')-65 ?>" max="<?= date('Y')-18 ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Chức vụ <span class="text-danger">*</span></label>
        <select name="chucvu" class="form-select" required>
            <option value="">--Chọn--</option>
            <option value="Công nhân" <?= (isset($nv['chucvu']) && $nv['chucvu'] == 'Công nhân') ? 'selected' : '' ?>>Công nhân</option>
            <option value="Tổ trưởng" <?= (isset($nv['chucvu']) && $nv['chucvu'] == 'Tổ trưởng') ? 'selected' : '' ?>>Tổ trưởng</option>
            <option value="Xưởng trưởng" <?= (isset($nv['chucvu']) && $nv['chucvu'] == 'Xưởng trưởng') ? 'selected' : '' ?>>Xưởng trưởng</option>
            <option value="Quản lý" <?= (isset($nv['chucvu']) && $nv['chucvu'] == 'Quản lý') ? 'selected' : '' ?>>Quản lý</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
        <select name="trangthai" class="form-select" required>
            <option value="">--Chọn--</option>
            <option value="Đang làm" <?= (isset($nv['trangthai']) && $nv['trangthai'] == 'Đang làm') ? 'selected' : '' ?>>Đang làm</option>
            <option value="Nghỉ việc" <?= (isset($nv['trangthai']) && $nv['trangthai'] == 'Nghỉ việc') ? 'selected' : '' ?>>Nghỉ việc</option>
            <option value="Tạm nghỉ" <?= (isset($nv['trangthai']) && $nv['trangthai'] == 'Tạm nghỉ') ? 'selected' : '' ?>>Tạm nghỉ</option>
        </select>
    </div>
</div>
