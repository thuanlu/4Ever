<?php ob_start(); ?>
<div class="container mt-4">
     <div class="card mt-3">
     <div class="card-header">
            <h3>Lịch sử Phiếu kết quả kiểm định</h3>
        </div>
    <div class="card-body">
    <table class="table table-bordered table-striped mt-3">
        <thead class="table-light">
            <tr>
                <th>STT</th>
                <th>Mã KD</th>
                <th>Mã phiếu KT</th>
                <th>Mã lô</th>
                <th>Sản phẩm</th>
                <th>Ngày lập</th>
                <th>Người kiểm tra</th>
                <th>Kết quả</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($history)) : ?>
                <?php $i = 1; foreach($history as $h) : ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $h['MaKD']; ?></td>
                        <td><?php echo $h['MaPhieuKT']; ?></td>
                        <td><?php echo $h['MaLoHang']; ?></td>
                        <td><?php echo $h['TenSanPham']; ?></td>
                        <td><?php echo $h['NgayLap']; ?></td>
                        <td><?php echo $h['NguoiKiemTra']; ?></td>
                        <td><?php echo $h['KetQua']; ?></td>
                        <td><?php echo $h['TrangThai']; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8" class="text-center">Chưa có lịch sử kiểm định.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>
