<?php ob_start(); ?>

<div class="container mt-4">
    <?php if (!empty($msg)): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> <strong><?= $msg ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="card mt-3 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Danh sách Yêu cầu Xuất nguyên liệu</h4>
        </div>
        <div class="card-body">
            <?php if (empty($requests)): ?>
                <p class="text-center text-muted my-4">Không có phiếu yêu cầu nào đang chờ.</p>
            <?php else: ?>
                <?php foreach ($requests as $r): ?>
                <div class="p-3 mb-3 bg-light rounded border d-flex justify-content-between align-items-center hover-shadow">
                    <div>
                        <strong class="text-primary fs-5">Mã Phiếu: <?= htmlspecialchars($r['MaPhieuYC']) ?></strong><br>
                        <span class="text-muted">
                            <i class=""></i>Người yêu cầu: <?= htmlspecialchars($r['NguoiYeuCau']) ?> 
                            | <i class=""></i>Phân xưởng nhận: <?= htmlspecialchars($r['TenPhanXuong']) ?>
                            | <i class=""></i>Ngày yêu cầu: <?= date('d/m/Y', strtotime($r['NgayLap'])) ?>
                        </span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-warning text-dark me-3 px-3 py-2">
                            <?= htmlspecialchars($r['TrangThai']) ?>
                        </span>

                        <a href="<?= BASE_URL ?>kho/xuatnguyenlieu/create/<?= $r['MaPhieuYC'] ?>" class="btn btn-outline-primary fw-bold">
                            Lập phiếu xuất <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        transition: 0.3s;
        background-color: #fff !important;
    }
</style>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>