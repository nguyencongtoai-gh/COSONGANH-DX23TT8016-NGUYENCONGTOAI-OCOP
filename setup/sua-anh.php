<?php
/**
 * File: setup/sua-anh.php
 * Bo sua loi "san pham khong co anh dai dien" khi seed-data.php da chay
 * truoc khi copy anh mau vao wp-content/uploads/ocoptv/.
 *
 * Tep nay KHONG tao du lieu moi, chi:
 *   - dang ky 22 anh sp-XX.png lam attachment va gan anh dai dien
 *     cho 22 san pham (doi chieu theo ten, bang 3.5 bao cao);
 *   - dang ky 18 anh gal-XX.png, tao lai 4 trang album neu thieu;
 *   - cap nhat trang "Thu vien hinh anh" neu con ban ghi chu;
 *   - gan logo cho website neu co logo.png va chua co logo.
 *
 * CACH CHAY:
 *   1. Copy toan bo anh mau (sp-01..22.png, gal-01..18.png, logo.png)
 *      vao  C:\xampp\htdocs\ocoptravinh\wp-content\uploads\ocoptv\
 *      (tao thu muc ocoptv ben trong uploads neu chua co).
 *      Neu DA chay seed truoc do voi anh cu, chi can ghi de 22 tep
 *      sp-XX.png bang anh moi roi go bo che do capnhat o buoc 3.
 *   2. Copy tep nay vao thu muc goc WordPress (ngang hang wp-config.php).
 *   3. Mo trinh duyet tai
 *        http://localhost/ocoptravinh/sua-anh.php?chay=1
 *      Them &capnhat=1 neu da tung gan anh cu truoc day (bo qua anh
 *      da gan, tao lai cac ban thu nho va gan lai anh dai dien):
 *        http://localhost/ocoptravinh/sua-anh.php?chay=1&capnhat=1
 *   4. Xem bao cao ket qua, XOA tep nay khoi may chu sau khi chay.
 *
 * Tep nay co the chay nhieu lan (an toan: bo qua nhung gi da co san).
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

// che do cap nhat anh moi cho site da gan anh cu
$cap_nhat = isset($_GET['capnhat']);

// ---------------------------------------------------------------------------
// 0. Tim va nap WordPress
// ---------------------------------------------------------------------------
$wp_load = null;
for ($i = 0; $i <= 4; $i++) {
    $duong_dan = dirname(__FILE__) . str_repeat('/..', $i) . '/wp-load.php';
    if (file_exists($duong_dan)) { $wp_load = realpath($duong_dan); break; }
}
if (!$wp_load) {
    header('Content-Type: text/html; charset=utf-8');
    exit('<h3>Khong tim thay wp-load.php</h3><p>Hay copy tep nay vao thu muc goc '
        . 'WordPress (ngang hang wp-config.php) roi chay lai.</p>');
}
require_once $wp_load;
require_once ABSPATH . 'wp-admin/includes/image.php';

// Trang huong dan khi chua co tham so ?chay=1
if (!isset($_GET['chay'])) {
    header('Content-Type: text/html; charset=utf-8');
    exit('<h2>OCOP Trà Vinh — bộ sửa ảnh sản phẩm</h2>'
        . '<p>Tệp này gán lại <strong>ảnh đại diện cho 22 sản phẩm OCOP</strong>, '
        . 'nhập 18 ảnh thư viện, tạo 4 album, cập nhật trang “Thư viện hình ảnh” '
        . 'và gán logo — <em>không tạo dữ liệu mới, không đụng nội dung sẵn có</em>.</p>'
        . '<p><strong>Điều kiện trước khi chạy:</strong> đã copy 41 file ảnh mẫu '
        . '(<code>sp-01.png</code>…<code>sp-22.png</code>, <code>gal-01.png</code>…'
        . '<code>gal-18.png</code>, <code>logo.png</code>) vào '
        . '<code>wp-content/uploads/ocoptv/</code>.</p>'
        . '<p><strong>Đã từng gán ảnh cũ trước đây?</strong> Ghi đè 22 file '
        . '<code>sp-XX.png</code> trong <code>uploads/ocoptv/</code> bằng bộ ảnh mới, '
        . 'rồi chạy với tham số <code>?chay=1&amp;capnhat=1</code> để tạo lại bản '
        . 'thu nhỏ và gán lại ảnh đại diện.</p>'
        . '<p>Để chạy, thêm tham số: <a href="?chay=1"><code>?chay=1</code></a> '
        . '(hoặc <a href="?chay=1&amp;capnhat=1"><code>?chay=1&amp;capnhat=1</code></a> '
        . 'nếu cần ghi đè ảnh cũ).</p>');
}

// ---------------------------------------------------------------------------
// 1. Do tim thu muc chua anh mau (ung cac truong hop copy sai vi tri)
// ---------------------------------------------------------------------------
$uploads    = wp_upload_dir();
$basedir    = trailingslashit($uploads['basedir']);
$ung_vien   = array(
    $basedir . 'ocoptv',            // dung vi tri
    $basedir . 'images',            // copy nguyen thu muc images
    $basedir . 'ocoptv/images',     // long them mot cap
    $basedir . 'sample-data',       // copy nguyen thu muc sample-data
    $basedir . 'sample-data/images',
);
$thu_muc_anh = '';
foreach ($ung_vien as $uv) {
    if (is_dir($uv) && glob($uv . '/sp-*.png')) { $thu_muc_anh = $uv; break; }
}

$thieu_anh = array();   // anh khong tim thay
$khong_tim_sp = array(); // san pham khong tim thay theo ten

// ---------------------------------------------------------------------------
// 2. Cac ham dung chung
// ---------------------------------------------------------------------------
/** Tim ID san pham WooCommerce theo dung tieu de */
function ocoptv_tim_sp($ten) {
    global $wpdb;
    return (int) $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts}
          WHERE post_type = 'product' AND post_title = %s
            AND post_status IN ('publish','pending','draft','future','private')
          ORDER BY ID ASC LIMIT 1", $ten));
}

