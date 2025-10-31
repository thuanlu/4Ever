<?php ob_start(); ?>
<div class="container mt-4">
    <div class="card mt-3">
        <div class="card-header">
            <h3>Phiếu Yêu Cầu Kiểm Tra Chờ Xử Lý</h3>
        </div>
        <div class="card-body">
            <?php foreach ($phieus as $p): ?>
            <div class="p-3 mb-2 bg-light rounded d-flex justify-content-between align-items-center">
                <div>
                    <strong class="text-primary">Mã Phiếu: <?= $p['MaPhieuKT'] ?></strong><br>
                    Sản phẩm: <?= $p['TenSanPham'] ?> | Ngày Yêu Cầu: <?= date('d/m/Y', strtotime($p['NgayKiemTra'])) ?>
                </div>
                <div>
                    <span class="badge bg-warning text-dark me-3">
                        <?= $p['TrangThai'] == 'Chờ kiểm tra' ? $p['TrangThai'] : '' ?>
                    </span>

                    <a href="<?= BASE_URL ?>qc/view/<?= $p['MaPhieuKT'] ?>" class="text-primary text-decoration-none">Ghi nhận →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>