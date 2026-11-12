# Website giới thiệu sản phẩm OCOP Trà Vinh

Đồ án cơ sở ngành — đề tài **“Xây dựng website giới thiệu sản phẩm OCOP địa phương”**.

Website xây dựng trên nền tảng **WordPress** (cục bộ qua XAMPP), tùy biến bằng
**child theme `ocoptv`** trên theme cha Astra, tích hợp **WooCommerce** cho
phần mở rộng bán hàng trực tuyến.


## Tính năng (đối chiếu 6 yêu cầu đề tài)

| # | Yêu cầu | Hiện thực trong mã nguồn |
|---|---|---|
| 1 | Cài WordPress cục bộ + tùy biến giao diện | XAMPP + WordPress 6.7; child theme `ocoptv`: logo, bộ màu xanh `#1B5E20` – vàng `#C9A227`, menu 8 mục, footer 3 cột (`assets/css/ocoptv.css`) |
| 2 | 8 trang nội dung | Trang chủ, Giới thiệu OCOP, Danh mục sản phẩm, Chi tiết sản phẩm, Đơn vị sản xuất, Tin tức – sự kiện, Thư viện hình ảnh, Liên hệ (`setup/seed-data.php` tạo sẵn) |
| 3 | ≥ 20 sản phẩm OCOP đủ 7 thuộc tính | 22 sản phẩm minh họa: tên, hình ảnh, mô tả, giá bán, nguồn gốc, đơn vị sản xuất, hạng sao (bảng 3.5 báo cáo) |
| 4 | Tìm kiếm, lọc, phân loại 5 tiêu chí | `inc/loc-san-pham.php`: tên (`s`), nhóm ngành + địa phương (`tax_query`), đơn vị + hạng sao (`meta_query`), khoảng giá (`_price` BETWEEN), sắp xếp |
| 5 | Child theme + shortcode tự viết | Child theme chuẩn WordPress (Template: astra); `[ocop_noibat]`, `[ocop_danhsach]` trong `shortcodes/` |
| 6 | Phân quyền 3 vai trò + kiểm duyệt | `inc/phan-quyen.php` (vai trò `dv_san_xuat`), `inc/kiem-duyet.php` (thư thông báo, cột hạng sao), trạng thái `pending` bắt buộc |
| + | Mở rộng: WooCommerce bán hàng | Sản phẩm đồng thời là sản phẩm WooCommerce: giá, tồn kho, giỏ hàng, thanh toán COD |

## Cấu trúc repository

```
csn-ocoptravinh-wordpress/
├── README.md                ← tệp này
├── setup/                   ← cài đặt & dữ liệu thử nghiệm
│   ├── HUONG-DAN-CAI-DAT.md ← hướng dẫn 8 bước (Phụ lục A)
│   ├── database.sql         ← tạo CSDL ocoptravinh + ocop_user
│   ├── seed-data.php        ← gie 22 sản phẩm, 21 đơn vị, 12 tin, 4 album, menu, tài khoản
│   └── sua-anh.php          ← sửa lỗi thiếu ảnh nếu gie dữ liệu trước khi copy ảnh
├── src/
│   ├── wp-content/themes/ocoptv/     ← MÃ NGUỒN CHÍNH (child theme)
│   │   ├── style.css        ← khai báo child theme (Template: astra)
│   │   ├── functions.php    ← nạp CSS/JS, require các module
│   │   ├── screenshot.png   ← ảnh đại diện theme
│   │   ├── inc/             ← custom-types, phan-quyen, kiem-duyet, loc-san-pham, trang-chi-tiet
│   │   ├── shortcodes/      ← ocop-noibat.php, ocop-danhsach.php
│   │   ├── template-parts/  ← card-san-pham.php, content-don-vi.php
│   │   ├── page-templates/  ← trang-lien-he.php (khuôn mẫu trang liên hệ)
│   │   ├── single-/archive-don_vi_san_xuat.php
│   │   └── assets/          ← css/ocoptv.css, js/ocoptv.js, images/logo.png
│   ├── plugins/DANH-SACH-PLUGIN.md  ← plugin cài từ wordpress.org
│   └── sample-data/images/  ← 40 ảnh minh họa (sp-01…22, gal-01…18)
├── progress-report/         ← 12 báo cáo tiến độ hằng tuần
└── thesis/                  ← tài liệu đồ án (doc, html, abs, refs, pdf)
```

