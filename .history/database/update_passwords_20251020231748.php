<?php
/**
 * Script cập nhật mật khẩu cho tài khoản demo
 * Chạy file này để cập nhật mật khẩu thành 123456 với hash đúng
 */

// Bao gồm file cấu hình
require_once '../config/config.php';
require_once '../config/database.php';

echo "<h2>Cập nhật Mật khẩu Demo - Hệ thống 4Ever</h2>";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Mật khẩu mới
    $newPassword = '123456';
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    echo "<p><strong>Mật khẩu gốc:</strong> $newPassword</p>";
    echo "<p><strong>Hash mới:</strong> " . substr($hashedPassword, 0, 50) . "...</p>";
    
    // Danh sách tài khoản cần cập nhật
    $accounts = [
        'admin',
        'kehoach01', 
        'xuongtruong01',
        'totruong01',
        'qc01',
        'khonl01',
        'khotp01',
        'congnhan01',
        'congnhan02'
    ];
    
    echo "<h3>Đang cập nhật mật khẩu...</h3>";
    
    // Cập nhật vào bảng NhanVien theo MaNV
    $updateQuery = "UPDATE NhanVien SET Password = :password WHERE MaNV = :username";
    $stmt = $conn->prepare($updateQuery);
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($accounts as $username) {
        try {
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':username', $username);
            
            if ($stmt->execute()) {
                echo "<p style='color: green'>✓ Cập nhật thành công: <strong>$username</strong></p>";
                $successCount++;
            } else {
                echo "<p style='color: red'>✗ Lỗi cập nhật: <strong>$username</strong></p>";
                $errorCount++;
            }
        } catch (PDOException $e) {
            echo "<p style='color: red'>✗ Lỗi: $username - " . $e->getMessage() . "</p>";
            $errorCount++;
        }
    }
    
    echo "<hr>";
    echo "<h3>Kết quả:</h3>";
    echo "<p><strong>Thành công:</strong> $successCount tài khoản</p>";
    echo "<p><strong>Lỗi:</strong> $errorCount tài khoản</p>";
    
    // Kiểm tra xác thực
    echo "<h3>Kiểm tra xác thực:</h3>";
    // Kiểm tra bản ghi admin trong bảng NhanVien (nếu có)
    $testQuery = "SELECT MaNV as username, Password as password FROM NhanVien WHERE MaNV = 'admin'";
    $testStmt = $conn->prepare($testQuery);
    $testStmt->execute();
    $testUser = $testStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($testUser && password_verify($newPassword, $testUser['password'])) {
        echo "<p style='color: green'><strong>✓ Kiểm tra thành công!</strong> Có thể đăng nhập bằng mật khẩu '$newPassword'</p>";
    } else {
        echo "<p style='color: red'><strong>✗ Kiểm tra thất bại!</strong> Vẫn có vấn đề với mật khẩu</p>";
    }
    
    echo "<div style='background: #e8f5e8; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h3 style='color: #2d5016;'>🎉 Hoàn tất!</h3>";
    echo "<p><strong>Bây giờ có thể đăng nhập với:</strong></p>";
    echo "<ul>";
    foreach ($accounts as $username) {
        echo "<li><strong>$username</strong> / $newPassword</li>";
    }
    echo "</ul>";
    echo "<p><a href='../index.php' target='_blank'>👉 Đăng nhập ngay</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #ffe6e6; padding: 15px; margin: 20px 0; border-radius: 5px; color: #d8000c;'>";
    echo "<h3>❌ Lỗi:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật Mật khẩu - 4Ever Factory</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
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
            font-weight: bold;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        ul {
            line-height: 1.8;
        }
        
        hr {
            border: 1px solid #eee;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Content được generate ở trên -->
    </div>
</body>
</html>