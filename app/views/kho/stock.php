<?php ob_start(); ?>

<style>
    /* --- Cấu hình màu sắc riêng cho Dashboard --- */
    :root {
        --color-red: #dc2626;
        --bg-red-light: #fef2f2;
        --color-orange: #ea580c;
        --bg-orange-light: #fff7ed;
        --color-green: #16a34a;
        --bg-green-light: #dcfce7;
        --color-blue: #2563eb;
        --border-radius: 12px;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    /* KHÔNG can thiệp vào body và container chung */
    
    /* CSS cho Dashboard Grid */
    .summary-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    .card-dashboard {
        background: #ffffff;
        border-radius: var(--border-radius);
        padding: 25px 30px;
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: center;
        border: 1px solid #e5e7eb;
    }

    .card-dashboard::before {
        content: "";
        position: absolute;
        left: 0;
        top: 15%;
        bottom: 15%;
        width: 6px;
        border-radius: 0 4px 4px 0;
    }

    .card-dashboard.red::before { background-color: #b91c1c; } 
    .card-dashboard.orange::before { background-color: #c2410c; } 

    .card-title { font-size: 16px; color: #111827; margin-bottom: 5px; font-weight: 600; }
    .card-number { font-size: 48px; font-weight: 500; line-height: 1.2; }
    .card-desc { font-size: 14px; font-style: italic; margin-top: 5px; color: #6b7280; }

    .text-red { color: var(--color-red); }
    .text-orange { color: var(--color-orange); }

    /* Tabs Style */
    .tabs { display: flex; gap: 5px; margin-left: 10px; }
    .tab-btn {
        padding: 10px 20px;
        border: none;
        background-color: #e5e7eb;
        color: #6b7280;
        font-weight: 600;
        font-size: 14px;
        border-radius: 8px 8px 0 0;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .tab-btn:hover { background-color: #d1d5db; }
    .tab-btn.active {
        background-color: #ffffff;
        color: var(--color-blue);
        box-shadow: 0 -2px 5px rgba(0,0,0,0.05);
        position: relative;
        z-index: 1; 
    }

    .content-box {
        background: #ffffff;
        border-radius: 0 12px 12px 12px; 
        padding: 30px;
        box-shadow: var(--shadow);
        min-height: 400px;
        margin-top: 0; 
        border: 1px solid #e5e7eb;
    }

    /* Table custom styles */
    .custom-table { width: 100%; border-collapse: collapse; }
    .custom-table th {
        text-align: left; padding: 15px; font-size: 13px; color: #6b7280;
        font-weight: 700; text-transform: uppercase; border-bottom: 1px solid #eee;
    }
    .custom-table td {
        padding: 18px 15px; vertical-align: middle; font-size: 14px; border-bottom: 1px solid #f9fafb;
    }
    
    tr.row-critical { background-color: var(--bg-red-light); }
    tr.row-warning { background-color: var(--bg-orange-light); }
    tr.row-safe { background-color: transparent; }

    /* Badges */
    .badge-status { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-block; }
    .badge-critical { background: #fee2e2; color: #991b1b; } 
    .badge-warning { background: #ffedd5; color: #9a3412; } 
    .badge-safe { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }    
    .badge-expired { background: #374151; color: white; }   

    .date-red { color: var(--color-red); font-weight: 700; }
    .date-orange { color: var(--color-orange); font-weight: 700; }
    .date-green { color: var(--color-green); font-weight: 700; }
    
    .btn-action {
        background-color: var(--color-blue); color: white; border: none; padding: 8px 16px;
        border-radius: 6px; cursor: pointer; font-size: 13px; display: inline-flex;
        align-items: center; gap: 6px; font-weight: 500; text-decoration: none;
    }
    .btn-action:hover { background-color: #1d4ed8; color: white; }

    .tab-content { display: none; }
    .tab-content.active { display: block; animation: fadeEffect 0.4s; }
    @keyframes fadeEffect { from {opacity: 0;} to {opacity: 1;} }
</style>

<div class="container mt-4 mb-5">
    
    <h3 class="text-center mb-4 text-uppercase fw-bold">Trung Tâm Cảnh Báo Tồn Kho</h3>

    <div class="summary-grid">
        <div class="card-dashboard red">
            <div class="card-title">Tồn kho dưới mức an toàn</div>
            <div class="card-number text-red"><?= $counts['low_stock'] ?? 0 ?></div>
            <div class="card-desc">Cần xử lý ngay</div>
        </div>
        
        <div class="card-dashboard orange">
            <div class="card-title">Cảnh báo hạn sử dụng</div>
            <div class="card-number text-orange"><?= $counts['expiring'] ?? 0 ?></div>
            <div class="card-desc">Lô hàng sắp hết hạn hoặc đã hết hạn</div>
        </div>
    </div>

    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('tabStock', this)">
            📦 Tồn kho thấp
        </button>
        <button class="tab-btn" onclick="switchTab('tabExpiry', this)">
            📅 Hạn sử dụng
        </button>
    </div>

    <div class="content-box">
        
        <div id="tabStock" class="tab-content active">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="10%">Mã SP</th>
                        <th width="30%">Tên Sản Phẩm</th>
                        <th width="10%">Tồn Kho Hiện Tại</th>
                        <th width="15%">Mức Tồn Kho Tối Thiểu</th>
                        <th width="15%">Trạng Thái</th>
                        <th width="15%">Hành Động</th>
                    </tr>
                </thead>
                <tbody id="stock-data-body">
                    <?php if (!empty($lowStockList)): ?>
                        <?php foreach ($lowStockList as $item): 
                            $tonKho = $item['SoLuongTonKho'];
                            $min    = $item['MucMin'];

                            $isCritical = false;
                            $showBtn    = false; 

                            if ($tonKho <= ($min / 2)) {
                                $rowClass   = 'row-critical';
                                $dateClass  = 'date-red';
                                $badgeClass = 'badge-critical';
                                $badgeText  = 'Nguy cấp';
                                $showBtn    = true; 
                            } elseif ($tonKho <= $min) {
                                $rowClass   = 'row-warning';
                                $dateClass  = 'date-orange';
                                $badgeClass = 'badge-warning';
                                $badgeText  = 'Sắp hết';
                                $showBtn    = true; 
                            } else {
                                $rowClass   = 'row-safe';
                                $dateClass  = 'date-green';
                                $badgeClass = 'badge-safe';
                                $badgeText  = 'An toàn';
                            }
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td class="fw-bold"><?= $item['MaNguyenLieu'] ?></td>
                            <td><?= $item['TenNguyenLieu'] ?></td>
                            <td class="<?= $dateClass ?>">
                                <?= number_format($tonKho) ?> <?= $item['DonViTinh'] ?>
                            </td>
                            <td><?= $min ?></td>
                            <td><span class="badge-status <?= $badgeClass ?>"><?= $badgeText ?></span></td>
                            <td>
                                <?php if ($showBtn): ?>
                                    <a href="<?= BASE_URL ?>kho/nhapnguyenlieu/create?maNL=<?= $item['MaNguyenLieu'] ?>" class="btn-action">
                                        📥 Báo cáo
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; padding:20px">Dữ liệu trống.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="tabExpiry" class="tab-content">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="10%">Mã NL</th>
                        <th width="30%">Tên Nguyên Liệu</th>
                        <th width="15%">Hạn Sử Dụng</th>
                        <th width="15%">Thời Gian Hết Hạn</th>
                        <th width="15%">Trạng Thái</th>
                        <th width="15%">Hành động</th>
                    </tr>
                </thead>
                <tbody id="expiry-data-body">
                    <?php if (!empty($expiringList)): ?>
                        <?php foreach ($expiringList as $item): 
                            $days = $item['SoNgayConLai'];
                            
                            $showBtn = false;
                            $link    = '#';
                            $btnText = '';
                            $btnStyle= '';

                            if ($days < 0) {
                                $rowStyle   = 'background-color: #f3f4f6; color: #666;';
                                $daysText   = "<span style='color:red; font-weight:bold'>Quá hạn " . abs($days) . " ngày</span>";
                                $badge      = '<span class="badge-status badge-expired">Đã hết hạn</span>';
                                $showBtn    = true;
                                $btnStyle   = "btn-action"; 
                                $btnText    = "📥 Báo cáo"; 
                                $link       = BASE_URL . "kho/xuatnguyenlieu?maNL=" . $item['MaNguyenLieu'];

                            } elseif ($days <= 30) {
                                $rowStyle   = 'background-color: #fff7ed;';
                                $daysText   = "<span style='color:#ea580c; font-weight:bold'>Còn $days ngày</span>";
                                $badge      = '<span class="badge-status badge-warning">Sắp hết hạn</span>';
                                $showBtn    = true;
                                $btnStyle   = "btn-action";
                                $btnText    = "📥 Báo cáo";
                                $link       = BASE_URL . "kho/xuatnguyenlieu/create?uu_tien=" . $item['MaNguyenLieu'];

                            } else {
                                $rowStyle   = 'background-color: white;';
                                $daysText   = "<span style='color:#16a34a; font-weight:bold'>Còn $days ngày</span>";
                                $badge      = '<span class="badge-status badge-safe">Còn hạn sử dụng</span>';
                            }
                        ?>
                        <tr style="<?= $rowStyle ?>">
                            <td class="fw-bold"><?= $item['MaNguyenLieu'] ?></td>
                            <td><?= $item['TenNguyenLieu'] ?></td>
                            <td><?= date('d/m/Y', strtotime($item['HanSuDung'])) ?></td>
                            <td><?= $daysText ?></td>
                            <td><?= $badge ?></td>
                            <td>
                                <?php if ($showBtn): ?>
                                    <a href="<?= $link ?>" class="btn-action" style="<?= $btnStyle ?>">
                                        <?= $btnText ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; padding:20px">Dữ liệu trống.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    function switchTab(tabId, btnElement) {
        var contents = document.querySelectorAll('.tab-content');
        contents.forEach(div => div.classList.remove('active'));
        var btns = document.querySelectorAll('.tab-btn');
        btns.forEach(btn => btn.classList.remove('active'));
        document.getElementById(tabId).classList.add('active');
        btnElement.classList.add('active');
    }
</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>

<?php if (!empty($criticalAlerts)): ?>
<div class="modal fade" id="criticalModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-danger" style="border-width: 3px;">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">
            <i class="fa-solid fa-triangle-exclamation"></i> CẢNH BÁO KHẨN CẤP
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p class="fw-bold text-danger">Phát hiện các vấn đề nghiêm trọng cần xử lý ngay:</p>
        <table class="table table-bordered table-striped">
            <thead class="table-danger">
                <tr>
                    <th>Mã</th>
                    <th>Tên Hàng Hóa</th>
                    <th>Tồn kho / Hạn dùng</th>
                    <th>Vấn đề</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($criticalAlerts as $alert): ?>
            <tr>
                <td class="fw-bold"><?= $alert['Ma'] ?></td>
                <td><?= $alert['Ten'] ?></td>
                <td class="text-center fw-bold"><?= $alert['SoLuongHoacHan'] ?></td>
                <td class="fw-bold text-danger text-uppercase"><?= $alert['TrangThai'] ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Đã hiểu</button>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var myModal = new bootstrap.Modal(document.getElementById('criticalModal'));
    myModal.show();
});
</script>
<?php endif; ?>
