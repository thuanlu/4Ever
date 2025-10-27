<?php
// Quick login test via PHP only
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../app/models/User.php';

$u = new User();
$identifier = $_GET['u'] ?? 'xuongtruong01';
$pwd = $_GET['p'] ?? '123456';

$result = $u->authenticate($identifier, $pwd);
header('Content-Type: text/plain; charset=utf-8');
if ($result) {
    echo "OK: Logged in as {$result['MaNV']} ({$result['HoTen']}) role={$result['ChucVu']}\n";
} else {
    echo "FAIL: invalid credentials for $identifier\n";
}