## Cài đặt nhanh

Xem chi tiết `setup/HUONG-DAN-CAI-DAT.md`. Tóm tắt:

1. Cài XAMPP 8.2, bật Apache + MySQL.
2. Chạy `setup/database.sql` trong phpMyAdmin.
3. Cài WordPress 6.7 vào `C:\xampp\htdocs\ocoptravinh`.
4. Cài plugin WooCommerce, ACF (+ tùy chọn CPT UI, WPForms, WP Super Cache)
   và theme Astra.
5. Copy `src/wp-content/themes/ocoptv` → `wp-content/themes/`; kích hoạt
   **OCOP TV**.
6. Copy `src/sample-data/images` → `wp-content/uploads/ocoptv/`.
7. Copy `setup/seed-data.php` vào thư mục gốc WordPress, mở
   `http://localhost/ocoptravinh/seed-data.php?chay=1`.
8. Đăng nhập và kiểm tra (tài khoản mẫu bên dưới).

## Tài khoản mẫu (đổi mật khẩu ngay khi triển khai)

| Tài khoản | Vai trò | Mật khẩu |
|---|---|---|
| `admin` | Quản trị viên | bạn đặt lúc cài WordPress |
| `bientap` | Biên tập viên | `Ocop@2026` |
| `donvi01` | Đơn vị sản xuất (HTX Dừa xanh Càng Long) | `Ocop@2026` |
| `donvi02` | Đơn vị sản xuất (Cơ sở kẹo dừa Hồng Phúc) | `Ocop@2026` |

## Sử dụng shortcode tự viết

```
[ocop_noibat]                                        ← 8 sản phẩm hạng 4 sao mới nhất (mặc định)
[ocop_noibat so_luong="4" nhom_nganh="luong-thuc-do-uong"]
[ocop_danhsach nhom_nganh="thu-cong-my-nghe" so_luong="6" cot="3"]
[ocop_danhsach dia_phuong="chau-thanh" sap_xep="gia-tang"]
[ocop_bo_loc]                                        ← in bộ lọc 5 tiêu chí tại vị trí tùy ý
```

Slug 4 nhóm ngành: `luong-thuc-do-uong`, `duoc-lieu-thao-moc`,
`thu-cong-my-nghe`, `bao-bi-vat-lieu`.

## Ghi chú quan trọng

- **Thiếu ảnh sản phẩm sau khi gie dữ liệu?** (thẻ sản phẩm hiện logo thay ảnh)
  nghĩa là `seed-data.php` chạy trước khi copy ảnh mẫu. Copy 41 file trong
  `src/sample-data/images` vào `wp-content/uploads/ocoptv/` rồi chạy
  `setup/sua-anh.php` (copy vào thư mục gốc WordPress, mở
  `http://localhost/ocoptravinh/sua-anh.php?chay=1`) — tệp này gán lại ảnh
  cho 22 sản phẩm, tạo 4 album và gán logo mà không tạo dữ liệu trùng.
- **Dữ liệu là dữ liệu minh họa** phục vụ học phần (22 sản phẩm, 21 đơn vị,
  tin tức, ảnh); không phải số liệu điều tra thực tế — thay bằng dữ liệu thật
  và ảnh chụp thật khi triển khai.
- Bộ dữ liệu có 7 sản phẩm hạng 4 sao; shortcode `[ocop_noibat]` mặc định
  yêu cầu 8 nên hiển thị tối đa 7 thẻ — tăng `so_luong` hoặc bỏ thuộc tính
  `hang_sao` nếu muốn đủ 8 thẻ.
- Mã nguồn phát hành theo giấy phép **GPL-2.0-or-later** (tương thích
  WordPress); các plugin/theme bên ngoài giữ giấy khoản riêng của tác giả.

## Liên hệ

Sinh viên thực hiện: ………… — Lớp: ………… — Giảng viên hướng dẫn: …………
