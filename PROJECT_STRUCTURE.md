# 📦 Tổng quan hệ thống Quản lý Sản xuất 4Ever

## 1. Cấu trúc thư mục

```
4Ever/
├── .htaccess                # Rewrite URL, bảo mật, cache
├── index.php                # Entry point, khởi tạo router
├── README.md                # Giới thiệu, hướng dẫn sử dụng
├── PROJECT_STRUCTURE.md     # Tổng hợp chi tiết hệ thống
├── app/
│   ├── controllers/         # Xử lý nghiệp vụ
│   │   ├── AuthController.php      # Đăng nhập, đăng xuất
│   │   ├── BaseController.php      # Controller cha, hàm dùng chung
│   │   ├── DashboardController.php # Dashboard tổng quan
│   │   ├── HomeController.php      # Trang chủ, chuyển hướng login
│   ├── models/
│   │   └── User.php                # Model người dùng, xác thực
│   ├── views/
│   │   ├── auth/
│   │   │   └── login.php           # Giao diện đăng nhập
│   │   ├── dashboard/
│   │   │   └── index.php           # Giao diện dashboard
│   │   └── layouts/
│   │       └── main.php            # Layout chung, sidebar, header
│   └── Router.php                  # Định tuyến URL → Controller
├── config/
│   ├── config.php           # Cấu hình chung, BASE_URL, session
│   └── database.php         # Kết nối database (PDO)
├── database/
│   ├── factory_management.sql # Tạo bảng, quan hệ, indexes
│   ├── sample_data.sql        # Dữ liệu mẫu: users, roles, sản phẩm...
│   ├── setup.php              # Script khởi tạo DB
│   ├── test_login.php         # Script test đăng nhập
│   └── update_passwords.php   # Script cập nhật hash mật khẩu
├── public/
│   ├── assets/                # Tài nguyên tĩnh (images, fonts)
│   ├── css/
│   │   └── style.css          # CSS custom cho UI
│   └── js/
│       └── main.js            # JS cho UI, dashboard, tiện ích
```

## 2. Database
- **15+ bảng**: users, roles, production_plans, workshops, teams, shifts, products, materials, suppliers, material_orders, material_order_details, material_receipts, quality_controls, attendances, production_progress, system_logs
- **Quan hệ**: Khóa ngoại, indexes tối ưu hóa truy vấn
- **Dữ liệu mẫu**: Đầy đủ các vai trò, ca làm việc, xưởng, tổ, sản phẩm, nguyên vật liệu, kế hoạch SX, phân công, đơn hàng

## 3. Backend (PHP MVC)
- **Controllers**: Xử lý logic, phân quyền, chuyển hướng, ghi log
- **Models**: Truy vấn DB, xác thực, CRUD
- **Views**: Hiển thị HTML/CSS/JS, responsive, thông báo
- **Router**: Định nghĩa route, gọi controller/action
- **Config**: Quản lý cấu hình, session, timezone

## 4. Frontend UI
- **Bootstrap 5, Font Awesome**: Giao diện hiện đại, responsive
- **Sidebar động**: Hiển thị menu theo vai trò
- **Dashboard**: Thống kê tổng quan, biểu đồ, quick actions
- **Form đăng nhập**: Hỗ trợ username/email/phone, thông báo lỗi
- **Custom CSS/JS**: Hiệu ứng, tiện ích, xác thực, thông báo

## 5. Luồng hoạt động
- Người dùng truy cập `/login` → nhập thông tin → AuthController xử lý xác thực qua User model
- Đăng nhập thành công: lưu session, ghi log, chuyển dashboard
- Dashboard hiển thị thống kê, quick actions, menu động
- Các module: Kế hoạch SX, xưởng, nguyên vật liệu, QC, chấm công, báo cáo

## 6. Bảo mật
- Mã hóa mật khẩu (bcrypt)
- Chống SQL injection (PDO)
- Quản lý session, phân quyền
- Chống truy cập trực tiếp vào config/app/database
- Nén gzip, cache static files

## 7. Hướng dẫn mở rộng
- Thêm controller/model/view cho từng module (production_plans, workshops, materials, QC, attendance...)
- Định nghĩa route mới trong `index.php`
- Sử dụng layout/main.php để tái sử dụng giao diện
- Tùy chỉnh CSS/JS trong public/

## 8. Tài khoản demo
| Username      | Email                | Phone        | Role                | Password |
|---------------|----------------------|--------------|---------------------|----------|
| admin         | admin@4ever.com      | 0901234567   | Ban Giám Đốc        | 123456   |
| kehoach01     | kehoach@4ever.com    | 0901234568   | Nhân viên Kế hoạch  | 123456   |
| xuongtruong01 | xuongtruong@4ever.com| 0901234569   | Xưởng trưởng        | 123456   |
| qc01          | qc@4ever.com         | 0901234571   | Nhân viên QC        | 123456   |
| congnhan01    | congnhan01@4ever.com | 0901234574   | Công nhân           | 123456   |

## 9. Đặc điểm nổi bật
- Kiến trúc rõ ràng, dễ mở rộng
- UI hiện đại, tối ưu cho desktop/mobile
- Đầy đủ nghiệp vụ sản xuất, kho, QC, nhân sự
- Dễ dàng tích hợp thêm API, báo cáo, xuất file

---
*File này tự động sinh bởi GitHub Copilot ngày 16/10/2025. Nếu cần chi tiết code từng file, vui lòng xem README.md hoặc liên hệ hỗ trợ.*
