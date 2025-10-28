<?php
// Tệp: app/models/DinhMucNguyenLieu.php

require_once APP_PATH . '/models/BaseModel.php';

class DinhMucNguyenLieu extends BaseModel {

    protected $tableName = 'dinhmucnguyenlieu';
    protected $primaryKey = 'MaDinhMuc';

    /**
     * HÀM QUAN TRỌNG CHO AJAX: Lấy dữ liệu BOM cho một danh sách Sản phẩm
     *
     * ĐÃ CẬP NHẬT: Thêm nl.DonViTinh và nl.GiaNhap vào câu SELECT
     */
    public function getBomDataForProducts($product_ids = []) {
        if (empty($product_ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));

        try {
            // **THÊM nl.DonViTinh, nl.GiaNhap VÀO SELECT**
            $sql = "
                SELECT
                    dmnl.MaSanPham,
                    dmnl.MaNguyenLieu,
                    dmnl.DinhMucSuDung,
                    nl.TenNguyenLieu,
                    nl.SoLuongTonKho,
                    nl.DonViTinh,  -- << Thêm dòng này
                    nl.GiaNhap     -- << Thêm dòng này
                FROM {$this->tableName} dmnl
                JOIN nguyenlieu nl ON dmnl.MaNguyenLieu = nl.MaNguyenLieu
                WHERE dmnl.MaSanPham IN ($placeholders)
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($product_ids);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Sắp xếp lại dữ liệu theo MaSanPham
            $bom_data = [];
            foreach ($results as $row) {
                $bom_data[$row['MaSanPham']][] = $row;
            }

            return $bom_data;

        } catch (PDOException $e) {
            error_log(__METHOD__ . "::Error: " . $e->getMessage());
            return [];
        }
    }
}
?>