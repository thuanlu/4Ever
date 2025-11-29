<?php
require_once CONFIG_PATH . '/database.php';

class XuatNguyenLieu {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    // 1. Lấy danh sách phiếu yêu cầu xuất NL đang chờ duyệt
    public function getPendingRequests() {
        $sql = "
            SELECT pyc.*, nv.HoTen AS NguoiYeuCau, px.TenPhanXuong
            FROM PhieuYeuCauXuatNguyenLieu pyc
            JOIN NhanVien nv ON pyc.MaNV = nv.MaNV
            JOIN PhanXuong px ON pyc.MaPhanXuong = px.MaPhanXuong
            WHERE pyc.TrangThai = 'Chờ duyệt'
        ";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Lấy 1 phiêú yêu cầu theo mã
    public function getRequestById($ma) {
        $sql = "
            SELECT pyc.*, nv.HoTen AS NguoiYeuCau, px.TenPhanXuong
            FROM PhieuYeuCauXuatNguyenLieu pyc
            JOIN NhanVien nv ON pyc.MaNV = nv.MaNV
            JOIN PhanXuong px ON pyc.MaPhanXuong = px.MaPhanXuong
            WHERE pyc.MaPhieuYC = ?
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$ma]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // 3. Lấy chi tiết YC + Tồn kho
    public function getRequestItems($ma) {
        $sql = "
            SELECT ct.*, nl.TenNguyenLieu, nl.DonViTinh, nl.SoLuongTonKho, nl.MaNguyenLieu
            FROM ChiTietPhieuYeuCauXuatNguyenLieu ct
            JOIN NguyenLieu nl ON ct.MaNguyenLieu = nl.MaNguyenLieu
            WHERE ct.MaPhieuYC = ?
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$ma]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Tạo phiếu xuất
    public function createExport($data, $items) {
        try {
            $this->conn->beginTransaction();

            // 1) Tạo phiếu xuất
            $sqlPX = "
                INSERT INTO PhieuXuatNguyenLieu (MaPX, MaPhieuYC, NgayLap, MaNV, GhiChu)
                VALUES (?, ?, ?, ?, ?)
            ";
            $stmt = $this->conn->prepare($sqlPX);
            $stmt->execute([
                $data['MaPX'],
                $data['MaPhieuYC'],
                $data['NgayLap'],
                $data['MaNV'],
                $data['GhiChu']
            ]);

            $ghiChu = [];

            // 2) Xử lý tồn kho + thiếu
            foreach ($items as $row) {
                $maNL   = $row['MaNguyenLieu'];
                // $yc     = $row['SoLuongYeuCau'];
                // $tonKho = $row['SoLuongTonKho'];

                // $slXuat  = min($yc, $tonKho);
                // $slThieu = max($yc - $tonKho, 0);

                $yc       = $row['SoLuongYeuCau'];
                $tonKho   = $row['TonKho'];
                $thucXuat = $row['ThucXuat'];

                if ($thucXuat > $tonKho) {
                    throw new Exception("Số lượng thực xuất vượt quá tồn kho của $maNL");
                }

                $slXuat  = $thucXuat;
                $slThieu = max($yc - $thucXuat, 0);


                if ($slXuat > 0) {
                    $stmt2 = $this->conn->prepare("
                        UPDATE NguyenLieu 
                        SET SoLuongTonKho = SoLuongTonKho - ? 
                        WHERE MaNguyenLieu = ?
                    ");
                    $stmt2->execute([$slXuat, $maNL]);
                }

                if ($slThieu > 0) {
                    $ghiChu[] = "{$maNL} thiếu {$slThieu}";
                }
            }

            // 3) Cập nhật trạng thái phiếu yêu cầu
            $status = empty($ghiChu) ? "Đã duyệt" : "Chờ bổ sung";

            $stmt = $this->conn->prepare("
                UPDATE PhieuYeuCauXuatNguyenLieu
                SET TrangThai = ?
                WHERE MaPhieuYC = ?
            ");
            $stmt->execute([$status, $data['MaPhieuYC']]);

            // 4) Ghi chú vào phiếu xuất
            if (!empty($ghiChu)) {
                $fullNote = $data['GhiChu'] . " | Thiếu: " . implode("; ", $ghiChu);
                $stmt = $this->conn->prepare("
                    UPDATE PhieuXuatNguyenLieu
                    SET GhiChu = ?
                    WHERE MaPX = ?
                ");
                $stmt->execute([$fullNote, $data['MaPX']]);
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            echo "Lỗi SQL: " . $e->getMessage();
            return false;
        }
    }
    // 5. Hiển thị popup cho số lượng NL tồn kho< số lượng yêu cầu:
    public function getCriticalAlerts($maPhieuYC) {
         $sql = "
            SELECT ct.MaNguyenLieu AS Ma,
                   nl.TenNguyenLieu AS Ten,
                   nl.SoLuongTonKho AS SoLuongHoacHan,
                   ct.SoLuong AS SoLuong,
                   'Thiếu' AS TrangThai
            FROM ChiTietPhieuYeuCauXuatNguyenLieu ct
            JOIN NguyenLieu nl ON ct.MaNguyenLieu = nl.MaNguyenLieu
            WHERE ct.MaPhieuYC = ? AND ct.SoLuong > nl.SoLuongTonKho
            ORDER BY nl.SoLuongTonKho ASC
        ";
        // return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$maPhieuYC]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastMaPX() {
        $sql = "SELECT MaPX FROM PhieuXuatNguyenLieu WHERE MaPX LIKE 'PXNL%' ORDER BY MaPX DESC LIMIT 1";
        return $this->conn->query($sql)->fetchColumn();
    }
}
?>
