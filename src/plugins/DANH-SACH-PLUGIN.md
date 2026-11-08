# Danh sách plugin và theme bên ngoài

Toàn bộ plugin/theme dưới đây tải trực tiếp từ kho WordPress.org
(không kèm mã nguồn trong repository vì dung lượng và để luôn dùng bản mới):

| STT | Thành phần | Phiên bản | Nguồn | Vai trò trong đồ án |
|---|---|---|---|---|
| 1 | Theme **Astra** | 4.6.x | wordpress.org/themes/astra | Theme cha — làm nền cho child theme `ocoptv` |
| 2 | **WooCommerce** | 9.x | wordpress.org/plugins/woocommerce | Kiểu nội dung `product`, giá bán, tồn kho, giỏ hàng, đặt hàng COD |
| 3 | **Advanced Custom Fields** | 6.x | wordpress.org/plugins/advanced-custom-fields | Giao diện nhập hạng sao, nguồn gốc, đơn vị sản xuất trên trang sản phẩm |
| 4 | **Custom Post Type UI** | 1.x | wordpress.org/plugins/custom-post-type-ui | Tùy chọn — kiểu nội dung và phân loại đã được đăng ký bằng code trong `inc/custom-types.php` |
| 5 | **WPForms Lite** | 1.8.x | wordpress.org/plugins/wpforms-lite | Biểu mẫu liên hệ (trang liên hệ đã có biểu mẫu tự viết thay thế) |
| 6 | **WP Super Cache** | 1.9.x | wordpress.org/plugins/wp-super-cache | Bộ nhớ đệm, dùng cho thí nghiệm hiệu năng ở Chương 4 |

Ghi chú:

- Child theme `ocoptv` tự đăng ký kiểu nội dung `don_vi_san_xuat`, phân loại
  `nhom_nganh_ocop`, `dia_phuong` và nhóm trường ACF bằng PHP
  (`inc/custom-types.php`), nên website chạy được ngay cả khi chưa cài
  Custom Post Type UI; ACF chỉ cần cho giao diện nhập liệu đẹp hơn
  (có hộp nhập liệu dự phòng nếu thiếu ACF).
- Plugin đề xuất thêm khi triển khai thật: **WP Mail SMTP** (gửi thư thông báo
  kiểm duyệt trên localhost), **Contact Form 7** hoặc WPForms bản đầy đủ.
