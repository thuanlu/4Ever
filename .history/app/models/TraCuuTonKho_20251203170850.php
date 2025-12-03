<?php
/**
 * Model TraCuuTonKho - Tra cứu tồn kho nguyên liệu và thành phẩm
 */

require_once CONFIG_PATH . '/database.php';

class TraCuuTonKho {
    private $conn;
    private $database;

    /**
     * Constructor - Khởi tạo kết nối database
     * @param PDO|null $db Kết nối database từ BaseController (nếu có)
     */
    public function __construct($db = null) {
        if ($db !== null && $db instanceof PDO) {
            $this->conn = $db;
        } else {
            $this->database = new Database();
            $this->conn = $this->database->getConnection();
        }
    }

    /**
     * Lấy danh sách nguyên liệu trong kho với bộ lọc
     * 
     * @param array $filters Mảng các bộ lọc: maNL, tenNL, nhaCungCap
     * @return array Danh sách nguyên liệu
     */
    public function getDanhSachNguyenLieu($filters = []) {
        if ($this->conn === null) {
            error_log("TraCuuTonKho::getDanhSachNguyenLieu - Database connection is null!");
            return [];
        }

        $query = "SELECT 
                    nl.MaNguyenLieu,
                    nl.TenNguyenLieu,
                    nl.SoLuongTonKho,
                    nl.DonViTinh,
                    ncc.TenNhaCungCap,
                    nl.MaNhaCungCap,
                    pnnl.NgayNhap,
                    nl.NgayCapNhat
                  FROM nguyenlieu nl
                  INNER JOIN nhacungcap ncc ON nl.MaNhaCungCap = ncc.MaNhaCungCap
                  LEFT JOIN (
                      SELECT 
                          ctnl.MaNguyenLieu,
                          MAX(pnnl.NgayNhap) as NgayNhap
                      FROM chitietphieunhapnguyenlieu ctnl
                      INNER JOIN phieunhanguyenlieu pnnl ON ctnl.MaPhieuNhap = pnnl.MaPhieuNhap
                      GROUP BY ctnl.MaNguyenLieu
                  ) pnnl ON nl.MaNguyenLieu = pnnl.MaNguyenLieu
                  WHERE 1=1";

        $params = [];

        // Áp dụng bộ lọc
        if (!empty($filters['maNL'])) {
            $query .= " AND nl.MaNguyenLieu LIKE :maNL";
            $params[':maNL'] = '%' . $filters['maNL'] . '%';
        }

        if (!empty($filters['tenNL'])) {
            $query .= " AND nl.TenNguyenLieu LIKE :tenNL";
            $params[':tenNL'] = '%' . $filters['tenNL'] . '%';
        }

        if (!empty($filters['nhaCungCap'])) {
            $query .= " AND (ncc.TenNhaCungCap LIKE :nhaCungCap OR nl.MaNhaCungCap = :nhaCungCapMa)";
            $params[':nhaCungCap'] = '%' . $filters['nhaCungCap'] . '%';
            $params[':nhaCungCapMa'] = $filters['nhaCungCap'];
        }

        $query .= " ORDER BY nl.MaNguyenLieu ASC";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting danh sach nguyen lieu: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách thành phẩm trong kho với bộ lọc
     * 
     * @param array $filters Mảng các bộ lọc: loai, maLH, ngayNhap
     * @return array Danh sách thành phẩm
     */
    public function getDanhSachThanhPham($filters = []) {
        if ($this->conn === null) {
            error_log("TraCuuTonKho::getDanhSachThanhPham - Database connection is null!");
            return [];
        }

        $query = "SELECT 
                    tk.MaSanPham,
                    sp.TenSanPham,
                    sp.Size,
                    sp.Mau,
                    tk.SoLuongHienTai,
                    tk.ViTriKho,
                    tk.NgayCapNhat,
                    pnsp.MaLoHang,
                    pnsp.NgayNhap,
                    pnsp.MaPhieuNhap
                  FROM tonkho tk
                  INNER JOIN sanpham sp ON tk.MaSanPham = sp.MaSanPham
                  LEFT JOIN (
                      SELECT 
                          pnsp1.MaLoHang,
                          lh1.MaSanPham,
                          pnsp1.NgayNhap,
                          pnsp1.MaPhieuNhap
                      FROM phieunhapsanpham pnsp1
                      INNER JOIN lohang lh1 ON pnsp1.MaLoHang = lh1.MaLoHang
                      INNER JOIN (
                          SELECT 
                              lh2.MaSanPham,
                              MAX(pnsp2.NgayNhap) as MaxNgayNhap
                          FROM phieunhapsanpham pnsp2
                          INNER JOIN lohang lh2 ON pnsp2.MaLoHang = lh2.MaLoHang
                          GROUP BY lh2.MaSanPham
                      ) latest ON lh1.MaSanPham = latest.MaSanPham 
                              AND pnsp1.NgayNhap = latest.MaxNgayNhap
                  ) pnsp ON tk.MaSanPham = pnsp.MaSanPham
                  WHERE 1=1";

        $params = [];

        // Áp dụng bộ lọc
        if (!empty($filters['loai'])) {
            // Lọc theo Size hoặc Mau (có thể mở rộng thêm)
            $query .= " AND (sp.Size LIKE :loai OR sp.Mau LIKE :loai)";
            $params[':loai'] = '%' . $filters['loai'] . '%';
        }

        if (!empty($filters['maLH'])) {
            $query .= " AND pnsp.MaLoHang LIKE :maLH";
            $params[':maLH'] = '%' . $filters['maLH'] . '%';
        }

        if (!empty($filters['ngayNhap'])) {
            $query .= " AND DATE(pnsp.NgayNhap) = :ngayNhap";
            $params[':ngayNhap'] = $filters['ngayNhap'];
        }

        $query .= " ORDER BY tk.NgayCapNhat DESC, tk.MaSanPham ASC";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting danh sach thanh pham: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách nhà cung cấp để hiển thị trong dropdown filter
     * 
     * @return array Danh sách nhà cung cấp
     */
    public function getDanhSachNhaCungCap() {
        if ($this->conn === null) {
            return [];
        }

        $query = "SELECT DISTINCT ncc.MaNhaCungCap, ncc.TenNhaCungCap
                  FROM nhacungcap ncc
                  INNER JOIN nguyenlieu nl ON ncc.MaNhaCungCap = nl.MaNhaCungCap
                  ORDER BY ncc.TenNhaCungCap ASC";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting danh sach nha cung cap: " . $e->getMessage());
            return [];
        }
    }
}
?>