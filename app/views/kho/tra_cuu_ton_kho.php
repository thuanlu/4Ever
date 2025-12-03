<?php
/**
 * View: Tra Cứu Tồn Kho
 * Hiển thị danh sách nguyên liệu và thành phẩm trong kho với bộ lọc
 */
ob_start();
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-search me-2 text-primary"></i>Tra Cứu Tồn Kho
            </h2>
            <p class="text-muted">Xem thông tin tồn kho nguyên liệu và thành phẩm</p>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" id="inventoryTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $activeTab === 'nguyenlieu' ? 'active' : '' ?>" 
                    id="nguyenlieu-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#nguyenlieu" 
                    type="button" 
                    role="tab"
                    onclick="switchTab('nguyenlieu')">
                <i class="fas fa-boxes me-2"></i>Nguyên Liệu
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link <?= $activeTab === 'thanhpham' ? 'active' : '' ?>" 
                    id="thanhpham-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#thanhpham" 
                    type="button" 
                    role="tab"
                    onclick="switchTab('thanhpham')">
                <i class="fas fa-box-open me-2"></i>Thành Phẩm
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="inventoryTabContent">
        <!-- Tab Nguyên Liệu -->
        <div class="tab-pane fade <?= $activeTab === 'nguyenlieu' ? 'show active' : '' ?>" 
             id="nguyenlieu" 
             role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-boxes me-2"></i>Danh sách Nguyên Liệu
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Bộ lọc -->
                    <form method="GET" action="<?= BASE_URL ?>tracuutonkho" class="mb-4">
                        <input type="hidden" name="tab" value="nguyenlieu">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="maNL" class="form-label">Mã Nguyên Liệu</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="maNL" 
                                       name="maNL" 
                                       value="<?= htmlspecialchars($filtersNL['maNL'] ?? '') ?>"
                                       placeholder="Nhập mã NL">
                            </div>
                            <div class="col-md-3">
                                <label for="tenNL" class="form-label">Tên Nguyên Liệu</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="tenNL" 
                                       name="tenNL" 
                                       value="<?= htmlspecialchars($filtersNL['tenNL'] ?? '') ?>"
                                       placeholder="Nhập tên NL">
                            </div>
                            <div class="col-md-3">
                                <label for="nhaCungCap" class="form-label">Nhà Cung Cấp</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nhaCungCap" 
                                       name="nhaCungCap" 
                                       value="<?= htmlspecialchars($filtersNL['nhaCungCap'] ?? '') ?>"
                                       placeholder="Nhập tên NCC">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill">
                                        <i class="fas fa-search me-2"></i>Lọc
                                    </button>
                                    <a href="<?= BASE_URL ?>tracuutonkho?tab=nguyenlieu" class="btn btn-secondary">
                                        <i class="fas fa-redo me-2"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Bảng danh sách -->
                    <?php if (empty($danhSachNguyenLieu)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Không tìm thấy nguyên liệu nào</h5>
                            <p class="text-muted">Vui lòng thử lại với bộ lọc khác</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Mã NL</th>
                                        <th>Tên Nguyên Liệu</th>
                                        <th>Số Lượng</th>
                                        <th>Đơn Vị</th>
                                        <th>Nhà Cung Cấp</th>
                                        <th>Ngày Nhập</th>
                                        <th>Ngày Cập Nhật</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($danhSachNguyenLieu as $nl): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary"><?= htmlspecialchars($nl['MaNguyenLieu']) ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($nl['TenNguyenLieu']) ?></td>
                                            <td>
                                                <span class="badge bg-info"><?= number_format($nl['SoLuongTonKho'], 2) ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($nl['DonViTinh']) ?></td>
                                            <td>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($nl['TenNhaCungCap']) ?></span>
                                            </td>
                                            <td>
                                                <?php if ($nl['NgayNhap']): ?>
                                                    <?= date('d/m/Y', strtotime($nl['NgayNhap'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= date('d/m/Y H:i', strtotime($nl['NgayCapNhat'])) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tab Thành Phẩm -->
        <div class="tab-pane fade <?= $activeTab === 'thanhpham' ? 'show active' : '' ?>" 
             id="thanhpham" 
             role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-box-open me-2"></i>Danh sách Thành Phẩm
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Bộ lọc -->
                    <form method="GET" action="<?= BASE_URL ?>tracuutonkho" class="mb-4">
                        <input type="hidden" name="tab" value="thanhpham">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="loai" class="form-label">Loại (Size/Màu)</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="loai" 
                                       name="loai" 
                                       value="<?= htmlspecialchars($filtersTP['loai'] ?? '') ?>"
                                       placeholder="Nhập size hoặc màu">
                            </div>
                            <div class="col-md-3">
                                <label for="maLH" class="form-label">Mã Lô Hàng</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="maLH" 
                                       name="maLH" 
                                       value="<?= htmlspecialchars($filtersTP['maLH'] ?? '') ?>"
                                       placeholder="Nhập mã lô hàng">
                            </div>
                            <div class="col-md-3">
                                <label for="ngayNhap" class="form-label">Ngày Nhập</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="ngayNhap" 
                                       name="ngayNhap" 
                                       value="<?= htmlspecialchars($filtersTP['ngayNhap'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill">
                                        <i class="fas fa-search me-2"></i>Lọc
                                    </button>
                                    <a href="<?= BASE_URL ?>tracuutonkho?tab=thanhpham" class="btn btn-secondary">
                                        <i class="fas fa-redo me-2"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Bảng danh sách -->
                    <?php if (empty($danhSachThanhPham)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Không tìm thấy thành phẩm nào</h5>
                            <p class="text-muted">Vui lòng thử lại với bộ lọc khác</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-primary">
                                    <tr>
                                        <th>Mã SP</th>
                                        <th>Tên Sản Phẩm</th>
                                        <th>Size</th>
                                        <th>Màu</th>
                                        <th>Số Lượng</th>
                                        <th>Vị Trí Kho</th>
                                        <th>Mã Lô Hàng</th>
                                        <th>Ngày Nhập</th>
                                        <th>Ngày Cập Nhật</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($danhSachThanhPham as $tp): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary"><?= htmlspecialchars($tp['MaSanPham']) ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($tp['TenSanPham']) ?></td>
                                            <td>
                                                <span class="badge bg-info"><?= htmlspecialchars($tp['Size'] ?? '-') ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($tp['Mau'] ?? '-') ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success"><?= number_format($tp['SoLuongHienTai']) ?></span>
                                            </td>
                                            <td><?= htmlspecialchars($tp['ViTriKho'] ?? 'Kho A') ?></td>
                                            <td>
                                                <?php if ($tp['MaLoHang']): ?>
                                                    <span class="badge bg-warning text-dark"><?= htmlspecialchars($tp['MaLoHang']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($tp['NgayNhap']): ?>
                                                    <?= date('d/m/Y', strtotime($tp['NgayNhap'])) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= date('d/m/Y H:i', strtotime($tp['NgayCapNhat'])) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    // Cập nhật URL với tab mới
    const url = new URL(window.location.href);
    url.searchParams.set('tab', tab);
    // Xóa các filter khi chuyển tab
    if (tab === 'nguyenlieu') {
        url.searchParams.delete('loai');
        url.searchParams.delete('maLH');
        url.searchParams.delete('ngayNhap');
    } else {
        url.searchParams.delete('maNL');
        url.searchParams.delete('tenNL');
        url.searchParams.delete('nhaCungCap');
    }
    window.location.href = url.toString();
}
</script>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>