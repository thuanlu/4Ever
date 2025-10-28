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