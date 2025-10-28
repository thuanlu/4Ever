# 🚀 Hướng Dẫn Cài Đặt Module Nhập Kho Thành Phẩm

## Bước 1: Chạy Migration Database

Mở **phpMyAdmin** hoặc **MySQL CLI** và chạy file migration để tạo các bảng và cột cần thiết:

### Cách 1: Sử dụng phpMyAdmin (Khuyên dùng)

1. Truy cập: `http://localhost/phpmyadmin`
2. Chọn database: `qlsx_4ever`
3. Click tab **SQL**
4. Copy toàn bộ nội dung file `database/migration_nhapkho.sql`
5. Paste vào ô SQL và click **Go**

### Cách 2: Sử dụng MySQL Command Line

```bash
# Với XAMPP trên Windows
cd C:\xampp\mysql\bin
mysql -u root < "C:\xampp\htdocs\4Ever\database\migration_nhapkho.sql"
```

### Cách 3: Sử dụng terminal trong Cursor/VS Code

Mở terminal trong project và chạy:

```bash
# Import SQL file vào MySQL
mysql -u root -p qlsx_4ever < database/migration_nhapkho.sql
# (Nhập password khi được hỏi, hoặc Enter nếu không có password)
```

## Bước 2: Kiểm tra Routes đã được thêm

File `index.php` đã được cập nhật với các routes mới. Không cần thêm gì nữa.

**Routes đã thêm:**
- `GET /nhapkho` → Hiển thị danh sách
- `POST /nhapkho/confirm` → Nhập kho một lô hàng
- `POST /nhapkho/confirm-multi` → Nhập kho nhiều lô hàng
- `GET /nhapkho/detail` → Lấy thông tin chi tiết

## Bước 3: Tạo dữ liệu mẫu (Tùy chọn)

Để test module, bạn có thể tạo một số lô hàng đã được QC duyệt:

### Trong phpMyAdmin, chạy SQL sau:

```sql
-- Thêm dữ liệu mẫu
USE qlsx_4ever;

-- Đảm bảo có sản phẩm trong bảng SanPham
INSERT INTO SanPham (MaSanPham, TenSanPham, Size, Mau, GiaXuat) VALUES
('SP001', 'Giày Thể Thao Nam', '42', 'Đen', 500000),
('SP002', 'Giày Thể Thao Nữ', '38', 'Trắng', 450000),
('SP003', 'Giày Chạy Bộ', '40', 'Xanh', 600000)
ON DUPLICATE KEY UPDATE TenSanPham = VALUES(TenSanPham);

-- Tạo lô hàng cần nhập kho (TrangThaiQC = 'Đạt')
INSERT INTO LoHang (MaLoHang, MaSanPham, SoLuong, TrangThaiQC, TrangThaiKho) VALUES
('LH001', 'SP001', 100, 'Đạt', 'Chưa nhập kho'),
('LH002', 'SP002', 150, 'Đạt', 'Chưa nhập kho'),
('LH003', 'SP003', 80, 'Đạt', 'Chưa nhập kho')
ON DUPLICATE KEY UPDATE TrangThaiQC = 'Đạt';
```

## Bước 4: Truy cập Module

1. **Khởi động XAMPP** (Apache + MySQL)
2. **Đăng nhập** với tài khoản nhân viên kho:
   - Username: (tài khoản có role = 'NVK' hoặc 'nhan_vien_kho_tp')
3. **Truy cập**: `http://localhost/4Ever/nhapkho`
   - Hoặc click menu **"Nhập kho TP"** trong sidebar

## Bước 5: Sử dụng Module

### Nhập kho đơn lẻ:
1. Tìm lô hàng cần nhập
2. Click nút **"Nhập Kho"**
3. Xác nhận trong popup
4. Kiểm tra thông báo kết quả

### Nhập kho nhiều lô hàng:
1. Chọn nhiều lô hàng (checkbox)
2. Click **"Xác Nhận Nhập Kho Đã Chọn"**
3. Xác nhận trong popup
4. Xem kết quả (bao nhiêu thành công, bao nhiêu thất bại)

## ✅ Kiểm tra kết quả

### Kiểm tra trong Database:

```sql
-- Xem lô hàng đã nhập kho
SELECT MaLoHang, MaSanPham, SoLuong, TrangThaiQC, TrangThaiKho 
FROM LoHang 
WHERE TrangThaiKho = 'Đã nhập kho';

-- Xem phiếu nhập kho
SELECT * FROM PhieuNhapSanPham 
ORDER BY NgayNhap DESC 
LIMIT 10;

-- Xem tồn kho
SELECT tk.*, sp.TenSanPham 
FROM TonKho tk
JOIN SanPham sp ON tk.MaSanPham = sp.MaSanPham;
```

## 🐛 Xử lý lỗi thường gặp

### Lỗi 1: "Route không tồn tại"
**Giải pháp**: 
- Kiểm tra file `index.php` đã có routes mới
- Restart Apache
- Clear browser cache

### Lỗi 2: "Class NhapKho not found"
**Giải pháp**:
- Kiểm tra file `app/models/NhapKho.php` có tồn tại
- Kiểm tra naming convention (tên file phải khớp tên class)

### Lỗi 3: "Column 'TrangThaiKho' không tồn tại"
**Giải pháp**:
- Chưa chạy migration
- Chạy lại file `migration_nhapkho.sql`

### Lỗi 4: "Không có lô hàng nào hiển thị"
**Giải pháp**:
- Tạo dữ liệu mẫu (bước 3)
- Đảm bảo có lô hàng với `TrangThaiQC = 'Đạt'`
- Kiểm tra quyền user (phải là NVK)

## 📁 Cấu trúc files đã tạo

```
✅ app/models/NhapKho.php                    # Model
✅ app/controllers/NhapKhoController.php      # Controller
✅ app/views/kho/nhap_kho_thanh_pham.php     # View
✅ database/migration_nhapkho.sql             # Migration
✅ docs/NHAP_KHO_MODULE.md                   # Documentation
✅ SETUP_NHAPKHO.md                          # Hướng dẫn này
```

## 🎉 Hoàn tất!

Module **Nhập Kho Thành Phẩm** đã sẵn sàng sử dụng. Bạn có thể:

- Xem chi tiết code trong từng file
- Mở rộng thêm tính năng
- Tùy chỉnh giao diện
- Thêm validation, logging, v.v.

## 📞 Support

Nếu gặp vấn đề, kiểm tra:
1. Error log: `C:\xampp\apache\logs\error.log`
2. Browser console: F12 → Console
3. Network tab: Kiểm tra HTTP requests

---

**Developer**: 4Ever Factory Team
**Date**: December 2024

