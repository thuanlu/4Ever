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
git clone https://github.com/4ever/factory-management.git
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
private $db_name = 'factory_management';
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
   - **Username:** admin
   - **Password:** 123456

### Tài khoản Demo khác
| Username | Password | Vai trò |
|----------|----------|---------|
| kehoach01 | 123456 | Nhân viên Kế hoạch |
| xuongtruong01 | 123456 | Xưởng trưởng |
| qc01 | 123456 | Nhân viên QC |
| congnhan01 | 123456 | Công nhân |

### Workflow Cơ bản

#### 1. Lập Kế hoạch Sản xuất
1. Đăng nhập với vai trò "Nhân viên Kế hoạch"
2. Vào **Kế hoạch SX** → **Tạo mới**
3. Nhập thông tin sản phẩm, số lượng, thời gian
4. Gửi để phê duyệt

#### 2. Phân công Xưởng
1. Xưởng trưởng đăng nhập
2. Vào **Quản lý Xưởng** → **Phân công**
3. Chọn kế hoạch đã được duyệt
4. Phân công cho các tổ sản xuất

#### 3. Quản lý Nguyên liệu
1. Nhân viên kho đăng nhập
2. Kiểm tra tồn kho → Đặt hàng nếu thiếu
3. Nhập kho khi có hàng về
4. Xuất kho theo yêu cầu sản xuất

#### 4. Kiểm tra Chất lượng
1. Nhân viên QC kiểm tra nguyên liệu đầu vào
2. Kiểm tra sản phẩm trong quá trình sản xuất
3. Kiểm tra sản phẩm hoàn thành
4. Ghi nhận kết quả và biện pháp khắc phục

#### 5. Chấm công
1. Nhân viên check-in khi bắt đầu ca
2. Check-out khi kết thúc ca
3. Hệ thống tự động tính giờ làm việc
4. Quản lý xem báo cáo chấm công

## 🔧 Cấu hình Nâng cao

### Backup Database
```sql
-- Backup thủ công
mysqldump -u root -p factory_management > backup_YYYYMMDD.sql

-- Restore
mysql -u root -p factory_management < backup_YYYYMMDD.sql
```

### Cấu hình Email (Tùy chọn)
Để gửi thông báo qua email, cấu hình SMTP trong `config/email.php`:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
```

### Tối ưu hóa Performance
```php
// Trong config/config.php
// Bật compression
ini_set('zlib.output_compression', 'On');

// Tăng memory limit
ini_set('memory_limit', '256M');

