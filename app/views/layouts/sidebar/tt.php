<?php
// views/layouts/sidebar/tt.php
// Nhận biến đánh dấu active từ view/layout
$active = $menuActive ?? ($active ?? '');
?>

<nav class="nav flex-column px-3 py-2">
  <a class="nav-link <?= $active==='dashboard' ? 'active' : '' ?>"
     href="<?= BASE_URL ?>totruong/dashboard">
    <i class="fa-solid fa-gauge-high me-2"></i>Dashboard
  </a>

  <a class="nav-link <?= $active==='phanca' ? 'active' : '' ?>"
     href="<?= BASE_URL ?>totruong/phancalamviec">
    <i class="fa-solid fa-people-group me-2"></i>Phân công & Lập ca
  </a>

  <a class="nav-link <?= $active==='chamcong' ? 'active' : '' ?>"
     href="<?= BASE_URL ?>attendance/by-product">
    <i class="fa-solid fa-clipboard-list me-2"></i>Chấm công theo SP
  </a>

  <a class="nav-link <?= $active==='tiendo' ? 'active' : '' ?>"
     href="<?= BASE_URL ?>team/progress">
    <i class="fa-solid fa-chart-line me-2"></i>Tiến độ tổ
  </a>

  <a class="nav-link <?= $active==='baocao' ? 'active' : '' ?>"
     href="<?= BASE_URL ?>team/report">
    <i class="fa-solid fa-file-lines me-2"></i>Báo cáo tổ SX
  </a>

  <hr class="my-3 border-light opacity-25">

  <a class="nav-link" href="<?= BASE_URL ?>logout">
    <i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất
  </a>
</nav>

