<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Hệ thống Quản lý Sản xuất'; ?> - 4Ever Factory</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #2c1e34ff 0%, #3d2b4fff 100%);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            margin: 2px 0;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .main-content {
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
            color: #667eea !important;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: none;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .alert {
            border: none;
            border-radius: 10px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar động theo vai trò -->
            <div class="col-md-3 col-lg-2 sidebar px-0">
                <div class="d-flex flex-column h-100">
                    <!-- User Info + Sidebar -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="p-3 text-center border-bottom border-light">
                            <h5 class="text-white mb-0">
                                <i class="fas fa-industry me-2"></i>4Ever Factory
                            </h5>
                            <small class="text-light">Quản lý Sản xuất</small>
                        </div>
                        <div class="p-3 border-bottom border-light">
                            <div class="text-center">
                                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                                <div class="mt-2">
                                    <div class="text-white fw-bold"><?php echo $_SESSION['full_name'] ?? 'Người dùng'; ?></div>
                                    <small class="text-light"><?php echo $this->getRoleDisplayName($_SESSION['user_role'] ?? ''); ?></small>
                                </div>
                            </div>
                        </div>
                        <?php
                        $roleRaw = $_SESSION['user_role'] ?? '';
                        $roleMap = [
                            'KH' => 'kh',
                            'BGD' => 'bgd',
                            'XT' => 'xt',
                            'TT' => 'tt',
                            'QC' => 'qc',
                            'NVK' => 'nvk',
                            'CN' => 'cn',
                            'ADMIN' => 'admin',
                            'ban_giam_doc' => 'bgd',
                            'nhan_vien_ke_hoach' => 'kh',
                            'xuong_truong' => 'xt',
                            'to_truong' => 'tt',
                            'nhan_vien_qc' => 'qc',
                            'nhan_vien_kho_nl' => 'nvk',
                            'cong_nhan' => 'cn',
                            'admin' => 'admin',
                        ];
                        $role = strtolower($roleMap[$roleRaw] ?? $roleRaw);
                        $sidebarFile = APP_PATH . '/views/layouts/sidebar/' . $role . '.php';
                        if (file_exists($sidebarFile)) {
                            include $sidebarFile;
                        } else {
                            include APP_PATH . '/views/layouts/sidebar/default.php';
                        }
                        ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Header -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                    <div class="container-fluid">
                        <?php
                        $roleRaw = $_SESSION['user_role'] ?? '';
                        $roleMap = [
                            'KH' => 'kehoachsanxuat',
                            'BGD' => 'giamdoc',
                            'XT' => 'xuongtruong',
                            'TT' => 'totruong',
                            'QC' => 'qc',
                            'NVK' => 'kho',
                            'CN' => 'congnhan',
                            'ADMIN' => 'admin',
                            'ban_giam_doc' => 'giamdoc',
                            'nhan_vien_ke_hoach' => 'kehoachsanxuat',
                            'xuong_truong' => 'xuongtruong',
                            'to_truong' => 'totruong',
                            'nhan_vien_qc' => 'qc',
                            'nhan_vien_kho_nl' => 'kho',
                            'cong_nhan' => 'congnhan',
                            'admin' => 'admin',
                        ];
                        $role = strtolower($roleMap[$roleRaw] ?? 'dashboard');
                        $dashboardUrl = BASE_URL . $role . '/dashboard';
                        ?>
                        <a class="navbar-brand" href="<?php echo $dashboardUrl; ?>">
                            <?php echo $pageTitle ?? 'Dashboard'; ?>

                        </a>
                        
                        <div class="navbar-nav ms-auto">
                            <span class="navbar-text">
                                <i class="fas fa-calendar me-1"></i>
                                <?php echo date('d/m/Y H:i'); ?>
                            </span>
                        </div>
                    </div>
                </nav>
                
                <!-- Alerts -->
                <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); endif; ?>
                
                <?php if (isset($_SESSION['warning'])): ?>
                <div class="alert alert-warning alert-dismissible fade show m-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo $_SESSION['warning']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['warning']); endif; ?>
                
                <!-- Content -->
                <div class="p-3">
                    <?php echo $content; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Auto-dismiss only dismissible alerts after 5 seconds (keep informational cards persistent)
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            try {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } catch (e) {
                console.error('Error closing alert:', e);
            }
        });
    }, 5000);
    
    // SỬA LỖI: Add active class to the BEST match only
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy path hiện tại, loại bỏ dấu / ở cuối (nếu có)
        var currentPath = window.location.pathname.replace(/\/$/, ''); 
        
        var navLinks = document.querySelectorAll('.sidebar .nav-link');
        var bestMatch = null;
        var bestMatchLength = 0;

        navLinks.forEach(function(link) {
            var href = link.getAttribute('href');
            if (!href) return;
            
            // Dùng new URL() để lấy pathname một cách an toàn
            var linkPath = new URL(link.href).pathname.replace(/\/$/, '');

            // 1. Kiểm tra xem link này có phải là "cha" của path hiện tại không
            if (currentPath.startsWith(linkPath)) {
                
                // 2. Kiểm tra xem link này có "khớp" tốt hơn (dài hơn) link trước đó không
                if (linkPath.length > bestMatchLength) {
                    bestMatchLength = linkPath.length;
                    bestMatch = link;
                }
            }
        });

        // 3. Xóa 'active' khỏi tất cả các link (để reset)
        navLinks.forEach(function(link) {
            link.classList.remove('active');
        });

        // 4. Chỉ thêm 'active' vào link khớp nhất
        if (bestMatch) {
            bestMatch.classList.add('active');
        }
    });
</script>
    
    <?php
    function getRoleDisplayName($role) {
        $roleNames = [
            'ban_giam_doc' => 'Ban Giám Đốc',
            'nhan_vien_ke_hoach' => 'Nhân viên Kế hoạch',
            'xuong_truong' => 'Xưởng trưởng',
            'to_truong' => 'Tổ trưởng',
            'nhan_vien_qc' => 'Nhân viên QC',
            'nhan_vien_kho_nl' => 'Nhân viên Kho NL',
            'nhan_vien_kho_tp' => 'Nhân viên Kho TP',
            'cong_nhan' => 'Công nhân'
        ];
        return $roleNames[$role] ?? $role;
    }
    ?>
</body>
</html>
