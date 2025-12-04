# 🚀 Quick Start - Module Nhập Kho Thành Phẩm

## ⚡ 3 Bước Để Bắt Đầu

### Bước 1: Chạy Migration (CHẠY NGAY!)

Mở **phpMyAdmin** → Chọn database `qlsx_4ever` → Tab SQL → Paste và chạy:

```sql
-- Copy toàn bộ nội dung trong file này:
-- database/migration_nhapkho.sql
```

📄 **File:** `database/migration_nhapkho.sql`

### Bước 2: Tạo Dữ Liệu Mẫu (Tùy chọn - để test)

Chạy SQL trong phpMyAdmin:

```sql
-- Tạo lô hàng cần nhập kho
INSERT INTO LoHang (MaLoHang, MaSanPham, SoLuong, TrangThaiQC, TrangThaiKho) VALUES
('LH001', 'SP001', 100, 'Đạt', 'Chưa nhập kho'),
('LH002', 'SP001', 150, 'Đạt', 'Chưa nhập kho')
ON DUPLICATE KEY UPDATE TrangThaiQC = 'Đạt';
```

### Bước 3: Truy Cập Module

```
🌐 URL: http://localhost/4Ever/nhapkho
👤 Login: Với tài khoản có role 'NVK'
```

---

## 🎯 Sử Dụng Nhanh

### Nhập kho 1 lô hàng:
1. Click nút **"Nhập Kho"** ở dòng lô hàng
2. Xác nhận trong popup
3. Done! ✅

### Nhập kho nhiều lô hàng:
1. Tick checkbox các lô hàng cần nhập
2. Click **"Xác Nhận Nhập Kho Đã Chọn"**
3. Xác nhận
4. Done! ✅

---

## 📁 Files Đã Tạo

| File | Mô tả |
|------|-------|
| `app/models/NhapKho.php` | Model xử lý database |
| `app/controllers/NhapKhoController.php` | Controller xử lý logic |
| `app/views/kho/nhap_kho_thanh_pham.php` | View giao diện |
| `database/migration_nhapkho.sql` | **Migration (CHẠY FILE NÀY!)** |
| `docs/NHAP_KHO_MODULE.md` | Tài liệu chi tiết |
| `SETUP_NHAPKHO.md` | Hướng dẫn cài đặt |
| `MODULE_SUMMARY.md` | Tóm tắt module |

---

## ✅ Checklist

- [ ] Đã chạy migration (`migration_nhapkho.sql`)
- [ ] Có lô hàng với `TrangThaiQC = 'Đạt'` trong database
- [ ] Đăng nhập với tài khoản NVK
- [ ] Truy cập: `http://localhost/4Ever/nhapkho`
- [ ] Test nhập kho 1 lô hàng
- [ ] Test nhập kho nhiều lô hàng
- [ ] Kiểm tra database đã cập nhật

---

## 🔍 Kiểm Tra Kết Quả

### Trong Database:

```sql
-- Xem lô hàng đã nhập kho
SELECT * FROM LoHang WHERE TrangThaiKho = 'Đã nhập kho';

-- Xem phiếu nhập kho
SELECT * FROM PhieuNhapSanPham ORDER BY NgayNhap DESC LIMIT 5;

-- Xem tồn kho
SELECT * FROM TonKho;
```

---

## ❓ Troubleshooting

### Lỗi: "Column 'TrangThaiKho' không tồn tại"
→ **Giải pháp:** Chưa chạy migration! Chạy file `migration_nhapkho.sql`

### Lỗi: "404 - Trang không tìm thấy"
→ **Giải pháp:** 
- Restart Apache trong XAMPP
- Clear browser cache
- Check URL: `/nhapkho` (không phải `/nhap-kho`)

### Không có lô hàng nào hiển thị
→ **Giải pháp:**
- Tạo lô hàng với `TrangThaiQC = 'Đạt'`
- Check dữ liệu trong database

---

## 📞 Hỗ Trợ

📖 Đọc thêm:
- `docs/NHAP_KHO_MODULE.md` - Chi tiết module
- `SETUP_NHAPKHO.md` - Hướng dẫn cài đặt
- `MODULE_SUMMARY.md` - Tóm tắt

🐛 Xem log:
- Apache log: `C:\xampp\apache\logs\error.log`
- Browser console: F12

---

**🎉 Chúc bạn sử dụng module thành công!**

