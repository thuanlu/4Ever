# 🏭 Hệ thống Quản lý Sản xuất Nhà máy 4Ever

## 📋 Giới thiệu

Hệ thống quản lý sản xuất toàn diện cho nhà máy, giúp số hóa và tối ưu hóa quy trình sản xuất từ lập kế hoạch đến quản lý chất lượng và chấm công.

## ✨ Tính năng chính

### 🎯 Quản lý Kế hoạch Sản xuất
- Tạo, chỉnh sửa và phê duyệt kế hoạch sản xuất
- Theo dõi tiến độ thực hiện
- Báo cáo hiệu suất sản xuất

### 🏢 Quản lý Xưởng & Tổ
- Phân công công việc cho các xưởng
- Quản lý tổ sản xuất
- Theo dõi năng suất từng đơn vị

### 📦 Quản lý Nguyên vật liệu
- Đặt hàng và nhập kho
- Kiểm tra tồn kho
- Xuất kho cho sản xuất
- Cảnh báo thiếu hàng

### ✅ Kiểm tra Chất lượng (QC)
- Kiểm tra chất lượng nguyên liệu
- Kiểm tra sản phẩm hoàn thành
- Ghi nhận và theo dõi lỗi
- Báo cáo chất lượng

### 👥 Quản lý Nhân viên & Chấm công
- Check-in/out tự động
- Quản lý ca làm việc
- Tính toán lương theo giờ
- Báo cáo chấm công

### 📊 Dashboard & Báo cáo
- Tổng quan tình hình sản xuất
- Biểu đồ và thống kê trực quan
- Báo cáo theo nhiều tiêu chí
- Xuất báo cáo PDF/Excel

## 👤 Phân quyền Người dùng

| Vai trò | Quyền hạn |
|---------|-----------|
| **Ban Giám Đốc** | Toàn quyền quản lý hệ thống |
| **Nhân viên Kế hoạch** | Lập và quản lý kế hoạch sản xuất |
| **Xưởng trưởng** | Quản lý xưởng và phân công công việc |
| **Tổ trưởng** | Quản lý tổ sản xuất |
| **Nhân viên QC** | Kiểm tra và báo cáo chất lượng |
| **Nhân viên Kho NL** | Quản lý kho nguyên vật liệu |
| **Nhân viên Kho TP** | Quản lý kho thành phẩm |
| **Công nhân** | Chấm công và xem thông tin cá nhân |

## 🛠️ Yêu cầu Hệ thống

- **Web Server:** Apache 2.4+
- **PHP:** 7.4+ (khuyến nghị 8.0+)
- **Database:** MySQL 5.7+ hoặc MariaDB 10.2+
- **Web Browser:** Chrome, Firefox, Safari, Edge (phiên bản mới)

## 📥 Cài đặt

### 1. Tải về và giải nén
```bash
# Clone repository hoặc tải về source code
git clone https://github.com/4ever
# Hoặc giải nén file zip vào thư mục web server
```

### 2. Cấu hình Web Server (XAMPP)
```bash
# Sao chép project vào thư mục htdocs
C:\xampp\htdocs\4Ever\
```

### 3. Thiết lập Database
```bash
# Truy cập script thiết lập database
http://localhost/4Ever/database/setup.php
```

### 4. Cấu hình Database (Tùy chọn)
Chỉnh sửa file `config/database.php` nếu cần:
```php
private $host = 'localhost';
private $db_name = 'qlsx_4ever';
private $username = 'root';
private $password = '';
```

### 5. Thiết lập quyền thư mục
```bash
# Đảm bảo web server có quyền ghi vào các thư mục
chmod 755 public/uploads/
chmod 755 logs/
```

## 🚀 Sử dụng

### Đăng nhập lần đầu
1. Truy cập: `http://localhost/4Ever/`
2. Đăng nhập với tài khoản mặc định:



login.php (view) hiển thị giao diện + thông báo.
