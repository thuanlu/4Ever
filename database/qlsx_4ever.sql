<<<<<<< HEAD
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 21, 2025 lúc 05:44 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `qlsx_4ever`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `calamviec`
--

CREATE TABLE `calamviec` (
  `MaCa` varchar(10) NOT NULL,
  `LoaiCa` varchar(20) NOT NULL,
  `GioBatDau` time NOT NULL,
  `GioKetThuc` time NOT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'Hoạt động'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Ca làm việc';

--
-- Đang đổ dữ liệu cho bảng `calamviec`
--

INSERT INTO `calamviec` (`MaCa`, `LoaiCa`, `GioBatDau`, `GioKetThuc`, `TrangThai`) VALUES
('CA01', 'Ca sáng', '07:00:00', '11:00:00', 'Hoạt động'),
('CA02', 'Ca chiều', '13:00:00', '17:00:00', 'Hoạt động');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chamcongsanpham`
--

CREATE TABLE `chamcongsanpham` (
  `MaChamCong` int(11) NOT NULL,
  `MaNV` varchar(10) NOT NULL,
  `MaCa` varchar(10) NOT NULL,
  `Ngay` date NOT NULL,
  `SoLuong` int(11) NOT NULL DEFAULT 0,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'Đã ghi nhận'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Chấm công theo sản phẩm và ca';

--
-- Đang đổ dữ liệu cho bảng `chamcongsanpham`
--

INSERT INTO `chamcongsanpham` (`MaChamCong`, `MaNV`, `MaCa`, `Ngay`, `SoLuong`, `TrangThai`) VALUES
(1, 'NCN001', 'CA01', '2025-10-16', 120, 'Đã ghi nhận'),
(2, 'NCN001', 'CA02', '2025-10-16', 80, 'Đã ghi nhận');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietdonhang`
--

CREATE TABLE `chitietdonhang` (
  `MaCTDH` int(11) NOT NULL,
  `MaDonHang` varchar(10) NOT NULL,
  `MaSanPham` varchar(10) NOT NULL,
  `SoLuong` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Chi tiết đơn hàng';

--
-- Đang đổ dữ liệu cho bảng `chitietdonhang`
--

INSERT INTO `chitietdonhang` (`MaCTDH`, `MaDonHang`, `MaSanPham`, `SoLuong`) VALUES
(1, 'DH01', 'SP01', 500),
(2, 'DH01', 'SP02', 300),
(3, 'DH02', 'SP01', 1000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietkehoach`
--

CREATE TABLE `chitietkehoach` (
  `MaChiTietKeHoach` varchar(20) NOT NULL,
  `MaKeHoach` varchar(10) NOT NULL,
  `MaSanPham` varchar(10) NOT NULL,
  `SanLuongMucTieu` int(11) NOT NULL DEFAULT 0,
  `MaNguyenLieu` varchar(10) DEFAULT NULL,
  `DinhMucBOM` decimal(10,2) DEFAULT 0.00,
  `CanBoSung` int(11) DEFAULT 0,
  `MaPhanXuong` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Chi tiết kế hoạch: sản phẩm, BOM, phân xưởng';

--
-- Đang đổ dữ liệu cho bảng `chitietkehoach`
--

INSERT INTO `chitietkehoach` (`MaChiTietKeHoach`, `MaKeHoach`, `MaSanPham`, `SanLuongMucTieu`, `MaNguyenLieu`, `DinhMucBOM`, `CanBoSung`, `MaPhanXuong`) VALUES
('CTKH01', 'KH01', 'SP01', 500, 'NL01', 2.50, 0, 'PX01'),
('CTKH02', 'KH01', 'SP02', 300, 'NL02', 0.50, 0, 'PX02'),
('CTKH03', 'KH02', 'SP01', 1000, 'NL01', 2.00, 0, 'PX02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietphieunhapnguyenlieu`
--

CREATE TABLE `chitietphieunhapnguyenlieu` (
  `MaPhieuNhap` varchar(10) NOT NULL,
  `MaNguyenLieu` varchar(10) NOT NULL,
  `SoLuongNhap` decimal(12,2) NOT NULL DEFAULT 0.00,
  `DonGia` decimal(12,2) NOT NULL DEFAULT 0.00,
  `ThanhTien` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Chi tiết phiếu nhập NL';

--
-- Đang đổ dữ liệu cho bảng `chitietphieunhapnguyenlieu`
--

INSERT INTO `chitietphieunhapnguyenlieu` (`MaPhieuNhap`, `MaNguyenLieu`, `SoLuongNhap`, `DonGia`, `ThanhTien`) VALUES
('PNNL01', 'NL01', 300.00, 150000.00, 45000000.00),
('PNNL01', 'NL03', 500.00, 12000.00, 6000000.00),
('PNNL02', 'NL02', 200.00, 50000.00, 10000000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietphieuyeucauxuatnguyenlieu`
--

CREATE TABLE `chitietphieuyeucauxuatnguyenlieu` (
  `MaCTNL` varchar(20) NOT NULL,
  `MaPhieuYC` varchar(10) NOT NULL,
  `MaNguyenLieu` varchar(10) NOT NULL,
  `SoLuong` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Chi tiết phiếu yêu cầu xuất NL';

--
-- Đang đổ dữ liệu cho bảng `chitietphieuyeucauxuatnguyenlieu`
--

INSERT INTO `chitietphieuyeucauxuatnguyenlieu` (`MaCTNL`, `MaPhieuYC`, `MaNguyenLieu`, `SoLuong`) VALUES
('CTNL01', 'PYCNL01', 'NL01', 100),
('CTNL02', 'PYCNL01', 'NL02', 20);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `congdoansx`
--

CREATE TABLE `congdoansx` (
  `MaCongDoan` varchar(10) NOT NULL,
  `TenCongDoan` varchar(100) NOT NULL COMMENT 'Ví dụ: Cắt, May, Ráp đế, Hoàn thiện, Đóng gói',
  `MaDayChuyen` varchar(10) NOT NULL,
  `SoLuongHoanThanh` int(11) NOT NULL DEFAULT 0,
  `TyLeHoanThanh` decimal(5,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Công đoạn sản xuất liên kết dây chuyền';

--
-- Đang đổ dữ liệu cho bảng `congdoansx`
--

INSERT INTO `congdoansx` (`MaCongDoan`, `TenCongDoan`, `MaDayChuyen`, `SoLuongHoanThanh`, `TyLeHoanThanh`) VALUES
('CD01', 'Cắt', 'DC01', 100, 20.00),
('CD02', 'May ráp', 'DC02', 200, 40.00),
('CD03', 'Đóng gói', 'DC03', 0, 0.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `daychuyen`
--

CREATE TABLE `daychuyen` (
  `MaDayChuyen` varchar(10) NOT NULL,
  `TenDayChuyen` varchar(100) NOT NULL,
  `MaPhanXuong` varchar(10) NOT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'Đang hoạt động',
  `SoLuongCongNhan` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Dây chuyền sản xuất thuộc phân xưởng';

--
-- Đang đổ dữ liệu cho bảng `daychuyen`
--

INSERT INTO `daychuyen` (`MaDayChuyen`, `TenDayChuyen`, `MaPhanXuong`, `TrangThai`, `SoLuongCongNhan`) VALUES
('DC01', 'Dây chuyền Cắt 1', 'PX01', 'Đang hoạt động', 12),
('DC02', 'Dây chuyền May 1', 'PX02', 'Đang hoạt động', 18),
('DC03', 'Dây chuyền Đóng gói 1', 'PX03', 'Đang hoạt động', 10);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donhang`
--

CREATE TABLE `donhang` (
  `MaDonHang` varchar(10) NOT NULL,
  `TenDonHang` varchar(100) NOT NULL,
  `NgayDat` date NOT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'Đang xử lý'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Đơn hàng (nguồn tạo kế hoạch)';

--
-- Đang đổ dữ liệu cho bảng `donhang`
--

INSERT INTO `donhang` (`MaDonHang`, `TenDonHang`, `NgayDat`, `TrangThai`) VALUES
('DH01', 'Đơn hàng Tháng 10', '2025-10-01', 'Đang xử lý'),
('DH02', 'Đơn hàng xuất Nhật', '2025-10-10', 'Đang xử lý');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kehoachcapxuong`
--

CREATE TABLE `kehoachcapxuong` (
  `MaKHCapXuong` varchar(10) NOT NULL,
  `MaKeHoach` varchar(10) NOT NULL,
  `MaPhanXuong` varchar(10) NOT NULL,
  `NgayLap` datetime NOT NULL DEFAULT current_timestamp(),
  `SoLuong` int(11) NOT NULL DEFAULT 0,
  `CongSuatDuKien` decimal(10,2) NOT NULL DEFAULT 0.00,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'Chưa thực hiện'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Kế hoạch cấp xưởng';

--
-- Đang đổ dữ liệu cho bảng `kehoachcapxuong`
--

INSERT INTO `kehoachcapxuong` (`MaKHCapXuong`, `MaKeHoach`, `MaPhanXuong`, `NgayLap`, `SoLuong`, `CongSuatDuKien`, `TrangThai`) VALUES
('KHCX01', 'KH01', 'PX01', '2025-10-05 10:00:00', 500, 90.50, 'Đang thực hiện'),
('KHCX02', 'KH02', 'PX02', '2025-10-12 08:00:00', 1000, 85.00, 'Chưa thực hiện');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kehoachsanxuat`
--

CREATE TABLE `kehoachsanxuat` (
  `MaKeHoach` varchar(10) NOT NULL,
  `TenKeHoach` varchar(100) DEFAULT NULL,
  `NgayBatDau` date NOT NULL,
  `NgayKetThuc` date NOT NULL,
  `MaNV` varchar(10) NOT NULL COMMENT 'người lập (FK)',
  `MaDonHang` varchar(10) NOT NULL COMMENT 'Đơn hàng nguồn (FK)',
  `TrangThai` varchar(20) NOT NULL DEFAULT 'Chờ duyệt',
  `TongChiPhiDuKien` decimal(15,2) NOT NULL DEFAULT 0.00,
  `SoLuongCongNhanCan` int(11) NOT NULL DEFAULT 0,
  `NgayLap` datetime NOT NULL DEFAULT current_timestamp(),
  `GhiChu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Kế hoạch sản xuất tổng';

--
-- Đang đổ dữ liệu cho bảng `kehoachsanxuat`
--

INSERT INTO `kehoachsanxuat` (`MaKeHoach`, `TenKeHoach`, `NgayBatDau`, `NgayKetThuc`, `MaNV`, `MaDonHang`, `TrangThai`, `TongChiPhiDuKien`, `SoLuongCongNhanCan`, `NgayLap`, `GhiChu`) VALUES
('KH01', 'SX Tháng 10 - Alpha', '2025-10-05', '2025-10-25', 'NKH001', 'DH01', 'Đã duyệt', 15000000.00, 30, '2025-10-02 09:00:00', 'Ưu tiên xuất sớm'),
('KH02', 'SX Xuất Nhật - Alpha', '2025-10-12', '2025-10-31', 'NKH001', 'DH02', 'Chờ duyệt', 25000000.00, 45, '2025-10-05 10:00:00', 'Yêu cầu kiểm tra chất lượng'),
('KH03', 'SX Giày Nữ - Summer 2025', '2025-10-15', '2025-11-05', 'NKH001', 'DH01', 'Đang thực hiện', 18000000.00, 25, '2025-10-10 08:30:00', 'Tiến độ 60% hoàn thành'),
('KH04', 'SX Giày Trẻ Em - MiniWalk', '2025-10-18', '2025-11-10', 'NKH001', 'DH02', 'Chờ duyệt', 12000000.00, 20, '2025-10-12 09:00:00', 'Yêu cầu thêm nhân lực'),
('KH05', 'SX Dép Lười - ComfortLine', '2025-10-20', '2025-11-15', 'NKH001', 'DH01', 'Đã duyệt', 9500000.00, 15, '2025-10-15 08:00:00', 'Đã nhập đủ nguyên liệu'),
('KH06', 'SX Giày Da Nam - Classic', '2025-10-22', '2025-11-12', 'NKH001', 'DH02', 'Đang thực hiện', 21000000.00, 40, '2025-10-18 08:45:00', 'Đang may công đoạn cuối'),
('KH07', 'SX Giày Nữ Cao Gót - 4EVER Lady', '2025-10-25', '2025-11-20', 'NKH001', 'DH01', 'Chờ duyệt', 27500000.00, 50, '2025-10-20 09:30:00', 'Chờ phê duyệt từ BGĐ'),
('KH08', 'SX Dép Xốp - DailyWear', '2025-10-28', '2025-11-25', 'NKH001', 'DH02', 'Đã duyệt', 8700000.00, 18, '2025-10-23 08:50:00', 'Sản lượng dự kiến cao'),
('KH09', 'SX Giày Boot Nam - Winter Line', '2025-11-01', '2025-11-28', 'NKH001', 'DH01', 'Đang thực hiện', 30000000.00, 60, '2025-10-26 09:00:00', 'Tiến độ đạt 45%'),
('KH10', 'SX Giày Lười Da - Classic 2025', '2025-11-03', '2025-11-30', 'NKH001', 'DH02', 'Hoàn thành', 19500000.00, 32, '2025-10-27 09:15:00', 'Đã bàn giao cho QC'),
('KH11', 'SX Giày Thể Thao Nữ - Sporty', '2025-11-05', '2025-12-02', 'NKH001', 'DH01', 'Hủy bỏ', 16500000.00, 28, '2025-10-28 08:45:00', 'Hủy do thiếu nguyên liệu'),
('KH12', 'SX Giày Công Sở - OfficeLine', '2025-11-07', '2025-12-05', 'NKH001', 'DH02', 'Đang thực hiện', 22000000.00, 36, '2025-10-30 08:00:00', 'Đang sản xuất giai đoạn 2');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ketquakiemdinh`
--

CREATE TABLE `ketquakiemdinh` (
  `MaKD` varchar(10) NOT NULL,
  `MaLoHang` varchar(10) NOT NULL,
  `MaPhieuKT` varchar(10) NOT NULL,
  `NgayLap` datetime NOT NULL DEFAULT current_timestamp(),
  `MaNV` varchar(10) NOT NULL,
  `KetQua` varchar(20) NOT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'Đã kiểm tra'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Kết quả kiểm định lô sản phẩm';

--
-- Đang đổ dữ liệu cho bảng `ketquakiemdinh`
--

INSERT INTO `ketquakiemdinh` (`MaKD`, `MaLoHang`, `MaPhieuKT`, `NgayLap`, `MaNV`, `KetQua`, `TrangThai`) VALUES
('KD01', 'LH01', 'PYC01', '2025-10-21 09:00:00', 'NQC001', 'Đạt', 'Đã kiểm tra'),
('KD02', 'LH02', 'PYC02', '2025-10-23 09:00:00', 'NQC001', 'Không đạt', 'Đã kiểm tra');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lohang`
--

CREATE TABLE `lohang` (
  `MaLoHang` varchar(10) NOT NULL,
  `MaSanPham` varchar(10) NOT NULL,
  `SoLuong` int(11) NOT NULL DEFAULT 0,
  `TrangThaiQC` varchar(20) NOT NULL DEFAULT 'Chưa kiểm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Lô sản phẩm để QC và nhập kho';

--
-- Đang đổ dữ liệu cho bảng `lohang`
--

INSERT INTO `lohang` (`MaLoHang`, `MaSanPham`, `SoLuong`, `TrangThaiQC`) VALUES
('LH01', 'SP01', 500, 'Chưa kiểm'),
('LH02', 'SP02', 300, 'Chưa kiểm');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguyenlieu`
--

CREATE TABLE `nguyenlieu` (
  `MaNguyenLieu` varchar(10) NOT NULL,
  `TenNguyenLieu` varchar(100) NOT NULL,
  `LoaiNguyenLieu` varchar(50) DEFAULT NULL,
  `DonViTinh` varchar(20) NOT NULL,
  `SoLuongTonKho` decimal(12,2) NOT NULL DEFAULT 0.00,
  `GiaNhap` decimal(12,2) NOT NULL DEFAULT 0.00,
  `HanSuDung` date DEFAULT NULL,
  `MaNhaCungCap` varchar(10) NOT NULL,
  `NgayCapNhat` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Nguyên vật liệu (da, keo, đế...)';

--
-- Đang đổ dữ liệu cho bảng `nguyenlieu`
--

INSERT INTO `nguyenlieu` (`MaNguyenLieu`, `TenNguyenLieu`, `LoaiNguyenLieu`, `DonViTinh`, `SoLuongTonKho`, `GiaNhap`, `HanSuDung`, `MaNhaCungCap`, `NgayCapNhat`) VALUES
('NL01', 'Da bò loại A', 'Da', 'm2', 500.00, 150000.00, '2025-12-31', 'NCC01', '2025-10-20 17:15:58'),
('NL02', 'Keo dán PU', 'Keo', 'kg', 200.00, 50000.00, '2025-09-15', 'NCC02', '2025-10-20 17:15:58'),
('NL03', 'Đế giày cao su', 'Phụ kiện', 'cái', 1000.00, 12000.00, '2026-01-01', 'NCC01', '2025-10-20 17:15:58');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhacungcap`
--

CREATE TABLE `nhacungcap` (
  `MaNhaCungCap` varchar(10) NOT NULL COMMENT 'PK nhà cung cấp',
  `TenNhaCungCap` varchar(100) NOT NULL COMMENT 'Tên nhà cung cấp',
  `DiaChi` varchar(200) NOT NULL,
  `SoDienThoai` varchar(20) NOT NULL,
  `Email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Danh sách nhà cung cấp';

--
-- Đang đổ dữ liệu cho bảng `nhacungcap`
--

INSERT INTO `nhacungcap` (`MaNhaCungCap`, `TenNhaCungCap`, `DiaChi`, `SoDienThoai`, `Email`) VALUES
('NCC01', 'Công ty TNHH Da Nam Long', 'Q12, TP.HCM', '0908123123', 'nanlong@da.vn'),
('NCC02', 'Công ty TNHH Keo Dán Việt', 'Bình Dương', '0908456789', 'keodanviet@gmail.com');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhanvien`
--

CREATE TABLE `nhanvien` (
  `MaNV` varchar(10) NOT NULL COMMENT 'PK nhân viên (NDBGD25...)',
  `HoTen` varchar(100) NOT NULL,
  `ChucVu` varchar(50) NOT NULL,
  `BoPhan` varchar(50) DEFAULT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'Đang làm việc'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Danh sách nhân viên';

--
-- Đang đổ dữ liệu cho bảng `nhanvien`
--

INSERT INTO `nhanvien` (`MaNV`, `HoTen`, `ChucVu`, `BoPhan`, `SoDienThoai`, `Password`, `TrangThai`) VALUES
('NAD001', 'Lư Minh Thuận', 'ADMIN', 'Phòng IT', '0908000008', '$2y$10$cHGFwvQW3FyQVJRNAzWWT.DXlhjr/2gtNkHltakxAXjVLIEwYZGji', 'Đang làm việc'),
('NBGD001', 'Nguyễn Văn An', 'BGD', 'Ban Giám Đốc', '0901000001', '$2y$10$4aeZcC4/aFqA4lzkyfG3WOe1Zo7PHFENUvhSFY1iNaRqC9l2zT1yi', 'Đang làm việc'),
('NCN001', 'Huỳnh Văn Nam', 'CN', 'Dây chuyền 2', '0907000007', '$2y$10$GyJpC20HAKai0b0on8/cEuW7rQcO52zD5HaJ0U1xlmDw/nnwEznc6', 'Đang làm việc'),
('NKH001', 'Trần Thị Bình', 'KH', 'Phòng Kế Hoạch', '0902000002', '$2y$10$sAQd1gCY2LRem4s9BnAO1uq8FvGW/7t64zlNf1xWSF1mOIXf7e6LC', 'Đang làm việc'),
('NQC001', 'Nguyễn Thị Trâm', 'QC', 'Phòng Kiểm Định', '0905000005', '$2y$10$ob6Jlaruf4oCTXYu4bYSTukp2fc9GuXb7rUkMNdndHsEkZpMxjuQu', 'Đang làm việc'),
('NTT001', 'Lê Văn Cường', 'TT', 'Tổ May 1', '0904000004', '$2y$10$mYFfG.bDFFyitC6tnca7b.SbPdSAAkG8.mLkiPqrwWmuAIqKJ98Bu', 'Đang làm việc'),
('NVK001', 'Võ Thanh Hòa', 'NVK', 'Kho Nguyên Liệu', '0906000006', '$2y$10$E08sjh6C4ityXIL5nW6SOewQ5OZJ3WfX79NlKWUGNosAjJASqBk6i', 'Đang làm việc'),
('NXT001', 'Phạm Văn Dũng', 'XT', 'Phân Xưởng 1', '0903000003', '$2y$10$wOp2wTZNDlAu6wH8x/EzkOqNBNSGGIFtZIIW3tLI2bmoRQfTd.KRa', 'Đang làm việc');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phanxuong`
--

CREATE TABLE `phanxuong` (
  `MaPhanXuong` varchar(10) NOT NULL,
  `TenPhanXuong` varchar(100) NOT NULL,
  `MoTa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Phân xưởng (ví dụ: Cắt, May, Đóng gói)';

--
-- Đang đổ dữ liệu cho bảng `phanxuong`
--

INSERT INTO `phanxuong` (`MaPhanXuong`, `TenPhanXuong`, `MoTa`) VALUES
('PX01', 'Phân xưởng Cắt', 'Cắt da, vải'),
('PX02', 'Phân xưởng May', 'May ráp chi tiết giày'),
('PX03', 'Phân xưởng Đóng gói', 'Hoàn thiện và đóng hộp');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieunhanguyenlieu`
--

CREATE TABLE `phieunhanguyenlieu` (
  `MaPhieuNhap` varchar(10) NOT NULL,
  `MaNhaCungCap` varchar(10) NOT NULL,
  `NgayNhap` datetime NOT NULL DEFAULT current_timestamp(),
  `MaNhanVien` varchar(10) DEFAULT NULL,
  `TongGiaTri` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Phiếu nhập nguyên liệu từ NCC';

--
-- Đang đổ dữ liệu cho bảng `phieunhanguyenlieu`
--

INSERT INTO `phieunhanguyenlieu` (`MaPhieuNhap`, `MaNhaCungCap`, `NgayNhap`, `MaNhanVien`, `TongGiaTri`) VALUES
('PNNL01', 'NCC01', '2025-10-01 09:00:00', 'NVK001', 25000000.00),
('PNNL02', 'NCC02', '2025-10-05 09:00:00', 'NVK001', 15000000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieunhapsanpham`
--

CREATE TABLE `phieunhapsanpham` (
  `MaPhieuNhap` varchar(10) NOT NULL,
  `MaKD` varchar(10) DEFAULT NULL,
  `NgayNhap` datetime NOT NULL DEFAULT current_timestamp(),
  `MaNhanVien` varchar(10) DEFAULT NULL,
  `MaLoHang` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Phiếu nhập thành phẩm vào kho (sau QC)';

--
-- Đang đổ dữ liệu cho bảng `phieunhapsanpham`
--

INSERT INTO `phieunhapsanpham` (`MaPhieuNhap`, `MaKD`, `NgayNhap`, `MaNhanVien`, `MaLoHang`) VALUES
('PNSP01', 'KD01', '2025-10-24 08:30:00', 'NVK001', 'LH01'),
('PNSP02', 'KD02', '2025-10-25 09:15:00', 'NVK001', 'LH02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieuxuatnguyenlieu`
--

CREATE TABLE `phieuxuatnguyenlieu` (
  `MaPX` varchar(10) NOT NULL,
  `MaPhieuYC` varchar(10) NOT NULL,
  `NgayLap` date NOT NULL DEFAULT curdate(),
  `MaNV` varchar(10) NOT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'Đã duyệt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Phiếu xuất nguyên liệu ra sản xuất';

--
-- Đang đổ dữ liệu cho bảng `phieuxuatnguyenlieu`
--

INSERT INTO `phieuxuatnguyenlieu` (`MaPX`, `MaPhieuYC`, `NgayLap`, `MaNV`, `TrangThai`) VALUES
('PXNL01', 'PYCNL01', '2025-10-11', 'NVK001', 'Đã duyệt');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieuyeucaukiemtralosp`
--

CREATE TABLE `phieuyeucaukiemtralosp` (
  `MaPhieuKT` varchar(10) NOT NULL,
  `MaLoHang` varchar(10) NOT NULL,
  `NgayKiemTra` date NOT NULL DEFAULT curdate(),
  `MaNV` varchar(10) NOT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'Chờ kiểm tra',
  `KetQua` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Phiếu yêu cầu kiểm tra lô sản phẩm';

--
-- Đang đổ dữ liệu cho bảng `phieuyeucaukiemtralosp`
--

INSERT INTO `phieuyeucaukiemtralosp` (`MaPhieuKT`, `MaLoHang`, `NgayKiemTra`, `MaNV`, `TrangThai`, `KetQua`) VALUES
('PYC01', 'LH01', '2025-10-20', 'NQC001', 'Chờ kiểm tra', NULL),
('PYC02', 'LH02', '2025-10-22', 'NQC001', 'Chờ kiểm tra', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieuyeucauxuatnguyenlieu`
--

CREATE TABLE `phieuyeucauxuatnguyenlieu` (
  `MaPhieuYC` varchar(10) NOT NULL,
  `MaPhanXuong` varchar(10) NOT NULL,
  `NgayLap` datetime NOT NULL DEFAULT current_timestamp(),
  `MaNV` varchar(10) NOT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'Chờ duyệt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Yêu cầu xuất NL từ xưởng';

--
-- Đang đổ dữ liệu cho bảng `phieuyeucauxuatnguyenlieu`
--

INSERT INTO `phieuyeucauxuatnguyenlieu` (`MaPhieuYC`, `MaPhanXuong`, `NgayLap`, `MaNV`, `TrangThai`) VALUES
('PYCNL01', 'PX01', '2025-10-10 08:00:00', 'NXT001', 'Đã duyệt');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sanpham`
--

CREATE TABLE `sanpham` (
  `MaSanPham` varchar(10) NOT NULL,
  `TenSanPham` varchar(100) NOT NULL,
  `Size` varchar(10) DEFAULT NULL,
  `Mau` varchar(30) DEFAULT NULL,
  `MoTa` text DEFAULT NULL,
  `GiaXuat` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Sản phẩm hoàn thiện';

--
-- Đang đổ dữ liệu cho bảng `sanpham`
--

INSERT INTO `sanpham` (`MaSanPham`, `TenSanPham`, `Size`, `Mau`, `MoTa`, `GiaXuat`) VALUES
('SP01', 'Giày thể thao 4EVER Alpha', '42', 'Đen', 'Mẫu thể thao bền, nhẹ', 450000.00),
('SP02', 'Giày sandal 4EVER Summer', '40', 'Nâu', 'Thoáng mát, nhẹ', 300000.00);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `calamviec`
--
ALTER TABLE `calamviec`
  ADD PRIMARY KEY (`MaCa`);

--
-- Chỉ mục cho bảng `chamcongsanpham`
--
ALTER TABLE `chamcongsanpham`
  ADD PRIMARY KEY (`MaChamCong`),
  ADD KEY `MaNV` (`MaNV`),
  ADD KEY `MaCa` (`MaCa`);

--
-- Chỉ mục cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD PRIMARY KEY (`MaCTDH`),
  ADD KEY `MaSanPham` (`MaSanPham`),
  ADD KEY `idx_chitietdonhang_dh` (`MaDonHang`);

--
-- Chỉ mục cho bảng `chitietkehoach`
--
ALTER TABLE `chitietkehoach`
  ADD PRIMARY KEY (`MaChiTietKeHoach`),
  ADD KEY `MaKeHoach` (`MaKeHoach`),
  ADD KEY `MaNguyenLieu` (`MaNguyenLieu`),
  ADD KEY `MaPhanXuong` (`MaPhanXuong`),
  ADD KEY `idx_chitietkehoach_sp` (`MaSanPham`);

--
-- Chỉ mục cho bảng `chitietphieunhapnguyenlieu`
--
ALTER TABLE `chitietphieunhapnguyenlieu`
  ADD PRIMARY KEY (`MaPhieuNhap`,`MaNguyenLieu`),
  ADD KEY `MaNguyenLieu` (`MaNguyenLieu`);

--
-- Chỉ mục cho bảng `chitietphieuyeucauxuatnguyenlieu`
--
ALTER TABLE `chitietphieuyeucauxuatnguyenlieu`
  ADD PRIMARY KEY (`MaCTNL`),
  ADD KEY `MaPhieuYC` (`MaPhieuYC`),
  ADD KEY `MaNguyenLieu` (`MaNguyenLieu`);

--
-- Chỉ mục cho bảng `congdoansx`
--
ALTER TABLE `congdoansx`
  ADD PRIMARY KEY (`MaCongDoan`),
  ADD KEY `MaDayChuyen` (`MaDayChuyen`);

--
-- Chỉ mục cho bảng `daychuyen`
--
ALTER TABLE `daychuyen`
  ADD PRIMARY KEY (`MaDayChuyen`),
  ADD KEY `MaPhanXuong` (`MaPhanXuong`);

--
-- Chỉ mục cho bảng `donhang`
--
ALTER TABLE `donhang`
  ADD PRIMARY KEY (`MaDonHang`);

--
-- Chỉ mục cho bảng `kehoachcapxuong`
--
ALTER TABLE `kehoachcapxuong`
  ADD PRIMARY KEY (`MaKHCapXuong`),
  ADD KEY `MaKeHoach` (`MaKeHoach`),
  ADD KEY `MaPhanXuong` (`MaPhanXuong`);

--
-- Chỉ mục cho bảng `kehoachsanxuat`
--
ALTER TABLE `kehoachsanxuat`
  ADD PRIMARY KEY (`MaKeHoach`),
  ADD KEY `MaNV` (`MaNV`),
  ADD KEY `MaDonHang` (`MaDonHang`);

--
-- Chỉ mục cho bảng `ketquakiemdinh`
--
ALTER TABLE `ketquakiemdinh`
  ADD PRIMARY KEY (`MaKD`),
  ADD KEY `MaLoHang` (`MaLoHang`),
  ADD KEY `MaPhieuKT` (`MaPhieuKT`),
  ADD KEY `MaNV` (`MaNV`);

--
-- Chỉ mục cho bảng `lohang`
--
ALTER TABLE `lohang`
  ADD PRIMARY KEY (`MaLoHang`),
  ADD KEY `idx_lohang_sp` (`MaSanPham`);

--
-- Chỉ mục cho bảng `nguyenlieu`
--
ALTER TABLE `nguyenlieu`
  ADD PRIMARY KEY (`MaNguyenLieu`),
  ADD KEY `idx_nguyenlieu_maNCC` (`MaNhaCungCap`);

--
-- Chỉ mục cho bảng `nhacungcap`
--
ALTER TABLE `nhacungcap`
  ADD PRIMARY KEY (`MaNhaCungCap`);

--
-- Chỉ mục cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`MaNV`);

--
-- Chỉ mục cho bảng `phanxuong`
--
ALTER TABLE `phanxuong`
  ADD PRIMARY KEY (`MaPhanXuong`);

--
-- Chỉ mục cho bảng `phieunhanguyenlieu`
--
ALTER TABLE `phieunhanguyenlieu`
  ADD PRIMARY KEY (`MaPhieuNhap`),
  ADD KEY `MaNhaCungCap` (`MaNhaCungCap`),
  ADD KEY `MaNhanVien` (`MaNhanVien`);

--
-- Chỉ mục cho bảng `phieunhapsanpham`
--
ALTER TABLE `phieunhapsanpham`
  ADD PRIMARY KEY (`MaPhieuNhap`),
  ADD KEY `MaKD` (`MaKD`),
  ADD KEY `MaNhanVien` (`MaNhanVien`),
  ADD KEY `MaLoHang` (`MaLoHang`);

--
-- Chỉ mục cho bảng `phieuxuatnguyenlieu`
--
ALTER TABLE `phieuxuatnguyenlieu`
  ADD PRIMARY KEY (`MaPX`),
  ADD KEY `MaPhieuYC` (`MaPhieuYC`),
  ADD KEY `MaNV` (`MaNV`);

--
-- Chỉ mục cho bảng `phieuyeucaukiemtralosp`
--
ALTER TABLE `phieuyeucaukiemtralosp`
  ADD PRIMARY KEY (`MaPhieuKT`),
  ADD KEY `MaLoHang` (`MaLoHang`),
  ADD KEY `MaNV` (`MaNV`);

--
-- Chỉ mục cho bảng `phieuyeucauxuatnguyenlieu`
--
ALTER TABLE `phieuyeucauxuatnguyenlieu`
  ADD PRIMARY KEY (`MaPhieuYC`),
  ADD KEY `MaPhanXuong` (`MaPhanXuong`),
  ADD KEY `MaNV` (`MaNV`);

--
-- Chỉ mục cho bảng `sanpham`
--
ALTER TABLE `sanpham`
  ADD PRIMARY KEY (`MaSanPham`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chamcongsanpham`
--
ALTER TABLE `chamcongsanpham`
  MODIFY `MaChamCong` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  MODIFY `MaCTDH` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chamcongsanpham`
--
ALTER TABLE `chamcongsanpham`
  ADD CONSTRAINT `chamcongsanpham_ibfk_1` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`),
  ADD CONSTRAINT `chamcongsanpham_ibfk_2` FOREIGN KEY (`MaCa`) REFERENCES `calamviec` (`MaCa`);

--
-- Các ràng buộc cho bảng `chitietdonhang`
--
ALTER TABLE `chitietdonhang`
  ADD CONSTRAINT `chitietdonhang_ibfk_1` FOREIGN KEY (`MaDonHang`) REFERENCES `donhang` (`MaDonHang`),
  ADD CONSTRAINT `chitietdonhang_ibfk_2` FOREIGN KEY (`MaSanPham`) REFERENCES `sanpham` (`MaSanPham`);

--
-- Các ràng buộc cho bảng `chitietkehoach`
--
ALTER TABLE `chitietkehoach`
  ADD CONSTRAINT `chitietkehoach_ibfk_1` FOREIGN KEY (`MaKeHoach`) REFERENCES `kehoachsanxuat` (`MaKeHoach`),
  ADD CONSTRAINT `chitietkehoach_ibfk_2` FOREIGN KEY (`MaSanPham`) REFERENCES `sanpham` (`MaSanPham`),
  ADD CONSTRAINT `chitietkehoach_ibfk_3` FOREIGN KEY (`MaNguyenLieu`) REFERENCES `nguyenlieu` (`MaNguyenLieu`),
  ADD CONSTRAINT `chitietkehoach_ibfk_4` FOREIGN KEY (`MaPhanXuong`) REFERENCES `phanxuong` (`MaPhanXuong`);

--
-- Các ràng buộc cho bảng `chitietphieunhapnguyenlieu`
--
ALTER TABLE `chitietphieunhapnguyenlieu`
  ADD CONSTRAINT `chitietphieunhapnguyenlieu_ibfk_1` FOREIGN KEY (`MaPhieuNhap`) REFERENCES `phieunhanguyenlieu` (`MaPhieuNhap`),
  ADD CONSTRAINT `chitietphieunhapnguyenlieu_ibfk_2` FOREIGN KEY (`MaNguyenLieu`) REFERENCES `nguyenlieu` (`MaNguyenLieu`);

--
-- Các ràng buộc cho bảng `chitietphieuyeucauxuatnguyenlieu`
--
ALTER TABLE `chitietphieuyeucauxuatnguyenlieu`
  ADD CONSTRAINT `chitietphieuyeucauxuatnguyenlieu_ibfk_1` FOREIGN KEY (`MaPhieuYC`) REFERENCES `phieuyeucauxuatnguyenlieu` (`MaPhieuYC`),
  ADD CONSTRAINT `chitietphieuyeucauxuatnguyenlieu_ibfk_2` FOREIGN KEY (`MaNguyenLieu`) REFERENCES `nguyenlieu` (`MaNguyenLieu`);

--
-- Các ràng buộc cho bảng `congdoansx`
--
ALTER TABLE `congdoansx`
  ADD CONSTRAINT `congdoansx_ibfk_1` FOREIGN KEY (`MaDayChuyen`) REFERENCES `daychuyen` (`MaDayChuyen`);

--
-- Các ràng buộc cho bảng `daychuyen`
--
ALTER TABLE `daychuyen`
  ADD CONSTRAINT `daychuyen_ibfk_1` FOREIGN KEY (`MaPhanXuong`) REFERENCES `phanxuong` (`MaPhanXuong`);

--
-- Các ràng buộc cho bảng `kehoachcapxuong`
--
ALTER TABLE `kehoachcapxuong`
  ADD CONSTRAINT `kehoachcapxuong_ibfk_1` FOREIGN KEY (`MaKeHoach`) REFERENCES `kehoachsanxuat` (`MaKeHoach`),
  ADD CONSTRAINT `kehoachcapxuong_ibfk_2` FOREIGN KEY (`MaPhanXuong`) REFERENCES `phanxuong` (`MaPhanXuong`);

--
-- Các ràng buộc cho bảng `kehoachsanxuat`
--
ALTER TABLE `kehoachsanxuat`
  ADD CONSTRAINT `kehoachsanxuat_ibfk_1` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`),
  ADD CONSTRAINT `kehoachsanxuat_ibfk_2` FOREIGN KEY (`MaDonHang`) REFERENCES `donhang` (`MaDonHang`);

--
-- Các ràng buộc cho bảng `ketquakiemdinh`
--
ALTER TABLE `ketquakiemdinh`
  ADD CONSTRAINT `ketquakiemdinh_ibfk_1` FOREIGN KEY (`MaLoHang`) REFERENCES `lohang` (`MaLoHang`),
  ADD CONSTRAINT `ketquakiemdinh_ibfk_2` FOREIGN KEY (`MaPhieuKT`) REFERENCES `phieuyeucaukiemtralosp` (`MaPhieuKT`),
  ADD CONSTRAINT `ketquakiemdinh_ibfk_3` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`);

--
-- Các ràng buộc cho bảng `lohang`
--
ALTER TABLE `lohang`
  ADD CONSTRAINT `lohang_ibfk_1` FOREIGN KEY (`MaSanPham`) REFERENCES `sanpham` (`MaSanPham`);

--
-- Các ràng buộc cho bảng `nguyenlieu`
--
ALTER TABLE `nguyenlieu`
  ADD CONSTRAINT `nguyenlieu_ibfk_1` FOREIGN KEY (`MaNhaCungCap`) REFERENCES `nhacungcap` (`MaNhaCungCap`);

--
-- Các ràng buộc cho bảng `phieunhanguyenlieu`
--
ALTER TABLE `phieunhanguyenlieu`
  ADD CONSTRAINT `phieunhanguyenlieu_ibfk_1` FOREIGN KEY (`MaNhaCungCap`) REFERENCES `nhacungcap` (`MaNhaCungCap`),
  ADD CONSTRAINT `phieunhanguyenlieu_ibfk_2` FOREIGN KEY (`MaNhanVien`) REFERENCES `nhanvien` (`MaNV`);

--
-- Các ràng buộc cho bảng `phieunhapsanpham`
--
ALTER TABLE `phieunhapsanpham`
  ADD CONSTRAINT `phieunhapsanpham_ibfk_1` FOREIGN KEY (`MaKD`) REFERENCES `ketquakiemdinh` (`MaKD`),
  ADD CONSTRAINT `phieunhapsanpham_ibfk_2` FOREIGN KEY (`MaNhanVien`) REFERENCES `nhanvien` (`MaNV`),
  ADD CONSTRAINT `phieunhapsanpham_ibfk_3` FOREIGN KEY (`MaLoHang`) REFERENCES `lohang` (`MaLoHang`);

--
-- Các ràng buộc cho bảng `phieuxuatnguyenlieu`
--
ALTER TABLE `phieuxuatnguyenlieu`
  ADD CONSTRAINT `phieuxuatnguyenlieu_ibfk_1` FOREIGN KEY (`MaPhieuYC`) REFERENCES `phieuyeucauxuatnguyenlieu` (`MaPhieuYC`),
  ADD CONSTRAINT `phieuxuatnguyenlieu_ibfk_2` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`);

--
-- Các ràng buộc cho bảng `phieuyeucaukiemtralosp`
--
ALTER TABLE `phieuyeucaukiemtralosp`
  ADD CONSTRAINT `phieuyeucaukiemtralosp_ibfk_1` FOREIGN KEY (`MaLoHang`) REFERENCES `lohang` (`MaLoHang`),
  ADD CONSTRAINT `phieuyeucaukiemtralosp_ibfk_2` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`);

--
-- Các ràng buộc cho bảng `phieuyeucauxuatnguyenlieu`
--
ALTER TABLE `phieuyeucauxuatnguyenlieu`
  ADD CONSTRAINT `phieuyeucauxuatnguyenlieu_ibfk_1` FOREIGN KEY (`MaPhanXuong`) REFERENCES `phanxuong` (`MaPhanXuong`),
  ADD CONSTRAINT `phieuyeucauxuatnguyenlieu_ibfk_2` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
=======
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
>>>>>>> origin/ke_hoach_san_xuat
