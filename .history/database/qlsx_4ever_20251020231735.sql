-- =========================================================
-- DATABASE: qlsx_4ever (version v3)
-- Dựa trên BaoCaoGK_BangCSDL.pdf (Nhóm 4EVER)
-- Dành cho XAMPP (MySQL/MariaDB) - Charset UTF8MB4, Engine InnoDB
-- =========================================================

CREATE DATABASE IF NOT EXISTS qlsx_4ever CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qlsx_4ever;

-- DROP trước khi tạo (thuận tiện chạy lại nhiều lần)
SET FOREIGN_KEY_CHECKS = 0;
-- (Drop tables in reverse dependency order)
DROP TABLE IF EXISTS PhieuNhapSanPham;
DROP TABLE IF EXISTS KetQuaKiemDinh;
DROP TABLE IF EXISTS PhieuYeuCauKiemTraLoSP;
DROP TABLE IF EXISTS PhieuYeuCauXuatNguyenLieu;
DROP TABLE IF EXISTS PhieuXuatNguyenLieu;
DROP TABLE IF EXISTS ChiTietPhieuYeuCauXuatNguyenLieu;
DROP TABLE IF EXISTS ChiTietPhieuNhapNguyenLieu;
DROP TABLE IF EXISTS PhieuNhaNguyenLieu;
DROP TABLE IF EXISTS PhieuNhapNguyenLieu;
DROP TABLE IF EXISTS ChiTietKeHoach;
DROP TABLE IF EXISTS KeHoachCapXuong;
DROP TABLE IF EXISTS KeHoachSanXuat;
DROP TABLE IF EXISTS ChiTietDonHang;
DROP TABLE IF EXISTS DonHang;
DROP TABLE IF EXISTS ChiTietDonHang;
DROP TABLE IF EXISTS ChiTietKeHoach;
DROP TABLE IF EXISTS ChiTietPhieuYeuCauXuatNguyenLieu;
DROP TABLE IF EXISTS ChamCongSanPham;
DROP TABLE IF EXISTS CaLamViec;
DROP TABLE IF EXISTS LoHang;
DROP TABLE IF EXISTS PhanXuong;
DROP TABLE IF EXISTS DayChuyen;
DROP TABLE IF EXISTS CongDoanSX;
DROP TABLE IF EXISTS SanPham;
DROP TABLE IF EXISTS NguyenLieu;
DROP TABLE IF EXISTS NhaCungCap;
DROP TABLE IF EXISTS NhanVien;
SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
-- Bảng: NhaCungCap
-- =========================================================
CREATE TABLE NhaCungCap (
    MaNhaCungCap VARCHAR(10) PRIMARY KEY COMMENT 'PK nhà cung cấp',
    TenNhaCungCap VARCHAR(100) NOT NULL COMMENT 'Tên nhà cung cấp',
    DiaChi VARCHAR(200) NOT NULL,
    SoDienThoai VARCHAR(20) NOT NULL,
    Email VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Danh sách nhà cung cấp';

-- =========================================================
-- Bảng: NhanVien
-- =========================================================
CREATE TABLE NhanVien (
    MaNV VARCHAR(10) PRIMARY KEY COMMENT 'PK nhân viên (NDBGD25...)',
    HoTen VARCHAR(100) NOT NULL,
    ChucVu VARCHAR(50) NOT NULL,
    BoPhan VARCHAR(50),
    SoDienThoai VARCHAR(15),
    -- Thêm cột Password để xác thực đăng nhập (bcrypt hash)
    Password VARCHAR(255) NULL,
    TrangThai VARCHAR(20) NOT NULL DEFAULT 'Đang làm việc'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Danh sách nhân viên';

-- =========================================================
-- Bảng: PhanXuong
-- =========================================================
CREATE TABLE PhanXuong (
    MaPhanXuong VARCHAR(10) PRIMARY KEY,
    TenPhanXuong VARCHAR(100) NOT NULL,
    MoTa TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Phân xưởng (ví dụ: Cắt, May, Đóng gói)';

-- =========================================================
-- Bảng: DayChuyen
-- =========================================================
CREATE TABLE DayChuyen (
    MaDayChuyen VARCHAR(10) PRIMARY KEY,
    TenDayChuyen VARCHAR(100) NOT NULL,
    MaPhanXuong VARCHAR(10) NOT NULL,
    TrangThai VARCHAR(20) NOT NULL DEFAULT 'Đang hoạt động',
    SoLuongCongNhan INT DEFAULT 0,
    FOREIGN KEY (MaPhanXuong) REFERENCES PhanXuong(MaPhanXuong)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Dây chuyền sản xuất thuộc phân xưởng';

-- =========================================================
-- Bảng: SanPham
-- =========================================================
CREATE TABLE SanPham (
    MaSanPham VARCHAR(10) PRIMARY KEY,
    TenSanPham VARCHAR(100) NOT NULL,
    Size VARCHAR(10),
    Mau VARCHAR(30),
    MoTa TEXT,
    GiaXuat DECIMAL(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Sản phẩm hoàn thiện';

-- =========================================================
-- Bảng: NguyenLieu
-- =========================================================
CREATE TABLE NguyenLieu (
    MaNguyenLieu VARCHAR(10) PRIMARY KEY,
    TenNguyenLieu VARCHAR(100) NOT NULL,
    LoaiNguyenLieu VARCHAR(50),
    DonViTinh VARCHAR(20) NOT NULL,
    SoLuongTonKho DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    GiaNhap DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    HanSuDung DATE,
    MaNhaCungCap VARCHAR(10) NOT NULL,
    NgayCapNhat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MaNhaCungCap) REFERENCES NhaCungCap(MaNhaCungCap)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Nguyên vật liệu (da, keo, đế...)';

-- =========================================================
-- Bảng: DonHang
-- =========================================================
CREATE TABLE DonHang (
    MaDonHang VARCHAR(10) PRIMARY KEY,
    TenDonHang VARCHAR(100) NOT NULL,
    NgayDat DATE NOT NULL,
    TrangThai VARCHAR(20) NOT NULL DEFAULT 'Đang xử lý'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Đơn hàng (nguồn tạo kế hoạch)';

-- =========================================================
-- Bảng: ChiTietDonHang
-- =========================================================
CREATE TABLE ChiTietDonHang (
    MaCTDH INT AUTO_INCREMENT PRIMARY KEY,
    MaDonHang VARCHAR(10) NOT NULL,
    MaSanPham VARCHAR(10) NOT NULL,
    SoLuong INT NOT NULL DEFAULT 0,
    FOREIGN KEY (MaDonHang) REFERENCES DonHang(MaDonHang),
    FOREIGN KEY (MaSanPham) REFERENCES SanPham(MaSanPham)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Chi tiết đơn hàng';

-- =========================================================
-- Bảng: KeHoachSanXuat
-- =========================================================
CREATE TABLE KeHoachSanXuat (
    MaKeHoach VARCHAR(10) PRIMARY KEY,
    TenKeHoach VARCHAR(100),
    NgayBatDau DATE NOT NULL,
    NgayKetThuc DATE NOT NULL,
    MaNV VARCHAR(10) NOT NULL COMMENT 'người lập (FK)',
    MaDonHang VARCHAR(10) NOT NULL COMMENT 'Đơn hàng nguồn (FK)',
    TrangThai VARCHAR(20) NOT NULL DEFAULT 'Chờ duyệt',
    TongChiPhiDuKien DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    SoLuongCongNhanCan INT NOT NULL DEFAULT 0,
    NgayLap DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    GhiChu TEXT,
    FOREIGN KEY (MaNV) REFERENCES NhanVien(MaNV),
    FOREIGN KEY (MaDonHang) REFERENCES DonHang(MaDonHang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Kế hoạch sản xuất tổng';

-- =========================================================
-- Bảng: ChiTietKeHoach
-- =========================================================
CREATE TABLE ChiTietKeHoach (
    MaChiTietKeHoach VARCHAR(20) PRIMARY KEY,
    MaKeHoach VARCHAR(10) NOT NULL,
    MaSanPham VARCHAR(10) NOT NULL,
    SanLuongMucTieu INT NOT NULL DEFAULT 0,
    MaNguyenLieu VARCHAR(10),
    DinhMucBOM DECIMAL(10,2) DEFAULT 0.00,
    CanBoSung INT DEFAULT 0,
    MaPhanXuong VARCHAR(10) NOT NULL,
    FOREIGN KEY (MaKeHoach) REFERENCES KeHoachSanXuat(MaKeHoach),
    FOREIGN KEY (MaSanPham) REFERENCES SanPham(MaSanPham),
    FOREIGN KEY (MaNguyenLieu) REFERENCES NguyenLieu(MaNguyenLieu),
    FOREIGN KEY (MaPhanXuong) REFERENCES PhanXuong(MaPhanXuong)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Chi tiết kế hoạch: sản phẩm, BOM, phân xưởng';

-- =========================================================
-- Bảng: KeHoachCapXuong
-- =========================================================
CREATE TABLE KeHoachCapXuong (
    MaKHCapXuong VARCHAR(10) PRIMARY KEY,
    MaKeHoach VARCHAR(10) NOT NULL,
    MaPhanXuong VARCHAR(10) NOT NULL,
    NgayLap DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    SoLuong INT NOT NULL DEFAULT 0,
    CongSuatDuKien DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    TrangThai VARCHAR(20) NOT NULL DEFAULT 'Chưa thực hiện',
    FOREIGN KEY (MaKeHoach) REFERENCES KeHoachSanXuat(MaKeHoach),
    FOREIGN KEY (MaPhanXuong) REFERENCES PhanXuong(MaPhanXuong)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Kế hoạch cấp xưởng';

-- =========================================================
-- Bảng: CongDoanSX
-- =========================================================
CREATE TABLE CongDoanSX (
    MaCongDoan VARCHAR(10) PRIMARY KEY,
    TenCongDoan VARCHAR(100) NOT NULL COMMENT 'Ví dụ: Cắt, May, Ráp đế, Hoàn thiện, Đóng gói',
    MaDayChuyen VARCHAR(10) NOT NULL,
    SoLuongHoanThanh INT NOT NULL DEFAULT 0,
    TyLeHoanThanh DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (MaDayChuyen) REFERENCES DayChuyen(MaDayChuyen)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Công đoạn sản xuất liên kết dây chuyền';

-- =========================================================
-- Bảng: LoHang
-- =========================================================
CREATE TABLE LoHang (
    MaLoHang VARCHAR(10) PRIMARY KEY,
    MaSanPham VARCHAR(10) NOT NULL,
    SoLuong INT NOT NULL DEFAULT 0,
    TrangThaiQC VARCHAR(20) NOT NULL DEFAULT 'Chưa kiểm',
    FOREIGN KEY (MaSanPham) REFERENCES SanPham(MaSanPham)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Lô sản phẩm để QC và nhập kho';

-- =========================================================
-- Bảng: PhieuYeuCauKiemTraLoSP
-- =========================================================
CREATE TABLE PhieuYeuCauKiemTraLoSP (
    MaPhieuKT VARCHAR(10) PRIMARY KEY,
    MaLoHang VARCHAR(10) NOT NULL,
    NgayKiemTra DATE NOT NULL DEFAULT (CURRENT_DATE()),
    MaNV VARCHAR(10) NOT NULL,
    TrangThai VARCHAR(20) NOT NULL DEFAULT 'Chờ kiểm tra',
    KetQua VARCHAR(50),
    FOREIGN KEY (MaLoHang) REFERENCES LoHang(MaLoHang),
    FOREIGN KEY (MaNV) REFERENCES NhanVien(MaNV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Phiếu yêu cầu kiểm tra lô sản phẩm';

-- =========================================================
-- Bảng: KetQuaKiemDinh
-- =========================================================
CREATE TABLE KetQuaKiemDinh (
    MaKD VARCHAR(10) PRIMARY KEY,
    MaLoHang VARCHAR(10) NOT NULL,
    MaPhieuKT VARCHAR(10) NOT NULL,
    NgayLap DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    MaNV VARCHAR(10) NOT NULL,
    KetQua VARCHAR(20) NOT NULL,
    TrangThai VARCHAR(20) NOT NULL DEFAULT 'Đã kiểm tra',
    FOREIGN KEY (MaLoHang) REFERENCES LoHang(MaLoHang),
    FOREIGN KEY (MaPhieuKT) REFERENCES PhieuYeuCauKiemTraLoSP(MaPhieuKT),
    FOREIGN KEY (MaNV) REFERENCES NhanVien(MaNV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Kết quả kiểm định lô sản phẩm';

-- =========================================================
-- Bảng: PhieuYeuCauXuatNguyenLieu
-- =========================================================
CREATE TABLE PhieuYeuCauXuatNguyenLieu (
    MaPhieuYC VARCHAR(10) PRIMARY KEY,
    MaPhanXuong VARCHAR(10) NOT NULL,
    NgayLap DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    MaNV VARCHAR(10) NOT NULL,
    TrangThai VARCHAR(20) NOT NULL DEFAULT 'Chờ duyệt',
    FOREIGN KEY (MaPhanXuong) REFERENCES PhanXuong(MaPhanXuong),
    FOREIGN KEY (MaNV) REFERENCES NhanVien(MaNV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Yêu cầu xuất NL từ xưởng';

-- =========================================================
-- Bảng: ChiTietPhieuYeuCauXuatNguyenLieu
-- =========================================================
CREATE TABLE ChiTietPhieuYeuCauXuatNguyenLieu (
    MaCTNL VARCHAR(20) PRIMARY KEY,
    MaPhieuYC VARCHAR(10) NOT NULL,
    MaNguyenLieu VARCHAR(10) NOT NULL,
    SoLuong INT NOT NULL DEFAULT 0,
    FOREIGN KEY (MaPhieuYC) REFERENCES PhieuYeuCauXuatNguyenLieu(MaPhieuYC),
    FOREIGN KEY (MaNguyenLieu) REFERENCES NguyenLieu(MaNguyenLieu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Chi tiết phiếu yêu cầu xuất NL';

-- =========================================================
-- Bảng: PhieuXuatNguyenLieu
-- =========================================================
CREATE TABLE PhieuXuatNguyenLieu (
    MaPX VARCHAR(10) PRIMARY KEY,
    MaPhieuYC VARCHAR(10) NOT NULL,
    NgayLap DATE NOT NULL DEFAULT (CURRENT_DATE()),
    MaNV VARCHAR(10) NOT NULL,
    TrangThai VARCHAR(20) NOT NULL DEFAULT 'Đã duyệt',
    FOREIGN KEY (MaPhieuYC) REFERENCES PhieuYeuCauXuatNguyenLieu(MaPhieuYC),
    FOREIGN KEY (MaNV) REFERENCES NhanVien(MaNV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Phiếu xuất nguyên liệu ra sản xuất';

-- =========================================================
-- Bảng: PhieuNhaNguyenLieu (phiếu nhập từ nhà cung cấp) / PhieuNhapNguyenLieu
-- =========================================================
CREATE TABLE PhieuNhaNguyenLieu (
    MaPhieuNhap VARCHAR(10) PRIMARY KEY,
    MaNhaCungCap VARCHAR(10) NOT NULL,
    NgayNhap DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    MaNhanVien VARCHAR(10),
    TongGiaTri DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (MaNhaCungCap) REFERENCES NhaCungCap(MaNhaCungCap),
    FOREIGN KEY (MaNhanVien) REFERENCES NhanVien(MaNV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Phiếu nhập nguyên liệu từ NCC';

-- =========================================================
-- Bảng: ChiTietPhieuNhapNguyenLieu
-- =========================================================
CREATE TABLE ChiTietPhieuNhapNguyenLieu (
    MaPhieuNhap VARCHAR(10) NOT NULL,
    MaNguyenLieu VARCHAR(10) NOT NULL,
    SoLuongNhap DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    DonGia DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    ThanhTien DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    PRIMARY KEY (MaPhieuNhap, MaNguyenLieu),
    FOREIGN KEY (MaPhieuNhap) REFERENCES PhieuNhaNguyenLieu(MaPhieuNhap),
    FOREIGN KEY (MaNguyenLieu) REFERENCES NguyenLieu(MaNguyenLieu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Chi tiết phiếu nhập NL';

-- =========================================================
-- Bảng: CaLamViec
-- =========================================================
CREATE TABLE CaLamViec (
    MaCa VARCHAR(10) PRIMARY KEY,
    LoaiCa VARCHAR(20) NOT NULL,
    GioBatDau TIME NOT NULL,
    GioKetThuc TIME NOT NULL,
    TrangThai VARCHAR(20) NOT NULL DEFAULT 'Hoạt động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Ca làm việc';

-- =========================================================
-- Bảng: ChamCongSanPham
-- =========================================================
CREATE TABLE ChamCongSanPham (
    MaChamCong INT AUTO_INCREMENT PRIMARY KEY,
    MaNV VARCHAR(10) NOT NULL,
    MaCa VARCHAR(10) NOT NULL,
    Ngay DATE NOT NULL,
    SoLuong INT NOT NULL DEFAULT 0,
    TrangThai VARCHAR(20) NOT NULL DEFAULT 'Đã ghi nhận',
    FOREIGN KEY (MaNV) REFERENCES NhanVien(MaNV),
    FOREIGN KEY (MaCa) REFERENCES CaLamViec(MaCa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Chấm công theo sản phẩm và ca';

-- =========================================================
-- Bảng: PhieuNhapSanPham (nhập thành phẩm vào kho, tham chiếu kết quả QC)
-- =========================================================
CREATE TABLE PhieuNhapSanPham (
    MaPhieuNhap VARCHAR(10) PRIMARY KEY,
    MaKD VARCHAR(10) NULL,
    NgayNhap DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    MaNhanVien VARCHAR(10),
    MaLoHang VARCHAR(10) NOT NULL,
    FOREIGN KEY (MaKD) REFERENCES KetQuaKiemDinh(MaKD),
    FOREIGN KEY (MaNhanVien) REFERENCES NhanVien(MaNV),
    FOREIGN KEY (MaLoHang) REFERENCES LoHang(MaLoHang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Phiếu nhập thành phẩm vào kho (sau QC)';

-- =========================================================
-- Indexes / small conveniences (tùy chọn)
-- =========================================================
CREATE INDEX idx_nguyenlieu_maNCC ON NguyenLieu(MaNhaCungCap);
CREATE INDEX idx_chitietkehoach_sp ON ChiTietKeHoach(MaSanPham);
CREATE INDEX idx_chitietdonhang_dh ON ChiTietDonHang(MaDonHang);
CREATE INDEX idx_lohang_sp ON LoHang(MaSanPham);

-- =========================================================
-- Hoàn tất
-- =========================================================
