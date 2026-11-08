<?php
/**
 * File: setup/seed-data.php
 * Bo gie du lieu thu nghiem cho website OCOP Tra Vinh:
 *   - 4 nhom nganh OCOP + 8 dia phuong;
 *   - 21 ho so don vi san xuat;
 *   - 22 san pham OCOP du 7 thuoc tinh (bang 3.5 bao cao);
 *   - 12 bai tin tuc - su kien, 4 album thu vien anh;
 *   - 6 trang tinh, menu 8 muc, tai khoan mau (bientap, donvi01, donvi02).
 *
 * CACH CHAY (chon mot trong hai cach):
 *   Cach 1: copy tep nay vao thu muc goc WordPress (ngang hang wp-config.php),
 *           mo trinh duyet tai  http://localhost/ocoptravinh/seed-data.php?chay=1
 *           (voi WP-CLI:        wp eval-file setup/seed-data.php --  '?chay=1')
 *   Cach 2: su dung WP-CLI tu thu muc goc WordPress:
 *           wp eval-file duong-dan/toi/seed-data.php
 *
 * LUU Y:
 *   - Chay SAU khi da cai WordPress, theme Astra, child theme ocoptv
 *     va cac plugin (WooCommerce, ACF...).
 *   - De co anh dai dien san pham, copy thu muc src/sample-data/images
 *     cua repository vao  wp-content/uploads/ocoptv/  truoc khi chay.
 *   - Du lieu la du lieu minh hoa cua do an, khong phai so lieu thuc te.
 *   - Sau khi chay xong, XOA tep nay khoi may chu cong khai va doi mat khau
 *     cac tai khoan mau (mat khau mac dinh luu trong README.md).
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('OCOPTV_SEED_MAT_KHAU', 'Ocop@2026'); // mat khau tai khoan mau

// ---------------------------------------------------------------------------
// 0. Tim va nap WordPress
// ---------------------------------------------------------------------------
$wp_load = null;
for ($i = 0; $i <= 4; $i++) {
    $duong_dan = dirname(__FILE__) . str_repeat('/..', $i) . '/wp-load.php';
    if (file_exists($duong_dan)) { $wp_load = realpath($duong_dan); break; }
    // cung cap voi cac tep wp-load.php o thu muc hien tai
    $duong_dan2 = dirname(__FILE__) . str_repeat('/..', $i) . '/wp-load.php';
    if (realpath($duong_dan2) && file_exists($duong_dan2)) { $wp_load = realpath($duong_dan2); break; }
}
if (!$wp_load) {
    header('Content-Type: text/html; charset=utf-8');
    exit('<h3>Khong tim thay wp-load.php</h3><p>Hay copy tep nay vao thu muc goc '
        . 'WordPress (ngang hang wp-config.php) roi chay lai.</p>');
}
require_once $wp_load;
require_once ABSPATH . 'wp-admin/includes/image.php';

/** Hien thi trang huong dan khi chua co tham so ?chay=1 */
if (!isset($_GET['chay'])) {
    header('Content-Type: text/html; charset=utf-8');
    exit('<h2>OCOP Trà Vinh — bộ gie dữ liệu thử nghiệm</h2>'
        . '<p>Tệp này sẽ tạo <strong>4 nhóm ngành, 8 địa phương, 21 đơn vị sản xuất, '
        . '22 sản phẩm OCOP, 12 bài tin tức, 4 album ảnh, 6 trang, menu 8 mục '
        . 'và các tài khoản mẫu</strong> (bientap, donvi01, donvi02).</p>'
        . '<p><strong>Cần có:</strong> WordPress đã cài, theme Astra + child theme '
        . '<em>ocoptv</em> + WooCommerce đang kích hoạt; ảnh mẫu đã copy vào '
        . '<code>wp-content/uploads/ocoptv/</code> (nếu muốn có ảnh).</p>'
        . '<p>Để chạy, thêm tham số: <a href="?chay=1"><code>?chay=1</code></a></p>');
}

if (get_option('ocoptv_da_seed') === '1') {
    header('Content-Type: text/html; charset=utf-8');
    exit('<h3>Dữ liệu đã được gie trước đó</h3>'
        . '<p>Website đã có bộ dữ liệu thử nghiệm (tuỳ chọn <code>ocoptv_da_seed</code>). '
        . 'Nếu muốn gie lại từ đầu, hãy cài lại WordPress rồi chạy tệp này lần nữa.</p>');
}

if (!post_type_exists('product')) {
    header('Content-Type: text/html; charset=utf-8');
    exit('<h3>Chưa kích hoạt WooCommerce</h3>'
        . '<p>Kiểu nội dung <code>product</code> chưa tồn tại — hãy cài và kích hoạt '
        . 'plugin WooCommerce trước khi gie dữ liệu.</p>');
}

// ===========================================================================
// 1. Vai tro dv_san_xuat + tai khoan mau
// ===========================================================================
if (get_role('dv_san_xuat') === null) {
    add_role('dv_san_xuat', 'Don vi san xuat', array(
        'read' => true, 'upload_files' => true,
        'edit_products' => true, 'edit_product' => true,
        'publish_products' => false, 'edit_others_products' => false,
        'delete_others_products' => false,
        'edit_don_vi_san_xuat' => true, 'edit_don_vi_san_xuats' => true,
        'publish_don_vi_san_xuats' => false,
        'edit_others_don_vi_san_xuats' => false,
        'delete_others_don_vi_san_xuats' => false,
    ));
}

/** Tao nguoi dung neu chua ton tai */
function ocoptv_seed_user($login, $email, $ten_hien_thi, $vai_tro, $mat_khau) {
    if (get_user_by('login', $login)) { return null; }
    return wp_insert_user(array(
        'user_login'   => $login,
        'user_email'   => $email,
        'display_name' => $ten_hien_thi,
        'user_pass'    => $mat_khau,
        'role'         => $vai_tro,
    ));
}

$quan_tri = get_users(array('role' => 'administrator', 'number' => 1));
$id_admin = $quan_tri ? $quan_tri[0]->ID : 1;