/** Dang ky mot tep anh lam attachment (dung lai neu da dang ky) */
function ocoptv_dang_ky_anh($tep) {
    if (!$tep || !file_exists($tep)) { return 0; }
    global $wpdb;
    $ten = basename($tep);

    // da co attachment voi chinh tep nay chua? (theo ten file — duy nhat)
    $att = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta}
          WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s
          LIMIT 1", '%' . $wpdb->esc_like($ten)));
    if ($att) { return $att; }

    $att = wp_insert_attachment(array(
        'post_mime_type' => 'image/png',
        'post_title'     => preg_replace('/[-_.]/', ' ', pathinfo($ten, PATHINFO_FILENAME)),
        'post_status'    => 'inherit',
    ), $tep, 0);
    if (!$att || is_wp_error($att)) { return 0; }
    wp_update_attachment_metadata($att, wp_generate_attachment_metadata($att, $tep));
    return (int) $att;
}

// ---------------------------------------------------------------------------
// 3. Gan anh dai dien cho 22 san pham (doi chieu ten, thu tu sp-01..sp-22)
// ---------------------------------------------------------------------------
$danh_sach_sp = array(
     1 => 'Dừa xiêm xanh Càng Long',
     2 => 'Dừa sáp Long Hòa',
     3 => 'Kẹo dừa Càng Long',
     4 => 'Mứt dừa sữa',
     5 => 'Nước cốt dừa tiệt trùng',
     6 => 'Dầu dừa nguyên chất Mỹ Long',
     7 => 'Dừa sấy dẻo Càng Long',
     8 => 'Gạo thơm hạt dài Đại An',
     9 => 'Bánh tét lá cẩm Trà Cú',
    10 => 'Tôm khô Duyên Hải',
    11 => 'Cá khô một nắng',
    12 => 'Mắm bò hóc truyền thống',
    13 => 'Nước dừa tươi đóng chai',
    14 => 'Mật ong hoa dừa',
    15 => 'Than hoạt tính vỏ dừa',
    16 => 'Trà hoa đậu biếc sấy khô',
    17 => 'Siro sơ ri cô đặc',
    18 => 'Tinh dầu tràm Ngũ Lạc',
    19 => 'Khăn lụa dệt truyền thống Khmer',
    20 => 'Giỏ đan bẹ dừa',
    21 => 'Đèn trang trí vỏ dừa',
    22 => 'Hộp giấy từ bã mủ dừa',
);

