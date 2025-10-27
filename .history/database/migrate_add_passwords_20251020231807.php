<?php
/**
 * Migration script: add Password column to NhanVien if missing
 * and set hashed default passwords for demo accounts.
 * Run once in browser: http://localhost/4Ever/database/migrate_add_passwords.php
 */
require_once '../config/config.php';
require_once '../config/database.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    echo '<h2>Migration: Add Password column + seed hashes</h2>';

    // 1) Add column Password if not exists
    $checkCol = $conn->prepare("SHOW COLUMNS FROM NhanVien LIKE 'Password'");
    $checkCol->execute();
    if ($checkCol->rowCount() === 0) {
        $conn->exec("ALTER TABLE NhanVien ADD COLUMN Password VARCHAR(255) NULL AFTER SoDienThoai");
        echo '<p>✓ Added column Password to NhanVien</p>';
    } else {
        echo '<p>• Column Password already exists</p>';
    }

    // 2) Seed/update demo accounts with hashed '123456'
    $password = '123456';
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $accounts = [
        // MaNV list should match your seed/demo users
        'admin', 'kehoach01', 'xuongtruong01', 'totruong01', 'qc01', 'khonl01', 'khotp01', 'congnhan01', 'congnhan02'
    ];

    $stmt = $conn->prepare("UPDATE NhanVien SET Password = :pwd WHERE MaNV = :id");
    $ok = 0; $miss = 0;
    foreach ($accounts as $id) {
        $stmt->execute([':pwd' => $hash, ':id' => $id]);
        if ($stmt->rowCount() > 0) $ok++; else $miss++;
    }

    echo '<p>✓ Updated passwords (hashed) for existing accounts: ' . $ok . '</p>';
    if ($miss > 0) echo '<p>• Accounts not found (skipped): ' . $miss . '</p>';

    echo '<div style="background:#e8f5e8;padding:12px;border-radius:6px;margin-top:12px">'
        . '<strong>Done.</strong> Try login with MaNV such as <code>xuongtruong01</code> / <code>123456</code>.'</n>
        . '</div>';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<div style="background:#ffe6e6;padding:12px;border-radius:6px">'
        . '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage())
        . '</div>';
}
