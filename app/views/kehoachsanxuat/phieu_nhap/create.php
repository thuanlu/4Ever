<?php
/**
 * View: Tạo phiếu nhập nguyên vật liệu
 * Hệ thống quản lý sản xuất nhà máy 4Ever
 */

// Bắt đầu output buffering để capture content
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus-circle"></i> Tạo Phiếu Nhập Nguyên Vật Liệu
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($_SESSION['errors'] as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php unset($_SESSION['errors']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($_SESSION['error']); ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form id="phieuNhapForm" method="POST" action="<?php echo BASE_URL; ?>/phieu-nhap/store">
                        <div class="row">
                            <!-- Thông tin cơ bản -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Thông tin cơ bản</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="maKeHoach" class="form-label">Chọn kế hoạch <span class="text-danger">*</span></label>
                                            <select class="form-select" id="maKeHoach" name="maKeHoach" required>
                                                <option value="">-- Chọn kế hoạch --</option>
                                                <?php foreach ($keHoach as $kh): ?>
                                                    <option value="<?php echo htmlspecialchars($kh['MaKeHoach']); ?>">
                                                        <?php echo htmlspecialchars($kh['MaKeHoach'] . ' - ' . $kh['TenKeHoach']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="ngayNhap" class="form-label">Ngày nhập <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="ngayNhap" name="ngayNhap" 
                                                   value="<?php echo date('Y-m-d'); ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="maNhaCungCap" class="form-label">Nhà cung cấp <span class="text-danger">*</span></label>
                                            <select class="form-select" id="maNhaCungCap" name="maNhaCungCap" required>
                                                <option value="">-- Chọn nhà cung cấp --</option>
                                                <?php foreach ($nhaCungCap as $ncc): ?>
                                                    <option value="<?php echo htmlspecialchars($ncc['MaNhaCungCap']); ?>">
                                                        <?php echo htmlspecialchars($ncc['TenNhaCungCap']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="thoiGianGiaoHang" class="form-label">Thời gian giao hàng <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="thoiGianGiaoHang" name="thoiGianGiaoHang" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Thông tin nhà cung cấp -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Thông tin nhà cung cấp</h5>
                                    </div>
                                    <div class="card-body" id="nhaCungCapInfo">
                                        <p class="text-muted">Vui lòng chọn nhà cung cấp để xem thông tin</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Danh sách nguyên vật liệu -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Danh sách nguyên vật liệu cần nhập</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="nguyenVatLieuList">
                                            <p class="text-muted">Vui lòng chọn kế hoạch để xem danh sách nguyên vật liệu</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin chi phí -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Thông tin chi phí</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="chiPhiKhac" class="form-label">Chi phí khác</label>
                                            <input type="number" class="form-control" id="chiPhiKhac" name="chiPhiKhac" 
                                                   min="0" step="0.01" value="0">
                                        </div>

                                        <div class="mb-3">
                                            <label for="vat" class="form-label">Thuế VAT (%)</label>
                                            <input type="number" class="form-control" id="vat" name="vat" 
                                                   min="0" max="100" step="0.01" value="10">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Tổng kết chi phí</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Tổng thành tiền:</strong>
                                            </div>
                                            <div class="col-6 text-end">
                                                <span id="tongThanhTien">0</span> VNĐ
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Chi phí khác:</strong>
                                            </div>
                                            <div class="col-6 text-end">
                                                <span id="chiPhiKhacDisplay">0</span> VNĐ
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Tổng trước VAT:</strong>
                                            </div>
                                            <div class="col-6 text-end">
                                                <span id="tongTruocVAT">0</span> VNĐ
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Tiền VAT:</strong>
                                            </div>
                                            <div class="col-6 text-end">
                                                <span id="tienVAT">0</span> VNĐ
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-6">
                                                <strong>Tổng chi phí:</strong>
                                            </div>
                                            <div class="col-6 text-end">
                                                <strong><span id="tongChiPhi">0</span> VNĐ</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ghi chú -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="ghiChu" class="form-label">Ghi chú</label>
                                            <textarea class="form-control" id="ghiChu" name="ghiChu" rows="3" 
                                                      placeholder="Nhập ghi chú (nếu có)"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden fields -->
                        <input type="hidden" id="chiTiet" name="chiTiet">
                        <input type="hidden" id="tongGiaTri" name="tongGiaTri">

                        <!-- Buttons -->
                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                <a href="<?php echo BASE_URL; ?>/phieu-nhap" class="btn btn-secondary me-2">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary" id="saveBtn" disabled>
                                    <i class="fas fa-save"></i> Lưu phiếu nhập
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const maKeHoachSelect = document.getElementById('maKeHoach');
    const maNhaCungCapSelect = document.getElementById('maNhaCungCap');
    const nhaCungCapInfo = document.getElementById('nhaCungCapInfo');
    const nguyenVatLieuList = document.getElementById('nguyenVatLieuList');
    const chiTietInput = document.getElementById('chiTiet');
    const tongGiaTriInput = document.getElementById('tongGiaTri');
    const saveBtn = document.getElementById('saveBtn');
    
    let chiTietNVL = [];
    
    // Xử lý khi chọn kế hoạch
    maKeHoachSelect.addEventListener('change', function() {
        if (this.value) {
            loadChiTietNVL(this.value);
        } else {
            nguyenVatLieuList.innerHTML = '<p class="text-muted">Vui lòng chọn kế hoạch để xem danh sách nguyên vật liệu</p>';
            chiTietNVL = [];
            updateChiTiet();
        }
    });
    
    // Xử lý khi chọn nhà cung cấp
    maNhaCungCapSelect.addEventListener('change', function() {
        if (this.value) {
            loadNhaCungCapInfo(this.value);
        } else {
            nhaCungCapInfo.innerHTML = '<p class="text-muted">Vui lòng chọn nhà cung cấp để xem thông tin</p>';
        }
    });
    
    // Load chi tiết nguyên vật liệu
    function loadChiTietNVL(maKeHoach) {
        fetch(`<?php echo BASE_URL; ?>/phieu-nhap/get-chi-tiet-nvl?maKeHoach=${maKeHoach}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    chiTietNVL = data.data;
                    renderNguyenVatLieuList();
                } else {
                    nguyenVatLieuList.innerHTML = '<p class="text-danger">Lỗi: ' + data.error + '</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                nguyenVatLieuList.innerHTML = '<p class="text-danger">Lỗi khi tải dữ liệu</p>';
            });
    }
    
    // Load thông tin nhà cung cấp
    function loadNhaCungCapInfo(maNhaCungCap) {
        fetch(`<?php echo BASE_URL; ?>/phieu-nhap/get-nha-cung-cap?maNhaCungCap=${maNhaCungCap}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const ncc = data.data;
                    nhaCungCapInfo.innerHTML = `
                        <div class="row">
                            <div class="col-12">
                                <h6>${ncc.TenNhaCungCap}</h6>
                                <p class="mb-1"><strong>Địa chỉ:</strong> ${ncc.DiaChi}</p>
                                <p class="mb-1"><strong>SĐT:</strong> ${ncc.SoDienThoai}</p>
                                <p class="mb-0"><strong>Email:</strong> ${ncc.Email}</p>
                            </div>
                        </div>
                    `;
                } else {
                    nhaCungCapInfo.innerHTML = '<p class="text-danger">Lỗi: ' + data.error + '</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                nhaCungCapInfo.innerHTML = '<p class="text-danger">Lỗi khi tải dữ liệu</p>';
            });
    }
    
    // Render danh sách nguyên vật liệu
    function renderNguyenVatLieuList() {
        if (chiTietNVL.length === 0) {
            nguyenVatLieuList.innerHTML = '<p class="text-muted">Không có nguyên vật liệu cần nhập</p>';
            return;
        }
        
        let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Mã NVL</th>
                            <th>Tên nguyên vật liệu</th>
                            <th>Đơn vị tính</th>
                            <th>Tồn kho hiện tại</th>
                            <th>Số lượng cần nhập</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        chiTietNVL.forEach((item, index) => {
            const thanhTien = item.SoLuongCanNhap * item.DonGia;
            html += `
                <tr>
                    <td>${item.MaNguyenLieu}</td>
                    <td>${item.TenNguyenLieu}</td>
                    <td>${item.DonViTinh}</td>
                    <td class="text-end">${parseFloat(item.SoLuongTonKho).toLocaleString()}</td>
                    <td class="text-end">${parseFloat(item.SoLuongCanNhap).toLocaleString()}</td>
                    <td class="text-end">${parseFloat(item.DonGia).toLocaleString()} VNĐ</td>
                    <td class="text-end"><strong>${parseFloat(thanhTien).toLocaleString()} VNĐ</strong></td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        
        nguyenVatLieuList.innerHTML = html;
        updateChiTiet();
    }
    
    // Cập nhật chi tiết và tính toán
    function updateChiTiet() {
        chiTietInput.value = JSON.stringify(chiTietNVL);
        calculateTotal();
        updateSaveButton();
    }
    
    // Tính toán tổng chi phí
    function calculateTotal() {
        let tongThanhTien = 0;
        
        chiTietNVL.forEach(item => {
            tongThanhTien += item.SoLuongCanNhap * item.DonGia;
        });
        
        const chiPhiKhac = parseFloat(document.getElementById('chiPhiKhac').value) || 0;
        const vat = parseFloat(document.getElementById('vat').value) || 0;
        
        const tongTruocVAT = tongThanhTien + chiPhiKhac;
        const tienVAT = tongTruocVAT * (vat / 100);
        const tongChiPhi = tongTruocVAT + tienVAT;
        
        // Cập nhật hiển thị
        document.getElementById('tongThanhTien').textContent = tongThanhTien.toLocaleString();
        document.getElementById('chiPhiKhacDisplay').textContent = chiPhiKhac.toLocaleString();
        document.getElementById('tongTruocVAT').textContent = tongTruocVAT.toLocaleString();
        document.getElementById('tienVAT').textContent = tienVAT.toLocaleString();
        document.getElementById('tongChiPhi').textContent = tongChiPhi.toLocaleString();
        
        // Cập nhật hidden field
        tongGiaTriInput.value = tongChiPhi;
    }
    
    // Cập nhật trạng thái nút lưu
    function updateSaveButton() {
        const canSave = chiTietNVL.length > 0 && 
                       document.getElementById('maNhaCungCap').value && 
                       document.getElementById('ngayNhap').value && 
                       document.getElementById('thoiGianGiaoHang').value;
        
        saveBtn.disabled = !canSave;
    }
    
    // Event listeners cho các input
    document.getElementById('chiPhiKhac').addEventListener('input', calculateTotal);
    document.getElementById('vat').addEventListener('input', calculateTotal);
    document.getElementById('maNhaCungCap').addEventListener('change', updateSaveButton);
    document.getElementById('ngayNhap').addEventListener('change', updateSaveButton);
    document.getElementById('thoiGianGiaoHang').addEventListener('change', updateSaveButton);
    
    // Validate form trước khi submit
    document.getElementById('phieuNhapForm').addEventListener('submit', function(e) {
        if (chiTietNVL.length === 0) {
            e.preventDefault();
            alert('Vui lòng chọn kế hoạch để xem danh sách nguyên vật liệu');
            return false;
        }
        
        if (!document.getElementById('maNhaCungCap').value) {
            e.preventDefault();
            alert('Vui lòng chọn nhà cung cấp');
            return false;
        }
        
        if (!document.getElementById('ngayNhap').value) {
            e.preventDefault();
            alert('Vui lòng chọn ngày nhập');
            return false;
        }
        
        if (!document.getElementById('thoiGianGiaoHang').value) {
            e.preventDefault();
            alert('Vui lòng chọn thời gian giao hàng');
            return false;
        }
    });
});
</script>

<?php
// Kết thúc output buffering và lưu content
$content = ob_get_clean();

// Include layout với content
require_once APP_PATH . '/views/layouts/main.php';
?>
