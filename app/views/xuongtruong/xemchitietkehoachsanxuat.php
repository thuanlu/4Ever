<?php
// File xemchitietkehoachsanxuat.php - View chi tiết kế hoạch sản xuất
// Biến truyền vào: $kehoach (thông tin tổng), $nhanvien, $donhang, $chitietkehoach (mảng chi tiết sản phẩm, phân xưởng)
?>
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <strong>Chi tiết kế hoạch sản xuất</strong>
    </div>
    
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-6"><strong>Mã KH:</strong> <?php echo htmlspecialchars($kehoach['MaKeHoach'] ?? ''); ?></div>
            <div class="col-md-6"><strong>Tên kế hoạch:</strong> <?php echo htmlspecialchars($kehoach['TenKeHoach'] ?? ''); ?></div>
        </div>
        <div class="row mb-2">
            <div class="col-md-6"><strong>Ngày bắt đầu:</strong> <?php echo htmlspecialchars($kehoach['NgayBatDau'] ?? ''); ?></div>
            <div class="col-md-6"><strong>Ngày kết thúc:</strong> <?php echo htmlspecialchars($kehoach['NgayKetThuc'] ?? ''); ?></div>
        </div>
        <div class="row mb-2">
            <div class="col-md-6"><strong>Người lập:</strong> <?php echo htmlspecialchars($nhanvien['HoTen'] ?? ''); ?></div>
            <div class="col-md-6"><strong>Mã đơn hàng:</strong> <?php echo htmlspecialchars($kehoach['MaDonHang'] ?? ''); ?></div>
        </div>
        <div class="row mb-2">
            <div class="col-md-6"><strong>Tên đơn hàng:</strong> <?php echo htmlspecialchars($donhang['TenDonHang'] ?? ''); ?></div>
            <div class="col-md-6"><strong>Trạng thái:</strong> <?php echo htmlspecialchars($kehoach['TrangThai'] ?? ''); ?></div>
        </div>
        <div class="row mb-2">
            <div class="col-md-6"><strong>Tổng chi phí dự kiến:</strong> <?php echo number_format($kehoach['TongChiPhiDuKien'] ?? 0); ?> VNĐ</div>
            <div class="col-md-6"><strong>Số lượng công nhân cần:</strong> <?php echo htmlspecialchars($kehoach['SoLuongCongNhanCan'] ?? ''); ?></div>
        </div>
        <div class="row mb-2">
            <div class="col-md-12"><strong>Ghi chú:</strong> <?php echo htmlspecialchars($kehoach['GhiChu'] ?? ''); ?></div>
        </div>
        <hr>
        <h5 class="mt-3 mb-3" style="font-weight:600;color:#6a89cc;">Chi tiết sản phẩm & phân xưởng</h5>
        <div class="table-responsive">
            <table class="table table-bordered" style="background:#fff;border-radius:8px;overflow:hidden;">
                <thead style="background:#f6f6f6;">
                    <tr>
                        <th style="font-weight:600;">Mã SP</th>
                        <th style="font-weight:600;">Tên sản phẩm</th>
                        <th style="font-weight:600;">Phân xưởng</th>
                        <th style="font-weight:600;">Sản lượng mục tiêu</th>
                        <th style="font-weight:600;">Cần bổ sung</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($chitietkehoach as $ct): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ct['MaSanPham']); ?></td>
                        <td><?php echo htmlspecialchars($ct['TenSanPham'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($ct['TenPhanXuong'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($ct['SanLuongMucTieu']); ?></td>
                        <td><?php echo htmlspecialchars($ct['CanBoSung']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
