<!-- Form kế hoạch sản xuất (dùng cho tạo & sửa) -->
<div class="container mt-4">
    <h2><?php echo isset($kehoach) ? 'Sửa kế hoạch sản xuất' : 'Tạo kế hoạch sản xuất mới'; ?></h2>
    <form method="post">
        <div class="mb-3">
            <label>Mã kế hoạch</label>
            <input type="text" name="MaKeHoach" class="form-control" value="<?php echo isset($kehoach) ? htmlspecialchars($kehoach['MaKeHoach']) : ''; ?>" <?php echo isset($kehoach) ? 'readonly' : ''; ?> required>
        </div>
        <div class="mb-3">
            <label>Tên kế hoạch</label>
            <input type="text" name="TenKeHoach" class="form-control" value="<?php echo isset($kehoach) ? htmlspecialchars($kehoach['TenKeHoach']) : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label>Ngày bắt đầu</label>
            <input type="date" name="NgayBatDau" class="form-control" value="<?php echo isset($kehoach) ? htmlspecialchars($kehoach['NgayBatDau']) : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label>Ngày kết thúc</label>
            <input type="date" name="NgayKetThuc" class="form-control" value="<?php echo isset($kehoach) ? htmlspecialchars($kehoach['NgayKetThuc']) : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label>Đơn hàng</label>
            <select name="MaDonHang" class="form-control" required>
                <option value="">-- Chọn đơn hàng --</option>
                <?php if (!empty($donhangs)) foreach ($donhangs as $dh): ?>
                    <option value="<?php echo htmlspecialchars($dh['MaDonHang']); ?>" <?php echo (isset($kehoach) && $kehoach['MaDonHang'] === $dh['MaDonHang']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($dh['MaDonHang'] . ' - ' . $dh['TenDonHang']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if (isset($kehoach)): ?>
        <div class="mb-3">
            <label>Trạng thái</label>
            <select name="TrangThai" class="form-control">
                <option value="Chờ duyệt" <?php if($kehoach['TrangThai']==='Chờ duyệt') echo 'selected'; ?>>Chờ duyệt</option>
                <option value="Đã duyệt" <?php if($kehoach['TrangThai']==='Đã duyệt') echo 'selected'; ?>>Đã duyệt</option>
                <option value="Đang thực hiện" <?php if($kehoach['TrangThai']==='Đang thực hiện') echo 'selected'; ?>>Đang thực hiện</option>
                <option value="Hoàn thành" <?php if($kehoach['TrangThai']==='Hoàn thành') echo 'selected'; ?>>Hoàn thành</option>
            </select>
        </div>
        <?php endif; ?>
        <div class="mb-3">
            <label>Tổng chi phí dự kiến</label>
            <input type="number" step="0.01" name="TongChiPhiDuKien" class="form-control" value="<?php echo isset($kehoach) ? htmlspecialchars($kehoach['TongChiPhiDuKien']) : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label>Số lượng công nhân cần</label>
            <input type="number" name="SoLuongCongNhanCan" class="form-control" value="<?php echo isset($kehoach) ? htmlspecialchars($kehoach['SoLuongCongNhanCan']) : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label>Ghi chú</label>
            <textarea name="GhiChu" class="form-control"><?php echo isset($kehoach) ? htmlspecialchars($kehoach['GhiChu']) : ''; ?></textarea>
        </div>
        <button type="submit" class="btn btn-success"><?php echo isset($kehoach) ? 'Cập nhật' : 'Lưu kế hoạch'; ?></button>
        <a href="<?php echo BASE_URL; ?>kehoachsanxuat" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