ocoptv_seed_user('bientap',  'bientap@ocoptravinh.local',  'Biên tập viên OCOP', 'editor',       OCOPTV_SEED_MAT_KHAU);
ocoptv_seed_user('donvi01',  'donvi01@ocoptravinh.local',  'HTX Dừa xanh Càng Long', 'dv_san_xuat', OCOPTV_SEED_MAT_KHAU);
ocoptv_seed_user('donvi02',  'donvi02@ocoptravinh.local',  'Cơ sở kẹo dừa Hồng Phúc', 'dv_san_xuat', OCOPTV_SEED_MAT_KHAU);

// ===========================================================================
// 2. Phan loai: 4 nhom nganh OCOP + 8 dia phuong
// ===========================================================================
$nhom_nganh = array(
    'luong-thuc-do-uong' => 'Nhóm 1: Lương thực, đồ uống, thủy sản',
    'duoc-lieu-thao-moc' => 'Nhóm 2: Dược liệu, thảo mộc',
    'thu-cong-my-nghe'   => 'Nhóm 3: Thủ công mỹ nghệ',
    'bao-bi-vat-lieu'    => 'Nhóm 4: Bao bì, vật liệu',
);
foreach ($nhom_nganh as $slug => $ten) {
    if (!term_exists($slug, 'nhom_nganh_ocop')) {
        wp_insert_term($ten, 'nhom_nganh_ocop', array('slug' => $slug));
    }
}

$dia_phuong = array(
    'thanh-pho-tra-vinh' => 'TP. Trà Vinh',
    'thi-xa-duyen-hai'   => 'TX. Duyên Hải',
    'duyen-hai'          => 'Duyên Hải',
    'cang-long'          => 'Càng Long',
    'chau-thanh'         => 'Châu Thành',
    'tieu-can'           => 'Tiểu Cần',
    'cau-ngang'          => 'Cầu Ngang',
    'tra-cu'             => 'Trà Cú',
);
foreach ($dia_phuong as $slug => $ten) {
    if (!term_exists($slug, 'dia_phuong')) {
        wp_insert_term($ten, 'dia_phuong', array('slug' => $slug));
    }
}

// ===========================================================================
// 3. 21 ho so don vi san xuat
//    [ten, dia_phuong, dia_chi, dien_thoai, thu_dien_tu, mo_ta]
// ===========================================================================
$don_vi_du_lieu = array(
    array('HTX Dừa xanh Càng Long',            'cang-long',          'Ấp Trung Hòa, xã Hàm Tân, huyện Càng Long',      '0290.374.2001', 'htxduaxanhcanglong@gmail.com',   'Hợp tác xã chuyên trồng và bao tiêu dừa xiêm xanh tại huyện Càng Long, liên kết với hơn 40 hộ dân.'),
    array('HTX Nông sản Long Hòa',             'thanh-pho-tra-vinh', 'Khóm 4, phường Long Hòa, TP. Trà Vinh',           '0290.385.1010', 'htxnongsanlonghoa@gmail.com',    'Hợp tác xã chế biến dừa sáp và các nông sản đặc sản của thành phố Trà Vinh.'),
    array('Cơ sở kẹo dừa Hồng Phúc',           'cang-long',          'Ấp Long Thạnh, xã Đại An, huyện Càng Long',       '0290.374.2111', 'kehongphuc@gmail.com',           'Cơ sở sản xuất kẹo dừa truyền thống hơn 20 năm, sản phẩm bán khắp các tỉnh miền Tây.'),
    array('Cơ sở mứt dừa Thanh Tuyền',         'cang-long',          'Ấp Thạnh Hòa, xã Trung An, huyện Càng Long',      '0290.374.2222', 'mutduathanhtuyen@gmail.com',     'Cơ sở hộ gia đình chuyên mứt dừa các vị: sữa, dâu, cacao nguyên chất.'),
    array('Công ty TNHH Tâm Việt',             'chau-thanh',         'Xã Hòa Minh, huyện Châu Thành',                   '0290.386.3003', 'info@tamviet-foods.vn',          'Doanh nghiệp chế biến nước cốt dừa tiệt trùng đóng lon, xuất khẩu nhỏ lẻ.'),
    array('HTX Dừa Mỹ Long',                   'cau-ngang',          'Ấp Mỹ Long B, xã Mỹ Long, huyện Cầu Ngang',       '0290.387.4004', 'htxduamylong@gmail.com',         'Hợp tác xã sản xuất dầu dừa nguyên chất lạnh theo phương pháp truyền thống.'),
    array('HTX Nông nghiệp Đại An',            'cang-long',          'Xã Đại An, huyện Càng Long',                      '0290.374.5005', 'htxdaian@gmail.com',             'Hợp tác xã trồng và xay xát gạo thơm hạt dài trên ruộng nhiễm mặn nhẹ.'),
    array('HTX Bánh tét lá cẩm Trà Cú',        'tra-cu',             'Xã Hàm Giang, huyện Trà Cú',                      '0290.388.6006', 'htxbanhtetlacam@gmail.com',      'Hợp tác xã người Khmer giữ nghề bánh tét lá cẩm tím đặc trưng của huyện Trà Cú.'),
    array('Công ty TNHH Hải sản Duyên Hải',    'duyen-hai',          'Khóm 1, thị trấn Duyên Hải, huyện Duyên Hải',     '0290.389.7007', 'info@haisanduyenhai.vn',         'Doanh nghiệp chế biến tôm – cá khô ven biển Duyên Hải, đạt chuẩn VSATTP.'),
    array('HTX Thủy sản Đông Hải',             'duyen-hai',          'Xã Long Hữu, huyện Duyên Hải',                    '0290.389.8008', 'htxdonghai@gmail.com',           'Hợp tác xã đánh bắt và sơ chế cá một nắng, tôm khô quanh năm.'),
    array('HTX Mắm bò hóc Trà Cú',             'tra-cu',             'Xã Đôn Châu, huyện Trà Cú',                       '0290.388.9009', 'htxmambohoc@gmail.com',          'Hợp tác xã sản xuất mắm bò hóc theo bí quyết Khmer truyền thống.'),
    array('Công ty TNHH Đồ uống Xanh',         'thanh-pho-tra-vinh', 'Khu công nghiệp Trà Vinh, phường 4, TP. Trà Vinh','0290.381.0010', 'info@do-uong-xanh.vn',           'Doanh nghiệp đóng chai nước dừa tươi không bảo quản lạnh, phân phối toàn quốc.'),
    array('Tổ hợp tác Ong Nhị Trường',         'cang-long',          'Xã Lương Hòa, huyện Càng Long',                   '0290.374.1011', 'totnghiepon@gmail.com',          'Tổ hợp tác nuôi ong thu mật hoa dừa trên các vườn dừa huyện Càng Long.'),
    array('Công ty TNHH Than hoạt tính Trà Vinh','chau-thanh',       'Thị trấn Châu Thành, huyện Châu Thành',           '0290.386.2012', 'info@thanhoattinh-tv.vn',        'Doanh nghiệp sản xuất than hoạt tính từ vỏ dừa phục vụ lọc nước, làm đẹp.'),
    array('HTX Dược liệu Trà Vinh',            'chau-thanh',         'Thị trấn Châu Thành, huyện Châu Thành',           '0290.386.3013', 'htxduoclieutv@gmail.com',        'Hợp tác xã sấy khô trà hoa đậu biếc và các dược liệu sạch theo tiêu chuẩn GACP.'),
    array('HTX Sơ ri Châu Thành',              'chau-thanh',         'Thị trấn Châu Thành, huyện Châu Thành',           '0290.386.4014', 'htxsorichauthanh@gmail.com',     'Hợp tác xã trồng sơ ri và cô đặc siro từ quả chín tươi 100%.'),
    array('HTX Tinh dầu tràm Ngũ Lạc',         'duyen-hai',          'Xã Ngũ Lạc, huyện Duyên Hải',                     '0290.389.5015', 'htxtinhdautram@gmail.com',       'Hợp tác tác xã chưng cất tinh dầu tràm gió nguyên chất theo kiểu thủ công.'),
    array('HTX Dệt lụa Khmer Trà Cú',          'tra-cu',             'Huyện Trà Cú, tỉnh Trà Vinh',                     '0290.388.6016', 'htxdetluakhmer@gmail.com',       'Hợp tác xã dệt khăn, vải lụa truyền thống của người Khmer trên khung gỗ.'),
    array('HTX Mây tre đan Châu Thành',        'chau-thanh',         'Huyện Châu Thành, tỉnh Trà Vinh',                 '0290.386.5017', 'htxmaytrechauthanh@gmail.com',   'Hợp tác xã đan giỏ, túi từ bẹ dừa và mây tre cho du lịch sinh thái.'),
    array('Cơ sở Thủ công Long Đức',           'thanh-pho-tra-vinh', 'Xã Long Đức, TP. Trà Vinh',                       '0290.382.7018', 'thuconglongduc@gmail.com',       'Cơ sở chế tác đèn trang trí, đồ lưu niệm từ vỏ dừa và gỗ dừa.'),
    array('Công ty TNHH Sinh học Xanh',        'chau-thanh',         'Huyện Châu Thành, tỉnh Trà Vinh',                 '0290.386.6019', 'info@sinhhocxanh.vn',            'Doanh nghiệp sản xuất hộp giấy sinh học từ bã mủ dừa, phân hủy sinh học.'),
);

