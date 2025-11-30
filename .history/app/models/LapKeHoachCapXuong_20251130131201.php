<?php
require_once APP_PATH . '/models/BaseModel.php';

class LapKeHoachCapXuong extends BaseModel {
    protected $tableName = 'kehoachcapxuong';
    protected $primaryKey = 'MaKHCapXuong';

    /**
     * Lấy tất cả kế hoạch cấp xưởng
     */
    public function getAll() {
        try {
            $sql = "SELECT * FROM {$this->tableName} ORDER BY NgayLap DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log(__METHOD__ . '::Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy kế hoạch cấp xưởng theo mã
     */
    public function getById($id) {
        try {
            $sql = "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log(__METHOD__ . '::Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Thêm mới kế hoạch cấp xưởng
     */
    public function create($data) {
        try {
            // Kiểm tra trùng mã KCX, nếu trùng thì tự động tăng số phía sau
            $maKHCapXuong = $data['ma_kh_cap_xuong'];
            $originalMaKHCapXuong = $maKHCapXuong;
            $suffix = 1;
            while ($this->getById($maKHCapXuong)) {
                // Nếu đã có mã này, tăng số phía sau
                $maKHCapXuong = $originalMaKHCapXuong . '-' . $suffix;
                $suffix++;
            }
            $sql = "INSERT INTO {$this->tableName} (MaKHCapXuong, MaKeHoach, MaPhanXuong, NgayLap, SoLuong, CongSuatDuKien, TrangThai) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $maKHCapXuong,
                $data['ma_kehoach'],
                $data['ma_phan_xuong'],
                $data['ngay_lap'],
                $data['so_luong'],
                $data['cong_suat_du_kien'] ?? 0,
                $data['trang_thai'] ?? 'Chưa thực hiện'
            ]);
            return $maKHCapXuong;
        } catch (PDOException $e) {
            error_log(__METHOD__ . '::Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật kế hoạch cấp xưởng
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE {$this->tableName} SET MaKeHoach = ?, MaPhanXuong = ?, NgayLap = ?, SoLuong = ?, CongSuatDuKien = ?, TrangThai = ? WHERE {$this->primaryKey} = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['ma_kehoach'],
                $data['ma_phan_xuong'],
                $data['ngay_lap'],
                $data['so_luong'],
                $data['cong_suat_du_kien'] ?? 0,
                $data['trang_thai'] ?? 'Chưa thực hiện',
                $id
            ]);
            return true;
        } catch (PDOException $e) {
            error_log(__METHOD__ . '::Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa kế hoạch cấp xưởng
     */
    public function delete($id) {
        try {
            $sql = "DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            error_log(__METHOD__ . '::Error: ' . $e->getMessage());
            return false;
        }
    }
}
