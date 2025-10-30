<?php
// public/api_phanca.php
require_once __DIR__ . '/../config/database.php';

 // chỉnh nếu đường dẫn khác

header('Content-Type: application/json; charset=utf-8');

$pdo = getPDO(); // hàm trong database.php trả về PDO

function j($ok, $data = []) {
  echo json_encode($ok ? array_merge(['ok' => true], $data)
                       : array_merge(['ok' => false], $data));
  exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$raw    = file_get_contents('php://input');
$input  = $raw ? json_decode($raw, true) : [];

try {
  switch ($action) {

    // 1) Đổ danh sách mã phiếu
    case 'tickets': {
      $stmt = $pdo->query("SELECT MaKHCapXuong AS id FROM KeHoachCapXuong ORDER BY MaKHCapXuong");
      j(true, ['items' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }

    // 2) Lấy thông tin 1 phiếu
    case 'get_ticket': {
      $ma = isset($_GET['ticketId']) ? $_GET['ticketId'] : '';
      if (!$ma) j(false, ['message' => 'Thiếu mã phiếu']);

      $sql = "
        SELECT 
          kcx.MaKHCapXuong,
          sp.TenSanPham,
          sp.Size,
          sp.Mau,
          kcx.SoLuong                     AS total_qty,
          ksx.NgayBatDau                  AS start_date,
          ksx.NgayKetThuc                 AS end_date,
          kcx.MaPhanXuong                 AS workshop
        FROM KeHoachCapXuong kcx
        JOIN KeHoachSanXuat ksx ON ksx.MaKeHoach = kcx.MaKeHoach
        JOIN ChiTietKeHoach ctk ON ctk.MaKeHoach = kcx.MaKeHoach
                               AND ctk.MaPhanXuong = kcx.MaPhanXuong
        JOIN SanPham sp         ON sp.MaSanPham = ctk.MaSanPham
        WHERE kcx.MaKHCapXuong = :ma
        LIMIT 1";
      $st = $pdo->prepare($sql);
      $st->execute([':ma' => $ma]);
      $row = $st->fetch(PDO::FETCH_ASSOC);
      if (!$row) j(false, ['message' => 'Không tìm thấy phiếu']);

      j(true, ['ticket' => [
        'ticket_id' => $row['MaKHCapXuong'],
        'work_name' => trim($row['TenSanPham']),
        'size'      => $row['Size'],
        'color'     => $row['Mau'],
        'total_qty' => (int)$row['total_qty'],
        'start_date'=> $row['start_date'],
        'end_date'  => $row['end_date'],
        'workshop'  => $row['workshop']
      ]]);
    }

    // 3) 20 công nhân theo ca
    case 'get_workers': {
      $shift = isset($_GET['shift']) ? $_GET['shift'] : 'morning';
      $px    = isset($_GET['px']) ? $_GET['px'] : '';

      $sql = "SELECT nv.MaNV AS id, nv.HoTen AS name
              FROM NhanVien nv
              JOIN NhanVien_CaLam nvc ON nvc.MaNV = nv.MaNV
              WHERE nvc.MaCa = :ca
              ORDER BY nv.MaNV
              LIMIT 20";
      $st = $pdo->prepare($sql);
      $st->execute([':ca' => $shift]);
      $rows = $st->fetchAll(PDO::FETCH_ASSOC);

      if (!$rows) {
        $st = $pdo->query("SELECT MaNV AS id, HoTen AS name FROM NhanVien ORDER BY MaNV LIMIT 20");
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
      }
      j(true, ['workers' => $rows]);
    }

    // 4) Lưu phân công
    case 'save': {
      $ticketId   = isset($input['ticketId'])   ? $input['ticketId']   : '';
      $shiftType  = isset($input['shiftType'])  ? $input['shiftType']  : '';
      $startDate  = isset($input['startDate'])  ? $input['startDate']  : '';
      $endDate    = isset($input['endDate'])    ? $input['endDate']    : '';
      $percentage = isset($input['percentage']) ? (int)$input['percentage'] : 0;
      $quantity   = isset($input['quantity'])   ? (int)$input['quantity']   : 0;
      $workerIds  = isset($input['workerIds'])  ? $input['workerIds']       : [];

      if (!$ticketId || !$shiftType || !$startDate || !$endDate) {
        j(false, ['message'=>'Thiếu dữ liệu bắt buộc']);
      }

      $ins = $pdo->prepare("
        INSERT INTO PhanCongCaLam(MaKHCapXuong, MaCa, NgayBatDau, NgayKetThuc, TyLe, SoLuong, CreatedBy)
        VALUES(:p,:ca,:bd,:kt,:tyle,:sl,:by)");
      $ins->execute([
        ':p'=>$ticketId, ':ca'=>$shiftType, ':bd'=>$startDate, ':kt'=>$endDate,
        ':tyle'=>$percentage, ':sl'=>$quantity, ':by'=>(isset($_SESSION['user_id'])?$_SESSION['user_id']:null)
      ]);
      $phanCongId = $pdo->lastInsertId();

      if ($workerIds) {
        $ins2 = $pdo->prepare("INSERT IGNORE INTO PhanCongCaLam_NhanVien(PhanCongId, MaNV) VALUES(:id,:nv)");
        foreach ($workerIds as $nv) {
          $ins2->execute([':id'=>$phanCongId, ':nv'=>$nv]);
        }
      }
      j(true, ['id'=>$phanCongId]);
    }

    // 5) Danh sách đã phân công
    case 'list': {
    $sql = "
        SELECT
            p.Id AS id,                           -- <== QUAN TRỌNG
            p.MaKHCapXuong AS ticket_id,
            p.MaCa           AS shift_type,
            p.NgayBatDau     AS start_date,
            p.NgayKetThuc    AS end_date,
            p.TyLe           AS percentage,
            p.SoLuong        AS quantity,
            COALESCE(GROUP_CONCAT(nv.HoTen ORDER BY nv.HoTen SEPARATOR ', '), '') AS workers
        FROM PhanCongCaLam p
        LEFT JOIN PhanCongCaLam_NhanVien pc ON pc.PhanCongId = p.Id
        LEFT JOIN NhanVien nv ON nv.MaNV = pc.MaNV
        GROUP BY p.Id
        ORDER BY p.Id DESC
    ";
    $items = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    j(true, ['items' => $items]);
}


    // 6) Xóa 1 phân công
   case 'remove': {
    $id = (int)($input['id'] ?? 0);
    if (!$id) j(false, ['message' => 'Thiếu id phân công cần xóa']);

    try {
        $pdo->beginTransaction();

        // Xóa con trước (phòng khi FK chưa set ON DELETE CASCADE)
        $stmt = $pdo->prepare('DELETE FROM PhanCongCaLam_NhanVien WHERE PhanCongId = ?');
        $stmt->execute([$id]);

        // Xóa cha
        $stmt = $pdo->prepare('DELETE FROM PhanCongCaLam WHERE Id = ?');
        $stmt->execute([$id]);

        $pdo->commit();
        j(true, ['deleted' => $id]);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        j(false, ['message' => 'Lỗi xóa: ' . $e->getMessage()]);
    }
}


    default:
      j(false, ['message'=>'Unknown action']);
  }
} catch (Exception $e) {
  j(false, ['message' => $e->getMessage()]);
}