$id_don_vi = array();
foreach ($don_vi_du_lieu as $i => $dv) {
    $id_don_vi[$i + 1] = wp_insert_post(array(
        'post_type'    => 'don_vi_san_xuat',
        'post_status'  => 'publish',
        'post_author'  => $id_admin,
        'post_title'   => $dv[0],
        'post_content' => '<p>' . $dv[5] . '</p><p>Liên hệ trực tiếp để đặt số lượng lớn '
            . 'hoặc tham quan quy trình sản xuất tại cơ sở.</p>',
        'post_excerpt' => wp_trim_words($dv[5], 22, '…'),
    ));
    if (!$id_don_vi[$i + 1] || is_wp_error($id_don_vi[$i + 1])) {
        $id_don_vi[$i + 1] = 0;
        continue;
    }
    wp_set_object_terms($id_don_vi[$i + 1], $dv[1], 'dia_phuong');
    update_post_meta($id_don_vi[$i + 1], 'dia_chi', $dv[2]);
    update_post_meta($id_don_vi[$i + 1], 'dien_thoai', $dv[3]);
    update_post_meta($id_don_vi[$i + 1], 'thu_dien_tu', $dv[4]);
}

// ===========================================================================
// 4. 22 san pham OCOP (bang 3.5 cua bao cao)
//    [ten, nhom_nganh, #don_vi, dia_phuong, sao, gia, nguon_goc, mo_ta]
// ===========================================================================
$san_pham_du_lieu = array(
    array('Dừa xiêm xanh Càng Long',            'luong-thuc-do-uong', 1,  'cang-long',          3, 25000,  'Xã Hàm Tân, huyện Càng Long',          'Dừa xiêm xanh trái nhỏ, nước ngọt thanh, cơm dẻo — đặc sản vườn dừa Càng Long. Bảo quản ở nhiệt độ thường, dùng tốt trong 10 ngày sau thu hoạch.'),
    array('Dừa sáp Long Hòa',                   'luong-thuc-do-uong', 2,  'thanh-pho-tra-vinh', 4, 90000,  'Phường Long Hòa, TP. Trà Vinh',        'Dừa sáp cơm dày, béo như kem dừa; hiếm gặp chỉ ở Trà Vinh. Được bao bọc kín và giữ lạnh khi vận chuyển.'),
    array('Kẹo dừa Càng Long',                  'luong-thuc-do-uong', 3,  'cang-long',          3, 60000,  'Xã Trung An, huyện Càng Long',         'Kẹo dừa nấu thủ công từ cơm dừa non và đường thốt nốt, dẻo thơm, đóng túi 500 g.'),
    array('Mứt dừa sữa',                        'luong-thuc-do-uong', 4,  'cang-long',          3, 55000,  'Xã Trung An, huyện Càng Long',         'Mứt dừa sợi mảnh ướp sữa tươi, các vị sữa – dâu – cacao, đóng lọ 350 g dùng làm quà biếu.'),
    array('Nước cốt dừa tiệt trùng',            'luong-thuc-do-uong', 5,  'chau-thanh',         3, 25000,  'Xã Hòa Minh, huyện Châu Thành',        'Nước cốt dừa nguyên chất tiệt trùng UHT, đóng lon 350 ml, thuận tiện nấu ăn và pha chế đồ uống.'),
    array('Dầu dừa nguyên chất Mỹ Long',       'duoc-lieu-thao-moc', 6,  'cau-ngang',          4, 120000, 'Xã Mỹ Long, huyện Cầu Ngang',          'Dầu dừa ép lạnh 100%, không chất bảo quản; dùng dưỡng da, dưỡng tóc và chế biến món ăn. Chai 250 ml.'),
    array('Dừa sấy dẻo Càng Long',              'luong-thuc-do-uong', 1,  'cang-long',          4, 95000,  'Xã Hàm Tân, huyện Càng Long',          'Dừa non sấy dẻo giòn ngọt tự nhiên, không tẩm đường, đóng túi hút chân không 300 g.'),
    array('Gạo thơm hạt dài Đại An',            'luong-thuc-do-uong', 7,  'cang-long',          3, 165000, 'Xã Đại An, huyện Càng Long',           'Gạo thơm hạt dài canh tác trên ruộng phèn nhẹ, bao túi 5 kg, cơm mềm thơm để lâu không khô.'),
    array('Bánh tét lá cẩm Trà Cú',             'luong-thuc-do-uong', 8,  'tra-cu',             4, 45000,  'Xã Hàm Giang, huyện Trà Cú',           'Bánh tét lá cẩm vỏ tím tự nhiên, nhân đậu xanh thịt heo, gói lá chuối theo kiểu Khmer. Đôi bánh 1 kg.'),
    array('Tôm khô Duyên Hải',                  'luong-thuc-do-uong', 9,  'duyen-hai',          3, 350000, 'Xã Long Hữu, huyện Duyên Hải',         'Tôm đất đánh bắt ven biển phơi một nắng, size nhỏ đều con, túi chân không 500 g đậm vị ngọt.'),
    array('Cá khô một nắng',                    'luong-thuc-do-uong', 10, 'duyen-hai',          3, 180000, 'Xã Long Hữu, huyện Duyên Hải',         'Cá kèo, cá sặc một nắng ướt mặn vừa, không chất bảo quản; túi 500 g bảo quản tủ lạnh.'),
    array('Mắm bò hóc truyền thống',            'luong-thuc-do-uong', 11, 'tra-cu',             4, 40000,  'Xã Đôn Châu, huyện Trà Cú',            'Nước mắm bò hóc lên men tự nhiên hơn 6 tháng theo bí quyết Khmer, vị đậm hậu ngọt, chai 250 ml.'),
    array('Nước dừa tươi đóng chai',            'luong-thuc-do-uong', 12, 'thanh-pho-tra-vinh', 3, 30000,  'Phường 4, TP. Trà Vinh',               'Nước dừa xiêm tươi đóng chai 330 ml không thêm đường, bảo quản lạnh, dùng trong 7 ngày.'),
    array('Mật ong hoa dừa',                    'duoc-lieu-thao-moc', 13, 'cang-long',          3, 180000, 'Xã Lương Hòa, huyện Càng Long',        'Mật ong khai thác trên vườn dừa tráng mật, màu vàng nhạt, hương thơm dịu; chai thủy tinh 500 ml.'),
    array('Than hoạt tính vỏ dừa',              'duoc-lieu-thao-moc', 14, 'chau-thanh',         4, 150000, 'Thị trấn Châu Thành, huyện Châu Thành','Than hoạt tính hoạt độ cao từ vỏ dừa, hạt 3–5 mm, dùng lọc nước và chăm sóc da. Túi 1 kg.'),
    array('Trà hoa đậu biếc sấy khô',           'duoc-lieu-thao-moc', 15, 'chau-thanh',         3, 85000,  'Thị trấn Châu Thành, huyện Châu Thành','Hoa đậu biếc sấy lạnh giữ màu xanh tím tự nhiên, pha nước vàng xanh đẹp mắt; hộp 100 g.'),
    array('Siro sơ ri cô đặc',                  'luong-thuc-do-uong', 16, 'chau-thanh',         3, 70000,  'Thị trấn Châu Thành, huyện Châu Thành','Siro sơ ri cô đặc từ quả chín tươi, giàu vitamin C, pha nước uống hoặc rót lên kem, chai 500 ml.'),
    array('Tinh dầu tràm Ngũ Lạc',              'duoc-lieu-thao-moc', 17, 'duyen-hai',          4, 110000, 'Xã Ngũ Lạc, huyện Duyên Hải',          'Tinh dầu tràm gió chưng cất thủ công, hàm lượng cineole cao, dùng xông, bôi da cho cả nhà; chai 30 ml.'),
    array('Khăn lụa dệt truyền thống Khmer',    'thu-cong-my-nghe',   18, 'tra-cu',             3, 250000, 'Huyện Trà Cú, tỉnh Trà Vinh',          'Khăn lụa dệt tay trên khung gỗ truyền thống của nghệ nhân Khmer, họa tiết sarongan, kích thước 1,8 m.'),
    array('Giỏ đan bẹ dừa',                     'thu-cong-my-nghe',   19, 'chau-thanh',         3, 60000,  'Huyện Châu Thành, tỉnh Trà Vinh',      'Giỏ đan từ bẹ dừa và mây tre, bền chắc, dùng đựng quà hoặc trang trí thân thiện môi trường.'),
    array('Đèn trang trí vỏ dừa',               'thu-cong-my-nghe',   20, 'thanh-pho-tra-vinh', 3, 120000, 'Xã Long Đức, TP. Trà Vinh',            'Đèn lồng và đèn bàn chạm khắc từ vỏ dừa già, giữ dầu tự nhiên của gỗ, mỗi chiếc một kiểu riêng.'),
    array('Hộp giấy từ bã mủ dừa',              'bao-bi-vat-lieu',    21, 'chau-thanh',         3, 300000, 'Huyện Châu Thành, tỉnh Trà Vinh',      'Bộ 50 hộp giấy ép từ bã mủ dừa, phân hủy sinh học 90 ngày, thay thế hộp nhựa đựng thực phẩm.'),
);

