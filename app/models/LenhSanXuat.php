<?php
// Phải require BaseModel
require_once APP_PATH . '/models/BaseModel.php'; 

// Phải kế thừa BaseModel
class LenhSanXuat extends BaseModel {
    
    // Tên bảng phải khớp với CSDL (viết thường)
    protected $tableName = 'lenhsanxuat'; 
    protected $primaryKey = 'MaLenhSX';

    // Hàm __construct() đã có trong BaseModel,
    // nên không cần viết lại.

    /**
     * Tạo một lệnh sản xuất mới
     * @param array $data Dữ liệu từ controller
     * (Phải khớp với các key trong $lenhData của controller)
     */
    public function create($data) {
        try {
            // Câu SQL phải khớp với tên cột trong CSDL
            $sql = "INSERT INTO {$this->tableName} 
                        (ma_ke_hoach_tong, ngay_lap_lenh, ma_day_chuyen, ma_to_truong, san_luong_muc_tieu, trang_thai) 
                    VALUES 
                        (:ma_ke_hoach_tong, :ngay_lap_lenh, :ma_day_chuyen, :ma_to_truong, :san_luong_muc_tieu, :trang_thai)";
            
            $stmt = $this->db->prepare($sql);
            
            // Bind param phải khớp với $data từ controller
            $stmt->bindParam(':ma_ke_hoach_tong', $data['ma_ke_hoach_tong']);
            $stmt->bindParam(':ngay_lap_lenh', $data['ngay_lap_lenh']);
            $stmt->bindParam(':ma_day_chuyen', $data['ma_day_chuyen']);
            $stmt->bindParam(':ma_to_truong', $data['ma_to_truong']);
            $stmt->bindParam(':san_luong_muc_tieu', $data['san_luong_muc_tieu']);
            $stmt->bindParam(':trang_thai', $data['trang_thai']);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            // Ghi log lỗi để dễ debug
            error_log(__METHOD__ . '::Error: ' . $e->getMessage());
            // Ném lỗi ra để Controller có thể bắt (catch)
            throw new Exception("Lỗi khi tạo lệnh sản xuất: " . $e->getMessage());
        }
    }
    
    // (Bạn có thể thêm các hàm khác như getById, update, delete nếu cần)
}
?>