<?php
// File xemchitietkehoachsanxuat.php - View chi tiết kế hoạch sản xuất
// Biến truyền vào: $kehoach (thông tin tổng), $nhanvien, $donhang, $chitietkehoach (mảng chi tiết sản phẩm, phân xưởng)
?>

<style>
.detail-header-gradient {
    background: linear-gradient(90deg, #6a89cc 0%, #b8e994 100%);
    color: #fff;
    font-weight: 600;
    font-size: 1.1rem;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    padding: 12px 24px;
    margin-bottom: 0;
}
.detail-card {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    background: #fff;
    margin-bottom: 24px;
}
.detail-info-row {
    margin-bottom: 8px;
}
.detail-label {
    font-weight: 500;
    color: #34495e;
}
.detail-value {
    font-weight: 400;
    color: #222;
}
.detail-table th {
    background: #f6f6f6;
    font-weight: 600;
}
.detail-table td, .detail-table th {
    vertical-align: middle;
}
</style>

<div class="detail-card">
    <div class="detail-header-gradient">
        Chi tiết kế hoạch sản xuất
    </div>
    <div class="p-4">
        <div class="row detail-info-row">
            <div class="col-md-6"><span class="detail-label">Mã KH:</span> <span class="detail-value"><?php echo htmlspecialchars($kehoach['MaKeHoach'] ?? ''); ?></span></div>
            <div class="col-md-6"><span class="detail-label">Tên kế hoạch:</span> <span class="detail-value"><?php echo htmlspecialchars($kehoach['TenKeHoach'] ?? ''); ?></span></div>
        </div>
        <div class="row detail-info-row">
            <div class="col-md-6"><span class="detail-label">Ngày bắt đầu:</span> <span class="detail-value"><?php echo htmlspecialchars($kehoach['NgayBatDau'] ?? ''); ?></span></div>
            <div class="col-md-6"><span class="detail-label">Ngày kết thúc:</span> <span class="detail-value"><?php echo htmlspecialchars($kehoach['NgayKetThuc'] ?? ''); ?></span></div>
        </div>
        <div class="row detail-info-row">
            <div class="col-md-6"><span class="detail-label">Người lập:</span> <span class="detail-value"><?php echo htmlspecialchars($nhanvien['HoTen'] ?? ''); ?></span></div>
            <div class="col-md-6"><span class="detail-label">Mã đơn hàng:</span> <span class="detail-value"><?php echo htmlspecialchars($kehoach['MaDonHang'] ?? ''); ?></span></div>
        </div>
        <div class="row detail-info-row">
            <div class="col-md-6"><span class="detail-label">Tên đơn hàng:</span> <span class="detail-value"><?php echo htmlspecialchars($donhang['TenDonHang'] ?? ''); ?></span></div>
            <div class="col-md-6"><span class="detail-label">Trạng thái:</span> <span class="detail-value"><?php echo htmlspecialchars($kehoach['TrangThai'] ?? ''); ?></span></div>
        </div>
        <div class="row detail-info-row">
            <div class="col-md-6"><span class="detail-label">Tổng chi phí dự kiến:</span> <span class="detail-value"><?php echo number_format($kehoach['TongChiPhiDuKien'] ?? 0); ?> VNĐ</span></div>
            <div class="col-md-6"><span class="detail-label">Số lượng công nhân cần:</span> <span class="detail-value"><?php echo htmlspecialchars($kehoach['SoLuongCongNhanCan'] ?? ''); ?></span></div>
        </div>
        <div class="row detail-info-row">
            <div class="col-md-12"><span class="detail-label">Ghi chú:</span> <span class="detail-value"><?php echo htmlspecialchars($kehoach['GhiChu'] ?? ''); ?></span></div>
        </div>
        <hr>
        <h5 class="mt-3 mb-3" style="font-weight:600;color:#6a89cc;">Chi tiết sản phẩm & phân xưởng</h5>
        <div class="table-responsive">
            <table class="table table-bordered detail-table">
                <thead>
                    <tr>
                        <th>Mã SP</th>
                        <th>Tên sản phẩm</th>
                        <th>Phân xưởng</th>
                        <th>Sản lượng mục tiêu</th>
                        <th>Cần bổ sung</th>
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