$id_san_pham = array();
foreach ($san_pham_du_lieu as $i => $sp) {
    list($ten, $nhom, $so_dv, $dp, $sao, $gia, $nguon, $mo_ta) = $sp;
    $sku = sprintf('OCOP-TV-%03d', $i + 1);
    $id = wp_insert_post(array(
        'post_type'    => 'product',
        'post_status'  => 'publish',
        'post_author'  => $id_admin,
        'post_title'   => $ten,
        'post_content' => '<p>' . $mo_ta . '</p>',
        'post_excerpt' => wp_trim_words($mo_ta, 20, '…'),
    ));
    if (!$id || is_wp_error($id)) { continue; }
    $id_san_pham[$i + 1] = $id;

    // ---- Bay thuoc tinh bat buoc cua san pham OCOP ----
    update_post_meta($id, 'hang_sao_ocop', (string) $sao);
    update_post_meta($id, 'nguon_goc', $nguon);
    update_post_meta($id, 'don_vi_san_xuat', $id_don_vi[$so_dv]);
    wp_set_object_terms($id, $nhom, 'nhom_nganh_ocop');
    wp_set_object_terms($id, $dp, 'dia_phuong');

    // ---- Gia ban, ton kho (WooCommerce) ----
    $ton_kho = 40 + (($i * 17) % 200); // 40–240 don vi
    if (class_exists('WooCommerce') && function_exists('wc_get_product')) {
        try {
            $p = wc_get_product($id);
            if ($p && is_callable(array($p, 'save'))) {
                $p->set_regular_price((string) $gia);
                $p->set_price((string) $gia);
                $p->set_sku($sku);
                $p->set_manage_stock(true);
                $p->set_stock_quantity($ton_kho);
                $p->set_stock_status('instock');
                $p->set_tax_status('taxable');
                $p->set_catalog_visibility('visible');
                $p->save();
            }
        } catch (Exception $e) {
            // Phu luu: ghi truc tiep vao wp_postmeta
            update_post_meta($id, '_sku', $sku);
            update_post_meta($id, '_regular_price', (string) $gia);
            update_post_meta($id, '_price', (string) $gia);
            update_post_meta($id, '_manage_stock', 'yes');
            update_post_meta($id, '_stock', $ton_kho);
            update_post_meta($id, '_stock_status', 'instock');
        }
    } else {
        update_post_meta($id, '_sku', $sku);
        update_post_meta($id, '_regular_price', (string) $gia);
        update_post_meta($id, '_price', (string) $gia);
        update_post_meta($id, '_manage_stock', 'yes');
        update_post_meta($id, '_stock', $ton_kho);
        update_post_meta($id, '_stock_status', 'instock');
    }
}

