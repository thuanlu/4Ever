<?php
ob_start();
?>
<div class="container mt-4">
    <div class="card mb-4">
        <div class="card-header gradient-header">
            <h4 class="mb-0 fw-bold" style="letter-spacing:0.5px;">Quản lý nhân viên</h4>
        </div>
        <style>
            .gradient-header {
                background: linear-gradient(90deg, #7b8cff 0%, #7b5fd4 100%);
                color: #fff;
                border-radius: 12px 12px 0 0;
                font-weight: bold;
                padding: 1.1rem 1.5rem;
            }
        </style>
        <div class="card-body pb-2">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Danh sách nhân viên</h5>
                <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#modalAddNV"><i class="fa fa-plus"></i> Thêm nhân viên</button>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã NV</th>
                            <th>Họ đệm</th>
                            <th>Tên</th>
                            <th>SĐT</th>
                            <th>Giới tính</th>
                            <th>Năm sinh</th>
                            <th>Chức vụ</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($nhanviens)): foreach ($nhanviens as $nv): ?>
                        <tr>
                            <td><?= htmlspecialchars($nv['manv']) ?></td>
                            <td><?= htmlspecialchars($nv['hodem']) ?></td>
                            <td><?= htmlspecialchars($nv['ten']) ?></td>
                            <td><?= htmlspecialchars($nv['sdt']) ?></td>
                            <td><?= htmlspecialchars($nv['gioitinh']) ?></td>
                            <td><?= htmlspecialchars($nv['namsinh']) ?></td>
                            <td><?= htmlspecialchars($nv['chucvu']) ?></td>
                            <td><?= htmlspecialchars($nv['trangthai']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditNV<?= $nv['manv'] ?>">Sửa</button>
                            </td>
                        </tr>
                        <!-- Modal Sửa nhân viên -->
                        <div class="modal fade" id="modalEditNV<?= $nv['manv'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="post" action="?action=edit_nv">
                                    <input type="hidden" name="manv" value="<?= htmlspecialchars($nv['manv']) ?>">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Sửa thông tin nhân viên</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php include APP_PATH . '/views/xuongtruong/quanlynhanvien/_form_nhanvien.php'; ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; else: ?>
                        <tr><td colspan="9" class="text-center">Chưa có nhân viên nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal Thêm nhân viên -->
    <div class="modal fade" id="modalAddNV" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="post" action="?action=add_nv">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm nhân viên mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php include APP_PATH . '/views/xuongtruong/quanlynhanvien/_form_nhanvien.php'; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Xác nhận</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include APP_PATH . '/views/layouts/main.php';
?>
