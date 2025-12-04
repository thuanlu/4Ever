<?php ob_start();
// $lines provided
$pageTitle = 'Theo dõi tiến độ sản xuất';
?>
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h3 class="mb-1">Theo dõi tiến độ sản xuất</h3>
                    <small class="text-muted">Chọn dây chuyền để xem tiến độ và thống kê trong khoảng ngày mong muốn.</small>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="get" action="<?= BASE_URL ?>xuongtruong/tien-do/show">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="daychuyen" class="form-label">Dây chuyền</label>
                        <select id="daychuyen" name="ma" class="form-select" required>
                            <option value="">-- Chọn dây chuyền --</option>
                            <?php
                                // Only list real configured dây chuyền in the select (skip placeholders)
                                $actualLines = array_filter($lines, function($r){ return empty($r['is_placeholder']) && !empty($r['MaDayChuyen']); });
                                foreach ($actualLines as $l):
                            ?>
                                <option value="<?= htmlspecialchars($l['MaDayChuyen']) ?>"><?= htmlspecialchars($l['TenDayChuyen']) ?> (<?= htmlspecialchars($l['MaDayChuyen']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Ngày bắt đầu</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Ngày kết thúc</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary w-100" type="submit" title="Xem thống kê"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-4">
        <h5 class="mb-3">Danh sách dây chuyền</h5>
        <div class="row">
            <?php foreach ($lines as $l): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-1"><?= htmlspecialchars($l['TenDayChuyen']) ?></h6>
                            <p class="text-muted mb-2 small">
                                <?php if (!empty($l['MaDayChuyen'])): ?>
                                    Mã: <?= htmlspecialchars($l['MaDayChuyen']) ?> · Phân xưởng: <?= htmlspecialchars($l['MaPhanXuong']) ?>
                                <?php else: ?>
                                    <em class="text-muted">Chưa cấu hình</em>
                                <?php endif; ?>
                            </p>
                            <div class="mt-auto d-flex gap-2">
                                <?php if (!empty($l['is_placeholder'])): ?>
                                    <button class="btn btn-outline-secondary btn-sm" disabled>Chưa có dữ liệu</button>
                                <?php else: ?>
                                    <a class="btn btn-outline-primary btn-sm" href="<?= BASE_URL ?>xuongtruong/tien-do/show/<?= urlencode($l['MaDayChuyen']) ?>">Xem</a>
                                    <a class="btn btn-outline-secondary btn-sm" href="#" onclick="navigator.clipboard?.writeText('<?= htmlspecialchars($l['MaDayChuyen']) ?>');return false;">Sao chép mã</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- small icon library (Bootstrap icons fallback) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>
