<?php ob_start();
// $lines provided
$pageTitle = 'Theo dõi tiến độ sản xuất';

// Auto-generate current month date range
$today = new DateTime();
$firstDayOfMonth = new DateTime($today->format('Y-m-01'));
$lastDayOfMonth = clone $today;
$lastDayOfMonth->modify('last day of this month');

$defaultStartDate = $firstDayOfMonth->format('Y-m-d');
$defaultEndDate = $lastDayOfMonth->format('Y-m-d');

// Use provided dates or defaults
$startDate = !empty($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : $defaultStartDate;
$endDate = !empty($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : $defaultEndDate;
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

                    <?php
                    // Sort lines by TenDayChuyen ascending so UI shows DB names in order.
                    if (!empty($lines) && is_array($lines)) {
                        // Natural sort: prefer MaDayChuyen numeric suffix if available (DC01 -> 1),
                        // otherwise fall back to TenDayChuyen string compare.
                        usort($lines, function($a, $b) {
                            $aCode = trim((string)($a['MaDayChuyen'] ?? ''));
                            $bCode = trim((string)($b['MaDayChuyen'] ?? ''));
                            $aName = trim((string)($a['TenDayChuyen'] ?? ''));
                            $bName = trim((string)($b['TenDayChuyen'] ?? ''));

                            // Helper to extract trailing number
                            $numA = null; $numB = null;
                            if (preg_match('/(\d+)$/', $aCode, $m)) $numA = intval($m[1]);
                            if (preg_match('/(\d+)$/', $bCode, $m)) $numB = intval($m[1]);

                            if ($numA !== null && $numB !== null) {
                                if ($numA === $numB) return strcasecmp($aCode, $bCode);
                                return $numA <=> $numB;
                            }

                            // If only one has numeric code, prefer that one first
                            if ($numA !== null && $numB === null) return -1;
                            if ($numA === null && $numB !== null) return 1;

                            // Fallback: compare names, empty names go last
                            if ($aName === '' && $bName === '') return 0;
                            if ($aName === '') return 1;
                            if ($bName === '') return -1;
                            return strcasecmp($aName, $bName);
                        });
                    }
                    ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="get" action="<?= BASE_URL ?>xuongtruong/tien-do/show">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="daychuyen" class="form-label">Dây chuyền</label>
                        <select id="daychuyen" name="ma" class="form-select" required>
                            <option value="">-- Chọn dây chuyền --</option>
                            <?php
                                $actualLines = array_filter($lines, function($r){ return empty($r['is_placeholder']) && !empty($r['MaDayChuyen']); });
                                foreach ($actualLines as $l):
                            ?>
                                <option value="<?= htmlspecialchars($l['MaDayChuyen']) ?>"><?= htmlspecialchars($l['TenDayChuyen']) ?> (<?= htmlspecialchars($l['MaDayChuyen']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Ngày bắt đầu</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" value="<?= $startDate ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Ngày kết thúc</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" value="<?= $endDate ?>" required>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" type="submit" title="Xem thống kê"><i class="bi bi-search"></i> Xem</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-4">
        <h5 class="mb-3">Danh sách dây chuyền</h5>
        <!-- debug info removed -->
        <?php if (!empty($rawLines) && isset($_GET['_dbg']) && $_GET['_dbg']=='1'): ?>
            <pre class="p-2 bg-light border small mb-3" style="max-height:200px;overflow:auto"><?= htmlspecialchars(json_encode($rawLines, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)) ?></pre>
        <?php endif; ?>
        <div id="canonical-lines" class="row">
            <?php foreach ($lines as $l): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <?php $title = !empty($l['TenDayChuyen']) ? $l['TenDayChuyen'] : 'Chưa cấu hình'; ?>
                                <h6 class="card-title mb-0"><?= htmlspecialchars($title) ?></h6>
                                <?php
                                    $status = $l['TrangThai'] ?? null;
                                    if (empty($l['MaDayChuyen'])) {
                                        $status = 'Chưa cấu hình';
                                    }
                                    $badgeClass = 'secondary';
                                    if ($status === 'Đang hoạt động') {
                                        $badgeClass = 'success';
                                    } elseif ($status === 'Tạm dừng') {
                                        $badgeClass = 'warning text-dark';
                                    } elseif ($status === 'Ngưng hoạt động') {
                                        $badgeClass = 'danger';
                                    } elseif ($status === 'Chưa cấu hình') {
                                        $badgeClass = 'secondary';
                                    }
                                ?>
                                <span class="badge bg-<?= $badgeClass ?> ms-2"><?= htmlspecialchars($status) ?></span>
                            </div>
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
                                    <a class="btn btn-outline-primary btn-sm" href="<?= BASE_URL ?>xuongtruong/tien-do/show?ma=<?= urlencode($l['MaDayChuyen']) ?>&start_date=<?= $defaultStartDate ?>&end_date=<?= $defaultEndDate ?>">Xem</a>
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

<!-- canonical API client rendering removed: showing all DB lines server-side -->
    </div>
</div>

<!-- small icon library (Bootstrap icons fallback) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>