// ===========================================================================
// 5. Nhap anh mau (neu da copy src/sample-data/images -> uploads/ocoptv)
// ===========================================================================
$uploads = wp_upload_dir();
$thu_muc_anh = trailingslashit($uploads['basedir']) . 'ocoptv';
$so_anh_sp = 0; $so_anh_gal = 0;

/** Dang ky mot tep anh trong uploads/ocoptv thanh attachment */
function ocoptv_seed_anh($tep) {
    if (!$tep || !file_exists($tep)) { return 0; }
    $ten = basename($tep);
    $att = wp_insert_attachment(array(
        'post_mime_type' => 'image/png',
        'post_title'     => preg_replace('/[-_.]/', ' ', pathinfo($ten, PATHINFO_FILENAME)),
        'post_status'    => 'inherit',
    ), $tep, 0);
    if (!$att || is_wp_error($att)) { return 0; }
    wp_update_attachment_metadata($att, wp_generate_attachment_metadata($att, $tep));
    return $att;
}

if (is_dir($thu_muc_anh)) {
    foreach (glob($thu_muc_anh . '/sp-*.png') as $tep) {
        $so = intval(preg_replace('/[^0-9]/', '', basename($tep, '.png')));
        if ($so && isset($id_san_pham[$so])) {
            $att = ocoptv_seed_anh($tep);
            if ($att) { set_post_thumbnail($id_san_pham[$so], $att); $so_anh_sp++; }
        }
    }
}

$id_anh_gal = array();
if (is_dir($thu_muc_anh)) {
    $tep_gal = glob($thu_muc_anh . '/gal-*.png');
    sort($tep_gal);
    foreach ($tep_gal as $tep) {
        $att = ocoptv_seed_anh($tep);
        if ($att) { $id_anh_gal[] = $att; $so_anh_gal++; }
    }
}

