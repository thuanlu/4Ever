<?php
// [THAY THẾ TOÀN BỘ FILE: app/views/kehoachsanxuat/form_nvl.php]

// --- BIẾN & THIẾT LẬP ---
// $isView được truyền từ create_nvl.php (false) hoặc view_nvl.php (true)
$isView = $isView ?? false;
$disabled = $isView ? 'disabled' : '';

// $phieu, $chiTiet, $kehoach_list, $nhaCungCapList, $currentUserName
// được truyền từ Controller (thông qua create_nvl.php hoặc view_nvl.php)
$phieu = $phieu ?? [];
$chiTiet = $chiTiet ?? [];
$kehoach_list = $kehoach_list ?? [];
$nhaCungCapList = $nhaCungCapList ?? [];
$currentUserName = $currentUserName ?? $_SESSION['full_name'] ?? 'N/A';
$currentDate = date('Y-m-d');

?>

<div class="card">
    <form action="<?php echo $isView ? '#' : BASE_URL . 'kehoachsanxuat/phieudatnvl/store'; ?>" method="POST" id="phieuNVLForm">
        <div class="card-header">
            <h5 class="mb-0"><?php echo $isView ? 'Chi tiết Phiếu Đặt NVL' : 'Tạo Phiếu Đặt Hàng NVL Mới'; ?></h5>
        </div>
        <div class="card-body">
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>


            <h6 class="text-primary">1. Thông tin chung</h6>
            <hr>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="MaPhieu" class="form-label fw-bold">Mã phiếu</label>
                    <input type="text" class="form-control" id="MaPhieu" name="MaPhieu" value="<?php echo htmlspecialchars($phieu['MaPhieu'] ?? 'Tự động tạo'); ?>" readonly>
                </div>
                <div class="col-md-4">
                    <label for="NgayLapPhieu" class="form-label fw-bold">Ngày lập phiếu</label>
                    <input type="date" class="form-control" id="NgayLapPhieu" name="NgayLapPhieu" value="<?php echo htmlspecialchars($phieu['NgayLapPhieu'] ?? $currentDate); ?>" readonly>
                </div>
                <div class="col-md-4">
                    <label for="NguoiLapPhieu" class="form-label fw-bold">Người lập phiếu</label>
                    <input type="text" class="form-control" id="NguoiLapPhieu" name="NguoiLapPhieu" value="<?php echo htmlspecialchars($phieu['NguoiLapPhieu'] ?? $currentUserName); ?>" readonly>
                </div>
                
                

                <div class="col-md-7">
                    <label for="MaKHSXSelect" class="form-label fw-bold">Kế hoạch cần lập phiếu</label>
                    
                    <?php // CHẾ ĐỘ XEM: Hiển thị text
                    if ($isView): 
                        $tenKHSX = $phieu['MaKHSX'] ?? 'Không có';
                        if (isset($phieu['TenKeHoach']) && $phieu['TenKeHoach']) {
                            $tenKHSX = $phieu['TenKeHoach'] . ' (' . $phieu['MaKHSX'] . ')';
                        }
                    ?>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($tenKHSX); ?>" disabled>
                        <input type="hidden" name="MaKHSX" value="<?php echo htmlspecialchars($phieu['MaKHSX'] ?? ''); ?>">
                    
                    <?php // CHẾ ĐỘ TẠO: Hiển thị dropdown
                    else: ?>
                        <select class="form-select" id="MaKHSXSelect" name="MaKHSX" <?php echo $disabled; ?> required>
                            <option value="">-- Chọn kế hoạch thiếu NVL --</option>
                            <?php foreach ($kehoach_list as $kh): ?>
                                <option value="<?php echo htmlspecialchars($kh['MaKeHoach']); ?>">
                                    <?php echo htmlspecialchars($kh['TenKeHoach']); ?> (<?php echo htmlspecialchars($kh['MaKeHoach']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>

                </div>
                
                 <div class="col-md-5">
                    <label for="TenPhieu" class="form-label fw-bold">Tên phiếu đặt NVL</label>
                    <input type="text" class="form-control" id="TenPhieu" name="TenPhieu" value="<?php echo htmlspecialchars($phieu['TenPhieu'] ?? ''); ?>" <?php echo $disabled; ?> required>
                </div>
            </div>

            <h6 class="text-primary mt-4">2. Thông tin nguyên vật liệu thiếu và chi phí (Tự động)</h6>
            <hr>
            
            <div id="nvl-loading" class="col-12 text-center" style="display: none;">
                 <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                 <p>Đang tải thông tin thiếu hụt NVL...</p>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="nvlTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 15%;">Mã nguyên liệu</th>
                            <th style="width: 30%;">Tên nguyên liệu</th>
                            <th style="width: 15%;">Số lượng thiếu</th>
                            <th style="width: 20%;">Đơn giá (dự kiến)</th>
                            <th style="width: 20%;">Thành tiền (dự kiến)</th>
                        </tr>
                    </thead>
                    <tbody id="nvlTableBody">
                        <?php // Dành cho CHẾ ĐỘ XEM (VIEW)
                        if ($isView && !empty($chiTiet)): ?>
                            <?php foreach ($chiTiet as $item): ?>
                                <tr>
                                    <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($item['MaNVL']); ?>" readonly></td>
                                    <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($item['TenNVL']); ?>" readonly></td>
                                    <td><input type="number" class="form-control" value="<?php echo htmlspecialchars($item['SoLuongCan']); ?>" readonly></td>
                                    <td><input type="number" class="form-control" value="<?php echo htmlspecialchars($item['DonGia']); ?>" readonly></td>
                                    <td><input type="number" class="form-control" value="<?php echo htmlspecialchars($item['ThanhTien']); ?>" readonly></td>
                                </tr>
                            <?php endforeach; ?>
                        
                        <?php // Dành cho CHẾ ĐỘ TẠO (CREATE)
                        elseif (!$isView): ?>
                            <tr>
                                <td colspan="5" class="text-center" id="nvl-placeholder">Vui lòng chọn Kế hoạch sản xuất...</td>
                            </tr>
                        <?php // Dành cho CHẾ ĐỘ XEM (VIEW) nhưng không có chi tiết
                        else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Không có chi tiết nguyên vật liệu.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="row justify-content-end mt-3">
                <div class="col-md-4">
                    <label for="TongChiPhiDuKien" class="form-label fw-bold">Tổng chi phí dự kiến</label>
                    <input type="number" class="form-control form-control-lg bg-light" id="TongChiPhiDuKien" name="TongChiPhiDuKien" value="<?php echo htmlspecialchars($phieu['TongChiPhiDuKien'] ?? 0); ?>" readonly>
                </div>
            </div>

            <h6 class="text-primary mt-4">3. Thông tin nhà cung cấp</h6>
            <hr>
            <div class="mb-3">
                <label for="MaNhaCungCap" class="form-label fw-bold">Nhà cung cấp (dự kiến)</label>
                
                <?php if ($isView): // CHẾ ĐỘ XEM
                ?>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($phieu['TenNhaCungCap'] ?? 'Chưa chọn'); ?>" disabled>
                    <input type="hidden" name="MaNhaCungCap" value="<?php echo htmlspecialchars($phieu['MaNhaCungCap'] ?? ''); ?>">
                <?php else: // CHẾ ĐỘ TẠO
                ?>
                    <select class="form-select" id="MaNhaCungCap" name="MaNhaCungCap" <?php echo $disabled; ?> required>
                        <option value="">-- Chọn nhà cung cấp --</option>
                        <?php if (isset($nhaCungCapList) && is_array($nhaCungCapList)): ?>
                            <?php foreach ($nhaCungCapList as $ncc): ?>
                                <option value="<?php echo htmlspecialchars($ncc['MaNhaCungCap']); ?>"
                                    <?php echo (isset($phieu['MaNhaCungCap']) && $phieu['MaNhaCungCap'] == $ncc['MaNhaCungCap']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ncc['TenNhaCungCap']); ?> (<?php echo htmlspecialchars($ncc['MaNhaCungCap']); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                <?php endif; ?>
            </div>
        </div>

        <div class="card-footer text-end">
            <a href="<?php echo BASE_URL; ?>kehoachsanxuat/phieudatnvl" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
            <?php if (!$isView): ?>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Tạo phiếu
                </button>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php // Chỉ chạy JavaScript ở chế độ TẠO MỚI (CREATE)
if (!$isView): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- Lấy các phần tử DOM ---
    const maKHSXSelect = document.getElementById('MaKHSXSelect');
    const tableBody = document.getElementById('nvlTableBody');
    const placeholder = document.getElementById('nvl-placeholder');
    const loadingDiv = document.getElementById('nvl-loading');
    const tongChiPhiInput = document.getElementById('TongChiPhiDuKien');
    const tenPhieuInput = document.getElementById('TenPhieu');
    const baseUrl = '<?php echo BASE_URL; ?>';

    // --- Xử lý sự kiện thay đổi Kế hoạch ---
    if (maKHSXSelect) {
        maKHSXSelect.addEventListener('change', function() {
            const maKeHoach = this.value;
            const tenKeHoachSelected = this.options[this.selectedIndex].text.trim();

            // Reset
            tableBody.innerHTML = '';
            tongChiPhiInput.value = 0;
            
            // [SỬA] Đã vô hiệu hóa tính năng tự động điền tên phiếu
            /*
            if (tenPhieuInput.value.trim() === '') {
                tenPhieuInput.value = `Phiếu NVL cho ${tenKeHoachSelected}`;
            }
            */

            if (!maKeHoach) {
                if (placeholder) tableBody.appendChild(placeholder);
                return;
            }

            // Hiển thị loading
            if (loadingDiv) loadingDiv.style.display = 'block';

            // --- Gọi AJAX (Fetch API) ---
            fetch(`${baseUrl}kehoachsanxuat/getThongTinThieuHutNVL/${maKeHoach}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Lỗi mạng: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    if (loadingDiv) loadingDiv.style.display = 'none';

                    if (data.error) {
                        throw new Error(data.error);
                    }

                    if (data.materials && data.materials.length > 0) {
                        buildHtmlTable(data.materials);
                    } else {
                        tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-success">Kế hoạch này không thiếu nguyên vật liệu.</td></tr>';
                    }
                })
                .catch(error => {
                    if (loadingDiv) loadingDiv.style.display = 'none';
                    tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Lỗi: ${error.message}</td></tr>`;
                });
        });
    }

    /**
     * Xây dựng bảng HTML từ dữ liệu JSON trả về
     */
    function buildHtmlTable(materials) {
        let totalCost = 0;
        tableBody.innerHTML = ''; // Xóa sạch

        materials.forEach((item, index) => {
            // Lấy giá trị, đảm bảo là số
            const soLuongThieu = parseFloat(item.SoLuongThieu) || 0;
            const donGia = parseFloat(item.DonGia) || 0; // DonGia này lấy từ CSDL (bảng nguyenlieu)
            const thanhTien = soLuongThieu * donGia;
            totalCost += thanhTien;

            const newRow = document.createElement('tr');
            
            // Tên của input PHẢI khớp với logic đọc ở Controller (hàm store)
            newRow.innerHTML = `
                <td>
                    <input type="text" class="form-control" name="materials[${index}][MaNVL]" value="${item.MaNVL}" readonly>
                </td>
                <td>
                    <input type="text" class="form-control" name="materials[${index}][TenNVL]" value="${item.TenNVL}" readonly>
                </td>
                <td>
                    <input type="number" class="form-control" name="materials[${index}][SoLuongCan]" value="${soLuongThieu.toFixed(2)}" readonly>
                </td>
                <td>
                    <input type="number" class="form-control" name="materials[${index}][DonGia]" value="${donGia.toFixed(2)}" readonly>
                </td>
                <td>
                    <input type="number" class="form-control" name="materials[${index}][ThanhTien]" value="${thanhTien.toFixed(2)}" readonly>
                </td>
            `;
            tableBody.appendChild(newRow);
        });

        // Cập nhật tổng chi phí
        tongChiPhiInput.value = totalCost.toFixed(2);
    }
});
</script>
<?php endif; ?>