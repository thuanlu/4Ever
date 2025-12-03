-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 03, 2025 lúc 11:02 AM
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
('afternoon', 'Ca Chiều', '14:00:00', '22:00:00', 'Hoạt động'),
('CA01', 'Ca sáng', '07:00:00', '11:00:00', 'Hoạt động'),
('CA02', 'Ca chiều', '13:00:00', '17:00:00', 'Hoạt động'),
('morning', 'Ca Sáng', '06:00:00', '14:00:00', 'Hoạt động'),
('night', 'Ca Đêm', '22:00:00', '06:00:00', 'Hoạt động');

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
(1, 'DH01', 'SP01', 100),
(2, 'DH01', 'SP02', 50),
(3, 'DH02', 'SP01', 500),
(4, 'DH03', 'SP01', 300),
(5, 'DH03', 'SP02', 200),
(6, 'DH04', 'SP01', 50),
(7, 'DH05', 'SP04', 100),
(8, 'DH06', 'SP01', 200),
(9, 'DH06', 'SP05', 300),
(10, 'DH07', 'SP02', 100),
(11, 'DH07', 'SP03', 50),
(12, 'DH07', 'SP05', 100),
(13, 'DH08', 'SP03', 20),
(14, 'DH09', 'SP04', 50),
(15, 'DH09', 'SP01', 100);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietkehoach`
--

CREATE TABLE `chitietkehoach` (
  `MaChiTietKeHoach` varchar(20) NOT NULL,
  `MaKeHoach` varchar(10) NOT NULL,
  `MaSanPham` varchar(10) NOT NULL,
  `SanLuongMucTieu` int(11) NOT NULL DEFAULT 0,
  `CanBoSung` int(11) DEFAULT 0 COMMENT 'Giá trị này nên được tính toán động',
  `MaPhanXuong` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Chi tiết kế hoạch: sản phẩm và phân xưởng';

--
-- Đang đổ dữ liệu cho bảng `chitietkehoach`
--

INSERT INTO `chitietkehoach` (`MaChiTietKeHoach`, `MaKeHoach`, `MaSanPham`, `SanLuongMucTieu`, `CanBoSung`, `MaPhanXuong`) VALUES
('KH07-01', 'KH07', 'SP03', 22, 0, 'PX02'),
('KHSX01-01', 'KH01', 'SP01', 100, 0, 'PX01'),
('KHSX01-02', 'KH01', 'SP01', 100, 0, 'PX02'),
('KHSX01-03', 'KH01', 'SP02', 50, 0, 'PX03'),
('KHSX03-01', 'KH03', 'SP01', 300, 0, 'PX01'),
('KHSX03-02', 'KH03', 'SP01', 300, 0, 'PX02'),
('KHSX03-03', 'KH03', 'SP02', 200, 0, 'PX03'),
('KHSX04-01', 'KH04', 'SP04', 100, 0, 'PX01'),
('KHSX04-02', 'KH04', 'SP04', 100, 0, 'PX02'),
('KHSX05-01', 'KH05', 'SP01', 200, 0, 'PX01'),
('KHSX05-02', 'KH05', 'SP05', 300, 0, 'PX03'),
('KHSX06-01', 'KH06', 'SP02', 100, 0, 'PX03'),
('KHSX06-02', 'KH06', 'SP03', 50, 0, 'PX01'),
('KHSX06-03', 'KH06', 'SP03', 50, 0, 'PX02'),
('KHSX06-04', 'KH06', 'SP05', 100, 0, 'PX03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitietphieudatnvl`
--

