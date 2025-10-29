<?php
// [FILE GIỮ NGUYÊN: app/views/kehoachsanxuat/index_nvl.php]

$pageTitle = "Danh sách Phiếu Đặt NVL";
ob_start();
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Danh sách Phiếu Đặt Hàng Nguyên Vật Liệu</h5>
        <a href="<?php echo BASE_URL; ?>kehoachsanxuat/phieudatnvl/create" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tạo Phiếu Mới
        </a>
    </div>
    <div class="card-body">
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): // Thêm để hiển thị lỗi khi xem phiếu không tồn tại ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Mã Phiếu</th>
                        <th scope="col">Tên Phiếu</th>
                        <th scope="col">Ngày Lập</th>
                        <th scope="col">Người Lập</th>
                        <th scope="col">Tổng Chi Phí (dự kiến)</th>
                        <th scope="col">Trạng Thái</th>
                        <th scope="col" style="width: 100px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($phieuDatList)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Chưa có phiếu đặt hàng nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($phieuDatList as $phieu): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($phieu['MaPhieu']); ?></td>
                                <td><?php echo htmlspecialchars($phieu['TenPhieu']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($phieu['NgayLapPhieu'])); ?></td>
                                <td><?php echo htmlspecialchars($phieu['NguoiLapPhieu']); ?></td>
                                <td class="text-end"><?php echo number_format($phieu['TongChiPhiDuKien'], 0, ',', '.'); ?> VND</td>
                                <td>
                                    <?php
                                        $status = htmlspecialchars($phieu['TrangThai']);
                                        $badge_class = 'badge '; // Bắt đầu với class chung

                                        // Sửa logic điều kiện
                                        if ($status === 'Đã duyệt') {
                                            $badge_class .= 'bg-success'; // Màu xanh lá
                                        } elseif ($status === 'Đã hủy') {
                                            $badge_class .= 'bg-danger'; // Màu đỏ
                                        } else { // Mặc định là 'Chờ duyệt'
                                            $badge_class .= 'bg-warning text-dark'; // Màu vàng
                                        }
                                    ?>
                                    <span class="<?php echo $badge_class; ?>"><?php echo $status; ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo BASE_URL; ?>kehoachsanxuat/phieudatnvl/view/<?php echo $phieu['MaPhieu']; ?>" class="btn btn-sm btn-info" title="Xem">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once APP_PATH . '/views/layouts/main.php';
?>