// ===========================================================================
// 6. 12 bai tin tuc - su kien
// ===========================================================================
$tin_tuc = array(
    array('Hội chợ sản phẩm OCOP Trà Vinh 2026 giới thiệu gần 100 gian hàng', 'Hội chợ – sự kiện',
        'Hội chợ diễn ra ba ngày tại trung tâm hội nghị tỉnh, quy tụ gần 100 gian hàng của các chủ thể OCOP trong và ngoài tỉnh. Đây là dịp các đơn vị giới thiệu sản phẩm mới, ký kết hợp tác phân phối với hệ thống siêu thị.'),
    array('Trà Vinh có thêm sản phẩm đạt chứng nhận OCOP hạng 4 sao', 'Tin hoạt động',
        'Sản phẩm dầu dừa nguyên chất của hợp tác xã địa phương vừa được hội đồng đánh giá tỉnh công nhận hạng 4 sao theo bộ tiêu chí chương trình OCOP. Việc này giúp chủ thể mở rộng kênh phân phối hiện đại.'),
    array('Đào tạo ứng dụng thương mại điện tử cho chủ thể OCOP', 'Hướng dẫn',
        'Khóa học trang bị kỹ năng chụp ảnh sản phẩm, viết mô tả và bán hàng trên nền tảng số cho hơn 80 học viên là đại diện các hợp tác xã, hộ kinh doanh tham gia chương trình OCOP của tỉnh.'),
    array('Sản phẩm OCOP Trà Vinh tham gia tuần lễ hàng Việt tại Hà Nội', 'Hội chợ – sự kiện',
        'Gian hàng Trà Vinh giới thiệu dừa sáp, kẹo dừa, tinh dầu tràm và khăn lụa Khmer, thu hút đông khách tham quan và đơn vị phân phối đặt hàng dự kiến cho mùa lễ hội cuối năm.'),
    array('HTX Dừa xanh Càng Long ký kết bao tiêu dừa tươi cho chuỗi siêu thị', 'Tin hoạt động',
        'Hợp tác xã và chuỗi siêu thị khu vực phía Nam ký thỏa thuận bao tiêu 300 tấn dừa tươi mỗi năm, giúp hơn 40 hộ thành viên ổn định đầu ra và thu nhập.'),
    array('Kỹ thuật bảo quản lạnh kéo dài thời hạn tiêu thụ dừa sáp', 'Hướng dẫn',
        'Áp dụng dự bao gói hút chân không kết hợp chuỗi lạnh 2–5 độ C, thời hạn sử dụng dừa sáp tăng từ 7 lên 20 ngày, mở khả năng xuất khẩu đi xa cho chủ thể sản xuất.'),
    array('Hướng dẫn đăng ký sản phẩm OCOP mới trên cổng thông tin quốc gia', 'Hướng dẫn',
        'Chủ thể chuẩn bị hồ sơ năng lực, kiểm nghiệm sản phẩm và bản tuyên bố quyền sử dụng nhãn; nộp trực tuyến trên cổng thông tin chương trình OCOP quốc gia và theo dõi tiến trình phê duyệt theo từng bước.'),
    array('Trà Vinh tổ chức giám sát đánh giá lại 15 sản phẩm OCOP hạng 3 sao', 'Tin hoạt động',
        'Đoàn giám sát kiểm tra điều kiện sản xuất, truy xuất nguồn gốc và chất lượng ổn định của các sản phẩm hạng 3 sao; kết quả là căn cứ để nâng hạng hoặc duy trì hạng sao.'),
    array('Không gian trưng bày sản phẩm OCOP mở cửa tại trung tâm thành phố', 'Tin hoạt động',
        'Không gian giới thiệu vĩnh viễn hơn 200 sản phẩm OCOP của tỉnh mở cửa miễn phí cho người dân và du khách, đồng thời là điểm giao dịch giữa chủ thể và nhà phân phối.'),
    array('Lễ hội Ok Om Bok giới thiệu sản phẩm thủ công mỹ nghệ Khmer', 'Hội chợ – sự kiện',
        'Trong khuôn khổ lễ hội mặt trăng của người Khmer, các sản phẩm khăn lụa, đèn vỏ dừa, giỏ đan được trưng bày và biểu diễn nghề dệt trực tiếp tại sân khấu dân gian.'),
    array('Hướng dẫn đặt hàng trực tuyến trên website OCOP Trà Vinh', 'Hướng dẫn',
        'Người dùng tìm sản phẩm theo nhóm ngành, địa phương hoặc hạng sao; xem chi tiết bảy thuộc tính sản phẩm; thêm vào giỏ và thanh toán khi nhận hàng. Hướng dẫn từng bước kèm hình ảnh minh họa.'),
    array('Kết nối chủ thể OCOP với hệ thống logistics nông sản ĐBSCL', 'Tin hoạt động',
        'Chương trình phối hợp đơn vị logistics mở tuyến vận chuyển lạnh từ Trà Vinh đi TP. Hồ Chí Minh, giảm 15% chi phí cho các chủ thể vận chuyển sản phẩm tươi.'),
);

foreach (array('Tin hoạt động', 'Hội chợ – sự kiện', 'Hướng dẫn') as $chuyen_muc) {
    if (!term_exists($chuyen_muc, 'category')) {
        wp_insert_term($chuyen_muc, 'category');
    }
}
foreach ($tin_tuc as $i => $tin) {
    wp_insert_post(array(
        'post_type'    => 'post',
        'post_status'  => 'publish',
        'post_author'  => $id_admin,
        'post_title'   => $tin[0],
        'post_content' => '<p>' . $tin[2] . '</p>',
        'post_excerpt' => wp_trim_words($tin[2], 24, '…'),
        'post_date'    => date('Y-m-d H:i:s', time() - ($i + 1) * 3 * 86400),
    ));
    // gan chuyen muc vua tao
}
// Gan chuyen muc cho tung bai (can lam sau khi tao het de co ID bai viet)
$tin_da_tao = get_posts(array('post_type' => 'post', 'numberposts' => 12, 'orderby' => 'date', 'order' => 'DESC'));
if ($tin_da_tao) {
    $phan_bo = array();
    foreach ($tin_tuc as $tin) { $phan_bo[$tin[0]] = $tin[1]; }
    foreach ($tin_da_tao as $bai) {
        if (isset($phan_bo[$bai->post_title])) {
            wp_set_object_terms($bai->ID, $phan_bo[$bai->post_title], 'category');
        }
    }
}

// ===========================================================================
// 7. Album thu vien anh + cac trang tinh
// ===========================================================================
function ocoptv_seed_trang($tieu_de, $noi_dung, $mau = '') {
    global $id_admin;
    $id = wp_insert_post(array(
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'post_author'  => $id_admin,
        'post_title'   => $tieu_de,
        'post_content' => $noi_dung,
    ));
    if ($id && !is_wp_error($id) && $mau) {
        update_post_meta($id, '_wp_page_template', $mau);
    }
    return ($id && !is_wp_error($id)) ? $id : 0;
}

