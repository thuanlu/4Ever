
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

