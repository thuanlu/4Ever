<?php
/**
 * Script thiết lập database cho hệ thống quản lý sản xuất
 * Chạy file này để tạo cơ sở dữ liệu và dữ liệu mẫu
 */

// Bao gồm file cấu hình
require_once '../config/config.php';

echo "<h2>Thiết lập Database - Hệ thống Quản lý Sản xuất 4Ever</h2>";

try {
    // Kết nối MySQL mà chưa chọn database cụ thể
    $host = 'localhost';
    $username = 'root';
    $password = '';
    
    $conn = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✓ Kết nối MySQL thành công</p>";
    
    // Đọc file SQL tạo database
    $sqlFile = '../database/factory_management.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Không tìm thấy file SQL: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Tách các câu lệnh SQL
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt);
        }
    );
    
    echo "<p>Đang tạo cơ sở dữ liệu và bảng...</p>";
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $conn->exec($statement);
            } catch (PDOException $e) {
                // Bỏ qua lỗi nếu database đã tồn tại
                if (strpos($e->getMessage(), 'database exists') === false) {
                    throw $e;
                }
            }
        }
    }
    
    echo "<p>✓ Tạo cơ sở dữ liệu thành công</p>";
    
    // Đọc file dữ liệu mẫu
    $sampleDataFile = '../database/sample_data.sql';
    if (file_exists($sampleDataFile)) {
        echo "<p>Đang thêm dữ liệu mẫu...</p>";
        
        $sampleSql = file_get_contents($sampleDataFile);
        $sampleStatements = array_filter(
            array_map('trim', explode(';', $sampleSql)),
            function($stmt) {
                return !empty($stmt) && !preg_match('/^--/', $stmt);
            }
        );
        
        foreach ($sampleStatements as $statement) {
            if (!empty($statement)) {
                try {
                    $conn->exec($statement);
                } catch (PDOException $e) {
                    // Bỏ qua lỗi duplicate entry
                    if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                        echo "<p style='color: orange'>Warning: " . $e->getMessage() . "</p>";
                    }
                }
            }
        }
        
        echo "<p>✓ Thêm dữ liệu mẫu thành công</p>";
    }
    
    // Kiểm tra dữ liệu
    $conn->exec("USE factory_management");
    
    $tables = [
        'roles' => 'Vai trò người dùng',
        'users' => 'Người dùng', 
        'workshops' => 'Xưởng sản xuất',
        'teams' => 'Tổ sản xuất',
        'products' => 'Sản phẩm',
        'materials' => 'Nguyên vật liệu',
        'production_plans' => 'Kế hoạch sản xuất'
    ];
    
    echo "<h3>Kiểm tra dữ liệu:</h3>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Bảng</th><th>Mô tả</th><th>Số dòng</th></tr>";
    
    foreach ($tables as $table => $description) {
        $stmt = $conn->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "<tr><td>$table</td><td>$description</td><td>$count</td></tr>";
    }
    
    echo "</table>";
    
    echo "<h3>Tài khoản đăng nhập mặc định:</h3>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Tên đăng nhập</th><th>Mật khẩu</th><th>Vai trò</th></tr>";
    
    $userStmt = $conn->query("
        SELECT u.username, u.full_name, r.role_description 
        FROM users u 
        INNER JOIN roles r ON u.role_id = r.id 
        ORDER BY u.id LIMIT 5
    ");
    
    while ($user = $userStmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$user['username']}</td>";
        echo "<td>123456</td>";
        echo "<td>{$user['role_description']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<div style='background: #e8f5e8; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3 style='color: #2d5016;'>🎉 Thiết lập hoàn tất!</h3>";
    echo "<p><strong>Truy cập hệ thống:</strong> <a href='../index.php' target='_blank'>http://localhost/4Ever/</a></p>";
    echo "<p><strong>Tài khoản admin:</strong> admin / 123456</p>";
    echo "<p><strong>Các tính năng chính:</strong></p>";
    echo "<ul>";
    echo "<li>✓ Quản lý kế hoạch sản xuất</li>";
    echo "<li>✓ Phân công xưởng và tổ sản xuất</li>";
    echo "<li>✓ Quản lý nguyên vật liệu</li>";
    echo "<li>✓ Kiểm tra chất lượng (QC)</li>";
    echo "<li>✓ Chấm công nhân viên</li>";
    echo "<li>✓ Dashboard và báo cáo</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #ffe6e6; padding: 15px; margin: 20px 0; border-radius: 5px; color: #d8000c;'>";
    echo "<h3>❌ Lỗi thiết lập:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p><strong>Hướng dẫn khắc phục:</strong></p>";
    echo "<ol>";
    echo "<li>Đảm bảo XAMPP đang chạy (Apache + MySQL)</li>";
    echo "<li>Kiểm tra thông tin kết nối database trong config/database.php</li>";
    echo "<li>Đảm bảo có quyền tạo database</li>";
    echo "</ol>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thiết lập Database - 4Ever Factory</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin: 10px 0;
        }
        
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        th {
            background: #667eea;
            color: white;
        }
        
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h2 {
            color: #667eea;
            text-align: center;
            margin-bottom: 30px;
        }
        
        h3 {
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
        }
        
        p {
            line-height: 1.6;
        }
        
        a {
            color: #667eea;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        ul, ol {
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Content được generate ở trên -->
    </div>
</body>
</html>
