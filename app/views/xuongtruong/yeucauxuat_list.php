<<<<<<< HEAD
<?php
// Dashboard chức năng cho Xưởng trưởng
?>
<div class="container py-4">
	<h2 class="mb-4"><i class="fas fa-industry me-2"></i>Dashboard Xưởng trưởng</h2>
	<div class="row g-4">
		<div class="col-md-6">
			<div class="card h-100">
				<div class="card-header bg-primary text-white">
					<i class="fas fa-arrow-up-right-from-square me-2"></i>Quản lý phiếu yêu cầu xuất nguyên liệu
				</div>
				<div class="card-body">
					<a href="/4Ever/xuongtruong/yeucauxuat_create" class="btn btn-success mb-2">
						<i class="fas fa-plus"></i> Tạo phiếu xuất nguyên liệu
					</a>
					<a href="/4Ever/xuongtruong/yeucauxuat_list" class="btn btn-outline-primary">
						<i class="fas fa-list"></i> Danh sách phiếu xuất
					</a>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card h-100">
				<div class="card-header bg-warning text-dark">
					<i class="fas fa-clipboard-check me-2"></i>Quản lý phiếu kiểm tra lô/sản phẩm
				</div>
				<div class="card-body">
					<a href="/4Ever/xuongtruong/phieu_kiemtra_lo" class="btn btn-warning mb-2">
						<i class="fas fa-search"></i> Kiểm tra lô/sản phẩm
					</a>
					<a href="/4Ever/xuongtruong/phieu_kiemtra_list" class="btn btn-outline-warning">
						<i class="fas fa-list"></i> Danh sách phiếu kiểm tra
					</a>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card h-100">
				<div class="card-header bg-info text-white">
					<i class="fas fa-chart-bar me-2"></i>Báo cáo & Thống kê
				</div>
				<div class="card-body">
					<a href="/4Ever/reports/production" class="btn btn-info mb-2">
						<i class="fas fa-chart-line"></i> Báo cáo sản xuất
					</a>
					<a href="/4Ever/reports/attendance" class="btn btn-outline-info">
						<i class="fas fa-user-check"></i> Thống kê chấm công
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
=======
<?php ob_start(); ?>
<div class="container mt-3">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h4 class="mb-0"><?php echo htmlspecialchars($pageTitle ?? 'Danh sách phiếu yêu cầu xuất'); ?></h4>
		<div>
			<a class="btn btn-outline-secondary btn-sm" href="<?php echo BASE_URL; ?>">Về Dashboard</a>
			<a class="btn btn-primary btn-sm" href="<?php echo BASE_URL; ?>yeucauxuat">Tạo phiếu mới</a>
		</div>
	</div>

	<form method="get" class="row g-2 mb-3">
		<div class="col-auto">
			<input type="text" name="q" value="<?php echo htmlspecialchars($keyword ?? ''); ?>" class="form-control form-control-sm" placeholder="Tìm mã / phân xưởng">
		</div>
		<div class="col-auto">
			<select name="status" class="form-select form-select-sm">
				<option value="">-- Tất cả trạng thái --</option>
				<option value="Nháp" <?php echo (isset($status) && $status==='Nháp')? 'selected':''; ?>>Nháp</option>
				<option value="Chờ duyệt" <?php echo (isset($status) && $status==='Chờ duyệt')? 'selected':''; ?>>Chờ duyệt</option>
				<option value="Đã duyệt" <?php echo (isset($status) && $status==='Đã duyệt')? 'selected':''; ?>>Đã duyệt</option>
			</select>
		</div>
		<div class="col-auto">
			<button class="btn btn-sm btn-primary">Lọc</button>
		</div>
	</form>

	<?php if (!empty($rows)): ?>
		<div class="table-responsive">
			<table class="table table-striped table-sm">
				<thead>
					<tr>
						<th>Mã phiếu</th>
						<th>Mã kế hoạch</th>
						<th>Ngày yêu cầu</th>
						<th>Trạng thái</th>
						<th>Xem</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($rows as $r): ?>
						<tr>
							<td><?php echo htmlspecialchars($r['ma_phieu'] ?? ''); ?></td>
							<td><?php echo htmlspecialchars($r['ma_kehoach'] ?? ''); ?></td>
							<td><?php echo htmlspecialchars($r['ngay_yeucau'] ?? ''); ?></td>
							<td><?php echo htmlspecialchars($r['trangthai'] ?? ''); ?></td>
								<td>
									<a class="btn btn-sm btn-outline-primary" href="<?php echo BASE_URL; ?>yeucauxuat/view?ma=<?php echo urlencode($r['ma_phieu'] ?? ''); ?>">Xem</a>
								</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php else: ?>
		<div class="alert alert-secondary">Không có phiếu nào phù hợp.</div>
	<?php endif; ?>

</div>

<?php $content = ob_get_clean(); include APP_PATH . '/views/layouts/main.php'; ?>
>>>>>>> 846529c2f597edacc8365b20a207f0deb2f52c10
