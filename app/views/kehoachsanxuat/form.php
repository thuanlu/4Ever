<?php
// Tệp: app/views/kehoachsanxuat/form.php (Phiên bản đầy đủ cuối cùng)

// --- BIẾN & THIẾT LẬP ---
$kehoach = $kehoach ?? [];
$is_editing = $is_editing ?? false;
$is_viewing = $is_viewing ?? false;
$form_title = $form_title ?? 'Tạo Kế hoạch Sản xuất Mới';
$plan_details = $plan_details ?? [];
$bom_data = $bom_data ?? [];
$currentUserFullName = $currentUserFullName ?? ''; // Đảm bảo biến luôn tồn tại
$ngayLapKeHoach = date('Y-m-d');

// --- Hàm trợ giúp ---
if (!function_exists('get_value')) {
    function get_value($field, $kehoach, $default = '') {
        return htmlspecialchars($_POST[$field] ?? $kehoach[$field] ?? $default, ENT_QUOTES, 'UTF-8');
    }
}
?>

<div class="alert alert-info py-2" role="alert">
    <h5 class="mb-0"><?php echo $form_title; ?></h5>
</div>

<?php if (!empty($error_message)): ?>
    <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<form method="post" id="planForm"
    action="<?php echo BASE_URL; ?>kehoachsanxuat/<?php echo $is_editing ? 'edit/' . get_value('MaKeHoach', $kehoach) : 'create'; ?>">
    <div class="row">

        <h5 class="mt-2 text-primary fw-bold">1. Thông tin chung</h5>
         <div class="col-md-3 mb-3">
             <label class="form-label fw-bold">Mã kế hoạch</label>
             <input type="text" name="MaKeHoach" class="form-control" value="<?php echo get_value('MaKeHoach', $kehoach); ?>" readonly>
         </div>
         <div class="col-md-5 mb-3">
             <label class="form-label fw-bold">Tên kế hoạch</label>
             <input type="text" name="TenKeHoach" class="form-control" value="<?php echo get_value('TenKeHoach', $kehoach); ?>" required <?php echo $is_viewing ? 'readonly' : ''; ?>>
         </div>
         <div class="col-md-4 mb-3">
             <label class="form-label fw-bold">Ngày lập kế hoạch</label>
             <?php
                // Lấy ngày lập từ DB hoặc ngày hiện tại
                $ngayLapValue = $kehoach['NgayLap'] ?? $ngayLapKeHoach;
                // Định dạng lại thành YYYY-MM-DD nếu nó là datetime
                if (strpos($ngayLapValue, ' ') !== false) {
                    $ngayLapValue = date('Y-m-d', strtotime($ngayLapValue));
                }
             ?>
             <input type="date" name="NgayLap" class="form-control"
                    value="<?php echo htmlspecialchars($ngayLapValue); ?>" readonly>
        </div>
         <div class="col-md-4 mb-3">
             <label class="form-label fw-bold">Người lập kế hoạch</label>
             <input type="hidden" name="MaNV" value="<?php echo get_value('MaNV', $kehoach, $currentUserId ?? ''); ?>">
             <input type="text" class="form-control" value="<?php echo get_value('HoTenNguoiLap', $kehoach, $currentUserFullName); ?>" readonly>
         </div>
         <div class="col-md-4 mb-3">
             <label class="form-label fw-bold">Ngày bắt đầu</label>
             <input type="date" name="NgayBatDau" id="ngayBatDau" class="form-control"
                    value="<?php echo get_value('NgayBatDau', $kehoach); ?>" required
                    <?php echo $is_viewing ? 'readonly' : ''; ?>>
             <div class="invalid-feedback" id="ngayBatDauError"></div>
         </div>
         <div class="col-md-4 mb-3">
             <label class="form-label fw-bold">Ngày kết thúc</label>
             <input type="date" name="NgayKetThuc" id="ngayKetThuc" class="form-control"
                    value="<?php echo get_value('NgayKetThuc', $kehoach); ?>" required
                    <?php echo $is_viewing ? 'readonly' : ''; ?>>
             <div class="invalid-feedback" id="ngayKetThucError"></div>
         </div>
         <div class="col-md-<?php echo ($is_editing || $is_viewing) ? 8 : 12; ?> mb-3">
             <label class="form-label fw-bold">Đơn hàng liên quan</label>
             <select name="MaDonHang" id="MaDonHangSelect" class="form-select" required <?php echo ($is_viewing || $is_editing) ? 'disabled' : ''; ?>>
                 <option value="">-- Chọn đơn hàng --</option>
                 <?php $selectedMaDonHang = get_value('MaDonHang', $kehoach); foreach ($donhangs as $dh): ?>
                     <option value="<?php echo $dh['MaDonHang']; ?>" <?php echo $selectedMaDonHang === $dh['MaDonHang'] ? 'selected' : ''; ?>>
                         <?php echo $dh['MaDonHang'] . ' - ' . $dh['TenDonHang']; ?>
                     </option>
                 <?php endforeach; ?>
             </select>
             <?php if ($is_viewing || $is_editing): ?>
                 <input type="hidden" name="MaDonHang" value="<?php echo $selectedMaDonHang; ?>">
             <?php endif; ?>
         </div>
         <?php if ($is_editing || $is_viewing): ?>
         <div class="col-md-4 mb-3">
             <label class="form-label fw-bold">Trạng thái kế hoạch</label>
             <?php if ($is_viewing): ?>
                 <input type="text" class="form-control" value="<?php echo get_value('TrangThai', $kehoach); ?>" readonly>
             <?php else: ?>
                 <select name="TrangThai" class="form-select">
                     <?php $currentStatus = get_value('TrangThai', $kehoach, 'Chờ duyệt'); $statuses = ['Chờ duyệt', 'Đã duyệt', 'Đang thực hiện', 'Hoàn thành', 'Hủy bỏ']; foreach ($statuses as $status): ?>
                         <option value="<?php echo $status; ?>" <?php echo $currentStatus === $status ? 'selected' : ''; ?>>
                             <?php echo $status; ?>
                         </option>
                     <?php endforeach; ?>
                 </select>
             <?php endif; ?>
         </div>
         <?php endif; ?>
        <div class="col-12"><hr></div>

        <h5 class="text-primary fw-bold">2. Thông tin chi tiết Sản phẩm & Phân bổ</h5>

        <div id="product-loading" class="col-12 text-center" style="display: none;">
             <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
             <p>Đang tải chi tiết đơn hàng...</p>
        </div>

        <div id="product-details-container" class="col-12">
            <?php
            if (empty($plan_details)):
            ?>
                <div class="alert alert-warning text-center" id="product-placeholder">Vui lòng **chọn Đơn hàng liên quan**...</div>
            <?php
            else:
                // CHẾ ĐỘ SỬA/XEM
                foreach ($plan_details as $index => $detail):
                    $maSP = $detail['MaSanPham']; $tenSP = $detail['TenSanPham']; $sanLuong = $detail['SanLuongMucTieu']; $selectedMaPhanXuong = $detail['MaPhanXuong'];
            ?>
            <div class="product-item p-3 border rounded mb-3" data-index="<?php echo $index; ?>">
                <h6 class="fw-bold">SẢN PHẨM <?php echo $index + 1; ?>: <?php echo $tenSP; ?></h6>
                <div class="row">
                    <div class="col-md-3 mb-3"><label class="form-label fw-bold">Mã sản phẩm</label><input type="text" name="products[<?php echo $index; ?>][MaSanPham]" class="form-control" value="<?php echo $maSP; ?>" readonly></div>
                    <div class="col-md-6 mb-3"><label class="form-label fw-bold">Tên sản phẩm</label><input type="text" name="products[<?php echo $index; ?>][TenSanPham]" class="form-control" value="<?php echo $tenSP; ?>" readonly required></div>
                    <div class="col-md-3 mb-3"><label class="form-label fw-bold">Tổng sản lượng (ĐH)</label><input type="number" name="products[<?php echo $index; ?>][SanLuongMucTieu]" class="form-control total-sanluong" value="<?php echo $sanLuong; ?>" <?php echo $is_viewing ? 'readonly' : 'required'; ?>></div>
                </div>

                <h6 class="fw-bold mt-3">Nguyên liệu chi tiết (BOM)</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm raw-material-table" data-product-id="<?php echo $maSP; ?>">
                        <thead>
                            <tr>
                                <th style="width: 15%">Mã NL</th>
                                <th style="width: 25%">Tên NL</th>
                                <th style="width: 10%">ĐVT</th>
                                <th style="width: 12%">Định mức</th>
                                <th style="width: 13%">Cần</th>
                                <th style="width: 12%">Tồn kho</th>
                                <th style="width: 13%">Bổ sung</th>
                            </tr>
                        </thead>
                        <tbody id="raw-material-body_<?php echo $index; ?>">
                            <?php
                            $current_bom = $bom_data[$maSP] ?? [];
                            foreach($current_bom as $i => $nl):
                                $dinhMuc = (float)($nl['DinhMucSuDung'] ?? 0);
                                $tonKho = (float)($nl['SoLuongTonKho'] ?? 0);
                                $donViTinh = $nl['DonViTinh'] ?? '';
                                $giaNhap = (float)($nl['GiaNhap'] ?? 0);
                                $soLuongCan = $dinhMuc * (float)$sanLuong;
                                $canBoSung = max(0.00, $soLuongCan - $tonKho);
                            ?>
                            <tr data-nl-ma="<?php echo $nl['MaNguyenLieu']; ?>" data-donvitinh="<?php echo $donViTinh; ?>" data-gianhap="<?php echo $giaNhap; ?>">
                                <td><input type="text" name="products[<?php echo $index; ?>][materials][<?php echo $i; ?>][MaNguyenLieu]" class="form-control form-control-sm" value="<?php echo htmlspecialchars($nl['MaNguyenLieu']); ?>" readonly></td>
                                <td><input type="text" name="products[<?php echo $index; ?>][materials][<?php echo $i; ?>][TenNguyenLieu]" class="form-control form-control-sm" value="<?php echo htmlspecialchars($nl['TenNguyenLieu']); ?>" readonly></td>
                                <td><input type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars($donViTinh); ?>" readonly></td>
                                <td><input type="number" step="0.01" name="products[<?php echo $index; ?>][materials][<?php echo $i; ?>][DinhMucBOM]" class="form-control form-control-sm bom-input" value="<?php echo number_format($dinhMuc, 2, '.', ''); ?>" readonly></td>
                                <td><input type="number" step="0.01" class="form-control form-control-sm required-qty-input" value="<?php echo number_format($soLuongCan, 2, '.', ''); ?>" readonly></td>
                                <td><input type="number" step="0.01" name="products[<?php echo $index; ?>][materials][<?php echo $i; ?>][TonKho]" class="form-control form-control-sm stock-input" value="<?php echo number_format($tonKho, 2, '.', ''); ?>" readonly></td>
                                <td><input type="number" step="0.01" name="products[<?php echo $index; ?>][materials][<?php echo $i; ?>][CanBoSung]" class="form-control form-control-sm needed-input" value="<?php echo number_format($canBoSung, 2, '.', ''); ?>" readonly></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row">
                     <div class="col-md-12 mb-3"><label class="form-label fw-bold">Phân xưởng sản xuất</label><select name="products[<?php echo $index; ?>][MaPhanXuong]" class="form-select" required <?php echo $is_viewing ? 'disabled' : ''; ?>><option value="">-- Chọn Phân Xưởng --</option><?php foreach ($xuongs as $xuong): ?><option value="<?php echo $xuong['MaPhanXuong']; ?>" <?php echo $selectedMaPhanXuong === $xuong['MaPhanXuong'] ? 'selected' : ''; ?>><?php echo $xuong['MaPhanXuong'] . ' - ' . $xuong['TenPhanXuong']; ?></option><?php endforeach; ?></select><?php if ($is_viewing): ?><input type="hidden" name="products[<?php echo $index; ?>][MaPhanXuong]" value="<?php echo $selectedMaPhanXuong; ?>"><?php endif; ?></div>
                </div>
            </div>
            <?php
                endforeach;
            endif;
            ?>
        </div>
        <div class="col-12"><hr></div>

        <h5 class="text-primary fw-bold">3. Tổng hợp nguyên liệu và chi phí</h5>
        <div class="col-12 mb-3">
            <h6 class="fw-bold">Nguyên liệu tổng hợp</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th style="width: 15%">Mã NL</th>
                            <th style="width: 25%">Tên NL</th>
                            <th style="width: 10%">ĐVT</th>
                            <th style="width: 15%">Cần (Tổng)</th>
                            <th style="width: 15%">Tồn kho</th>
                            <th style="width: 20%">Cần bổ sung</th>
                        </tr>
                    </thead>
                    <tbody id="total-raw-material-body"></tbody>
                </table>
            </div>
        </div>

        <div class="col-12 mb-3">
            <h6 class="fw-bold mt-3">Chi phí dự kiến (VND)</h6>
            <div class="row">
                <div class="col-md-3 mb-3"><label for="chiPhiNguyenLieu" class="form-label fw-bold">CP Nguyên liệu (Tổng)</label><input type="number" step="0.01" name="ChiPhiNguyenLieu" id="chiPhiNguyenLieu" class="form-control" value="<?php echo get_value('ChiPhiNguyenLieu', $kehoach, '0'); ?>" readonly></div>
                <div class="col-md-3 mb-3"><label for="chiPhiNhanCong" class="form-label fw-bold">CP Nhân công (Xưởng)</label><input type="number" step="0.01" name="ChiPhiNhanCong" id="chiPhiNhanCong" class="form-control" value="<?php echo get_value('ChiPhiNhanCong', $kehoach, '0'); ?>" readonly></div>
                <div class="col-md-3 mb-3"><label for="chiPhiKhac" class="form-label fw-bold">Chi phí khác</label><input type="number" step="0.01" name="ChiPhiKhac" id="chiPhiKhac" class="form-control" value="<?php echo get_value('ChiPhiKhac', $kehoach, '0'); ?>" <?php echo $is_viewing ? 'readonly' : ''; ?>></div>
                <div class="col-md-3 mb-3"><label for="tongChiPhiDuKien" class="form-label fw-bold">Tổng chi phí</label><input type="number" step="0.01" name="TongChiPhiDuKien" id="tongChiPhiDuKien" class="form-control" value="<?php echo get_value('TongChiPhiDuKien', $kehoach, '0'); ?>" readonly></div>
            </div>
        </div>

        <div class="col-12 mb-3"><label class="form-label fw-bold">Ghi chú (chung)</label><textarea name="GhiChu" class="form-control" rows="3" <?php echo $is_viewing ? 'readonly' : ''; ?>><?php echo get_value('GhiChu', $kehoach); ?></textarea></div>
    </div>
    <hr>
    <div class="d-flex justify-content-end">
         <a href="<?php echo BASE_URL; ?>kehoachsanxuat" class="btn btn-secondary me-2"><i class="fas fa-arrow-left me-1"></i> Quay lại</a>
         <?php if (!$is_viewing): ?>
         <button type="submit" id="submitButton" class="btn btn-success"><i class="fas fa-save me-1"></i> Lưu kế hoạch</button>
         <?php endif; ?>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Element References ---
        const productContainer = document.getElementById('product-details-container');
        const productLoading = document.getElementById('product-loading');
        const productPlaceholder = document.getElementById('product-placeholder');
        const maDonHangSelect = document.getElementById('MaDonHangSelect');
        const totalBody = document.getElementById('total-raw-material-body');
        const chiPhiNguyenLieu = document.getElementById('chiPhiNguyenLieu');
        const chiPhiNhanCong = document.getElementById('chiPhiNhanCong');
        const chiPhiKhac = document.getElementById('chiPhiKhac');
        const tongChiPhiDuKien = document.getElementById('tongChiPhiDuKien');
        const ngayBatDauInput = document.getElementById('ngayBatDau');
        const ngayKetThucInput = document.getElementById('ngayKetThuc');
        const ngayBatDauError = document.getElementById('ngayBatDauError');
        const ngayKetThucError = document.getElementById('ngayKetThucError');
        const submitButton = document.getElementById('submitButton');
        const today = new Date().toISOString().split('T')[0];

        /**
         * Validate Start and End Dates
         */
        function validateDates() {
            let isValid = true;
            const startDate = ngayBatDauInput ? ngayBatDauInput.value : null;
            const endDate = ngayKetThucInput ? ngayKetThucInput.value : null;

            // Reset errors
            if (ngayBatDauInput) ngayBatDauInput.classList.remove('is-invalid');
            if (ngayKetThucInput) ngayKetThucInput.classList.remove('is-invalid');
            if(ngayBatDauError) ngayBatDauError.textContent = '';
            if(ngayKetThucError) ngayKetThucError.textContent = '';

            // 1. Kiểm tra Ngày Bắt đầu vs Hôm nay
            // **SỬA LỖI Ở ĐÂY: Dùng '<' thay vì '<='**
            if (ngayBatDauInput && startDate && startDate <= today) {
                ngayBatDauInput.classList.add('is-invalid');
                if(ngayBatDauError) ngayBatDauError.textContent = 'Ngày bắt đầu phải sau hôm nay.';
                isValid = false;
            }

            // 2. Kiểm tra Ngày Kết thúc vs Ngày Bắt đầu (Giữ nguyên)
            if (ngayBatDauInput && ngayKetThucInput && startDate && endDate && endDate <= startDate) {
                ngayKetThucInput.classList.add('is-invalid');
                 if(ngayKetThucError) ngayKetThucError.textContent = 'Ngày kết thúc phải sau Ngày bắt đầu.';
                isValid = false;
            }

            // Enable/Disable Save button
            if (submitButton) {
                submitButton.disabled = !isValid;
            }
            return isValid;
        }

        /**
         * Update Summary Table and Material Cost
         */
        window.updateTotalRawMaterials = function() {
            const totalMaterials = {};
            let totalMaterialCost = 0;

            document.querySelectorAll('.raw-material-table tbody tr').forEach(row => {
                const maNL = row.getAttribute('data-nl-ma');
                if (maNL) {
                    const tenNL = row.cells[1].querySelector('input').value;
                    const dinhMuc = parseFloat(row.cells[3].querySelector('input').value) || 0; // Định mức index 3
                    const tonKho = parseFloat(row.cells[5].querySelector('input').value) || 0; // Tồn kho index 5
                    const donViTinh = row.getAttribute('data-donvitinh') || '';
                    const giaNhap = parseFloat(row.getAttribute('data-gianhap')) || 0;

                    const productItem = row.closest('.product-item');
                    const sanLuongInput = productItem ? productItem.querySelector('.total-sanluong') : null;
                    const sanLuong = sanLuongInput ? (parseFloat(sanLuongInput.value) || 0) : 0;

                    const soLuongCan = dinhMuc * sanLuong;
                    const canBoSung = Math.max(0, soLuongCan - tonKho);

                    const requiredQtyInput = row.cells[4].querySelector('input'); // Cần index 4
                    if (requiredQtyInput) requiredQtyInput.value = soLuongCan.toFixed(2);
                    const neededInput = row.cells[6].querySelector('input'); // Bổ sung index 6
                    if (neededInput) neededInput.value = canBoSung.toFixed(2);

                    totalMaterialCost += soLuongCan * giaNhap;

                    if (!totalMaterials[maNL]) { totalMaterials[maNL] = { ten: tenNL, dvt: donViTinh, canBoSung: 0, tonKho: tonKho }; }
                    totalMaterials[maNL].canBoSung += canBoSung;
                }
            });

            if (chiPhiNguyenLieu) { chiPhiNguyenLieu.value = totalMaterialCost.toFixed(2); }

            totalBody.innerHTML = '';
            if (Object.keys(totalMaterials).length === 0) { totalBody.innerHTML = '<tr><td colspan="6" class="text-center">Chưa có dữ liệu NL.</td></tr>'; return; }
            for (const maNL in totalMaterials) {
                const material = totalMaterials[maNL]; const soLuongCanTong = material.tonKho + material.canBoSung;
                const newRow = `<tr><td>${maNL}</td><td>${material.ten}</td><td>${material.dvt}</td><td>${soLuongCanTong.toFixed(2)}</td><td>${material.tonKho.toFixed(2)}</td><td>${material.canBoSung.toFixed(2)}</td></tr>`;
                totalBody.insertAdjacentHTML('beforeend', newRow);
            }
        };

        /**
         * Update Total Cost
         */
        window.updateTotalCost = function() {
            if (!chiPhiNguyenLieu || !chiPhiNhanCong || !chiPhiKhac || !tongChiPhiDuKien) { return; }
            const cpNL = parseFloat(chiPhiNguyenLieu.value) || 0;
            const cpNC = parseFloat(chiPhiNhanCong.value) || 0; // TODO: Implement labor cost calculation
            const cpKhac = parseFloat(chiPhiKhac.value) || 0;
            const tongCP = cpNL + cpNC + cpKhac;
            tongChiPhiDuKien.value = tongCP.toFixed(2);
        };

        // --- Event Listeners ---

        // Only add AJAX listener in CREATE mode
        <?php if (!$is_editing && !$is_viewing): ?>
        if(maDonHangSelect) {
            maDonHangSelect.addEventListener('change', function() {
                 const maDonHang = this.value;
                 if (!maDonHang) { productContainer.innerHTML = ''; if (productPlaceholder) productContainer.appendChild(productPlaceholder); updateTotalRawMaterials(); updateTotalCost(); return; }
                 productContainer.innerHTML = ''; productLoading.style.display = 'block';
                 const baseUrl = '<?php echo BASE_URL; ?>';
                 fetch(`${baseUrl}kehoachsanxuat/getDonHangDetails/${maDonHang}`)
                     .then(response => { if (!response.ok) { throw new Error('Status: ' + response.status); } return response.json(); })
                     .then(data => {
                         productLoading.style.display = 'none'; if (data.error) { throw new Error(data.error); }
                         buildProductHtml(data.products, data.bom_data);
                         updateTotalRawMaterials(); // Update materials & cost
                         updateTotalCost(); // Update total cost
                     })
                     .catch(error => { productLoading.style.display = 'none'; productContainer.innerHTML = `<div class="alert alert-danger">Lỗi tải chi tiết: ${error.message}</div>`; updateTotalRawMaterials(); updateTotalCost(); });
            });
        }

        // Build HTML for products and BOM (AJAX response)
        function buildProductHtml(products, bom_data) {
             productContainer.innerHTML = '';
             if (!products || products.length === 0) { productContainer.innerHTML = `<div class="alert alert-warning">ĐH không có SP.</div>`; return; }
             products.forEach((product, index) => {
                 const maSP = product.MaSanPham; const tenSP = product.TenSanPham; const sanLuong = parseFloat(product.SoLuong) || 0;
                 let productHtml = `<div class="product-item p-3 border rounded mb-3" data-index="${index}"><h6 class="fw-bold">SP ${index + 1}: ${tenSP}</h6><div class="row"><div class="col-md-3 mb-3"><label class="form-label fw-bold">Mã SP</label><input type="text" name="products[${index}][MaSanPham]" class="form-control" value="${maSP}" readonly></div><div class="col-md-6 mb-3"><label class="form-label fw-bold">Tên SP</label><input type="text" name="products[${index}][TenSanPham]" class="form-control" value="${tenSP}" readonly required></div><div class="col-md-3 mb-3"><label class="form-label fw-bold">Sản lượng</label><input type="number" name="products[${index}][SanLuongMucTieu]" class="form-control total-sanluong" value="${sanLuong}" required></div></div><h6 class="fw-bold mt-3">Nguyên liệu (BOM)</h6><div class="table-responsive"><table class="table table-bordered table-sm raw-material-table" data-product-id="${maSP}"><thead><tr><th style="width: 15%">Mã NL</th><th style="width: 25%">Tên NL</th><th style="width: 10%">ĐVT</th><th style="width: 12%">Định mức</th><th style="width: 13%">Cần</th><th style="width: 12%">Tồn kho</th><th style="width: 13%">Bổ sung</th></tr></thead><tbody id="raw-material-body_${index}">`; // Header updated
                 const current_bom = bom_data[maSP] || [];
                 if (current_bom.length > 0) {
                     current_bom.forEach((nl, i) => {
                         const dinhMuc = parseFloat(nl.DinhMucSuDung) || 0; const tonKho = parseFloat(nl.SoLuongTonKho) || 0;
                         const donViTinh = nl.DonViTinh || '';
                         const giaNhap = parseFloat(nl.GiaNhap) || 0;
                         const soLuongCan = dinhMuc * sanLuong; const canBoSung = Math.max(0, soLuongCan - tonKho);
                         productHtml += `<tr data-nl-ma="${nl.MaNguyenLieu}" data-donvitinh="${donViTinh}" data-gianhap="${giaNhap}"><td><input type="text" name="products[${index}][materials][${i}][MaNguyenLieu]" class="form-control form-control-sm" value="${nl.MaNguyenLieu}" readonly></td><td><input type="text" name="products[${index}][materials][${i}][TenNguyenLieu]" class="form-control form-control-sm" value="${nl.TenNguyenLieu}" readonly></td><td><input type="text" class="form-control form-control-sm" value="${donViTinh}" readonly></td><td><input type="number" step="0.01" name="products[${index}][materials][${i}][DinhMucBOM]" class="form-control form-control-sm bom-input" value="${dinhMuc.toFixed(2)}" readonly></td><td><input type="number" step="0.01" class="form-control form-control-sm required-qty-input" value="${soLuongCan.toFixed(2)}" readonly></td><td><input type="number" step="0.01" name="products[${index}][materials][${i}][TonKho]" class="form-control form-control-sm stock-input" value="${tonKho.toFixed(2)}" readonly></td><td><input type="number" step="0.01" name="products[${index}][materials][${i}][CanBoSung]" class="form-control form-control-sm needed-input" value="${canBoSung.toFixed(2)}" readonly></td></tr>`;
                     });
                 } else { productHtml += `<tr><td colspan="7" class="text-center text-danger">Chưa có BOM.</td></tr>`; }
                 productHtml += `</tbody></table></div><div class="row"><div class="col-md-12 mb-3"><label class="form-label fw-bold">Phân xưởng</label><select name="products[${index}][MaPhanXuong]" class="form-select" required><option value="">-- Chọn --</option><?php foreach ($xuongs as $xuong): ?><option value="<?php echo $xuong['MaPhanXuong']; ?>"><?php echo $xuong['MaPhanXuong'] . ' - ' . $xuong['TenPhanXuong']; ?></option><?php endforeach; ?></select></div></div></div>`;
                 productContainer.insertAdjacentHTML('beforeend', productHtml);
             });
        }
        <?php endif; ?>

        // Initial Calculations on Load
        updateTotalRawMaterials(); // Calculate Material Cost first
        updateTotalCost();         // Then calculate Total Cost
        validateDates();           // Validate initial dates

        if (!<?php echo $is_viewing ? 'true' : 'false'; ?>) {
            if (ngayBatDauInput) ngayBatDauInput.addEventListener('input', validateDates); // Gọi ngay khi thay đổi
            if (ngayKetThucInput) ngayKetThucInput.addEventListener('input', validateDates); // Gọi ngay khi thay đổi

            const planForm = document.getElementById('planForm');
            if (planForm) {
                planForm.addEventListener('submit', function(event) {
                    if (!validateDates()) { // Kiểm tra lần cuối trước khi submit
                        event.preventDefault();
                        alert('Vui lòng kiểm tra lại Ngày bắt đầu và Ngày kết thúc.');
                    }
                    // Thêm kiểm tra khác nếu cần (ví dụ: đã chọn đơn hàng chưa?)
                    if (maDonHangSelect && !maDonHangSelect.value && !<?php echo $is_editing ? 'true' : 'false'; ?>) {
                         event.preventDefault();
                         alert('Vui lòng chọn Đơn hàng liên quan.');
                    }
                });
            }
             // Listener cho chi phí khác (Giữ nguyên)
            if (chiPhiKhac) { chiPhiKhac.addEventListener('input', updateTotalCost); }
            // Listener khi sản lượng thay đổi (Giữ nguyên)
            if (productContainer) { productContainer.addEventListener('input', function(e) { /* ... */ }); }
        }

    });
</script>