# Module: Nhập Kho Thành Phẩm

## 📋 Tổng Quan

Module **Nhập Kho Thành Phẩm** cho phép nhân viên kho quản lý việc nhập các lô sản phẩm đã được QC duyệt vào kho thành phẩm. Module được xây dựng theo mô hình **MVC (Model-View-Controller)** trong PHP.

## 🎯 Use Case

### Actor
- **Nhân viên kho thành phẩm**

### Mục tiêu
Cập nhật dữ liệu lô hàng đã được QC duyệt vào kho thành phẩm.

### Tiền điều kiện
- Người dùng đã đăng nhập thành công
- Có danh sách thành phẩm đã được QC duyệt (TrangThaiQC = 'Đạt')

### Hậu điều kiện
- Hệ thống hiển thị thông báo "Nhập Thành Công"
- Cập nhật dữ liệu vào CSDL:
  - Bảng `lo_hang`: Cập nhật TrangThaiKho = 'Đã nhập kho'
  - Bảng `phieu_nhap_kho`: Tạo phiếu nhập kho
  - Bảng `ton_kho`: Cập nhật số lượng tồn kho

### Ngoại lệ
- Nếu lỗi kết nối hoặc lỗi cập nhật, hiển thị thông báo "Lỗi kết nối! Vui lòng thử lại sau."

## 📁 Cấu Trúc Files

```
4Ever/
├── app/
│   ├── controllers/
│   │   └── NhapKhoController.php      # Controller xử lý logic
│   ├── models/
│   │   └── NhapKho.php                # Model truy vấn database
│   └── views/
│       └── kho/
│           └── nhap_kho_thanh_pham.php # View giao diện
├── database/
│   ├── qlsx_4ever.sql                # Database gốc
│   └── migration_nhapkho.sql         # Migration thêm bảng/cột
└── docs/
    └── NHAP_KHO_MODULE.md            # Tài liệu này
```

## 🗄️ Cấu Trúc Database

### 1. Bảng LoHang (đã có, được bổ sung)

```sql
CREATE TABLE LoHang (
    MaLoHang VARCHAR(10) PRIMARY KEY,
    MaSanPham VARCHAR(10) NOT NULL,
    SoLuong INT NOT NULL DEFAULT 0,
    TrangThaiQC VARCHAR(20) NOT NULL DEFAULT 'Chưa kiểm',
    TrangThaiKho VARCHAR(20) NOT NULL DEFAULT 'Chưa nhập kho', -- ✨ NEW
    FOREIGN KEY (MaSanPham) REFERENCES SanPham(MaSanPham)
);
```

### 2. Bảng PhieuNhapSanPham (đã có)

```sql
CREATE TABLE PhieuNhapSanPham (
    MaPhieuNhap VARCHAR(10) PRIMARY KEY,
    MaKD VARCHAR(10),
    NgayNhap DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    MaNhanVien VARCHAR(10),
    MaLoHang VARCHAR(10) NOT NULL,
    GhiChu TEXT, -- ✨ NEW
    FOREIGN KEY (MaKD) REFERENCES KetQuaKiemDinh(MaKD),
    FOREIGN KEY (MaNhanVien) REFERENCES NhanVien(MaNV),
    FOREIGN KEY (MaLoHang) REFERENCES LoHang(MaLoHang)
);
```

### 3. Bảng TonKho (MỚI)

```sql
CREATE TABLE TonKho (
    id INT AUTO_INCREMENT PRIMARY KEY,
    MaSanPham VARCHAR(10) NOT NULL,
    SoLuongHienTai INT NOT NULL DEFAULT 0,
    ViTriKho VARCHAR(50) DEFAULT 'Kho A',
    NgayCapNhat DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    GhiChu TEXT,
    FOREIGN KEY (MaSanPham) REFERENCES SanPham(MaSanPham),
    UNIQUE KEY unique_ma_san_pham (MaSanPham)
);
```

## 🚀 Cài Đặt

### Bước 1: Chạy Migration

Mở phpMyAdmin hoặc MySQL CLI và chạy file migration:

```bash
# Sử dụng XAMPP MySQL
cd C:\xampp\mysql\bin
mysql -u root < C:\xampp\htdocs\4Ever\database\migration_nhapkho.sql
```

Hoặc copy nội dung file `migration_nhapkho.sql` và chạy trong phpMyAdmin.

### Bước 2: Kiểm Tra Routes

File `index.php` đã được cập nhật với các routes mới:

```php
// Routes cho Nhập Kho Thành Phẩm
$router->addRoute('GET', '/nhapkho', 'NhapKhoController', 'index');
$router->addRoute('POST', '/nhapkho/confirm', 'NhapKhoController', 'confirmImport');
$router->addRoute('POST', '/nhapkho/confirm-multi', 'NhapKhoController', 'confirmImportMulti');
$router->addRoute('GET', '/nhapkho/detail', 'NhapKhoController', 'getDetail');
```

## 💻 Cách Sử Dụng

### 1. Truy cập Module

Đăng nhập với tài khoản **Nhân viên kho (NVK)** và truy cập:

```
http://localhost/4Ever/nhapkho
```

Hoặc click vào menu **"Nhập kho TP"** trong sidebar.

