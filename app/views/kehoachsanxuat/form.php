<?php $pageTitle = "Kế hoạch Sản xuất"; ?>
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
             <?php
                // SỬA: Luôn ở chế độ chỉ đọc (readonly) cho cả View và Edit
                $currentStatus = get_value('TrangThai', $kehoach, 'Chờ duyệt');
             ?>
             <input type="text" class="form-control" value="<?php echo $currentStatus; ?>" readonly>
             
             <?php
             // Gửi giá trị trạng thái đi ngầm khi chỉnh sửa,
             // để logic POST trong Controller không làm mất trạng thái
             if ($is_editing): ?>
                <input type="hidden" name="TrangThai" value="<?php echo $currentStatus; ?>">
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
<div class="col-md-3 mb-3">
    <label class="form-label fw-bold">Tổng sản lượng (ĐH)</label>
    <input type="number" name="products[<?php echo $index; ?>][SanLuongMucTieu]" class="form-control total-sanluong" value="<?php echo $sanLuong; ?>" <?php echo $is_viewing ? 'readonly' : 'required'; ?>>
    
    <input type="hidden" class="dinh-muc-gio-cong" value="<?php echo htmlspecialchars($detail['DinhMucGioCong'] ?? 2); ?>">
    </div>                
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
                                $tonKho = round((float)($nl['SoLuongTonKho'] ?? 0)); // SỬA 1: làm tròn Tồn kho
                                $donViTinh = $nl['DonViTinh'] ?? '';
                                $giaNhap = (float)($nl['GiaNhap'] ?? 0);
                                $soLuongCan = ceil($dinhMuc * (float)$sanLuong); // SỬA 2: làm tròn lên
                                $canBoSung = max(0, $soLuongCan - $tonKho);
                            ?>
                            <tr data-nl-ma="<?php echo $nl['MaNguyenLieu']; ?>" data-donvitinh="<?php echo $donViTinh; ?>" data-gianhap="<?php echo $giaNhap; ?>">
                                <td><input type="number" step="0" name="products[<?php echo $index; ?>][materials][<?php echo $i; ?>][DinhMucBOM]" class="form-control form-control-sm bom-input" value="<?php echo number_format($dinhMuc, 2, '.', ''); ?>" readonly></td>
                                <td><input type="number" step="0" class="form-control form-control-sm required-qty-input" value="<?php echo $soLuongCan; ?>" readonly></td>
                                <td><input type="number" step="0" name="products[<?php echo $index; ?>][materials][<?php echo $i; ?>][TonKho]" class="form-control form-control-sm stock-input" value="<?php echo $tonKho; ?>" readonly></td>
                                <td><input type="number" step="0" name="products[<?php echo $index; ?>][materials][<?php echo $i; ?>][CanBoSung]" class="form-control form-control-sm needed-input" value="<?php echo $canBoSung; ?>" readonly></td>
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
    <div class="col-md-3 mb-3">
        <label for="chiPhiNguyenLieu" class="form-label fw-bold">CP Nguyên liệu (Tổng)</label>
        <?php $cpnl_value = (float)get_value('ChiPhiNguyenLieu', $kehoach, '0'); ?>
        <input type="text" id="chiPhiNguyenLieuDisplay" class="form-control text-end" value="<?php echo number_format($cpnl_value, 0, ',', '.'); ?>" readonly>
        <input type="hidden" name="ChiPhiNguyenLieu" id="chiPhiNguyenLieu" value="<?php echo $cpnl_value; ?>">
    </div>
    <div class="col-md-3 mb-3">
        <label for="chiPhiNhanCong" class="form-label fw-bold">CP Nhân công (Xưởng)</label>
        <?php $cpnc_value = (float)get_value('ChiPhiNhanCong', $kehoach, '0'); ?>
        <input type="text" id="chiPhiNhanCongDisplay" class="form-control text-end" value="<?php echo number_format($cpnc_value, 0, ',', '.'); ?>" readonly>
        <input type="hidden" name="ChiPhiNhanCong" id="chiPhiNhanCong" value="<?php echo $cpnc_value; ?>">
    </div>
    <div class="col-md-3 mb-3">
        <label for="chiPhiKhac" class="form-label fw-bold">Chi phí khác</label>
        <?php $cpk_value = (float)get_value('ChiPhiKhac', $kehoach, '0'); ?>
        <input type="text" id="chiPhiKhacDisplay" class="form-control text-end" value="<?php echo number_format($cpk_value, 0, ',', '.'); ?>" readonly>
        <input type="hidden" name="ChiPhiKhac" id="chiPhiKhac" value="<?php echo $cpk_value; ?>">
    </div>
    <div class="col-md-3 mb-3">
        <label for="tongChiPhiDuKien" class="form-label fw-bold">Tổng chi phí</label>
        <?php $tcp_value = (float)get_value('TongChiPhiDuKien', $kehoach, '0'); ?>
        <input type="text" id="tongChiPhiDuKienDisplay" class="form-control text-end fw-bold" value="<?php echo number_format($tcp_value, 0, ',', '.'); ?>" readonly>
        <input type="hidden" name="TongChiPhiDuKien" id="tongChiPhiDuKien" value="<?php echo $tcp_value; ?>">
    </div>
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
        const xuongList = <?php echo json_encode($xuongs ?? []); ?>; // <-- THÊM DÒNG NÀY
        // --- Element References ---
        const productContainer = document.getElementById('product-details-container');
        const productLoading = document.getElementById('product-loading');
        const productPlaceholder = document.getElementById('product-placeholder');
        const maDonHangSelect = document.getElementById('MaDonHangSelect');
        const totalBody = document.getElementById('total-raw-material-body');

        // --- References to HIDDEN number inputs ---
        const chiPhiNguyenLieu = document.getElementById('chiPhiNguyenLieu');
        const chiPhiNhanCong = document.getElementById('chiPhiNhanCong');
        const chiPhiKhac = document.getElementById('chiPhiKhac');
        const tongChiPhiDuKien = document.getElementById('tongChiPhiDuKien');

        // --- References to VISIBLE text inputs for display ---
        const chiPhiNguyenLieuDisplay = document.getElementById('chiPhiNguyenLieuDisplay');
        const chiPhiNhanCongDisplay = document.getElementById('chiPhiNhanCongDisplay');
        const chiPhiKhacDisplay = document.getElementById('chiPhiKhacDisplay');
        const tongChiPhiDuKienDisplay = document.getElementById('tongChiPhiDuKienDisplay');

        const ngayBatDauInput = document.getElementById('ngayBatDau');
        const ngayKetThucInput = document.getElementById('ngayKetThuc');
        const ngayBatDauError = document.getElementById('ngayBatDauError');
        const ngayKetThucError = document.getElementById('ngayKetThucError');
        const submitButton = document.getElementById('submitButton');
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Đặt về đầu ngày để so sánh

        /**
         * Format number to Vietnamese currency string
         */
        function formatCurrency(number) {
            return parseFloat(number).toLocaleString('vi-VN');
        }

        /**
         * Validate Start and End Dates
         */
        function validateDates() {
            let isValid = true;
            const startDateValue = ngayBatDauInput ? ngayBatDauInput.value : null;
            const endDateValue = ngayKetThucInput ? ngayKetThucInput.value : null;

            // Reset errors
            if (ngayBatDauInput) ngayBatDauInput.classList.remove('is-invalid');
            if (ngayKetThucInput) ngayKetThucInput.classList.remove('is-invalid');
            if(ngayBatDauError) ngayBatDauError.textContent = '';
            if(ngayKetThucError) ngayKetThucError.textContent = '';

            // 1. Kiểm tra Ngày Bắt đầu vs Hôm nay
            if (ngayBatDauInput && startDateValue) {
                const startDate = new Date(startDateValue);
                if (startDate < today) {
                    ngayBatDauInput.classList.add('is-invalid');
                    if(ngayBatDauError) ngayBatDauError.textContent = 'Ngày bắt đầu không được là ngày trong quá khứ.';
                    isValid = false;
                }
            }

            // 2. Kiểm tra Ngày Kết thúc vs Ngày Bắt đầu
            if (ngayBatDauInput && ngayKetThucInput && startDateValue && endDateValue && endDateValue < startDateValue) {
                ngayKetThucInput.classList.add('is-invalid');
                 if(ngayKetThucError) ngayKetThucError.textContent = 'Ngày kết thúc phải sau hoặc bằng Ngày bắt đầu.';
                isValid = false;
            }

            if (submitButton) {
                submitButton.disabled = !isValid;
            }
            return isValid;
        }

        /**
         * Update Summary Table and Material Cost (Đã sửa định dạng tiền)
         */
        window.updateTotalRawMaterials = function() {
            const totalMaterials = {};
            let totalMaterialCost = 0;

            document.querySelectorAll('.raw-material-table tbody tr').forEach(row => {
                const maNL = row.getAttribute('data-nl-ma');
                if (maNL) {
                    const tenNL = row.cells[1].querySelector('input').value;
                    const dinhMuc = parseFloat(row.cells[3].querySelector('input').value) || 0;
                    const tonKho = parseFloat(row.cells[5].querySelector('input').value) || 0;
                    const donViTinh = row.getAttribute('data-donvitinh') || '';
                    const giaNhap = parseFloat(row.getAttribute('data-gianhap')) || 0;

                    const productItem = row.closest('.product-item');
                    const sanLuongInput = productItem ? productItem.querySelector('.total-sanluong') : null;
                    const sanLuong = sanLuongInput ? (parseFloat(sanLuongInput.value) || 0) : 0;

                    const soLuongCan = dinhMuc * sanLuong;
                    const canBoSung = Math.max(0, soLuongCan - tonKho);

                    const requiredQtyInput = row.cells[4].querySelector('input');
                    if (requiredQtyInput) requiredQtyInput.value = soLuongCan.toFixed(2);
                    const neededInput = row.cells[6].querySelector('input');
                    if (neededInput) neededInput.value = canBoSung.toFixed(2);

                    totalMaterialCost += soLuongCan * giaNhap;

                    if (!totalMaterials[maNL]) {
                        totalMaterials[maNL] = { ten: tenNL, dvt: donViTinh, canTong: 0, canBoSung: 0, tonKho: tonKho };
                    }
                    totalMaterials[maNL].canTong += soLuongCan;
                    totalMaterials[maNL].canBoSung += canBoSung;
                }
            });

            // Cập nhật chi phí nguyên liệu
            if (chiPhiNguyenLieu) { chiPhiNguyenLieu.value = totalMaterialCost; } // Hidden input (số)
            if (chiPhiNguyenLieuDisplay) { chiPhiNguyenLieuDisplay.value = formatCurrency(totalMaterialCost); } // Display input (text)


            totalBody.innerHTML = '';
            if (Object.keys(totalMaterials).length === 0) {
                totalBody.innerHTML = '<tr><td colspan="6" class="text-center">Chưa có dữ liệu NL.</td></tr>';
                return;
            }

            for (const maNL in totalMaterials) {
                const material = totalMaterials[maNL];
                const newRow = `<tr>
                                    <td>${maNL}</td>
                                    <td>${material.ten}</td>
                                    <td>${material.dvt}</td>
                                    <td>${material.canTong.toFixed(2)}</td>
                                    <td>${material.tonKho.toFixed(2)}</td>
                                    <td>${material.canBoSung.toFixed(2)}</td>
                                </tr>`;
                totalBody.insertAdjacentHTML('beforeend', newRow);
            }
        };

        /**
         * Tính toán chi phí nhân công (THEO ĐỊNH MỨC GIỜ CÔNG)
         */
        window.updateLaborCost = function() {
            if (!chiPhiNhanCong) return;

            // 1. Đặt đơn giá của bạn ở đây (ví dụ: 30,000 VND/giờ)
            const DON_GIA_MOT_GIO_CONG = 30000;
            
            let tongGioCong = 0;

            // 2. Duyệt qua từng "product-item"
            document.querySelectorAll('.product-item').forEach(item => {
                const sanLuongInput = item.querySelector('.total-sanluong');
                const dinhMucInput = item.querySelector('.dinh-muc-gio-cong');

                const sanLuong = sanLuongInput ? (parseFloat(sanLuongInput.value) || 0) : 0;
                
                // Chỗ này sẽ tự động đọc value="2" từ input ẩn
                const dinhMuc = dinhMucInput ? (parseFloat(dinhMucInput.value) || 0) : 0; 
                
                // 3. Cộng dồn TỔNG GIỜ CÔNG (Sản Lượng * 2)
                tongGioCong += sanLuong * dinhMuc;
            });

            // 4. Tính tổng chi phí
            const totalLaborCost = tongGioCong * DON_GIA_MOT_GIO_CONG;

            // 5. Cập nhật chi phí nhân công (cả ô ẩn và ô hiển thị)
            if (chiPhiNhanCong) { chiPhiNhanCong.value = totalLaborCost; } 
            if (chiPhiNhanCongDisplay) { chiPhiNhanCongDisplay.value = formatCurrency(totalLaborCost); } 
        };

        /**
         * Update Total Cost (Đã sửa định dạng tiền)
         */
        window.updateTotalCost = function() {
            if (!chiPhiNguyenLieu || !chiPhiNhanCong || !chiPhiKhac || !tongChiPhiDuKien) { return; }

            // Lấy giá trị SỐ từ các ô ẩn
            const cpNL = parseFloat(chiPhiNguyenLieu.value) || 0;
            const cpNC = parseFloat(chiPhiNhanCong.value) || 0;

            // Tính CP Khác và Tổng CP
            const cpKhacValue = (cpNL + cpNC) * 0.05;
            const tongCP = cpNL + cpNC + cpKhacValue;

            // Cập nhật CP Khác
            if (chiPhiKhac) { chiPhiKhac.value = cpKhacValue; } // Ô ẩn
            if (chiPhiKhacDisplay) { chiPhiKhacDisplay.value = formatCurrency(cpKhacValue); } // Ô hiển thị

            // Cập nhật Tổng Chi Phí
            if (tongChiPhiDuKien) { tongChiPhiDuKien.value = tongCP; } // Ô ẩn
            if (tongChiPhiDuKienDisplay) { tongChiPhiDuKienDisplay.value = formatCurrency(tongCP); } // Ô hiển thị
        };

        // --- Event Listeners ---

        <?php if (!$is_editing && !$is_viewing): ?>
        if(maDonHangSelect) {
            maDonHangSelect.addEventListener('change', function() {
                 const maDonHang = this.value;
                 if (!maDonHang) {
                     productContainer.innerHTML = '';
                     if (productPlaceholder) productContainer.appendChild(productPlaceholder);
                     updateTotalRawMaterials();
                     window.updateLaborCost();
                     updateTotalCost();
                     return;
                 }
                 productContainer.innerHTML = ''; productLoading.style.display = 'block';
                 const baseUrl = '<?php echo BASE_URL; ?>';
                 fetch(`${baseUrl}kehoachsanxuat/getDonHangDetails/${maDonHang}`)
                     .then(response => { if (!response.ok) { throw new Error('Status: ' + response.status); } return response.json(); })
                     .then(data => {
                         productLoading.style.display = 'none'; if (data.error) { throw new Error(data.error); }
                         buildProductHtml(data.products, data.bom_data);
                         updateTotalRawMaterials(); // 1. Tính NL
                         window.updateLaborCost();    // 2. Tính Nhân công
                         updateTotalCost();         // 3. Tính Tổng
                     })
                     .catch(error => {
                         productLoading.style.display = 'none';
                         productContainer.innerHTML = `<div class="alert alert-danger">Lỗi tải chi tiết: ${error.message}</div>`;
                         updateTotalRawMaterials();
                         window.updateLaborCost();
                         updateTotalCost();
                     });
            });
        }

        // Build HTML for products and BOM (AJAX response)
        function buildProductHtml(products, bom_data) {
             productContainer.innerHTML = '';
             if (!products || products.length === 0) { productContainer.innerHTML = `<div class="alert alert-warning">ĐH không có SP.</div>`; return; }
             
             products.forEach((product, index) => {
                 const maSP = product.MaSanPham; 
                 const tenSP = product.TenSanPham; 
                 const sanLuong = parseFloat(product.SoLuong) || 0;
                 
                 // Lấy DinhMucGioCong từ JSON, nếu không có thì dùng 2
                 const dinhMucGioCong = parseFloat(product.DinhMucGioCong) || 2; 

                 let productHtml = `
                    <div class="product-item p-3 border rounded mb-3" data-index="${index}">
                        <h6 class="fw-bold">SP ${index + 1}: ${tenSP}</h6>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Mã SP</label>
                                <input type="text" name="products[${index}][MaSanPham]" class="form-control" value="${maSP}" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tên SP</label>
                                <input type="text" name="products[${index}][TenSanPham]" class="form-control" value="${tenSP}" readonly required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Sản lượng</label>
                                <input type="number" name="products[${index}][SanLuongMucTieu]" class="form-control total-sanluong" value="${sanLuong}" required>
                                
                                <input type="hidden" class="dinh-muc-gio-cong" value="${dinhMucGioCong}">
                            </div>
                        </div>
                        <h6 class="fw-bold mt-3">Nguyên liệu (BOM)</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm raw-material-table" data-product-id="${maSP}">
                                <thead>
                                    <tr>
                                        <th style="width: 15%">Mã NL</th><th style="width: 25%">Tên NL</th><th style="width: 10%">ĐVT</th>
                                        <th style="width: 12%">Định mức</th><th style="width: 13%">Cần</th>
                                        <th style="width: 12%">Tồn kho</th><th style="width: 13%">Bổ sung</th>
                                    </tr>
                                </thead>
                                <tbody id="raw-material-body_${index}">`;
                 
                 const current_bom = bom_data[maSP] || [];
                 if (current_bom.length > 0) {
                     current_bom.forEach((nl, i) => {
                         const dinhMuc = parseFloat(nl.DinhMucSuDung) || 0; const tonKho = parseFloat(nl.SoLuongTonKho) || 0;
                         const donViTinh = nl.DonViTinh || '';
                         const giaNhap = parseFloat(nl.GiaNhap) || 0;
                         const soLuongCan = dinhMuc * sanLuong; const canBoSung = Math.max(0, soLuongCan - tonKho);
                         productHtml += `<tr data-nl-ma="${nl.MaNguyenLieu}" data-donvitinh="${donViTinh}" data-gianhap="${giaNhap}">
                                             <td><input type="text" name="products[${index}][materials][${i}][MaNguyenLieu]" class="form-control form-control-sm" value="${nl.MaNguyenLieu}" readonly></td>
                                             <td><input type="text" name="products[${index}][materials][${i}][TenNguyenLieu]" class="form-control form-control-sm" value="${nl.TenNguyenLieu}" readonly></td>
                                             <td><input type="text" class="form-control form-control-sm" value="${donViTinh}" readonly></td>
                                             <td><input type="number" step="0" name="products[${index}][materials][${i}][DinhMucBOM]" class="form-control form-control-sm bom-input" value="${dinhMuc.toFixed(2)}" readonly></td>
                                             <td><input type="number" step="0" class="form-control form-control-sm required-qty-input" value="${soLuongCan.toFixed(2)}" readonly></td>
                                             <td><input type="number" step="0" name="products[${index}][materials][${i}][TonKho]" class="form-control form-control-sm stock-input" value="${tonKho.toFixed(2)}" readonly></td>
                                             <td><input type="number" step="0" name="products[${index}][materials][${i}][CanBoSung]" class="form-control form-control-sm needed-input" value="${canBoSung.toFixed(2)}" readonly></td>
                                         </tr>`;
                     });
                 } else { productHtml += `<tr><td colspan="7" class="text-center text-danger">Chưa có BOM.</td></tr>`; }
                 
                 productHtml += `</tbody></table></div>
                                 <div class="row">
                                     <div class="col-md-12 mb-3">
                                         <label class="form-label fw-bold">Phân xưởng</label>
                                         <select name="products[${index}][MaPhanXuong]" class="form-select" required>
                                             <option value="">-- Chọn --</option>`;
                 
                 // --- ===== ĐÃ SỬA: Dùng vòng lặp JS thay vì PHP ===== ---
                 if (xuongList && xuongList.length > 0) {
                     xuongList.forEach(xuong => {
                         productHtml += `<option value="${xuong.MaPhanXuong}">${xuong.MaPhanXuong} - ${xuong.TenPhanXuong}</option>`;
                     });
                 }
                 // --- ===== KẾT THÚC SỬA LỖI ===== ---

                 productHtml += `</select></div></div></div>`;
                 productContainer.insertAdjacentHTML('beforeend', productHtml);
             });
        }
        <?php endif; ?>

        // Initial Calculations on Load (Thứ tự rất quan trọng)
        updateTotalRawMaterials(); // 1. Tính NL
        window.updateLaborCost();    // 2. Tính Nhân công
        updateTotalCost();         // 3. Tính Tổng
        validateDates();           // 4. Validate

        if (!<?php echo $is_viewing ? 'true' : 'false'; ?>) {
            // Listener cho Ngày (CHỈ VALIDATE, KHÔNG TÍNH TOÁN LẠI CHI PHÍ)
            if (ngayBatDauInput) ngayBatDauInput.addEventListener('input', function() {
                validateDates();
            });
            if (ngayKetThucInput) ngayKetThucInput.addEventListener('input', function() {
                validateDates();
            });

            // Submit validation
            const planForm = document.getElementById('planForm');
            if (planForm) {
                planForm.addEventListener('submit', function(event) {
                    if (!validateDates()) {
                        event.preventDefault();
                        alert('Vui lòng kiểm tra lại Ngày bắt đầu và Ngày kết thúc.');
                    }
                    if (maDonHangSelect && !maDonHangSelect.value && !<?php echo $is_editing ? 'true' : 'false'; ?>) {
                         event.preventDefault();
                         alert('Vui lòng chọn Đơn hàng liên quan.');
                    }
                });
            }

            // Listener cho thay đổi trong Product Container (ĐÃ SỬA LỖI)
            if (productContainer) {
                productContainer.addEventListener('input', function(e) {
                    
                    // Nếu là ô Sản lượng
                    if (e.target.classList.contains('total-sanluong')) {
                        window.updateTotalRawMaterials(); // 1. Cập nhật NVL
                        window.updateLaborCost();       // 2. Cập nhật Nhân công
                        window.updateTotalCost();         // 3. Cập nhật Tổng
                    }
                    
                    // (Đã xóa listener của Phân xưởng, vì nó không ảnh hưởng CPNC nữa)
                });
            }
        }

    });
</script>