$album_du_lieu = array(
    'Album: Hội chợ OCOP 2026'      => array_slice($id_anh_gal, 0, 5),
    'Album: Cánh đồng và làng nghề' => array_slice($id_anh_gal, 5, 5),
    'Album: Sản phẩm OCOP tiêu biểu' => array_slice($id_anh_gal, 10, 4),
    'Album: Đêm văn hóa Khmer'       => array_slice($id_anh_gal, 14, 4),
);
$id_album = array();
foreach ($album_du_lieu as $ten_album => $ids) {
    $id_a = 0;
    if (!empty($ids)) {
        $id_a = ocoptv_seed_trang($ten_album,
            '[gallery ids="' . implode(',', $ids) . '" columns="3" link="file"]');
    }
    $id_album[] = $id_a;
}

$id_trang_chu = ocoptv_seed_trang('Trang chủ',
    '<p class="ocop-gioi-thieu-ngan">Chào mừng bạn đến với <strong>OCOP Trà Vinh</strong> — '
    . 'cổng giới thiệu các sản phẩm chương trình Một xã một sản phẩm của tỉnh Trà Vinh. '
    . 'Khám phá đặc sản địa phương theo nhóm ngành, địa phương, đơn vị sản xuất và hạng sao OCOP.</p>'
    . "\n\n<h2>Sản phẩm OCOP nổi bật</h2>\n"
    . '[ocop_noibat so_luong="8"]' . "\n\n"
    . '<h2>Mới giới thiệu</h2>' . "\n"
    . '[ocop_danhsach so_luong="4" cot="4"]');

ocoptv_seed_trang('Giới thiệu OCOP',
    '<h2>Chương trình OCOP là gì?</h2>'
    . '<p>Chương trình "Một xã một sản phẩm" (OCOP) khởi xướng tại Nhật Bản và được Thủ tướng Chính phủ '
    . 'phê duyệt tại Việt Nam theo Quyết định số 490/QĐ-TTg năm 2018; giai đoạn 2023–2025 tiếp tục được triển khai '
    . 'theo Quyết định số 919/QĐ-TTg năm 2022. Chương trình hướng tới phát triển sản phẩm chủ lực của từng địa phương '
    . 'theo chuỗi giá trị, do chính người dân là chủ thể sáng tạo và thụ hưởng.</p>'
    . '<h2>Đề án OCOP Trà Vinh</h2>'
    . '<p>Trà Vinh xác định OCOP là một trong những nhiệm vụ trọng tâm về nông nghiệp, nông thôn mới. '
    . 'Đến tháng 2/2024, tỉnh có 299 sản phẩm OCOP của 199 chủ thể được công nhận, trong đó 245 sản phẩm hạng 3 sao '
    . 'và 44 sản phẩm hạng 4 sao — phản ánh thế mạnh về dừa, thủy sản và sản phẩm văn hóa Khmer.</p>'
    . '<h2>Website này làm gì?</h2>'
    . '<p>Website giới thiệu và quảng bá sản phẩm OCOP của địa phương: tra cứu thông tin sản phẩm theo nhóm ngành, '
    . 'địa phương, đơn vị sản xuất và hạng sao; giới thiệu hồ sơ đơn vị sản xuất; cập nhật tin tức – sự kiện; '
    . 'hỗ trợ đặt hàng trực tuyến thông qua tích hợp WooCommerce. Mọi thông tin sản phẩm do chủ thể cung cấp '
    . 'và được biên tập kiểm duyệt trước khi công bố.</p>');

// Trang thu vien: lien ket toi 4 album vua tao (neu co)
$ds_album = '';
foreach ($id_album as $ia) {
    if ($ia) {
        $ds_album .= '<li><a href="' . esc_url(get_permalink($ia)) . '">'
            . esc_html(get_the_title($ia)) . '</a></li>';
    }
}
ocoptv_seed_trang('Thư viện hình ảnh',
    '<p>Thư viện hình ảnh hoạt động, hội chợ và sản phẩm OCOP Trà Vinh. Chọn một album để xem ảnh lớn.</p>'
    . ($ds_album ? '<ul class="ocop-danh-sach-album">' . $ds_album . '</ul>'
       : '<p><em>Chưa nhập ảnh mẫu — copy thư mục <code>src/sample-data/images</code> '
       . 'vào <code>wp-content/uploads/ocoptv/</code> rồi gie lại dữ liệu để có album.</em></p>'));

ocoptv_seed_trang('Hướng dẫn mua hàng',
    '<h2>Quy trình đặt hàng</h2>'
    . '<ol><li>Tìm sản phẩm bằng ô tìm kiếm hoặc bộ lọc theo nhóm ngành, địa phương, đơn vị, hạng sao, khoảng giá.</li>'
    . '<li>Xem trang chi tiết sản phẩm: mô tả, giá bán, nguồn gốc, đơn vị sản xuất, hạng sao OCOP.</li>'
    . '<li>Nhấn "Thêm vào giỏ" rồi vào trang giỏ hàng để điều chỉnh số lượng.</li>'
    . '<li>Điền thông tin giao hàng và chọn hình thức thanh toán khi nhận hàng (COD).</li>'
    . '<li>Nhận hàng, kiểm tra và thanh toán cho đơn vị vận chuyển.</li></ol>'
    . '<h2>Mua trực tiếp từ đơn vị sản xuất</h2>'
    . '<p>Mỗi trang chi tiết sản phẩm đều có khối "Mua từ đơn vị sản xuất" dẫn tới hồ sơ đơn vị kèm địa chỉ, '
    . 'điện thoại và thư điện tử để khách hàng liên hệ đặt số lượng lớn.</p>');

ocoptv_seed_trang('Liên hệ', '', 'page-templates/trang-lien-he.php');
$id_trang_tin = ocoptv_seed_trang('Tin tức – sự kiện',
    '<p>Tin hoạt động chương trình OCOP, hội chợ – sự kiện và hướng dẫn sử dụng website.</p>');

// Tim lai ID cac trang da tao de xay dung menu
function ocoptv_tim_trang($tieu_de) {
    $trang = get_page_by_title($tieu_de);
    return $trang ? $trang->ID : 0;
}

