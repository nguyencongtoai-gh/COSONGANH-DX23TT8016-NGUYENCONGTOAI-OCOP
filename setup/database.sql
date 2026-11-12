-- =====================================================================
-- File: setup/database.sql
-- Website OCOP Trà Vinh — đồ án cơ sở ngành
--
-- Tệp này tạo CƠ SỞ DỮ LIỆU RỖNG ocoptravinh và người dùng ocop_user
-- (bước 2 – Phụ lục A của báo cáo). Chạy bằng phpMyAdmin (tab SQL)
-- hoặc dòng lệnh:  mysql -u root -p < setup/database.sql
--
-- DỮ LIỆU THỬ NGHIỆM (22 sản phẩm OCOP, 21 đơn vị sản xuất, 12 bài tin,
-- 4 album, 6 trang, menu, tài khoản mẫu) KHÔNG nằm trong tệp này:
-- sau khi cài WordPress + theme + plugin (bước 3–6), chạy
-- setup/seed-data.php để tự động gie toàn bộ dữ liệu (bước 7).
-- =====================================================================

CREATE DATABASE IF NOT EXISTS ocoptravinh
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Trên XAMPP có thể bỏ khối bên dưới (dùng tài khoản root của phpMyAdmin);
-- giữ lại khi muốn tách quyền truy cập theo người dùng riêng của đồ án.
CREATE USER IF NOT EXISTS 'ocop_user'@'localhost' IDENTIFIED BY 'ocop_mat_khau_du_an';
GRANT ALL PRIVILEGES ON ocoptravinh.* TO 'ocop_user'@'localhost';
FLUSH PRIVILEGES;
