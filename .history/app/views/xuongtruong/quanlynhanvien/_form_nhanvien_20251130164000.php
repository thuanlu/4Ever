<?php
// Form nhập/sửa nhân viên, dùng cho cả thêm và sửa
// Các biến: $nv (nếu sửa), hoặc để trống nếu thêm mới
?>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-bold">Mã nhân viên <span class="text-danger">*</span></label>
        <input type="text" name="MaNV" class="form-control" value="<?= htmlspecialchars($nv['MaNV'] ?? '') ?>" required maxlength="10">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Họ tên <span class="text-danger">*</span></label>
        <input type="text" name="HoTen" class="form-control" value="<?= htmlspecialchars($nv['HoTen'] ?? '') ?>" required maxlength="100">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Giới tính <span class="text-danger">*</span></label>
        <select name="GioiTinh" class="form-select" required>
            <option value="">--Chọn--</option>
            <option value="Nam" <?= (isset($nv['GioiTinh']) && $nv['GioiTinh'] == 'Nam') ? 'selected' : '' ?>>Nam</option>
            <option value="Nữ" <?= (isset($nv['GioiTinh']) && $nv['GioiTinh'] == 'Nữ') ? 'selected' : '' ?>>Nữ</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Năm sinh <span class="text-danger">*</span></label>
        <input type="number" name="NamSinh" class="form-control" value="<?= htmlspecialchars($nv['NamSinh'] ?? '') ?>" required min="1950" max="<?= date('Y') ?>">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Chức vụ <span class="text-danger">*</span></label>
        <input type="text" name="ChucVu" class="form-control" value="<?= htmlspecialchars($nv['ChucVu'] ?? '') ?>" required maxlength="50">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Bộ phận</label>
        <input type="text" name="BoPhan" class="form-control" value="<?= htmlspecialchars($nv['BoPhan'] ?? '') ?>" maxlength="50">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Số điện thoại</label>
        <input type="text" name="SoDienThoai" class="form-control" value="<?= htmlspecialchars($nv['SoDienThoai'] ?? '') ?>" maxlength="15">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Mật khẩu</label>
        <input type="text" name="Password" class="form-control" value="<?= htmlspecialchars($nv['Password'] ?? '') ?>" maxlength="255">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
        <input type="text" name="TrangThai" class="form-control" value="<?= htmlspecialchars($nv['TrangThai'] ?? '') ?>" required maxlength="20">
    </div>
</div>