// ===========================================================================
// 8. Menu chinh 8 muc + cau hinh trang chu
// ===========================================================================
$menu_id = wp_create_nav_menu('Menu chính');
if (!is_wp_error($menu_id)) {
    $them_muc = function ($tieu_de, $type, $object, $object_id, $url) use ($menu_id) {
        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-title'     => $tieu_de,
            'menu-item-type'      => $type,
            'menu-item-object'    => $object,
            'menu-item-object-id' => $object_id,
            'menu-item-url'       => $url,
            'menu-item-status'    => 'publish',
        ));
    };

    $id_gt  = ocoptv_tim_trang('Giới thiệu OCOP');
    $id_tv  = ocoptv_tim_trang('Thư viện hình ảnh');
    $id_lh  = ocoptv_tim_trang('Liên hệ');

    $them_muc('Trang chủ', 'post_type', 'page', $id_trang_chu, '');
    $them_muc('Giới thiệu OCOP', 'post_type', 'page', $id_gt, '');
    $them_muc('Sản phẩm', 'custom', 'custom', 0, get_post_type_archive_link('product'));
    $them_muc('Đơn vị sản xuất', 'custom', 'custom', 0, get_post_type_archive_link('don_vi_san_xuat'));
    $them_muc('Tin tức – sự kiện', 'post_type', 'page', $id_trang_tin, '');
    $them_muc('Thư viện hình ảnh', 'post_type', 'page', $id_tv, '');
    $them_muc('Liên hệ', 'post_type', 'page', $id_lh, '');
    $them_muc('Giỏ hàng', 'custom', 'custom', 0, home_url('/cart/'));

    set_theme_mod('nav_menu_locations', array('primary' => $menu_id));
}

// Trang chu tinh + trang bai viet
update_option('show_on_front', 'page');
update_option('page_on_front', $id_trang_chu);
update_option('page_for_posts', $id_trang_tin);
update_option('blogname', 'OCOP Trà Vinh');
update_option('blogdescription', 'Giới thiệu sản phẩm OCOP địa phương Trà Vinh');
update_option('permalink_structure', '/%postname%/');
flush_rewrite_rules();

// Danh dau da gie du lieu
update_option('ocoptv_da_seed', '1');
update_option('ocoptv_ngay_seed', current_time('mysql'));

// ===========================================================================
// 9. Bao cao ket qua
// ===========================================================================
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="utf-8"><title>Gie dữ liệu OCOP Trà Vinh — xong</title>
<style>
body{font-family:Arial,Helvetica,sans-serif;max-width:760px;margin:40px auto;padding:0 18px;color:#212;line-height:1.6}
h1,h2{color:#1B5E20}table{border-collapse:collapse;width:100%;margin:14px 0}
th,td{border:1px solid #ddd;padding:8px 10px;text-align:left;font-size:14px}
th{background:#C8E6C9}.ok{color:#1B5E20;font-weight:700}.note{background:#F6EFD9;border:1px solid #E0D6B4;padding:12px 14px;border-radius:8px}
code{background:#f4f4f4;padding:1px 5px;border-radius:4px}
</style></head>
<body>
<h1>✓ Gie dữ liệu thử nghiệm hoàn tất</h1>
<p>Website <strong>OCOP Trà Vinh</strong> đã được tạo bộ dữ liệu minh họa của đồ án.</p>

<h2>Kết quả</h2>
<table>
<tr><th>Nội dung</th><th>Số lượng</th></tr>
<tr><td>Đơn vị sản xuất</td><td><?php echo count(array_filter($id_don_vi)); ?></td></tr>
<tr><td>Sản phẩm OCOP (đủ 7 thuộc tính)</td><td><?php echo count($id_san_pham); ?></td></tr>
<tr><td>Ảnh sản phẩm đã gán</td><td><?php echo $so_anh_sp; ?></td></tr>
<tr><td>Ảnh thư viện đã nhập</td><td><?php echo $so_anh_gal; ?></td></tr>
<tr><td>Bài tin tức – sự kiện</td><td><?php echo count($tin_tuc); ?></td></tr>
<tr><td>Album + trang tĩnh</td><td>4 album + 6 trang</td></tr>
<tr><td>Menu chính</td><td>8 mục</td></tr>
</table>
<?php if ($so_anh_sp === 0) : ?>
<p class="note">Chưa gán ảnh sản phẩm — copy thư mục <code>src/sample-data/images</code> của repository
vào <code>wp-content/uploads/ocoptv/</code> rồi gie lại (cài lại WordPress) để có ảnh minh họa.</p>
<?php endif; ?>

<h2>Tài khoản mẫu (đổi mật khẩu ngay!)</h2>
<table>
<tr><th>Tài khoản</th><th>Vai trò</th><th>Mật khẩu</th></tr>
<tr><td>admin</td><td>Quản trị viên (tạo lúc cài WordPress)</td><td>bạn tự đặt</td></tr>
<tr><td>bientap</td><td>Biên tập viên — duyệt / trả lại sản phẩm</td><td><code>Ocop@2026</code></td></tr>
<tr><td>donvi01</td><td>Đơn vị sản xuất — gửi sản phẩm chờ duyệt</td><td><code>Ocop@2026</code></td></tr>
<tr><td>donvi02</td><td>Đơn vị sản xuất — gửi sản phẩm chờ duyệt</td><td><code>Ocop@2026</code></td></tr>
</table>

<h2>Việc cần làm tiếp theo</h2>
<ol>
<li>Đổi mật khẩu tất cả tài khoản mẫu (Users → All Users).</li>
<li><strong>Xóa tệp seed-data.php</strong> khỏi thư mục WordPress sau khi chạy.</li>
<li>Vào <em>Appearance → Customize</em> tải logo <code>assets/images/logo.png</code> của child theme.</li>
<li>Thử quy trình kiểm duyệt: đăng nhập <code>donvi01</code> → thêm sản phẩm mới →
    đăng nhập <code>bientap</code> → Products → chờ kiểm duyệt → duyệt và công bố.</li>
<li>Thay ảnh minh họa bằng ảnh thật của sản phẩm khi triển khai thực tế.</li>
</ol>
<p><a href="<?php echo esc_url(home_url('/')); ?>">&laquo; Về trang chủ website</a></p>
</body></html>
