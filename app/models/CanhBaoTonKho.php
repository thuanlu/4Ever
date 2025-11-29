<?php
require_once CONFIG_PATH . '/database.php';

class CanhBaoTonKho {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    //1. Lấy Nguyên Liệu Sắp Hết (Mặc định < 100)
    // public function getLowStockMaterials() {
    //     // Gán cứng mức tối thiểu là 100
    //     $limit = 100; 

    //     $sql = "
    //         SELECT 
    //             MaNguyenLieu,
    //             TenNguyenLieu,
    //             SoLuongTonKho,
    //             DonViTinh,
    //             $limit as MucMin  -- Giả lập cột MucMin để View không bị lỗi
    //         FROM nguyenlieu
    //         WHERE SoLuongTonKho <= $limit
    //         ORDER BY SoLuongTonKho ASC
    //     ";
    //     return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    // }

    // 2. Lấy SẢN PHẨM Sắp Hết (Từ bảng tonkho và sanpham)
    // public function getLowStockMaterials() {
    //     // Gán cứng mức tối thiểu là 100
    //     $limit = 100; 
    //     $sql = "
    //         SELECT 
    //             t.MaSanPham AS MaNguyenLieu,    
    //             s.TenSanPham AS TenNguyenLieu,
    //             t.SoLuongHienTai AS SoLuongTonKho,
    //             'Đôi' AS DonViTinh,             -- Gán cứng đơn vị tính là 'Đôi' vì bảng SP không có cột này
    //             $limit as MucMin
    //         FROM tonkho t
    //         JOIN sanpham s ON t.MaSanPham = s.MaSanPham
    //         WHERE t.SoLuongHienTai <= $limit
    //         ORDER BY t.SoLuongHienTai ASC
    //     ";
    //     return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    // }

    //3. Lấy Nguyên Liệu Sắp Hết Hạn hoặc Đã Hết Hạn
    // public function getExpiringMaterials($daysWarning = 30) {
    //     $sql = "
    //         SELECT 
    //             MaNguyenLieu,
    //             TenNguyenLieu,
    //             HanSuDung,
    //             SoLuongTonKho,
    //             DATEDIFF(HanSuDung, CURDATE()) AS SoNgayConLai
    //         FROM nguyenlieu
    //         WHERE SoLuongTonKho > 0 
    //         AND (
    //             HanSuDung < CURDATE() 
    //             OR DATEDIFF(HanSuDung, CURDATE()) <= ?
    //         )
    //         ORDER BY HanSuDung ASC
    //     ";
        
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->execute([$daysWarning]);
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

    // Đếm số lượng hiển thị lên thẻ Dashboard
    // public function getCounts() {
    //     $lowStock = count($this->getLowStockMaterials());
    //     $expiring = count($this->getExpiringMaterials());
    //     return ['low_stock' => $lowStock, 'expiring' => $expiring];
    //}

    
    //1. Lấy danh sách sản phẩm TỒN KHO (Lấy tất cả để hiển thị cả Xanh/Cam/Đỏ)
    public function getLowStockMaterials() {
        $limit = 100; 
        $sql = "
            SELECT 
                t.MaSanPham AS MaNguyenLieu,    
                s.TenSanPham AS TenNguyenLieu,
                t.SoLuongHienTai AS SoLuongTonKho,
                'Đôi' AS DonViTinh,
                $limit as MucMin
            FROM tonkho t
            JOIN sanpham s ON t.MaSanPham = s.MaSanPham
            ORDER BY t.SoLuongHienTai ASC
        ";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    //2. Lấy danh sách Nguyên liệu HẠN SỬ DỤNG (Lấy tất cả)
    public function getExpiringMaterials() {
        $sql = "
            SELECT 
                MaNguyenLieu,
                TenNguyenLieu,
                HanSuDung,
                SoLuongTonKho,
                DATEDIFF(HanSuDung, CURDATE()) AS SoNgayConLai
            FROM nguyenlieu
            WHERE SoLuongTonKho > 0 
            ORDER BY HanSuDung ASC
        ";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Đếm số lượng CẢNH BÁO (Chỉ đếm cái xấu để hiện lên Dashboard)
    public function getCounts() {
        $limit = 100;
        $daysWarning = 30;

        // Đếm tồn kho thấp
        $sqlStock = "SELECT COUNT(*) FROM tonkho WHERE SoLuongHienTai <= $limit";
        $lowStock = $this->conn->query($sqlStock)->fetchColumn();

        // Đếm hết hạn hoặc sắp hết hạn
        $sqlExpiry = "SELECT COUNT(*) FROM nguyenlieu 
                      WHERE SoLuongTonKho > 0 
                      AND (HanSuDung < CURDATE() OR DATEDIFF(HanSuDung, CURDATE()) <= $daysWarning)";
        $expiring = $this->conn->query($sqlExpiry)->fetchColumn();

        return ['low_stock' => $lowStock, 'expiring' => $expiring];
    }

    // 4. Lấy danh sách cảnh báo nguy cấp (hết hàng hoặc hết hạn)
    // public function getCriticalAlerts() {
    //     $sql = "
    //         SELECT 
    //             MaNguyenLieu,
    //             TenNguyenLieu,
    //             SoLuongTonKho,
    //             HanSuDung,
    //             DATEDIFF(HanSuDung, CURDATE()) AS SoNgayConLai,
    //             CASE 
    //                 WHEN SoLuongTonKho <= 0 THEN 'HẾT HÀNG'
    //                 WHEN HanSuDung < CURDATE() THEN 'HẾT HẠN'
    //             END AS TrangThaiNguyCap
    //         FROM nguyenlieu
    //         WHERE 
    //             SoLuongTonKho <= 0
    //             OR HanSuDung < CURDATE()
    //     ";

    //     return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    // }

    //4. Lấy danh sách cảnh báo nguy cấp (hết hàng hoặc hết hạn) để hiện popup
    public function getCriticalAlerts() {
        // Mức báo động hết hàng (Ví dụ <= 100 là báo động đỏ)
        $limitStock = 100; 

        $sql = "
            /* PHẦN 1: LẤY SẢN PHẨM HẾT HÀNG (Từ bảng tonkho + sanpham) */
            SELECT 
                t.MaSanPham AS Ma,
                s.TenSanPham AS Ten,
                CONCAT(format(t.SoLuongHienTai, 0), ' Đôi') AS SoLuongHoacHan,
                'HẾT HÀNG' AS TrangThai,
                'text-danger' AS MauSac
            FROM tonkho t
            JOIN sanpham s ON t.MaSanPham = s.MaSanPham
            WHERE t.SoLuongHienTai < $limitStock

            UNION ALL

            /* PHẦN 2: LẤY NGUYÊN LIỆU HẾT HẠN (Từ bảng nguyenlieu) */
            SELECT 
                MaNguyenLieu AS Ma,
                TenNguyenLieu AS Ten,
                DATE_FORMAT(HanSuDung, '%d/%m/%Y') AS SoLuongHoacHan,
                'HẾT HẠN' AS TrangThai,
                'text-danger' AS MauSac
            FROM nguyenlieu
            WHERE HanSuDung < CURDATE()
        ";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}