<?php ob_start(); ?>

<div class="container mt-4 mb-5">

    <?php if (!empty($msg)): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> <strong><?= $msg ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-uppercase">Danh sách yêu cầu chờ kiểm định</h5>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead class="table-info text-center sticky-top">
                        <tr>
                            <th class="text-center">Mã Phiếu Yêu Cầu</th>
                            <th class="text-center">Mã Lô Hàng</th>
                            <th class="text-center">Tên Sản Phẩm</th>
                            <th class="text-center">Ngày Yêu Cầu</th>
                            <th class="text-center">Tình trạng Lô Hàng</th>
                            <th class="text-center">Trạng Thái Phiếu Yêu Cầu</th>
                            <th class="text-center" style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($phieus)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-3">Không có phiếu nào đang chờ kiểm tra.</td></tr>
                        <?php else: ?>
                            <?php foreach ($phieus as $p): ?>
                            <tr class="<?= (isset($_GET['ma_phieu']) && $_GET['ma_phieu'] == $p['MaPhieuKT']) ? 'table-warning border border-primary' : '' ?>">
                                <td class="text-center fw-bold text-primary"><?= htmlspecialchars($p['MaPhieuKT']) ?></td>
                                <td class="text-center fw-bold"><?= htmlspecialchars($p['MaLoHang']) ?></td>
                                <td><?= htmlspecialchars($p['TenSanPham']) ?></td>
                                <td class="text-center"><?= date('d/m/Y', strtotime($p['NgayKiemTra'])) ?></td>
                                
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= htmlspecialchars($p['TrangThaiQC']) ?></span>
                                </td>

                                <td class="text-center">
                                    <?php if ($p['TrangThai'] == 'Chờ kiểm tra'): ?>
                                        <span class="badge bg-warning text-dark">Chờ kiểm tra</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= htmlspecialchars($p['TrangThai']) ?></span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>qc?ma_phieu=<?= $p['MaPhieuKT'] ?>" 
                                       class="btn btn-outline-primary fw-bold btn-sm">
                                         Kiểm định
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if (!empty($selectedPhieu)): ?>
    <div id="processingForm" class="card shadow-sm border mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-uppercase fw-bold">
                Phiếu Kết Quả Kiểm Định <?= $selectedPhieu['MaPhieuKT'] ?>
            </h5>
        </div>
        <div class="card-body">
            <form id="formKiemDinh" action="<?= BASE_URL ?>qc/store" method="POST">
                <input type="hidden" name="MaPhieuKT" value="<?= $selectedPhieu['MaPhieuKT'] ?>">
                <input type="hidden" name="MaLoHang" value="<?= $selectedPhieu['MaLoHang'] ?>">
                <h6 class="text-primary fw-bold mb-3">1. Thông tin phiếu yêu cầu kiểm định</h6>
                <div class="border rounded p-3 mb-4 ">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Mã Phiếu Yêu Cầu</label>
                            <input type="text" class="form-control bg-white " value="<?= $selectedPhieu['MaPhieuKT'] ?>" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small ">Mã Lô Hàng</label>
                            <input type="text" class="form-control bg-white " value="<?= $selectedPhieu['MaLoHang'] ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small ">Sản Phẩm</label>
                            <input type="text" class="form-control bg-white" value="<?= $selectedPhieu['TenSanPham'] ?>" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small ">Người yêu cầu</label>
                            <input type="text" class="form-control bg-white" value="<?= $selectedPhieu['NguoiYeuCau'] ?? 'Kho/SX' ?>" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small ">Số lượng lô hàng</label>
                            <input type="text" class="form-control bg-white text-dark" value="<?= number_format($selectedPhieu['SoLuong'] ?? 0) ?>" readonly>
                        </div>
                         <div class="col-md-3">
                            <label class="form-label fw-bold small ">Trạng Thái Lô Hàng</label>
                            <input type="text" class="form-control bg-white text-danger" value="<?= $selectedPhieu['TrangThaiQC'] ?>" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small ">Trạng Thái Phiếu</label>
                            <input type="text" class="form-control bg-white text-danger" value="<?= $selectedPhieu['TrangThai'] ?>" readonly>
                        </div>
                    </div>
                </div>

                <h6 class="text-primary fw-bold mb-3">2. Ghi nhận kết quả kiểm định</h6>
                <div class="card-body p-4 border rounded mb-3 shadow-sm">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Kết luận kiểm định <span class="text-danger">*</span></label>
                            <div class="d-flex gap-5 mt-1 border p-3 rounded bg-white">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="KetQua" id="kqDat" value="Đạt" required>
                                    <label class="form-check-label fw-bold text-success fs-5" for="kqDat">Đạt</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="KetQua" id="kqKhongDat" value="Không đạt">
                                    <label class="form-check-label fw-bold text-danger fs-5" for="kqKhongDat">Không đạt</label>
                                </div>
                            </div>
                            <div id="error-radio" class="text-danger small mt-1 d-none">Vui lòng chọn kết quả kiểm định.</div>
                        </div>
                    </div>

                    <div id="nguyenNhanBox" class="mb-3 d-none bg-danger bg-opacity-10 p-3 rounded border border-danger">
                        <label class="form-label fw-bold text-danger">Ghi chú / Nguyên nhân từ chối <span class="text-danger">*</span></label>
                        <textarea name="GhiChu" id="txtGhiChu" class="form-control" rows="3" 
                            placeholder="Ví dụ: Sai màu sắc, Lỗi form dáng, Rách vải..."></textarea>
                        <div id="error-ghichu" class="text-danger fw-bold mt-1 d-none"></div>
                    </div>
                    <div class="row g-3 mt-2 border-top pt-3 rounded mx-0">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nhân viên kiểm định</label>
                            <div class="input-group">
                                <input type="text" class="form-control fw-bold" value="<?= $_SESSION['full_name'] ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Ngày thực hiện</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-white" value="<?= date('d/m/Y H:i') ?>" readonly>
                            </div>
                        </div>
                    </div>
                    </div>

                <div class="d-flex justify-content-end pt-2">
                    <a href="<?= BASE_URL ?>qc" class="btn btn-secondary me-2" style="min-width: 120px;">Hủy bỏ</a>
                    <button type="submit" class="btn btn-success fw-bold" style="min-width: 150px;">
                     Lưu Kết Quả
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Logic ẩn hiện khung nhập lý do
            document.getElementById("processingForm").scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            const radioButtons = document.querySelectorAll('input[name="KetQua"]');
            const errorBox = document.getElementById('nguyenNhanBox');
            const txtGhiChu = document.getElementById('txtGhiChu');
            
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'Không đạt') {
                        errorBox.classList.remove('d-none');
                        txtGhiChu.focus();
                    } else {
                        errorBox.classList.add('d-none');
                        txtGhiChu.value = ''; // Xóa ghi chú nếu chọn lại Đạt
                        document.getElementById('error-ghichu').classList.add('d-none');
                    }
                });
            });

            const form = document.getElementById('formKiemDinh');
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Kiểm tra đã chọn Radio chưa
                const selectedRadio = document.querySelector('input[name="KetQua"]:checked');
                if (!selectedRadio) {
                    document.getElementById('error-radio').classList.remove('d-none');
                    isValid = false;
                } else {
                    document.getElementById('error-radio').classList.add('d-none');
                    
                    // Nếu chọn "Không đạt", kiểm tra nội dung Ghi chú
                    if (selectedRadio.value === 'Không đạt') {
                        const content = txtGhiChu.value.trim();
                        const errorText = document.getElementById('error-ghichu');
                        
                        // Regex: Phải chứa ít nhất 1 ký tự chữ cái Tiếng Việt hoặc Latin
                        // (Cho phép số và ký tự đặc biệt, nhưng BẮT BUỘC phải có chữ để tránh spam toàn dấu chấm)
                        const hasLetters = /[a-zA-Zà-ỹÀ-Ỹ]/.test(content);

                        if (content.length === 0) {
                            errorText.textContent = "Vui lòng nhập nguyên nhân từ chối.";
                            errorText.classList.remove('d-none');
                            isValid = false;
                        } else if (!hasLetters) {
                            errorText.textContent = "Nguyên nhân không hợp lệ (Phải chứa ký tự chữ cái mô tả lỗi).";
                            errorText.classList.remove('d-none');
                            isValid = false;
                        } else {
                            errorText.classList.add('d-none');
                        }
                    }
                }

                if (!isValid) {
                    e.preventDefault(); // Chặn gửi form nếu lỗi
                }
            });
        });
    </script>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0 text-uppercase">Lịch sử ghi nhận kết quả</h5>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-hover mb-0 align-middle">
                    <thead class="table-info text-center sticky-top">
                        <tr>
                            <th style="width: 50px;">STT</th>
                            <th>Mã KD</th>
                            <th>Mã PYC</th>
                            <th>Mã Lô</th>
                            <th>Sản Phẩm</th>
                            <th>Ngày Lập</th>
                            <th>Kết Quả</th>
                            <th>Trạng Thái</th>
                            <th style="width: 150px;">Nguyên Nhân</th>
                            <!-- <th style="width: 100px;">Chi tiết</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($history)): ?>
                            <tr><td colspan="8" class="text-center py-3">Chưa có lịch sử.</td></tr>
                        <?php else: ?>
                            <?php $i = 1; foreach ($history as $h): ?>
                            <tr>
                                <td class="text-center"><?= $i++ ?></td>
                                <td class="text-center fw-bold"><?= htmlspecialchars($h['MaKD']) ?></td>
                                 <td class="text-center"><?= htmlspecialchars($h['MaPhieuKT']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($h['MaLoHang']) ?></td>
                                <td><?= htmlspecialchars($h['TenSanPham']) ?></td>
                                <td class="text-center"><?= date('d/m/Y', strtotime($h['NgayLap'])) ?></td>
                                <td class="text-center">
                                    <?php if($h['KetQua'] == 'Đạt'): ?>
                                        <span class="badge bg-success">Đạt</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Không đạt</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($h['TrangThai'] == 'Đã kiểm tra'): ?>
                                        <span class="badge bg-info text-dark">Đã kiểm tra</span>
                                    <?php elseif($h['TrangThai'] == 'Bị từ chối'): ?>
                                        <span class="badge bg-danger">Bị từ chối</span>
                                    <?php else: ?>
                                        <?= htmlspecialchars($h['TrangThai']) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?= htmlspecialchars($h['GhiChu']) ?></td>
                                <!-- <td class="text-center">
                                    <button class="btn btn-outline-info btn-sm view-btn" data-id="<?= $h['MaPhieuKT'] ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td> -->
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>