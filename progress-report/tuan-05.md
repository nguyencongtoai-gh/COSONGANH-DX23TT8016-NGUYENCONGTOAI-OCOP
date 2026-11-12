# Báo cáo tiến độ tuần 05
**Tuần 5/12 — Xây dựng child theme ocoptv**
## Việc đã thực hiện
- Tạo thư mục wp-content/themes/ocoptv với style.css khai báo Template: astra.
- Viết functions.php: enqueue đúng thứ tự (cha → con → ocoptv.css), require 7 module.
- Đăng ký kiểu nội dung, phân loại và nhóm trường ACF bằng PHP (inc/custom-types.php).
- Tùy biến nhận diện: logo, bộ màu xanh #1B5E20 – vàng #C9A227, footer 3 cột, ảnh đại diện theme.

## Kết quả tuần
Child theme kích hoạt thành công, giao diện mang nhận diện OCOP.

## Kế hoạch tuần tới
Xây dựng bộ dữ liệu thử nghiệm

## Commit gợi ý
`commit: 'child-theme-ocoptv-nhan-dien-xanh-vang'`