// Tối ưu session
ini_set('session.cache_expire', 180);
```

## 📱 Responsive Design

Hệ thống được thiết kế responsive, hoạt động tốt trên:
- 💻 Desktop (1920x1080+)
- 💻 Laptop (1366x768+)
- 📱 Tablet (768x1024)
- 📱 Mobile (375x667+)

## 🔒 Bảo mật

### Các biện pháp bảo mật đã triển khai:
- ✅ Mã hóa mật khẩu bằng bcrypt
- ✅ Session management an toàn
- ✅ SQL injection prevention (PDO)
- ✅ XSS protection
- ✅ CSRF token validation
- ✅ File upload security
- ✅ Access control theo vai trò

### Khuyến nghị bảo mật:
1. Đổi mật khẩu mặc định ngay lập tức
2. Sử dụng HTTPS trong môi trường production
3. Backup database định kỳ
4. Cập nhật hệ thống thường xuyên
5. Monitor log files

## 🐛 Troubleshooting

### Lỗi thường gặp:

#### 1. Không kết nối được database
```
Kiểm tra:
- MySQL service đang chạy
- Thông tin kết nối trong config/database.php
- User có quyền truy cập database
```

#### 2. Lỗi 404 Not Found
```
Kiểm tra:
- File .htaccess có tồn tại
- Module rewrite được bật trong Apache
- Đường dẫn BASE_URL trong config
```

#### 3. Không đăng nhập được
```
Kiểm tra:
- Dữ liệu mẫu đã được import
- Session có hoạt động
- Mật khẩu đúng (mặc định: 123456)
```

#### 4. Giao diện bị lỗi
```
Kiểm tra:
- Kết nối internet (CDN Bootstrap, FontAwesome)
- File CSS/JS có tồn tại
- Console browser có lỗi không
```

## 📞 Hỗ trợ

### Thông tin liên hệ:
- **Email:** support@4ever.com
- **Phone:** +84 123 456 789
- **Website:** https://4ever.com

### Tài liệu kỹ thuật:
- **API Documentation:** `/docs/api.md`
- **Database Schema:** `/docs/database.md`
- **Development Guide:** `/docs/development.md`

## 📄 License

Dự án này được phát triển bởi nhóm 4Ever cho mục đích học tập và nghiên cứu.

---

## 🤝 Đóng góp

Chúng tôi hoan nghênh mọi đóng góp để cải thiện hệ thống:

1. Fork project
2. Tạo feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

---

**Phát triển bởi nhóm 4Ever** 🚀

*Hệ thống Quản lý Sản xuất Nhà máy - Phiên bản 1.0.0*



---------------------------------------------------------------------------------
Cây thư mục của em đang theo kiểu MVC (Model – View – Controller)

🏗 Cấu trúc thư mục 4EVER
1. app/controllers/

AuthController.php → Xử lý logic đăng nhập, đăng xuất, kiểm tra user.

BaseController.php → Controller cha, có thể chứa các hàm dùng chung cho các controller khác.

DashboardController.php → Quản lý logic hiển thị dashboard (trang chính sau khi đăng nhập).

HomeController.php → Xử lý trang chủ hoặc landing page.

👉 Tóm lại: Đây là nơi xử lý nghiệp vụ và nhận/gửi dữ liệu giữa Model ↔ View.

2. app/models/

User.php → Đại diện cho bảng users trong database.

Chứa các hàm: findByUsername(), checkPassword(), getAll(), save(), …

Đây là lớp kết nối giữa PHP và database.

👉 Đây là nơi xử lý dữ liệu.

3. app/views/

auth/login.php → File giao diện mà em gửi anh lúc nãy (trang login).

dashboard/index.php → View của trang dashboard.

layouts/ → Chứa layout chung (ví dụ: header, footer, sidebar) để tái sử dụng.

👉 Đây là nơi hiển thị HTML/CSS/JS ra màn hình.

4. app/Router.php

File định tuyến (router) → Xác định URL nào sẽ gọi đến controller nào, hàm nào.
Ví dụ:

/login → gọi AuthController@login()

/dashboard → gọi DashboardController@index()

5. config/

config.php → Chứa cấu hình chung (BASE_URL, môi trường, timezone…).

database.php → Chứa thông tin kết nối database (host, username, password, dbname).

6. database/

Thường chứa file migration hoặc script SQL để tạo bảng, seed dữ liệu.

7. public/

assets/ → Nơi chứa tài nguyên tĩnh (hình ảnh, fonts, …).

css/style.css → CSS riêng của hệ thống.

js/main.js → JavaScript riêng.

👉 Đây là thư mục public mà webserver (Apache/Nginx) sẽ load ra ngoài.

8. File gốc

.htaccess → Dùng để rewrite URL (ví dụ: /login thay vì index.php?url=login).

index.php → File vào đầu tiên khi chạy app (entry point).

Thường dùng để load Router.php → rồi định tuyến vào controller thích hợp.

🔄 Dòng chảy khi em đăng nhập

Người dùng mở /login → Router.php gọi AuthController@login().

AuthController nhận dữ liệu từ form → gọi User model để kiểm tra trong DB.

Nếu đúng → set $_SESSION['success'] + chuyển hướng đến DashboardController@index().
Nếu sai → set $_SESSION['error'] + load lại view auth/login.php.

login.php (view) hiển thị giao diện + thông báo.