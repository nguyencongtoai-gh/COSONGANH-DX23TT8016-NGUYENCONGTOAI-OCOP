# Hướng dẫn cài đặt và chạy hệ thống

Trình tự dưới đây dựng lại toàn bộ website trên máy cá nhân chạy Windows
trong khoảng 30 phút, tương ứng Phụ lục A của báo cáo đồ án.

## Yêu cầu môi trường

| Thành phần | Phiên bản | Vai trò |
|---|---|---|
| XAMPP | 8.2.x | Apache 2.4 + PHP 8.2 + MariaDB 10.4 + phpMyAdmin |
| WordPress | 6.7.x | Nền tảng hệ quản trị nội dung |
| Theme Astra | 4.6.x | Theme cha của child theme `ocoptv` |
| WooCommerce | 9.x | Sản phẩm, giỏ hàng, đặt hàng |
| Advanced Custom Fields | 6.x | Trường nghiệp vụ (hạng sao, nguồn gốc…) |
| Custom Post Type UI | 1.x | Tùy chọn — kiểu nội dung đã đăng ký bằng code trong theme |
| WPForms | 1.8.x | Biểu mẫu liên hệ (tùy chọn) |
| WP Super Cache | 1.9.x | Bộ nhớ đệm tăng tốc |

## Các bước cài đặt

1. **Cài XAMPP 8.2.x** từ apachefriends.org vào `C:\xampp`; khởi động
   Apache và MySQL trong XAMPP Control Panel; kiểm tra cổng 80/3306 không
   bị chiếm dụng.
2. **Tạo cơ sở dữ liệu**: mở phpMyAdmin tại `http://localhost/phpmyadmin`,
   chạy nội dung `setup/database.sql` (tab SQL) để tạo CSDL `ocoptravinh`
   (utf8mb4_unicode_ci) và người dùng `ocop_user`.
3. **Cài WordPress 6.7**: tải tại wordpress.org, giải nén vào
   `C:\xampp\htdocs\ocoptravinh`, mở `http://localhost/ocoptravinh`, chạy
   trình cài đặt 5 phút: khai báo cơ sở dữ liệu, đặt tên website
   **“OCOP Trà Vinh”**, tạo tài khoản quản trị.
4. **Cài theme và plugin**: vào *Plugins → Add New* cài và kích hoạt
   WooCommerce (chọn đơn vị tiền VND trong trình hướng dẫn nhanh, hình thức
   thanh toán: chuyển khoản/thanh toán khi nhận hàng), Advanced Custom Fields,
   WPForms, WP Super Cache; vào *Appearance → Themes → Add New* cài Astra.
   Xem `src/plugins/DANH-SACH-PLUGIN.md`.
5. **Cài child theme ocoptv**: sao chép thư mục
   `src/wp-content/themes/ocoptv` vào `wp-content/themes` của WordPress,
   vào *Appearance → Themes* và kích hoạt **OCOP TV**.
6. **Copy ảnh mẫu**: sao chép toàn bộ thư mục `src/sample-data/images`
   vào `wp-content/uploads/ocoptv/` (bước này cho phép seeder gán ảnh
   đại diện cho 22 sản phẩm và tạo 4 album thư viện).
7. **Gie dữ liệu thử nghiệm**: sao chép `setup/seed-data.php` vào thư mục
   gốc WordPress (ngang hàng `wp-config.php`), mở trình duyệt tại
   `http://localhost/ocoptravinh/seed-data.php?chay=1` rồi làm theo trang
   kết quả. Seeder tạo: 4 nhóm ngành, 8 địa phương, 21 đơn vị sản xuất,
   22 sản phẩm OCOP đủ 7 thuộc tính, 12 bài tin tức, 4 album, 6 trang,
   menu 8 mục và các tài khoản mẫu. Kiểm tra dòng “Ảnh sản phẩm đã gán”
   trong trang kết quả — nếu bằng 0, xem mục xử lý sự cố bên dưới.
8. **Kiểm tra**: đăng nhập `wp-admin`; xem trang chủ, trang danh mục sản phẩm
   (thử bộ lọc 5 tiêu chí), trang chi tiết sản phẩm, trang đơn vị; thử quy
   trình gửi – kiểm duyệt bằng tài khoản `donvi01` rồi duyệt bằng `bientap`.

## Tài khoản mẫu

| Tài khoản | Vai trò | Mật khẩu |
|---|---|---|
| `admin` (bạn tạo lúc cài) | Quản trị viên — toàn quyền | tự đặt |
| `bientap` | Biên tập viên — duyệt / trả lại sản phẩm | `Ocop@2026` |
| `donvi01` | Đơn vị sản xuất — gửi sản phẩm chờ duyệt | `Ocop@2026` |
| `donvi02` | Đơn vị sản xuất — gửi sản phẩm chờ duyệt | `Ocop@2026` |

> **Đổi mật khẩu ngay khi triển khai thực tế** và xóa `seed-data.php`
> khỏi máy chủ sau khi chạy.

## Xử lý sự cố thường gặp

- **Cổng 80 bận** (Skype/IIS): tắt ứng dụng chiếm cổng hoặc đổi cổng Apache
  trong `httpd.conf`, khi đó URL là `http://localhost:8080/ocoptravinh`.
- **Trang sản phẩm báo lỗi 404**: vào *Settings → Permalinks* nhấn Save
  (seeder đã đặt cấu trúc `/%postname%/`, chỉ cần flush lại).
- **Ảnh sản phẩm thiếu** (thẻ sản phẩm hiện logo thay ảnh): nguyên nhân là
  chạy `seed-data.php` trước khi copy ảnh mẫu — seeder bỏ qua phần gán ảnh
  và không cho chạy lại. Cách sửa: copy 41 file ảnh từ `src/sample-data/images`
  vào `wp-content/uploads/ocoptv/`, sao chép `setup/sua-anh.php` vào thư mục
  gốc WordPress rồi mở `http://localhost/ocoptravinh/sua-anh.php?chay=1` —
  tệp này chỉ gán lại ảnh cho 22 sản phẩm, tạo 4 album và gán logo, không
  tạo dữ liệu trùng lặp; chạy xong thì xóa tệp khỏi máy chủ.
- **Thư thông báo kiểm duyệt không gửi được**: `wp_mail` trên localhost
  cần SMTP — cài plugin WP Mail SMTP và cấu hình gmail ứng dụng; logic
  kiểm duyệt vẫn hoạt động bình thường không phụ thuộc thư.
- **Giao diện chưa mang màu xanh – vàng**: xác nhận đang kích hoạt
  *OCOP TV* (child theme), không phải Astra.
