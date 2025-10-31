<?php $pageTitle = "Kế hoạch Sản xuất"; ?>
<?php

ob_start();
?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-clipboard-list me-2"></i>Danh sách Kế hoạch Sản xuất</h2>
        <a href="<?php echo BASE_URL; ?>kehoachsanxuat/create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tạo Kế hoạch Mới
        </a>
    </div>

    <?php if (isset($error_message)): // Hiển thị lỗi nếu Controller truyền sang ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): // Hiển thị lỗi từ session ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Mã KH</th>
                            <th scope="col">Tên kế hoạch</th>
                            <th scope="col">Ngày Bắt đầu</th>
                            <th scope="col">Ngày Kết thúc</th>
                            <th scope="col">Đơn hàng</th>
                            <th scope="col">Người lập</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col" class="text-center">Thao tác</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($kehoachs)): ?>
                            <?php foreach ($kehoachs as $k): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($k['MaKeHoach']); ?></td>
                                <td><?php echo htmlspecialchars($k['TenKeHoach']); ?></td>
                                <td><?php echo htmlspecialchars($k['NgayBatDau']); ?></td>
                                <td><?php echo htmlspecialchars($k['NgayKetThuc']); ?></td>
                                <td><?php echo htmlspecialchars($k['TenDonHang']); ?></td>
                                <td><?php echo htmlspecialchars($k['NguoiLap']); ?></td>
                                <td>
                                    <?php 
                                        $status = htmlspecialchars($k['TrangThai']);
                                        $badge_class = 'badge ';
                                        if ($status === 'Hoàn thành') $badge_class .= 'bg-success';
                                        else if ($status === 'Đã duyệt') $badge_class .= 'bg-primary';
                                        else if ($status === 'Đang thực hiện') $badge_class .= 'bg-info text-dark';
                                        else if ($status === 'Hủy bỏ') $badge_class .= 'bg-danger';
                                        else $badge_class .= 'bg-warning text-dark'; // Chờ duyệt
                                    ?>
                                    <span class="<?php echo $badge_class; ?>"><?php echo $status; ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo BASE_URL; ?>kehoachsanxuat/view/<?php echo $k['MaKeHoach']; ?>" class="btn btn-sm btn-outline-info me-1" title="Xem">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>kehoachsanxuat/edit/<?php echo $k['MaKeHoach']; ?>" class="btn btn-sm btn-outline-warning" title="Sửa">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <?php echo (isset($error_message)) ? 'Không thể tải dữ liệu.' : 'Chưa có kế hoạch sản xuất nào được tạo.'; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
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