### 2. Giao diện chính

- **Danh sách lô hàng**: Hiển thị tất cả lô hàng có `TrangThaiQC = 'Đạt'`
- **Tìm kiếm**: Tìm kiếm theo mã lô hàng
- **Lọc**: Lọc theo tên sản phẩm
- **Chọn lô hàng**: Checkbox để chọn nhiều lô hàng

### 3. Nhập kho đơn lẻ

1. Click nút **"Nhập Kho"** ở dòng lô hàng cần nhập
2. Xác nhận trong modal popup
3. Hệ thống sẽ:
   - Cập nhật `TrangThaiKho` của lô hàng
   - Tạo phiếu nhập kho
   - Cập nhật tồn kho

### 4. Nhập kho nhiều lô hàng

1. Chọn các lô hàng cần nhập (checkbox)
2. Click nút **"Xác Nhận Nhập Kho Đã Chọn"**
3. Xác nhận trong modal
4. Hệ thống sẽ xử lý từng lô hàng và báo cáo kết quả

## 🔧 API Endpoints

### 1. GET /nhapkho
**Mô tả**: Hiển thị danh sách lô hàng cần nhập kho

**Response**: HTML view

### 2. POST /nhapkho/confirm
**Mô tả**: Nhập kho một lô hàng

**Request Body**:
```json
{
  "maLoHang": "LH001"
}
```

**Response**:
```json
{
  "success": true,
  "maPhieuNhap": "PNTP20231215123001",
  "message": "Nhập kho thành công!"
}
```

### 3. POST /nhapkho/confirm-multi
**Mô tả**: Nhập kho nhiều lô hàng

**Request Body**:
```json
{
  "danhSachLoHang": ["LH001", "LH002", "LH003"]
}
```

**Response**:
```json
{
  "success": true,
  "successCount": 3,
  "failCount": 0,
  "details": [...]
}
```

### 4. GET /nhapkho/detail?maLoHang=LH001
**Mô tả**: Lấy thông tin chi tiết lô hàng

**Response**:
```json
{
  "success": true,
  "data": {
    "MaLoHang": "LH001",
    "MaSanPham": "SP001",
    "TenSanPham": "Giày Thể Thao",
    ...
  }
}
```

## 📝 Quy Trình Nghiệp Vụ

```
1. Sản xuất → Tạo lô hàng (LoHang)
   ├── TrangThaiQC = 'Chưa kiểm'
   └── TrangThaiKho = 'Chưa nhập kho'

2. QC kiểm tra → Cập nhật TrangThaiQC
   ├── 'Đạt' → Lô hàng đủ điều kiện nhập kho
   └── 'Không đạt' → Không thể nhập kho

3. Nhập kho (Module này)
   ├── Cập nhật TrangThaiKho = 'Đã nhập kho'
   ├── Tạo PhieuNhapSanPham
   └── Cập nhật TonKho (tăng số lượng)

4. Xác minh
   └── Kiểm tra tồn kho, phiếu nhập
```

## 🎨 Tính Năng

### ✅ Các tính năng đã triển khai

- [x] Hiển thị danh sách lô hàng cần nhập kho
- [x] Tìm kiếm theo mã lô hàng
- [x] Lọc theo sản phẩm
- [x] Nhập kho đơn lẻ (một lô hàng)
- [x] Nhập kho nhiều lô hàng cùng lúc
- [x] Xác nhận bằng modal popup
- [x] Hiển thị thông báo kết quả (thành công/thất bại)
- [x] Cập nhật trạng thái lô hàng
- [x] Tạo phiếu nhập kho
- [x] Cập nhật tồn kho tự động
- [x] Transaction để đảm bảo data integrity
- [x] Responsive design với Bootstrap 5
- [x] Thống kê tổng quan (tổng lô hàng, đã nhập, chờ nhập)

### 🔄 Có thể mở rộng

- [ ] Xuất file Excel danh sách lô hàng
- [ ] In phiếu nhập kho
- [ ] Lịch sử nhập kho
- [ ] Thống kê biểu đồ
- [ ] Tự động gửi email thông báo
- [ ] Quét barcode QR code

## 🐛 Xử Lý Lỗi

### Lỗi thường gặp

1. **Lỗi kết nối database**
   - Kiểm tra cấu hình trong `config/database.php`
   - Đảm bảo MySQL đang chạy

2. **Không tìm thấy Model/Controller**
   - Kiểm tra tên file và class
   - Kiểm tra namespace (nếu có)

3. **Không có lô hàng cần nhập**
   - Kiểm tra dữ liệu trong bảng `LoHang`
   - Đảm bảo có ít nhất một lô hàng với `TrangThaiQC = 'Đạt'`

### Debug

Bật debug mode trong `config/config.php`:

```php
define('DEBUG', true);
```

Xem log trong:
- Apache error log
- Browser console (F12)

## 📞 Liên Hệ

- **Developer**: Hệ thống Quản lý Sản xuất 4Ever Factory
- **Version**: 1.0.0
- **Date**: 2024

## 📄 License

Internal Use Only - 4Ever Factory

---

**Note**: Module này yêu cầu PHP 7.4+, MySQL 5.7+, và XAMPP hoặc môi trường tương đương.

