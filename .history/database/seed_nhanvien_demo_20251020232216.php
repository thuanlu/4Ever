<?php
/**
 * Seed demo NhanVien records if not exists
 * Visit: http://localhost/4Ever/database/seed_nhanvien_demo.php
 */
require_once '../config/config.php';
require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    echo '<h2>Seed demo NhanVien</h2>';

    // Ensure Password column exists
    $checkCol = $conn->prepare("SHOW COLUMNS FROM NhanVien LIKE 'Password'");
    $checkCol->execute();
    if ($checkCol->rowCount() === 0) {
        $conn->exec("ALTER TABLE NhanVien ADD COLUMN Password VARCHAR(255) NULL AFTER SoDienThoai");
        echo '<p>✓ Added column Password</p>';
    }

    $pwd = '123456';
    $hash = password_hash($pwd, PASSWORD_DEFAULT);

    $rows = [
        ['MaNV' => 'admin',        'HoTen' => 'Quản trị hệ thống',       'ChucVu' => 'ADMIN', 'BoPhan' => 'BGD',     'SoDienThoai' => '0900000000'],
        ['MaNV' => 'kehoach01',    'HoTen' => 'Nhân viên Kế hoạch 01',   'ChucVu' => 'KH',    'BoPhan' => 'Kế hoạch','SoDienThoai' => '0901234568'],
        ['MaNV' => 'xuongtruong01','HoTen' => 'Xưởng trưởng 01',         'ChucVu' => 'XT',    'BoPhan' => 'Xưởng',  'SoDienThoai' => '0901234569'],
        ['MaNV' => 'totruong01',   'HoTen' => 'Tổ trưởng 01',            'ChucVu' => 'TT',    'BoPhan' => 'Xưởng',  'SoDienThoai' => '0901234570'],
        ['MaNV' => 'qc01',         'HoTen' => 'Nhân viên QC 01',         'ChucVu' => 'QC',    'BoPhan' => 'QC',     'SoDienThoai' => '0901234571'],
        ['MaNV' => 'khonl01',      'HoTen' => 'Nhân viên Kho NL 01',     'ChucVu' => 'NVK',   'BoPhan' => 'Kho NL', 'SoDienThoai' => '0901234572'],
        ['MaNV' => 'khotp01',      'HoTen' => 'Nhân viên Kho TP 01',     'ChucVu' => 'NVK',   'BoPhan' => 'Kho TP', 'SoDienThoai' => '0901234573'],
        ['MaNV' => 'congnhan01',   'HoTen' => 'Công nhân 01',            'ChucVu' => 'CN',    'BoPhan' => 'Xưởng',  'SoDienThoai' => '0901234574']
    ];

    $insert = $conn->prepare("INSERT INTO NhanVien (MaNV, HoTen, ChucVu, BoPhan, SoDienThoai, Password, TrangThai) VALUES (:MaNV, :HoTen, :ChucVu, :BoPhan, :SoDienThoai, :Password, 'Đang làm việc')");
    $update = $conn->prepare("UPDATE NhanVien SET HoTen=:HoTen, ChucVu=:ChucVu, BoPhan=:BoPhan, SoDienThoai=:SoDienThoai, Password=:Password WHERE MaNV=:MaNV");
    $exists = $conn->prepare("SELECT MaNV FROM NhanVien WHERE MaNV = :MaNV");

    $added = 0; $updated = 0;
    foreach ($rows as $r) {
        $exists->execute([':MaNV' => $r['MaNV']]);
        $r['Password'] = $hash;
        if ($exists->rowCount() === 0) {
            $insert->execute($r);
            $added++;
        } else {
            $update->execute($r);
            $updated++;
        }
    }

    echo '<p>Added: ' . $added . ', Updated: ' . $updated . '</p>';
    echo '<div style="background:#e8f5e8;padding:12px;border-radius:6px;margin-top:12px">'
        . 'You can now login. Example: <code>xuongtruong01</code> / <code>' . htmlspecialchars($pwd) . '</code>'
        . '</div>';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<div style="background:#ffe6e6;padding:12px;border-radius:6px">'
        . '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage())
        . '</div>';
}
