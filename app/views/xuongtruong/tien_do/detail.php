<?php ob_start();
// $line, $stats
$pageTitle = 'Thống kê & Tiến độ dây chuyền';
?>
<div class="container mt-4">
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h3 class="mb-0">Thống kê dây chuyền</h3>
                    <div class="text-muted small"><?= htmlspecialchars($line['TenDayChuyen']) ?> (<?= htmlspecialchars($line['MaDayChuyen']) ?>)</div>
                </div>
                <div>
                    <a href="<?= BASE_URL ?>xuongtruong/tien-do" class="btn btn-outline-secondary">Quay lại</a>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!$stats): ?>
        <div class="alert alert-warning">Chưa có dữ liệu thống kê cho dây chuyền này.</div>
    <?php else: ?>
    <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle text-muted">Kế hoạch </h6>
                        <div class="h2 fw-bold mt-2"><?= number_format($stats['planned']) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle text-muted">Hoàn thành</h6>
                        <div class="h2 text-success fw-bold mt-2"><?= number_format($stats['finished']) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle text-muted">Tỷ lệ hoàn thành</h6>
                        <?php $pct = $stats['planned'] > 0 ? round(($stats['finished'] / $stats['planned']) * 100, 1) : 0; ?>
                        <div class="h2 fw-bold mt-2"><?= $pct ?>%</div>
                        <div class="progress mt-2" style="height:10px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= max(0, min(100, $pct)) ?>%;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($stats['startDate']) && !empty($stats['endDate'])): ?>
            <div class="mb-3 text-muted">Khoảng: <strong><?= htmlspecialchars($stats['startDate']) ?></strong> → <strong><?= htmlspecialchars($stats['endDate']) ?></strong></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <canvas id="tienDoChart" style="max-height:360px; width:100%;"></canvas>
            </div>
        </div>

        <?php if (!empty($stats['labels']) && !empty($stats['data'])): ?>
            <!-- Chart.js -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                (function(){
                    const labels = <?= json_encode($stats['labels'], JSON_UNESCAPED_UNICODE) ?>;
                    const data = <?= json_encode($stats['data'], JSON_UNESCAPED_UNICODE) ?>;

                    const canvas = document.getElementById('tienDoChart');
                    const ctx = canvas.getContext('2d');

                    // Create a palette and per-bar colors (one color per day)
                    const palette = [
                        'rgba(58,123,213,0.95)',
                        'rgba(75,192,192,0.92)',
                        'rgba(255,159,64,0.92)',
                        'rgba(255,99,132,0.92)',
                        'rgba(153,102,255,0.92)',
                        'rgba(99,181,136,0.92)',
                        'rgba(134,185,255,0.92)'
                    ];
                    const bgColors = labels.map((_, i) => palette[i % palette.length]);
                    const borderColors = bgColors.map(c => c.replace('0.92', '1').replace('0.95','1'));

                    const maxVal = Math.max.apply(null, data.map(v => Number(v) || 0));
                    const suggestedMax = maxVal > 0 ? Math.ceil((maxVal + (maxVal * 0.15)) / 10) * 10 : undefined; // add 15% headroom and round
                    const stepSize = suggestedMax ? Math.ceil(suggestedMax / 5) : undefined;

                    // Optional shadow plugin for subtle depth
                    const shadowPlugin = {
                        id: 'shadowPlugin',
                        beforeDatasetsDraw: (chart) => {
                            const ctx = chart.ctx;
                            ctx.save();
                            ctx.shadowColor = 'rgba(0,0,0,0.06)';
                            ctx.shadowBlur = 8;
                            ctx.shadowOffsetX = 0;
                            ctx.shadowOffsetY = 3;
                        },
                        afterDatasetsDraw: (chart) => {
                            chart.ctx.restore();
                        }
                    };

                    // Plugin to draw value labels above bars
                    const valueLabelPlugin = {
                        id: 'valueLabelPlugin',
                        afterDatasetsDraw: (chart) => {
                            const ctx = chart.ctx;
                            chart.data.datasets.forEach((dataset, dsIndex) => {
                                const meta = chart.getDatasetMeta(dsIndex);
                                meta.data.forEach((bar, index) => {
                                    const val = dataset.data[index] ?? 0;
                                    const x = bar.x;
                                    const y = bar.y;
                                    ctx.save();
                                    ctx.fillStyle = '#212529';
                                    ctx.font = '600 12px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial';
                                    ctx.textAlign = 'center';
                                    ctx.textBaseline = 'bottom';
                                    // If bar is very short, draw label above the bar; otherwise inside near top
                                    const textY = y - 6;
                                    ctx.fillText(Number(val).toLocaleString(), x, textY);
                                    ctx.restore();
                                });
                            });
                        }
                    };

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Số lượng hoàn thành (ngày)',
                                data: data,
                                backgroundColor: bgColors,
                                borderColor: borderColors,
                                borderWidth: 0,
                                borderRadius: 8,
                                barPercentage: 0.62,
                                categoryPercentage: 0.62,
                                hoverBackgroundColor: bgColors
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: { padding: { top: 8, right: 12, left: 6, bottom: 6 } },
                            interaction: { mode: 'index', intersect: false },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    align: 'center',
                                    labels: { usePointStyle: true, pointStyle: 'rectRounded', boxWidth: 12 }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(ctx) {
                                            const val = ctx.parsed.y ?? ctx.parsed ?? 0;
                                            return ctx.dataset.label + ': ' + Number(val).toLocaleString();
                                        }
                                    },
                                    backgroundColor: 'rgba(0,0,0,0.75)'
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#495057' }
                                },
                                y: {
                                    beginAtZero: true,
                                    suggestedMax: suggestedMax > 0 ? suggestedMax : undefined,
                                    grid: { color: 'rgba(200,210,215,0.35)', borderDash: [4,4] },
                                    ticks: { color: '#495057', stepSize: stepSize }
                                }
                            },
                            animation: { easing: 'easeOutQuad', duration: 750 }
                        },
                        plugins: [shadowPlugin, valueLabelPlugin]
                    });
                })();
            </script>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>