CREATE TABLE `chitietphieudatnvl` (
  `MaChiTiet` int(11) NOT NULL COMMENT 'PK - Mã chi tiết tự tăng',
  `MaPhieu` varchar(10) NOT NULL COMMENT 'FK - Liên kết bảng phieudatnvl',
  `MaNVL` varchar(10) NOT NULL COMMENT 'FK - Mã nguyên vật liệu',
  `TenNVL` varchar(100) DEFAULT NULL COMMENT 'Tên NVL (snapshot tại thời điểm tạo)',
  `DonViTinh` varchar(20) DEFAULT NULL,
  `SoLuongCan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `DonGia` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Đơn giá dự kiến',
  `ThanhTien` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Thành tiền dự kiến'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Chi tiết các NVL trong Phiếu đặt hàng';

--
-- Đang đổ dữ liệu cho bảng `chitietphieudatnvl`
--

INSERT INTO `chitietphieudatnvl` (`MaChiTiet`, `MaPhieu`, `MaNVL`, `TenNVL`, `DonViTinh`, `SoLuongCan`, `DonGia`, `ThanhTien`) VALUES
(1, 'PDNL01', 'NL01', 'Da bò loại A', NULL, 250.00, 150000.00, 37500000.00),
(2, 'PDNL01', 'NL02', 'Keo dán PU', NULL, 25.00, 50000.00, 1250000.00),
(3, 'PDNL02', 'NL03', 'Đế giày cao su', NULL, 1000.00, 12000.00, 12000000.00);

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
('CTNL01', 'PYCNL01', 'NL01', 250),
('CTNL02', 'PYCNL02', 'NL01', 10),
('CTNL03', 'PYCNL02', 'NL02', 50);

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
  `SoLuongCongNhan` int(11) NOT NULL DEFAULT 40 COMMENT 'Số công nhân cố định mỗi dây chuyền',
  `MaToTruong` varchar(10) DEFAULT NULL COMMENT 'Tổ trưởng phụ trách dây chuyền'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Dây chuyền sản xuất thuộc phân xưởng';

--
-- Đang đổ dữ liệu cho bảng `daychuyen`
--

INSERT INTO `daychuyen` (`MaDayChuyen`, `TenDayChuyen`, `MaPhanXuong`, `TrangThai`, `SoLuongCongNhan`, `MaToTruong`) VALUES
('DC01', 'Dây chuyền 1', 'PX01', 'Đang hoạt động', 40, 'NTT001'),
('DC02', 'Dây chuyền 2', 'PX01', 'Đang hoạt động', 40, 'NTT002'),
('DC03', 'Dây chuyền 3', 'PX01', 'Đang hoạt động', 40, 'NTT003'),
('DC04', 'Dây chuyền 4', 'PX01', 'Đang hoạt động', 40, 'NTT004'),
('DC05', 'Dây chuyền 5', 'PX01', 'Đang hoạt động', 40, 'NTT005'),
('DC06', 'Dây chuyền 6', 'PX01', 'Đang hoạt động', 40, 'NTT006'),
('DC07', 'Dây chuyền 7', 'PX01', 'Đang hoạt động', 40, 'NTT007'),
('DC08', 'Dây chuyền 8', 'PX01', 'Đang hoạt động', 40, 'NTT008'),
('DC09', 'Dây chuyền 9', 'PX01', 'Đang hoạt động', 40, 'NTT009'),
('DC10', 'Dây chuyền 10', 'PX01', 'Đang hoạt động', 40, 'NTT010'),
('DC11', 'Dây chuyền 1', 'PX02', 'Đang hoạt động', 40, 'NTT011'),
('DC12', 'Dây chuyền 2', 'PX02', 'Đang hoạt động', 40, 'NTT012'),
('DC13', 'Dây chuyền 3', 'PX02', 'Đang hoạt động', 40, 'NTT013'),
('DC14', 'Dây chuyền 4', 'PX02', 'Đang hoạt động', 40, 'NTT014'),
('DC15', 'Dây chuyền 5', 'PX02', 'Đang hoạt động', 40, 'NTT015'),
('DC16', 'Dây chuyền 6', 'PX02', 'Đang hoạt động', 40, 'NTT016'),
('DC17', 'Dây chuyền 7', 'PX02', 'Đang hoạt động', 40, 'NTT017'),
('DC18', 'Dây chuyền 8', 'PX02', 'Đang hoạt động', 40, 'NTT018'),
('DC19', 'Dây chuyền 9', 'PX02', 'Đang hoạt động', 40, 'NTT019'),
('DC20', 'Dây chuyền 10', 'PX02', 'Đang hoạt động', 40, 'NTT020');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dinhmucnguyenlieu`
--

CREATE TABLE `dinhmucnguyenlieu` (
  `MaDinhMuc` varchar(10) NOT NULL,
  `MaSanPham` varchar(10) NOT NULL,
  `MaNguyenLieu` varchar(10) NOT NULL,
  `DinhMucSuDung` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Lượng NL cần cho 1 đơn vị SP'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Định mức sử dụng nguyên liệu (BOM)';

--
-- Đang đổ dữ liệu cho bảng `dinhmucnguyenlieu`
--

INSERT INTO `dinhmucnguyenlieu` (`MaDinhMuc`, `MaSanPham`, `MaNguyenLieu`, `DinhMucSuDung`) VALUES
('DM01', 'SP01', 'NL01', 2.50),
('DM02', 'SP01', 'NL02', 0.10),
('DM03', 'SP02', 'NL03', 2.00),
('DM04', 'SP03', 'NL08', 3.00),
('DM05', 'SP03', 'NL03', 2.00),
('DM06', 'SP03', 'NL05', 0.50),
('DM07', 'SP04', 'NL04', 3.50),
('DM08', 'SP04', 'NL06', 2.00),
('DM09', 'SP04', 'NL05', 0.75),
('DM10', 'SP05', 'NL07', 2.00),
('DM11', 'SP05', 'NL01', 0.50);

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
('DH01', 'Đơn hàng Tháng 11', '2025-10-01', 'Đang xử lý'),
('DH02', 'Đơn hàng xuất Nhật', '2025-10-10', 'Đang xử lý'),
('DH03', 'Đơn hàng T11 - Đại lý A', '2025-10-25', 'Đang xử lý'),
('DH04', 'Đơn hàng T11 - Khách lẻ B', '2025-10-28', 'Đang xử lý'),
('DH05', 'Đơn hàng cuối năm - KH A', '2025-10-30', 'Đang xử lý'),
('DH06', 'Đơn hàng xuất khẩu T12', '2025-10-30', 'Đang xử lý'),
('DH07', 'Đơn hàng B2B - 3 SP', '2025-10-30', 'Đang xử lý'),
('DH08', 'Đơn hàng lẻ (SP mới)', '2025-10-31', 'Đang xử lý'),
('DH09', 'Đơn hàng Tết 2026', '2025-10-31', 'Đang xử lý');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kehoachcapxuong`
--

CREATE TABLE `kehoachcapxuong` (
  `MaKHCapXuong` varchar(20) NOT NULL,
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
('KCX-20251130-03', 'KH03', 'PX01', '2025-11-30 16:42:08', 300, 0.00, 'Chờ duyệt');

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
  `ChiPhiNguyenLieu` decimal(15,2) NOT NULL DEFAULT 0.00,
  `ChiPhiNhanCong` decimal(15,2) NOT NULL DEFAULT 0.00,
  `ChiPhiKhac` decimal(15,2) NOT NULL DEFAULT 0.00,
  `SoLuongCongNhanCan` int(11) NOT NULL DEFAULT 0,
  `NgayLap` datetime NOT NULL DEFAULT current_timestamp(),
  `GhiChu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Kế hoạch sản xuất tổng';

--
-- Đang đổ dữ liệu cho bảng `kehoachsanxuat`
--

INSERT INTO `kehoachsanxuat` (`MaKeHoach`, `TenKeHoach`, `NgayBatDau`, `NgayKetThuc`, `MaNV`, `MaDonHang`, `TrangThai`, `TongChiPhiDuKien`, `ChiPhiNguyenLieu`, `ChiPhiNhanCong`, `ChiPhiKhac`, `SoLuongCongNhanCan`, `NgayLap`, `GhiChu`) VALUES
('KH01', 'Kế hoạch cho ĐH01', '2025-11-24', '2025-12-30', 'NKH001', 'DH01', 'Đã duyệt', 50000000.00, 0.00, 0.00, 0.00, 20, '2025-10-28 01:10:07', 'Ưu tiên sản xuất SP01'),
('KH02', 'Kế hoạch cho ĐH02', '2025-11-10', '2025-12-28', 'NKH001', 'DH02', 'Chờ duyệt', 200000000.00, 0.00, 0.00, 0.00, 50, '2025-10-28 01:10:07', 'Hàng xuất khẩu, yêu cầu QC kỹ'),
('KH03', 'Kế hoạch cho ĐH03', '2025-11-05', '2025-11-20', 'NKH001', 'DH03', 'Đã duyệt', 150000000.00, 0.00, 0.00, 0.00, 40, '2025-10-30 01:25:01', 'Hàng Đại lý A, ưu tiên'),
('KH04', 'KH cho ĐH05 (Boot nữ)', '2025-11-20', '2025-12-10', 'NKH001', 'DH05', 'Chờ duyệt', 90000000.00, 0.00, 0.00, 0.00, 30, '2025-10-30 04:10:00', 'Plan 1 - Chờ duyệt'),
('KH05', 'KH cho ĐH06 (XK T12)', '2025-11-22', '2025-12-15', 'NKH001', 'DH06', 'Đã duyệt', 120000000.00, 0.00, 0.00, 0.00, 45, '2025-10-30 04:11:00', 'Plan 2 - Đã duyệt (Đủ NVL)'),
('KH06', 'KH cho ĐH07 (B2B - 3SP)', '2025-11-25', '2025-12-20', 'NKH001', 'DH07', 'Đã duyệt', 85000000.00, 0.00, 0.00, 0.00, 35, '2025-10-30 04:12:00', 'Cảnh báo: Thiếu Nguyên vật liệu NL08 (Da bê). Cần 150 m2, Tồn kho 10 m2.'),
('KH07', 'Kế hoạch T10_25 DH02', '2025-10-31', '2025-11-02', 'NKH001', 'DH08', 'Chờ duyệt', 19323150.00, 17083000.00, 1320000.00, 920150.00, 0, '2025-10-30 15:33:39', '');

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
  `TrangThai` varchar(50) NOT NULL DEFAULT 'Đã kiểm tra',
  `GhiChu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Kết quả kiểm định lô sản phẩm';

--
-- Đang đổ dữ liệu cho bảng `ketquakiemdinh`
--

INSERT INTO `ketquakiemdinh` (`MaKD`, `MaLoHang`, `MaPhieuKT`, `NgayLap`, `MaNV`, `KetQua`, `TrangThai`, `GhiChu`) VALUES
('KD01', 'LH01', 'PYCQC01', '2025-10-29 09:00:00', 'NQC001', 'Đạt', 'Đã kiểm tra', NULL),
('KD02', 'LH02', 'PYCQC02', '2025-10-30 09:00:00', 'NQC001', 'Chờ xử lý', 'Chờ kiểm tra', NULL),
('KD03', 'LH03', 'PYCQC03', '2025-10-30 13:40:31', 'NQC001', 'Đạt', 'Đã kiểm tra', NULL),
('KDf323d0', 'LH02', 'PYCQC02', '2025-10-30 15:44:31', 'NQC001', 'Đạt', 'Đã kiểm tra', NULL),
('KDf4ad96', 'LH01', 'PYCQC05', '2025-10-30 15:48:31', 'NQC001', 'Đạt', 'Đã kiểm tra', NULL),
('KH04', 'LH04', 'PYCQC04', '2025-10-16 15:12:20', 'NQC001', 'Đạt', 'Đã kiểm tra', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lenhsanxuat`
--

CREATE TABLE `lenhsanxuat` (
  `MaLenhSX` int(11) NOT NULL COMMENT 'Mã lệnh sản xuất tự tăng',
  `ma_ke_hoach_tong` varchar(10) NOT NULL COMMENT 'FK Kế hoạch tổng',
  `ngay_lap_lenh` date NOT NULL COMMENT 'Ngày lập lệnh cấp xưởng',
  `ma_day_chuyen` varchar(10) NOT NULL COMMENT 'FK Dây chuyền',
  `ma_to_truong` varchar(10) NOT NULL COMMENT 'FK Tổ trưởng',
  `san_luong_muc_tieu` int(11) NOT NULL DEFAULT 0 COMMENT 'Sản lượng được giao',
  `san_luong_thuc_te` int(11) NOT NULL DEFAULT 0 COMMENT 'Sản lượng đã hoàn thành',
  `trang_thai` varchar(50) NOT NULL DEFAULT 'Mới tạo' COMMENT 'Mới tạo, Đang thực hiện, Hoàn thành',
  `ngay_bat_dau_thuc_te` date DEFAULT NULL,
  `ngay_ket_thuc_thuc_te` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Lệnh sản xuất chi tiết cho từng dây chuyền';

--
-- Đang đổ dữ liệu cho bảng `lenhsanxuat`
--

INSERT INTO `lenhsanxuat` (`MaLenhSX`, `ma_ke_hoach_tong`, `ngay_lap_lenh`, `ma_day_chuyen`, `ma_to_truong`, `san_luong_muc_tieu`, `san_luong_thuc_te`, `trang_thai`, `ngay_bat_dau_thuc_te`, `ngay_ket_thuc_thuc_te`) VALUES
(31, 'KH03', '2025-11-30', 'DC01', 'NTT001', 300, 0, 'Mới tạo', '2025-11-05', '2025-11-20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lohang`
--

CREATE TABLE `lohang` (
  `MaLoHang` varchar(10) NOT NULL,
  `MaSanPham` varchar(10) NOT NULL,
  `SoLuong` int(11) NOT NULL DEFAULT 0,
  `TrangThaiQC` varchar(20) NOT NULL DEFAULT 'Chưa kiểm',
  `TrangThaiKho` varchar(20) NOT NULL DEFAULT 'Chưa nhập kho' COMMENT 'Trạng thái nhập kho (Chưa nhập kho, Đã nhập kho)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Lô sản phẩm để QC và nhập kho';

--
-- Đang đổ dữ liệu cho bảng `lohang`
--

INSERT INTO `lohang` (`MaLoHang`, `MaSanPham`, `SoLuong`, `TrangThaiQC`, `TrangThaiKho`) VALUES
('LH01', 'SP01', 100, 'Đã kiểm', 'Chưa nhập kho'),
('LH02', 'SP02', 50, 'Chưa kiểm', 'Chưa nhập kho'),
('LH03', 'SP03', 50, 'Đã kiểm', 'Chưa nhập kho'),
('LH04', 'SP04', 300, 'Chưa kiểm', 'Chưa nhập kho');

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
('NL03', 'Đế giày cao su', 'Phụ kiện', 'cái', 1000.00, 12000.00, '2026-01-01', 'NCC01', '2025-10-20 17:15:58'),
('NL04', 'Da lộn cao cấp', 'Da', 'm2', 200.00, 180000.00, '2026-01-01', 'NCC01', '2025-10-30 04:00:00'),
('NL05', 'Chỉ may polyester (cuộn 500m)', 'Phụ kiện', 'cuộn', 1000.00, 5000.00, '2026-01-01', 'NCC02', '2025-10-30 04:00:00'),
('NL06', 'Đế boot cao su (nữ)', 'Phụ kiện', 'cái', 300.00, 35000.00, '2026-01-01', 'NCC01', '2025-10-30 04:00:00'),
('NL07', 'Đế dép xốp EVA', 'Phụ kiện', 'cái', 500.00, 8000.00, '2026-01-01', 'NCC01', '2025-10-30 04:00:00'),
('NL08', 'Da bê (cho SP03)', 'Da', 'm2', 10.00, 250000.00, '2026-01-01', 'NCC01', '2025-10-30 04:00:00'),
('NL09', 'Lót giày êm', 'Phụ kiện', 'cái', 1000.00, 7000.00, '2026-01-01', 'NCC02', '2025-10-30 04:00:00');

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
('NCC01', 'Công ty TNHH Da Nam Long', 'Q12, TP.HCM', '0908123123', 'namlong@da.vn'),
('NCC02', 'Công ty TNHH Keo Dán Việt', 'Bình Dương', '0908456789', 'keodanviet@gmail.com');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhanvien`
--

CREATE TABLE `nhanvien` (
  `MaNV` varchar(10) NOT NULL COMMENT 'PK nhân viên (NDBGD25...)',
  `HoTen` varchar(100) NOT NULL,
  `GioiTinh` enum('Nam','Nữ') NOT NULL,
  `NamSinh` year(4) NOT NULL,
  `ChucVu` varchar(50) NOT NULL,
  `BoPhan` varchar(50) DEFAULT NULL,
  `SoDienThoai` varchar(15) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'Đang làm việc'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Danh sách nhân viên';

--
-- Đang đổ dữ liệu cho bảng `nhanvien`
--

INSERT INTO `nhanvien` (`MaNV`, `HoTen`, `GioiTinh`, `NamSinh`, `ChucVu`, `BoPhan`, `SoDienThoai`, `Password`, `TrangThai`) VALUES
('CN001', 'Nguyễn Văn A', 'Nam', '1990', 'CN', 'Dây chuyền 1', '090800001', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN002', 'Trần Văn B', 'Nam', '1990', 'CN', 'Dây chuyền 1', '090800002', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN003', 'Lê Văn C', 'Nam', '1990', 'CN', 'Dây chuyền 2', '090800003', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN004', 'Phạm Văn D', 'Nam', '1990', 'CN', 'Dây chuyền 2', '090800004', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN005', 'Đỗ Văn E', 'Nam', '1990', 'CN', 'Dây chuyền 3', '090800005', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN006', 'Nguyễn Văn 06', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800006', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN007', 'Nguyễn Văn 07', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800007', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN008', 'Nguyễn Văn 08', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800008', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN009', 'Nguyễn Văn 09', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800009', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN010', 'Nguyễn Văn 10', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800010', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN011', 'Nguyễn Văn 11', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800011', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN012', 'Nguyễn Văn 12', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800012', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN013', 'Nguyễn Văn 13', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800013', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN014', 'Nguyễn Văn 14', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800014', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN015', 'Nguyễn Văn 15', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800015', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN016', 'Nguyễn Văn 16', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800016', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN017', 'Nguyễn Văn 17', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800017', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN018', 'Nguyễn Văn 18', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800018', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN019', 'Nguyễn Văn 19', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800019', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN020', 'Nguyễn Văn 20', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800020', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN021', 'Nguyễn Văn 21', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800021', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN022', 'Nguyễn Văn 22', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800022', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN023', 'Nguyễn Văn 23', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800023', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN024', 'Nguyễn Văn 24', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800024', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN025', 'Nguyễn Văn 25', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800025', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN026', 'Nguyễn Văn 26', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800026', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN027', 'Nguyễn Văn 27', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800027', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN028', 'Nguyễn Văn 28', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800028', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN029', 'Nguyễn Văn 29', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800029', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN030', 'Nguyễn Văn 30', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800030', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN031', 'Nguyễn Văn 31', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800031', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN032', 'Nguyễn Văn 32', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800032', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN033', 'Nguyễn Văn 33', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800033', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN034', 'Nguyễn Văn 34', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800034', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN035', 'Nguyễn Văn 35', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800035', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN036', 'Nguyễn Văn 36', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800036', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN037', 'Nguyễn Văn 37', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800037', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN038', 'Nguyễn Văn 38', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800038', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN039', 'Nguyễn Văn 39', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800039', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('CN040', 'Nguyễn Văn 40', 'Nam', '1990', 'CN', 'Phân xưởng 1', '090800040', '$2y$10$wZP7oRXF.r8yYwZHwRWGPOdY4v0AuBqQH9iGzKB8KpYp1b7ZfMBqS', 'Đang làm việc'),
('NAD001', 'Lư Minh Thuận', 'Nam', '1995', 'ADMIN', 'Phòng IT', '0908000008', '$2y$10$cHGFwvQW3FyQVJRNAzWWT.DXlhjr/2gtNkHltakxAXjVLIEwYZGji', 'Đang làm việc'),
('NBGD001', 'Nguyễn Nhật Minh Quang', 'Nam', '1995', 'BGD', 'Ban Giám Đốc', '0901000001', '$2y$10$4aeZcC4/aFqA4lzkyfG3WOe1Zo7PHFENUvhSFY1iNaRqC9l2zT1yi', 'Đang làm việc'),
('NCN001', 'Huỳnh Văn Nam', 'Nam', '1995', 'CN', 'Dây chuyền 2', '0907000007', '$2y$10$GyJpC20HAKai0b0on8/cEuW7rQcO52zD5HaJ0U1xlmDw/nnwEznc6', 'Đang làm việc'),
('NKH001', 'Trần Thanh Trường', 'Nam', '1995', 'KH', 'Phòng Kế Hoạch', '0902000002', '$2y$10$sAQd1gCY2LRem4s9BnAO1uq8FvGW/7t64zlNf1xWSF1mOIXf7e6LC', 'Đang làm việc'),
('NQC001', 'Phạm Thị Ngọc Diễm', 'Nam', '1995', 'QC', 'Phòng Kiểm Định', '0905000005', '$2y$10$ob6Jlaruf4oCTXYu4bYSTukp2fc9GuXb7rUkMNdndHsEkZpMxjuQu', 'Đang làm việc'),
('NTT001', 'Trương Huỳnh Kim Yến', 'Nam', '1995', 'TT', 'Dây chuyền 1', '0904000004', '$2y$10$AObMwjQdWifQBa8MOchr1.iSgEkHUsxhAZfBQnv2k3QRLOC9j4eR2', 'Đang làm việc'),
('NTT002', 'Lê Văn Cường', 'Nam', '1995', 'TT', 'Dây chuyền 2', '0904000002', '$2y$10$WIsUmtybJXQHIVEIYXGorOWlU6xPLRsi2xQ4PR5srSNcrcW/AvYL6', 'Đang làm việc'),
('NTT003', 'Nguyễn Thị Tâm', 'Nam', '1995', 'TT', 'Dây chuyền 3', '0904000003', '$2y$10$6Oo/gmKHXaqstYky3OzRs.zqK3PwjlBQIB.IYcEerWZFNf9HMUiHq', 'Đang làm việc'),
('NTT004', 'Phạm Văn Thành', 'Nam', '1995', 'TT', 'Dây chuyền 4', '0904000004', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzABCD1', 'Đang làm việc'),
('NTT005', 'Lê Hoàng Phúc', 'Nam', '1995', 'TT', 'Dây chuyền 5', '0904000005', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzABCD2', 'Đang làm việc'),
('NTT006', 'Ngô Minh Hòa', 'Nam', '1995', 'TT', 'Dây chuyền 6', '0904000006', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzABCD3', 'Đang làm việc'),
('NTT007', 'Trần Nhật Tân', 'Nam', '1995', 'TT', 'Dây chuyền 7', '0904000007', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzABCD4', 'Đang làm việc'),
('NTT008', 'Võ Thanh Duy', 'Nam', '1995', 'TT', 'Dây chuyền 8', '0904000008', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzABCD5', 'Đang làm việc'),
('NTT009', 'Đỗ Hữu Trí', 'Nam', '1995', 'TT', 'Dây chuyền 9', '0904000009', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzABCD6', 'Đang làm việc'),
('NTT010', 'Nguyễn Tấn Khải', 'Nam', '1995', 'TT', 'Dây chuyền 10', '0904000010', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzABCD7', 'Đang làm việc'),
('NTT011', 'Nguyễn Văn Hải', 'Nam', '1995', 'TT', 'Dây chuyền 1', '0904000011', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzA11', 'Đang làm việc'),
('NTT012', 'Lê Quốc Hưng', 'Nam', '1995', 'TT', 'Dây chuyền 2', '0904000012', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzA12', 'Đang làm việc'),
('NTT013', 'Phạm Hoàng Duy', 'Nam', '1995', 'TT', 'Dây chuyền 3', '0904000013', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzA13', 'Đang làm việc'),
('NTT014', 'Đỗ Quốc Việt', 'Nam', '1995', 'TT', 'Dây chuyền 4', '0904000014', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzA14', 'Đang làm việc'),
('NTT015', 'Ngô Anh Kiệt', 'Nam', '1995', 'TT', 'Dây chuyền 5', '0904000015', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzA15', 'Đang làm việc'),
('NTT016', 'Trần Minh Khoa', 'Nam', '1995', 'TT', 'Dây chuyền 6', '0904000016', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzA16', 'Đang làm việc'),
('NTT017', 'Võ Tấn Phát', 'Nam', '1995', 'TT', 'Dây chuyền 7', '0904000017', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzA17', 'Đang làm việc'),
('NTT018', 'Hồ Gia Bảo', 'Nam', '1995', 'TT', 'Dây chuyền 8', '0904000018', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzA18', 'Đang làm việc'),
('NTT019', 'Lưu Hoàng Tú', 'Nam', '1995', 'TT', 'Dây chuyền 9', '0904000019', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzA19', 'Đang làm việc'),
('NTT020', 'Nguyễn Trọng Tín', 'Nam', '1995', 'TT', 'Dây chuyền 10', '0904000020', '$2y$10$abcdabcdabcdabcdabcdabCdEfGhIjKlMnOpQrStUvWxYzA20', 'Đang làm việc'),
('NVK001', 'Phạm Thành Khang', 'Nam', '1995', 'NVK', 'Kho Nguyên Liệu', '0906000006', '$2y$10$E08sjh6C4ityXIL5nW6SOewQ5OZJ3WfX79NlKWUGNosAjJASqBk6i', 'Đang làm việc'),
('NXT001', 'Lư Minh Thuận', 'Nam', '1995', 'XT', 'Phân Xưởng 1', '0903000003', '$2y$10$AByV5tdEBi7dIj95hSaBnOP.a4yNwuoQShYDUNnTWXu2fMv1myuM2', 'Đang làm việc'),
('NXT002', 'Nguyễn Văn Hậu', 'Nam', '1995', 'XT', 'Phân xưởng 2', '0903000004', '$2y$10$q011V/tMftYS2XAflRT0muR3hGX8Z8CJ5qCFqVTPpnIG6IcXYSH6i', 'Đang làm việc'),
('NXT003', 'Lê Hoàng Phúc', 'Nam', '1995', 'XT', 'Phân xưởng 3', '0903000005', '$2y$10$AbCdEf1234567890AbCdEfO12abcdEfghijkLmnoPqrstuV', 'Đang làm việc'),
('NXT004', 'Trần Thị Ngọc Hân', 'Nam', '1995', 'XT', 'Phân xưởng 4', '0903000006', '$2y$10$ZxcVbnm123456789QWERtyuIoPAsdFghJklMnOpQrstu12', 'Đang làm việc'),
('NXT005', 'Võ Thành Đạt', 'Nam', '1995', 'XT', 'Phân xưởng 5', '0903000007', '$2y$10$FghJKLmnOp123456789QweRTYuiopASDfghJKLmnBvCxz12', 'Đang làm việc'),
('NXT006', 'Nguyễn Thùy Tiên', 'Nam', '1995', 'XT', 'Phân xưởng 6', '0903000008', '$2y$10$QweRtyUiop123456789AsDfGhJkLzXcVbNmQweRtyUiop12', 'Đang làm việc'),
('NXT007', 'Đặng Minh Khang', 'Nam', '1995', 'XT', 'Phân xưởng 7', '0903000009', '$2y$10$MnBvCxZ123456789QWerTyUiOpAsDfGhJkLzXcVbNmErT12', 'Đang làm việc'),
('NXT008', 'Hồ Quỳnh Như', 'Nam', '1995', 'XT', 'Phân xưởng 8', '0903000010', '$2y$10$LpOiUyTrEwQ123456789AsDfGhJkLpOiUyTrEwQmNbVcX12', 'Đang làm việc'),
('NXT009', 'Phạm Quốc Huy', 'Nam', '1995', 'XT', 'Phân xưởng 9', '0903000011', '$2y$10$QazWsxEdC123456789RfVtGbYhnUjMikOlPqAzWsXeDc12', 'Đang làm việc'),
('NXT010', 'Nguyễn Thảo Vy', 'Nam', '1995', 'XT', 'Phân xưởng 10', '0903000012', '$2y$10$VfRgTgBhN123456789UjMiKoLpOiUhYgTfrEdCvBgNhUj12', 'Đang làm việc');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhanvien_calam`
--

CREATE TABLE `nhanvien_calam` (
  `MaNV` varchar(10) NOT NULL,
  `MaCa` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Liên kết Công nhân - Ca làm việc';

--
-- Đang đổ dữ liệu cho bảng `nhanvien_calam`
--

INSERT INTO `nhanvien_calam` (`MaNV`, `MaCa`) VALUES
('CN001', 'morning'),
('CN002', 'morning'),
('CN003', 'morning'),
('CN004', 'morning'),
('CN005', 'morning'),
('CN006', 'morning'),
('CN007', 'morning'),
('CN008', 'morning'),
('CN009', 'morning'),
('CN010', 'morning'),
('CN011', 'morning'),
('CN012', 'morning'),
('CN013', 'morning'),
('CN014', 'morning'),
('CN015', 'morning'),
('CN016', 'morning'),
('CN017', 'morning'),
('CN018', 'morning'),
('CN019', 'morning'),
('CN020', 'morning'),
('CN021', 'afternoon'),
('CN022', 'afternoon'),
('CN023', 'afternoon'),
('CN024', 'afternoon'),
('CN025', 'afternoon'),
('CN026', 'afternoon'),
('CN027', 'afternoon'),
('CN028', 'afternoon'),
('CN029', 'afternoon'),
('CN030', 'afternoon'),
('CN031', 'afternoon'),
('CN032', 'afternoon'),
('CN033', 'afternoon'),
('CN034', 'afternoon'),
('CN035', 'afternoon'),
('CN036', 'afternoon'),
('CN037', 'afternoon'),
('CN038', 'afternoon'),
('CN039', 'afternoon'),
('CN040', 'afternoon');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phancongcalam`
--

CREATE TABLE `phancongcalam` (
  `Id` int(11) NOT NULL,
  `MaKHCapXuong` varchar(20) NOT NULL,
  `MaCa` varchar(10) NOT NULL,
  `NgayBatDau` date NOT NULL,
  `NgayKetThuc` date NOT NULL,
  `TyLe` int(11) NOT NULL DEFAULT 0,
  `SoLuong` int(11) NOT NULL DEFAULT 0,
  `CreatedBy` varchar(20) DEFAULT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `phancongcalam`
--

INSERT INTO `phancongcalam` (`Id`, `MaKHCapXuong`, `MaCa`, `NgayBatDau`, `NgayKetThuc`, `TyLe`, `SoLuong`, `CreatedBy`, `CreatedAt`) VALUES
(3, 'P001', 'morning', '2025-11-01', '2025-11-15', 63, 315, NULL, '2025-11-01 11:20:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phancongcalam_nhanvien`
--

CREATE TABLE `phancongcalam_nhanvien` (
  `PhanCongId` int(11) NOT NULL,
  `MaNV` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `phancongcalam_nhanvien`
--

INSERT INTO `phancongcalam_nhanvien` (`PhanCongId`, `MaNV`) VALUES
(3, 'CN001'),
(3, 'CN005'),
(3, 'CN009'),
(3, 'CN013'),
(3, 'CN017'),
(3, 'CN002'),
(3, 'CN006'),
(3, 'CN010'),
(3, 'CN014'),
(3, 'CN018'),
(3, 'CN019'),
(3, 'CN015'),
(3, 'CN011'),
(3, 'CN007'),
(3, 'CN003'),
(3, 'CN004'),
(3, 'CN008'),
(3, 'CN012'),
(3, 'CN016'),
(3, 'CN020');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phanxuong`
--

CREATE TABLE `phanxuong` (
  `MaPhanXuong` varchar(10) NOT NULL,
  `TenPhanXuong` varchar(100) NOT NULL,
  `MaXuongTruong` varchar(10) DEFAULT NULL COMMENT 'Mã nhân viên làm xưởng trưởng',
  `MoTa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Phân xưởng (ví dụ: Cắt, May, Đóng gói)';

--
-- Đang đổ dữ liệu cho bảng `phanxuong`
--

INSERT INTO `phanxuong` (`MaPhanXuong`, `TenPhanXuong`, `MaXuongTruong`, `MoTa`) VALUES
('PX01', 'Phân xưởng 1', 'NXT001', ''),
('PX02', 'Phân xưởng 2', 'NXT002', ''),
('PX03', 'Phân xưởng 3', 'NXT003', ''),
('PX04', 'Phân xưởng 4', 'NXT004', NULL),
('PX05', 'Phân xưởng 5', 'NXT005', NULL),
('PX06', 'Phân xưởng 6', 'NXT006', NULL),
('PX07', 'Phân xưởng 7', 'NXT007', NULL),
('PX08', 'Phân xưởng 8', 'NXT008', NULL),
('PX09', 'Phân xưởng 9', 'NXT009', NULL),
('PX10', 'Phân xưởng 10', 'NXT010', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieudatnvl`
--

CREATE TABLE `phieudatnvl` (
  `MaPhieu` varchar(10) NOT NULL COMMENT 'PK - Mã phiếu (VD: PDNL01)',
  `TenPhieu` varchar(255) NOT NULL COMMENT 'Tên phiếu đặt NVL',
  `NgayLapPhieu` date NOT NULL COMMENT 'Ngày lập phiếu',
  `NguoiLapPhieu` varchar(100) DEFAULT NULL COMMENT 'Tên người lập (từ session)',
  `MaKHSX` varchar(10) DEFAULT NULL COMMENT 'FK - Kế hoạch SX liên quan (nếu có)',
  `MaNhaCungCap` varchar(10) DEFAULT NULL COMMENT 'FK - Ma Nha Cung Cap',
  `TongChiPhiDuKien` decimal(15,2) NOT NULL DEFAULT 0.00,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'Chờ duyệt' COMMENT 'Trạng thái phiếu: Chờ duyệt, Đã duyệt, Đã hủy'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Phiếu đặt hàng Nguyên vật liệu (Purchase Order)';

--
-- Đang đổ dữ liệu cho bảng `phieudatnvl`
--

INSERT INTO `phieudatnvl` (`MaPhieu`, `TenPhieu`, `NgayLapPhieu`, `NguoiLapPhieu`, `MaKHSX`, `MaNhaCungCap`, `TongChiPhiDuKien`, `TrangThai`) VALUES
('PDNL01', 'Phiếu đặt Da bò và Keo cho KHSX01', '2025-10-29', 'Trần Thị Bình', 'KH01', 'NCC01', 38750000.00, 'Đã duyệt'),
('PDNL02', 'Phiếu đặt Đế cao su (bổ sung)', '2025-10-30', 'Trần Thị Bình', NULL, 'NCC01', 12000000.00, 'Đã duyệt'),
('PDNL03', 'Phiếu NVL cho Kế hoạch cho ĐH03 (KH03)', '2025-10-30', 'Trần Thị Bình', 'KH03', 'NCC02', 0.00, 'Đã duyệt');

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
('PNNL01', 'NCC01', '2025-10-25 09:00:00', 'NVK001', 51000000.00),
('PNNL02', 'NCC02', '2025-10-26 09:00:00', 'NVK001', 10000000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieunhapsanpham`
--

CREATE TABLE `phieunhapsanpham` (
  `MaPhieuNhap` varchar(10) NOT NULL,
  `MaKD` varchar(10) DEFAULT NULL,
  `NgayNhap` datetime NOT NULL DEFAULT current_timestamp(),
  `MaNhanVien` varchar(10) DEFAULT NULL,
  `MaLoHang` varchar(10) NOT NULL,
  `GhiChu` text DEFAULT NULL COMMENT 'Ghi chú khi nhập kho'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Phiếu nhập thành phẩm vào kho (sau QC)';

--
-- Đang đổ dữ liệu cho bảng `phieunhapsanpham`
--

INSERT INTO `phieunhapsanpham` (`MaPhieuNhap`, `MaKD`, `NgayNhap`, `MaNhanVien`, `MaLoHang`, `GhiChu`) VALUES
('PNSP01', 'KD01', '2025-10-30 15:01:43', 'NVK001', 'LH01', NULL),
('PNSP02', NULL, '2025-10-30 09:15:00', 'NVK001', 'LH02', NULL),
('PNTP202510', 'KH04', '2025-10-30 15:20:07', 'NVK001', 'LH04', NULL);

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
('PXNL01', 'PYCNL01', '2025-10-28', 'NVK001', 'Đã duyệt'),
('PXNL02', 'PYCNL02', '2025-10-29', 'NVK001', 'Chờ duyệt');

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
('PYCQC01', 'LH01', '2025-10-29', 'NTT001', 'Đã kiểm tra', 'Đạt'),
('PYCQC02', 'LH02', '2025-10-30', 'NTT001', 'Đã kiểm định - Đạt', 'Đạt'),
('PYCQC03', 'LH03', '2025-10-30', 'NTT001', 'Đã kiểm tra', 'Đạt'),
('PYCQC04', 'LH03', '2025-10-23', 'NXT002', 'Đã kiểm định - Đạt', 'Đạt'),
('PYCQC05', 'LH01', '2025-10-29', 'NXT001', 'Đã kiểm định - Đạt', 'Đạt');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phieuyeucauxuatnguyenlieu`
--

CREATE TABLE `phieuyeucauxuatnguyenlieu` (
  `MaPhieuYC` varchar(10) NOT NULL,
  `MaKeHoach` varchar(10) NOT NULL,
  `MaPhanXuong` varchar(10) NOT NULL,
  `NgayLap` datetime NOT NULL DEFAULT current_timestamp(),
  `MaNV` varchar(10) NOT NULL,
  `TrangThai` varchar(20) NOT NULL DEFAULT 'Chờ duyệt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Yêu cầu xuất NL từ xưởng';

--
-- Đang đổ dữ liệu cho bảng `phieuyeucauxuatnguyenlieu`
--

INSERT INTO `phieuyeucauxuatnguyenlieu` (`MaPhieuYC`, `MaKeHoach`, `MaPhanXuong`, `NgayLap`, `MaNV`, `TrangThai`) VALUES
('PYCNL01', 'KH01', 'PX01', '2025-10-28 08:00:00', 'NXT001', 'Đã duyệt'),
('PYCNL02', 'KH02', 'PX02', '2025-10-28 09:00:00', 'NXT001', 'Chờ duyệt');

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
('SP02', 'Giày sandal 4EVER Summer', '40', 'Nâu', 'Thoáng mát, nhẹ', 300000.00),
('SP03', 'Giày da nam 4EVER Classic', '41', 'Đen', 'Giày da công sở, đế khâu', 750000.00),
('SP04', 'Giày boot nữ 4EVER Rebel', '38', 'Nâu Đất', 'Boot cổ cao, da lộn', 890000.00),
('SP05', 'Dép quai ngang 4EVER Comfy', '39', 'Trắng', 'Dép đi trong nhà, đế EVA', 150000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `system_logs`
--

CREATE TABLE `system_logs` (
  `MaLog` int(11) NOT NULL,
  `user_id` varchar(10) DEFAULT NULL COMMENT 'MaNV của người thực hiện',
  `action` varchar(50) NOT NULL COMMENT 'Hành động (vd: login, logout, create, update, delete)',
  `table_name` varchar(50) DEFAULT NULL COMMENT 'Bảng bị ảnh hưởng',
  `record_id` varchar(50) DEFAULT NULL COMMENT 'ID của bản ghi bị ảnh hưởng',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Nhật ký hoạt động hệ thống';

--
-- Đang đổ dữ liệu cho bảng `system_logs`
--

INSERT INTO `system_logs` (`MaLog`, `user_id`, `action`, `table_name`, `record_id`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 'NKH001', 'login', 'nhanvien', 'NKH001', '127.0.0.1', 'Mozilla/5.0...', '2025-10-30 05:49:03'),
(2, 'NKH001', 'create', 'kehoachsanxuat', 'KHSX02', '127.0.0.1', 'Mozilla/5.0...', '2025-10-30 05:50:11'),
(3, 'NVK001', 'update', 'nguyenlieu', 'NL01', '127.0.0.1', 'Mozilla/5.0...', '2025-10-30 05:52:00'),
(4, 'NXT001', 'logout', 'users', 'NXT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 01:59:58'),
(5, 'NKH001', 'logout', 'users', 'NKH001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 02:02:02'),
(6, 'NXT001', 'logout', 'users', 'NXT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 02:22:07'),
(7, 'NKH001', 'logout', 'users', 'NKH001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 02:34:52'),
(8, 'NXT001', 'logout', 'users', 'NXT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 03:46:43'),
(9, 'NVK001', 'logout', 'users', 'NVK001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 03:47:44'),
(10, 'NTT001', 'logout', 'users', 'NTT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 04:02:00'),
(11, 'NKH001', 'logout', 'users', 'NKH001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 04:35:58'),
(12, 'NTT001', 'logout', 'users', 'NTT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 04:44:31'),
(13, 'NVK001', 'logout', 'users', 'NVK001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 04:45:03'),
(14, 'NQC001', 'logout', 'users', 'NQC001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 04:47:03'),
(15, 'NVK001', 'logout', 'users', 'NVK001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 04:56:44'),
(16, 'NTT001', 'logout', 'users', 'NTT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 05:01:56'),
(17, 'NKH001', 'logout', 'users', 'NKH001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 06:19:55'),
(18, 'NXT001', 'logout', 'users', 'NXT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 07:52:28'),
(19, 'NKH001', 'logout', 'users', 'NKH001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 07:59:39'),
(20, 'NVK001', 'logout', 'users', 'NVK001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 08:02:12'),
(21, 'NTT001', 'logout', 'users', 'NTT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 08:02:40'),
(22, 'NQC001', 'logout', 'users', 'NQC001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 08:07:03'),
(23, 'NVK001', 'logout', 'users', 'NVK001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 08:09:27'),
(24, 'NKH001', 'logout', 'users', 'NKH001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 08:09:44'),
(25, 'NVK001', 'logout', 'users', 'NVK001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 08:13:17'),
(26, 'NXT001', 'logout', 'users', 'NXT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 08:13:43'),
(27, 'NVK001', 'logout', 'users', 'NVK001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 08:17:13'),
(28, 'NVK001', 'logout', 'users', 'NVK001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 08:19:38'),
(29, 'NVK001', 'logout', 'users', 'NVK001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 08:21:34'),
(30, 'NTT001', 'logout', 'users', 'NTT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 08:26:51'),
(31, 'NKH001', 'logout', 'users', 'NKH001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 08:36:42'),
(32, 'NXT001', 'logout', 'users', 'NXT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 08:43:09'),
(33, 'NTT001', 'logout', 'users', 'NTT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 08:44:04'),
(34, 'NQC001', 'logout', 'users', 'NQC001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 08:47:04'),
(35, 'NQC001', 'logout', 'users', 'NQC001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-30 08:48:58'),
(36, 'NXT001', 'logout', 'users', 'NXT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-21 13:52:03'),
(37, 'NTT001', 'logout', 'users', 'NTT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-25 13:57:57'),
(38, 'NXT001', 'logout', 'users', 'NXT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-25 14:15:51'),
(39, 'NKH001', 'logout', 'users', 'NKH001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-25 14:19:08'),
(40, 'NKH001', 'logout', 'users', 'NKH001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-25 14:33:42'),
(41, 'NXT001', 'logout', 'users', 'NXT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-25 14:48:37'),
(42, 'NKH001', 'logout', 'users', 'NKH001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-25 14:52:41'),
(43, 'NXT001', 'logout', 'users', 'NXT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-26 04:30:44'),
(44, 'NKH001', 'logout', 'users', 'NKH001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-26 04:31:23'),
(45, 'NXT001', 'logout', 'users', 'NXT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-26 04:37:58'),
(46, 'NTT001', 'logout', 'users', 'NTT001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-26 04:38:05'),
(47, 'NKH001', 'logout', 'users', 'NKH001', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0', '2025-11-26 04:40:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tonkho`
--

CREATE TABLE `tonkho` (
  `id` int(11) NOT NULL COMMENT 'ID tự tăng',
  `MaSanPham` varchar(10) NOT NULL COMMENT 'Mã sản phẩm (FK)',
  `SoLuongHienTai` int(11) NOT NULL DEFAULT 0 COMMENT 'Số lượng hiện tại trong kho',
  `ViTriKho` varchar(50) DEFAULT 'Kho A' COMMENT 'Vị trí lưu trữ trong kho',
  `NgayCapNhat` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày cập nhật cuối',
  `GhiChu` text DEFAULT NULL COMMENT 'Ghi chú bổ sung'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Bảng quản lý tồn kho thành phẩm';

--
-- Đang đổ dữ liệu cho bảng `tonkho`
--

INSERT INTO `tonkho` (`id`, `MaSanPham`, `SoLuongHienTai`, `ViTriKho`, `NgayCapNhat`, `GhiChu`) VALUES
(1, 'SP01', 100, 'Kho A', '2025-10-30 13:37:37', NULL),
(2, 'SP03', 450, 'Kho A', '2025-10-30 15:00:59', NULL),
(7, 'SP04', 600, 'Kho A', '2025-10-30 15:20:07', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `vw_lohangcannhapkho`
-- (See below for the actual view)
--
CREATE TABLE `vw_lohangcannhapkho` (
`MaLoHang` varchar(10)
,`MaSanPham` varchar(10)
,`TenSanPham` varchar(100)
,`Size` varchar(10)
,`Mau` varchar(30)
,`SoLuong` int(11)
,`TrangThaiQC` varchar(20)
,`TrangThaiKho` varchar(20)
,`SoLuongTonKho` int(11)
);

-- --------------------------------------------------------

--
-- Cấu trúc cho view `vw_lohangcannhapkho`
--
DROP TABLE IF EXISTS `vw_lohangcannhapkho`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_lohangcannhapkho`  AS SELECT `lh`.`MaLoHang` AS `MaLoHang`, `lh`.`MaSanPham` AS `MaSanPham`, `sp`.`TenSanPham` AS `TenSanPham`, `sp`.`Size` AS `Size`, `sp`.`Mau` AS `Mau`, `lh`.`SoLuong` AS `SoLuong`, `lh`.`TrangThaiQC` AS `TrangThaiQC`, `lh`.`TrangThaiKho` AS `TrangThaiKho`, coalesce(`tk`.`SoLuongHienTai`,0) AS `SoLuongTonKho` FROM ((`lohang` `lh` join `sanpham` `sp` on(`lh`.`MaSanPham` = `sp`.`MaSanPham`)) left join `tonkho` `tk` on(`lh`.`MaSanPham` = `tk`.`MaSanPham`)) WHERE `lh`.`TrangThaiQC` in ('Đạt','Đã kiểm') ORDER BY `lh`.`MaLoHang` DESC ;

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
  ADD KEY `MaPhanXuong` (`MaPhanXuong`),
  ADD KEY `idx_chitietkehoach_sp` (`MaSanPham`);

--
-- Chỉ mục cho bảng `chitietphieudatnvl`
--
ALTER TABLE `chitietphieudatnvl`
  ADD PRIMARY KEY (`MaChiTiet`),
  ADD KEY `idx_chitietphieudatnvl_maphieu` (`MaPhieu`),
  ADD KEY `idx_chitietphieudatnvl_manvl` (`MaNVL`);

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
  ADD KEY `MaPhanXuong` (`MaPhanXuong`),
  ADD KEY `fk_daychuyen_toruong` (`MaToTruong`);

--
-- Chỉ mục cho bảng `dinhmucnguyenlieu`
--
ALTER TABLE `dinhmucnguyenlieu`
  ADD PRIMARY KEY (`MaDinhMuc`),
  ADD UNIQUE KEY `uk_sp_nl` (`MaSanPham`,`MaNguyenLieu`),
  ADD KEY `MaNguyenLieu` (`MaNguyenLieu`);

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
-- Chỉ mục cho bảng `lenhsanxuat`
--
ALTER TABLE `lenhsanxuat`
  ADD PRIMARY KEY (`MaLenhSX`),
  ADD KEY `ma_ke_hoach_tong` (`ma_ke_hoach_tong`),
  ADD KEY `ma_day_chuyen` (`ma_day_chuyen`),
  ADD KEY `ma_to_truong` (`ma_to_truong`);

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
-- Chỉ mục cho bảng `phancongcalam`
--
ALTER TABLE `phancongcalam`
  ADD PRIMARY KEY (`Id`);

--
-- Chỉ mục cho bảng `phanxuong`
--
ALTER TABLE `phanxuong`
  ADD PRIMARY KEY (`MaPhanXuong`),
  ADD KEY `fk_phanxuong_xuongtruong` (`MaXuongTruong`);

--
-- Chỉ mục cho bảng `phieudatnvl`
--
ALTER TABLE `phieudatnvl`
  ADD PRIMARY KEY (`MaPhieu`),
  ADD KEY `idx_phieudatnvl_khsx` (`MaKHSX`),
  ADD KEY `idx_phieudatnvl_ncc` (`MaNhaCungCap`);

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
-- Chỉ mục cho bảng `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`MaLog`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`);

--
-- Chỉ mục cho bảng `tonkho`
--
ALTER TABLE `tonkho`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tonkho_masp` (`MaSanPham`),
  ADD KEY `idx_tonkho_sanpham` (`MaSanPham`),
  ADD KEY `idx_tonkho_ngaycapnhat` (`NgayCapNhat`);

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
  MODIFY `MaCTDH` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `chitietphieudatnvl`
--
ALTER TABLE `chitietphieudatnvl`
  MODIFY `MaChiTiet` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK - Mã chi tiết tự tăng', AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `lenhsanxuat`
--
ALTER TABLE `lenhsanxuat`
  MODIFY `MaLenhSX` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Mã lệnh sản xuất tự tăng', AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT cho bảng `phancongcalam`
--
ALTER TABLE `phancongcalam`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `MaLog` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT cho bảng `tonkho`
--
ALTER TABLE `tonkho`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID tự tăng', AUTO_INCREMENT=8;

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
  ADD CONSTRAINT `chitietkehoach_ibfk_1` FOREIGN KEY (`MaKeHoach`) REFERENCES `kehoachsanxuat` (`MaKeHoach`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chitietkehoach_ibfk_2` FOREIGN KEY (`MaSanPham`) REFERENCES `sanpham` (`MaSanPham`),
  ADD CONSTRAINT `chitietkehoach_ibfk_3` FOREIGN KEY (`MaPhanXuong`) REFERENCES `phanxuong` (`MaPhanXuong`);

--
-- Các ràng buộc cho bảng `chitietphieudatnvl`
--
ALTER TABLE `chitietphieudatnvl`
  ADD CONSTRAINT `fk_chitietphieudatnvl_manvl` FOREIGN KEY (`MaNVL`) REFERENCES `nguyenlieu` (`MaNguyenLieu`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chitietphieudatnvl_maphieu` FOREIGN KEY (`MaPhieu`) REFERENCES `phieudatnvl` (`MaPhieu`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `daychuyen_ibfk_1` FOREIGN KEY (`MaPhanXuong`) REFERENCES `phanxuong` (`MaPhanXuong`),
  ADD CONSTRAINT `fk_daychuyen_toruong` FOREIGN KEY (`MaToTruong`) REFERENCES `nhanvien` (`MaNV`);

--
-- Các ràng buộc cho bảng `dinhmucnguyenlieu`
--
ALTER TABLE `dinhmucnguyenlieu`
  ADD CONSTRAINT `dinhmucnguyenlieu_ibfk_1` FOREIGN KEY (`MaSanPham`) REFERENCES `sanpham` (`MaSanPham`),
  ADD CONSTRAINT `dinhmucnguyenlieu_ibfk_2` FOREIGN KEY (`MaNguyenLieu`) REFERENCES `nguyenlieu` (`MaNguyenLieu`);

--
-- Các ràng buộc cho bảng `kehoachcapxuong`
--
ALTER TABLE `kehoachcapxuong`
  ADD CONSTRAINT `kehoachcapxuong_ibfk_1` FOREIGN KEY (`MaKeHoach`) REFERENCES `kehoachsanxuat` (`MaKeHoach`) ON DELETE CASCADE ON UPDATE CASCADE,
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
-- Các ràng buộc cho bảng `lenhsanxuat`
--
ALTER TABLE `lenhsanxuat`
  ADD CONSTRAINT `lenhsanxuat_ibfk_1` FOREIGN KEY (`ma_ke_hoach_tong`) REFERENCES `kehoachsanxuat` (`MaKeHoach`) ON DELETE CASCADE,
  ADD CONSTRAINT `lenhsanxuat_ibfk_2` FOREIGN KEY (`ma_day_chuyen`) REFERENCES `daychuyen` (`MaDayChuyen`),
  ADD CONSTRAINT `lenhsanxuat_ibfk_3` FOREIGN KEY (`ma_to_truong`) REFERENCES `nhanvien` (`MaNV`);

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
-- Các ràng buộc cho bảng `phanxuong`
--
ALTER TABLE `phanxuong`
  ADD CONSTRAINT `fk_phanxuong_xuongtruong` FOREIGN KEY (`MaXuongTruong`) REFERENCES `nhanvien` (`MaNV`);

--
-- Các ràng buộc cho bảng `phieudatnvl`
--
ALTER TABLE `phieudatnvl`
  ADD CONSTRAINT `fk_phieudatnvl_khsx` FOREIGN KEY (`MaKHSX`) REFERENCES `kehoachsanxuat` (`MaKeHoach`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_phieudatnvl_ncc` FOREIGN KEY (`MaNhaCungCap`) REFERENCES `nhacungcap` (`MaNhaCungCap`) ON DELETE SET NULL ON UPDATE CASCADE;

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

--
-- Các ràng buộc cho bảng `tonkho`
--
ALTER TABLE `tonkho`
  ADD CONSTRAINT `fk_tonkho_sanpham` FOREIGN KEY (`MaSanPham`) REFERENCES `sanpham` (`MaSanPham`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
