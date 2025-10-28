-- =========================================================
-- Migration: Nhập Kho Thành Phẩm Module
-- Chức năng: Thêm các bảng và cột cần thiết cho module nhập kho thành phẩm
-- =========================================================

USE qlsx_4ever;

-- =========================================================
-- 1. Cập nhật bảng LoHang: Thêm cột TrangThaiKho
-- =========================================================
ALTER TABLE LoHang 
ADD COLUMN TrangThaiKho VARCHAR(20) NOT NULL DEFAULT 'Chưa nhập kho' 
AFTER TrangThaiQC 
COMMENT 'Trạng thái nhập kho (Chưa nhập kho, Đã nhập kho)';

-- Cập nhật các lô hàng hiện có
UPDATE LoHang SET TrangThaiKho = 'Chưa nhập kho' WHERE TrangThaiKho IS NULL;

-- =========================================================
-- 2. Tạo bảng TonKho (Tồn kho thành phẩm)
-- =========================================================
CREATE TABLE IF NOT EXISTS TonKho (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID tự tăng',
    MaSanPham VARCHAR(10) NOT NULL COMMENT 'Mã sản phẩm (FK)',
    SoLuongHienTai INT NOT NULL DEFAULT 0 COMMENT 'Số lượng hiện tại trong kho',
    ViTriKho VARCHAR(50) DEFAULT 'Kho A' COMMENT 'Vị trí lưu trữ trong kho',
    NgayCapNhat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật cuối',
    GhiChu TEXT COMMENT 'Ghi chú bổ sung',
    FOREIGN KEY (MaSanPham) REFERENCES SanPham(MaSanPham),
    UNIQUE KEY unique_ma_san_pham (MaSanPham)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Bảng quản lý tồn kho thành phẩm';

-- Tạo index cho hiệu suất
CREATE INDEX idx_tonkho_sanpham ON TonKho(MaSanPham);
CREATE INDEX idx_tonkho_ngaycapnhat ON TonKho(NgayCapNhat);

-- =========================================================
-- 3. Cập nhật bảng PhieuNhapSanPham (nếu cần)
-- Thêm cột GhiChu nếu chưa có
-- =========================================================
ALTER TABLE PhieuNhapSanPham 
ADD COLUMN GhiChu TEXT 
AFTER MaLoHang 
COMMENT 'Ghi chú khi nhập kho';

-- =========================================================
-- 4. Insert dữ liệu mẫu (nếu cần test)
-- Chỉ chạy khi cần dữ liệu test
-- =========================================================
/*
-- Dữ liệu mẫu cho LoHang (đã được QC duyệt - để test module nhập kho)
INSERT INTO LoHang (MaLoHang, MaSanPham, SoLuong, TrangThaiQC, TrangThaiKho) VALUES
('LH001', 'SP001', 100, 'Đạt', 'Chưa nhập kho'),
('LH002', 'SP002', 150, 'Đạt', 'Chưa nhập kho'),
('LH003', 'SP003', 80, 'Đạt', 'Chưa nhập kho')
ON DUPLICATE KEY UPDATE TrangThaiKho = 'Chưa nhập kho';
*/

-- =========================================================
-- 5. Tạo View để dễ dàng query (tùy chọn)
-- =========================================================
CREATE OR REPLACE VIEW vw_LoHangCanNhapKho AS
SELECT 
    lh.MaLoHang,
    lh.MaSanPham,
    sp.TenSanPham,
    sp.Size,
    sp.Mau,
    lh.SoLuong,
    lh.TrangThaiQC,
    lh.TrangThaiKho,
    tk.SoLuongHienTai AS SoLuongTonKho
FROM LoHang lh
INNER JOIN SanPham sp ON lh.MaSanPham = sp.MaSanPham
LEFT JOIN TonKho tk ON lh.MaSanPham = tk.MaSanPham
WHERE lh.TrangThaiQC = 'Đạt'
ORDER BY lh.MaLoHang DESC;

-- =========================================================
-- Hoàn tất Migration
-- =========================================================
SELECT 'Migration completed successfully!' AS Status;