$so_gan = 0; $so_da_co = 0; $so_cap_nhat = 0;
if ($thu_muc_anh) {
    foreach ($danh_sach_sp as $so => $ten) {
        $tep = $thu_muc_anh . sprintf('/sp-%02d.png', $so);
        if (!file_exists($tep)) { $thieu_anh[] = basename($tep); continue; }

        $id_sp = ocoptv_tim_sp($ten);
        if (!$id_sp) { $khong_tim_sp[] = $ten; continue; }

        if (has_post_thumbnail($id_sp) && !$cap_nhat) { $so_da_co++; continue; }

        $att = ocoptv_dang_ky_anh($tep);
        if ($att) {
            if ($cap_nhat) {
                // tao lai metadata + cac ban thu nho tu tep anh moi
                wp_update_attachment_metadata($att, wp_generate_attachment_metadata($att, $tep));
                $so_cap_nhat++;
            }
            set_post_thumbnail($id_sp, $att); $so_gan++;
        }
        else      { $thieu_anh[] = basename($tep); }
    }
}

// ---------------------------------------------------------------------------
// 4. Nhap 18 anh thu vien (gal-XX.png)
// ---------------------------------------------------------------------------
$id_gal = array();
if ($thu_muc_anh) {
    $tep_gal = glob($thu_muc_anh . '/gal-*.png');
    sort($tep_gal);
    foreach ($tep_gal as $tep) {
        $att = ocoptv_dang_ky_anh($tep);
        if ($att) { $id_gal[] = $att; }
    }
}

// ---------------------------------------------------------------------------
// 5. Tao lai 4 trang album neu thieu + cap nhat trang Thu vien hinh anh
// ---------------------------------------------------------------------------
$album_du_lieu = array(
    'Album: Hội chợ OCOP 2026'      => array_slice($id_gal, 0, 5),
    'Album: Cánh đồng và làng nghề' => array_slice($id_gal, 5, 5),
    'Album: Sản phẩm OCOP tiêu biểu' => array_slice($id_gal, 10, 4),
    'Album: Đêm văn hóa Khmer'       => array_slice($id_gal, 14, 4),
);
$album_tao = 0; $id_album = array();
foreach ($album_du_lieu as $ten_album => $ids) {
    $trang = get_page_by_title($ten_album);
    if ($trang) { $id_album[] = $trang->ID; continue; }
    if (empty($ids)) { continue; }
    $id_a = wp_insert_post(array(
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'post_title'   => $ten_album,
        'post_content' => '[gallery ids="' . implode(',', $ids) . '" columns="3" link="file"]',
    ));
    if ($id_a && !is_wp_error($id_a)) { $id_album[] = $id_a; $album_tao++; }
}

// Cap nhat trang "Thu vien hinh anh" neu dang hien ghi chu "Chưa nhập ảnh mẫu"
$trang_tv = get_page_by_title('Thư viện hình ảnh');
$cap_nhat_tv = false;
if ($trang_tv && strpos($trang_tv->post_content, 'Chưa nhập ảnh mẫu') !== false && !empty($id_album)) {
    $ds = '';
    foreach ($id_album as $ia) {
        $ds .= '<li><a href="' . esc_url(get_permalink($ia)) . '">'
             . esc_html(get_the_title($ia)) . '</a></li>';
    }
    wp_update_post(array(
        'ID'           => $trang_tv->ID,
        'post_content' => '<p>Thư viện hình ảnh hoạt động, hội chợ và sản phẩm '
            . 'OCOP Trà Vinh. Chọn một album để xem ảnh lớn.</p>'
            . '<ul class="ocop-danh-sach-album">' . $ds . '</ul>',
    ));
    $cap_nhat_tv = true;
}

// ---------------------------------------------------------------------------
// 6. Gan logo cho website (neu co logo.png va chua co custom_logo)
// ---------------------------------------------------------------------------
$logo_da_gan = false;
if ($thu_muc_anh && file_exists($thu_muc_anh . '/logo.png') && !get_theme_mod('custom_logo')) {
    $att = ocoptv_dang_ky_anh($thu_muc_anh . '/logo.png');
    if ($att) { set_theme_mod('custom_logo', $att); $logo_da_gan = true; }
}

