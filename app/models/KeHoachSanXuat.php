<?php
// Tệp: app/models/KeHoachSanXuat.php

// Kế thừa từ BaseModel để có các hàm CRUD chung
require_once APP_PATH . '/models/BaseModel.php';

class KeHoachSanXuat extends BaseModel {
    
    // Ghi đè các thuộc tính của BaseModel
    protected $tableName = 'kehoachsanxuat';
    protected $primaryKey = 'MaKeHoach';

    /**
     * HÀM BỊ THIẾU: Lấy Mã Kế hoạch cuối cùng
     * Được gọi bởi: KeHoachSanXuatController::loadCreateView()
     */
    public function getLastMaKeHoach() {
        try {
            // Sắp xếp theo MaKeHoach giảm dần (ví dụ: KH12, KH11, ...)
            // và chỉ lấy 1 dòng đầu tiên
            $sql = "SELECT {$this->primaryKey} 
                    FROM {$this->tableName} 
                    ORDER BY {$this->primaryKey} DESC 
                    LIMIT 1";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            // fetchColumn(0) chỉ lấy giá trị của cột đầu tiên
            $result = $stmt->fetchColumn(0); 
            
            return $result; // Sẽ trả về 'KH12' hoặc false nếu bảng rỗng
            
        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            return false; // Trả về false nếu có lỗi
        }
    }
    
    /**
     * HÀM MÀ HÀM EDIT/VIEW SẼ CẦN:
     * Lấy kế hoạch theo ID, JOIN với tên người lập
     * Được gọi bởi: KeHoachSanXuatController::loadEditView()
     */
    public function getByIdWithNguoiLap($maKeHoach) {
         try {
            $sql = "SELECT k.*, n.HoTen AS HoTenNguoiLap 
                    FROM {$this->tableName} k
                    LEFT JOIN nhanvien n ON k.MaNV = n.MaNV
                    WHERE k.{$this->primaryKey} = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$maKeHoach]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Các hàm CRUD chung (getAll, getById, create, update, delete)
    // đã được kế thừa từ BaseModel.php

    public function getPlannedDonHangIds() {
        try {
            // Chỉ chọn cột MaDonHang và loại bỏ các giá trị trùng lặp (DISTINCT)
            $sql = "SELECT DISTINCT MaDonHang FROM {$this->tableName}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            // fetchAll(PDO::FETCH_COLUMN, 0) lấy tất cả giá trị của cột đầu tiên (MaDonHang)
            // thành một mảng đơn giản, ví dụ: ['DH01', 'DH02']
            $ids = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

            return $ids ?? []; // Trả về mảng ID hoặc mảng rỗng

        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            return []; // Trả về mảng rỗng nếu có lỗi
        }
    }

}
?>

