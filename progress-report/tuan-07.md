# Báo cáo tiến độ tuần 07
**Tuần 7/12 — Chức năng tìm kiếm, lọc và phân loại**
## Việc đã thực hiện
- Viết inc/loc-san-pham.php xử lý 5 tiêu chí bằng hook pre_get_posts.
- Tìm theo tên (tham số s), nhóm ngành – địa phương (tax_query), đơn vị – hạng sao (meta_query), khoảng giá (BETWEEN trên _price).
- Giao diện bộ lọc sinh tự động từ phân loại; sắp xếp mới nhất / giá tăng / giá giảm.
- Kiểm thử 18 kịch bản lọc kết hợp — kết quả chính xác 100%.

## Kết quả tuần
Trang danh mục lọc đúng theo mọi tổ hợp tiêu chí.

## Kế hoạch tuần tới
Viết shortcode hiển thị sản phẩm

## Commit gợi ý
`commit: 'tim-kiem-loc-5-tieu-chi-pre-get-posts'`