// ---------------------------------------------------------------------------
// 7. Bao cao ket qua
// ---------------------------------------------------------------------------
header('Content-Type: text/html; charset=utf-8');
$hop_le = $thu_muc_anh && ($so_gan + $so_da_co) > 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="utf-8"><title>Sửa ảnh sản phẩm — OCOP Trà Vinh</title>
<style>
body{font-family:Arial,Helvetica,sans-serif;max-width:760px;margin:40px auto;padding:0 18px;color:#212;line-height:1.6}
h1,h2{color:#1B5E20}table{border-collapse:collapse;width:100%;margin:14px 0}
th,td{border:1px solid #ddd;padding:8px 10px;text-align:left;font-size:14px}
th{background:#C8E6C9}.ok{color:#1B5E20;font-weight:700}
.note{background:#F6EFD9;border:1px solid #E0D6B4;padding:12px 14px;border-radius:8px}
.loi{background:#FDE8E8;border:1px solid #E7A6A6;padding:12px 14px;border-radius:8px}
code{background:#f4f4f4;padding:1px 5px;border-radius:4px}
</style></head>
<body>
<h1>✓ Bộ sửa ảnh sản phẩm đã chạy xong</h1>

<?php if (!$thu_muc_anh) : ?>
<h2>Chưa tìm thấy ảnh mẫu trong thư mục uploads</h2>
<div class="loi">
<p>Đã dò các vị trí: <code>uploads/ocoptv/</code>, <code>uploads/images/</code>,
<code>uploads/ocoptv/images/</code>, <code>uploads/sample-data/</code>,
<code>uploads/sample-data/images/</code> — không có file <code>sp-*.png</code> nào.</p>
<p><strong>Cách khắc phục:</strong> mở thư mục repository trên máy, vào
<code>src/sample-data/images</code>, copy <strong>hết 41 file</strong> vào
<code>wp-content/uploads/ocoptv/</code> (tạo thư mục <code>ocoptv</code> mới bên trong
<code>uploads</code> nếu chưa có), rồi mở lại địa chỉ này kèm <code>?chay=1</code>.</p>
</div>
<?php else : ?>
<h2>Kết quả</h2>
<table>
<tr><th>Nội dung</th><th>Số lượng</th></tr>
<tr><td>Ảnh đại diện sản phẩm đã gán lần này</td><td class="ok"><?php echo $so_gan; ?>/22</td></tr>
<tr><td>Sản phẩm đã có ảnh từ trước (bỏ qua)</td><td><?php echo $so_da_co; ?></td></tr>
<tr><td>Ảnh tạo lại bản thu nhỏ (chế độ capnhat)</td><td><?php echo $so_cap_nhat; ?></td></tr>
<tr><td>Ảnh thư viện đã đăng ký</td><td><?php echo count($id_gal); ?>/18</td></tr>
<tr><td>Trang album tạo mới</td><td><?php echo $album_tao; ?>/4</td></tr>
<tr><td>Trang “Thư viện hình ảnh” cập nhật danh sách album</td><td><?php echo $cap_nhat_tv ? 'có' : 'không cần'; ?></td></tr>
<tr><td>Logo website gán tự động</td><td><?php echo $logo_da_gan ? 'có' : 'không (gán thủ công qua Customize)'; ?></td></tr>
<tr><td>Thư mục ảnh sử dụng</td><td><code><?php echo esc_html(str_replace(ABSPATH, '', $thu_muc_anh)); ?></code></td></tr>
</table>
<?php endif; ?>

<?php if (!empty($thieu_anh)) : ?>
<div class="loi"><p><strong>Ảnh thiếu file:</strong> <code><?php echo esc_html(implode(', ', $thieu_anh)); ?></code>.
Kiểm tra lại thư mục <code>uploads/ocoptv/</code> đã đủ 41 file chưa.</p></div>
<?php endif; ?>
<?php if (!empty($khong_tim_sp)) : ?>
<div class="loi"><p><strong>Không tìm thấy sản phẩm theo tên:</strong>
<?php echo esc_html(implode('; ', $khong_tim_sp)); ?> — có thể tiêu đề đã bị đổi
(tìm thủ công trong <em>Products</em> rồi gán ảnh qua “Product image”).</p></div>
<?php endif; ?>

<?php if ($hop_le) : ?>
<h2>Việc cần làm tiếp theo</h2>
<ol>
<li>Mở <a href="<?php echo esc_url(home_url('/')); ?>">trang chủ website</a> —
    các thẻ sản phẩm giờ đã có ảnh thật thay cho logo.</li>
<li>Ảnh chưa hiện ngay? Xóa bộ nhớ đệm (WP Super Cache → Delete Cache)
    rồi tải lại trang (Ctrl+F5).</li>
<li><strong>Xóa tệp <code>sua-anh.php</code></strong> khỏi máy chủ sau khi chạy.</li>
</ol>
<?php endif; ?>
</body></html>
