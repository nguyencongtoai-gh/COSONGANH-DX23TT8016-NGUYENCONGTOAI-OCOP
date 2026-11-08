<?php
/**
 * Template Name: Trang Liên Hệ
 * File: wp-content/themes/ocoptv/page-templates/trang-lien-he.php
 * Khuon mau trang lien he: thong tin ban quan ly chuong trinh OCOP,
 * ban do nung (Google Maps) va bieu mau lien he.
 *
 * Ghi chu: tren may cuc bo XAMPP, wp_mail khong gui duoc that su tru khi
 * cau hinh SMTP (plugin WP Mail SMTP). Bieu mau van hoat dong va ghi nhan.
 */

// ---- Xu ly gui bieu mau (gui ve chinh trang nay, khong can admin-post) ----
$da_gui = false;
$loi = array();
$thong_bao = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['ocop_lien_he'])) {
    if (!wp_verify_nonce($_POST['ocop_nonce'] ?? '', 'ocop_lien_he')) {
        $loi[] = 'Phiên gửi không hợp lệ, vui lòng thử lại.';
    } else {
        $ho_ten     = sanitize_text_field(wp_unslash($_POST['ho_ten'] ?? ''));
        $thu        = sanitize_email(wp_unslash($_POST['thu_dien_tu'] ?? ''));
        $dien_thoai = sanitize_text_field(wp_unslash($_POST['dien_thoai'] ?? ''));
        $noi_dung   = sanitize_textarea_field(wp_unslash($_POST['noi_dung'] ?? ''));

        if (!$ho_ten || !is_email($thu) || !$noi_dung) {
            $loi[] = 'Vui lòng điền đầy đủ họ tên, thư điện tử hợp lệ và nội dung.';
        } else {
            $body = "Họ tên: {$ho_ten}\n"
                  . "Thư điện tử: {$thu}\n"
                  . "Điện thoại: {$dien_thoai}\n\n"
                  . "Nội dung:\n{$noi_dung}";
            wp_mail(get_option('admin_email'),
                'OCOP Trà Vinh: thư liên hệ từ website', $body);
            $da_gui = true;
        }
    }
}

get_header(); ?>
<div class="ocop-wrap ocop-trang-lien-he">

    <h1 class="ocop-tieu-de-trang">Liên hệ</h1>
    <p class="ocop-mo-ta-trang">
        Quý độc giả, chủ thể OCOP và đơn vị phân phối vui lòng liên hệ
        Ban quản lý chương trình OCOP Trà Vinh qua các kênh dưới đây
        hoặc gửi thư trực tiếp bằng biểu mẫu.
    </p>

    <div class="ocop-lien-he__the">
        <div class="ocop-lien-he__the-mot">
            <h3>Địa chỉ</h3>
            <p>Ban quản lý chương trình OCOP Trà Vinh<br>
               Sở Công Thương tỉnh Trà Vinh (dữ liệu minh họa)</p>
        </div>
        <div class="ocop-lien-he__the-mot">
            <h3>Điện thoại</h3>
            <p><a href="tel:+842903842000">(0290) 384 2000</a></p>
        </div>
        <div class="ocop-lien-he__the-mot">
            <h3>Thư điện tử</h3>
            <p><a href="mailto:ocop.travinh@example.vn">ocop.travinh@example.vn</a></p>
        </div>
    </div>

    <div class="ocop-lien-he__ban-do">
        <!-- Khung ban do Google Maps (can co ket noi Internet de hien thi) -->
        <iframe title="Bản đồ Trà Vinh"
                src="https://maps.google.com/maps?q=Tr%C3%A0%20Vinh&t=&z=12&ie=UTF8&iwloc=&output=embed"
                loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen></iframe>
    </div>

    <div class="ocop-lien-he__bieu-mau">
        <h2>Gửi thư cho chúng tôi</h2>

        <?php if ($da_gui) : ?>
            <p class="ocop-thong-bao ocop-thong-bao--ok">
                Cảm ơn <strong><?php echo esc_html($ho_ten ?? ''); ?></strong>!
                Thư của bạn đã được ghi nhận và sẽ được phản hồi trong hai ngày làm việc.
            </p>
        <?php endif; ?>

        <?php if ($loi) : ?>
            <ul class="ocop-thong-bao ocop-thong-bao--loi">
                <?php foreach ($loi as $l) : ?>
                    <li><?php echo esc_html($l); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!--
            Neu da cai WPForms: co the thay the khoi form duoi day bang
            shortcode cua phieu da tao, vi du:  [wpforms id="123"]
        -->
        <form method="post" action="#ocop-lien-he-form" id="ocop-lien-he-form" class="ocop-form">
            <?php wp_nonce_field('ocop_lien_he', 'ocop_nonce'); ?>
            <input type="hidden" name="ocop_lien_he" value="1">
            <p class="ocop-form__hang">
                <label for="ocop-ho-ten">Họ tên <span class="ocop-bat-buoc">*</span></label>
                <input type="text" id="ocop-ho-ten" name="ho_ten" required
                       value="<?php echo esc_attr($ho_ten ?? ''); ?>">
            </p>
            <p class="ocop-form__hang">
                <label for="ocop-thu">Thư điện tử <span class="ocop-bat-buoc">*</span></label>
                <input type="email" id="ocop-thu" name="thu_dien_tu" required
                       value="<?php echo esc_attr($thu ?? ''); ?>">
            </p>
            <p class="ocop-form__hang">
                <label for="ocop-dien-thoai">Điện thoại</label>
                <input type="text" id="ocop-dien-thoai" name="dien_thoai"
                       value="<?php echo esc_attr($dien_thoai ?? ''); ?>">
            </p>
            <p class="ocop-form__hang">
                <label for="ocop-noi-dung">Nội dung <span class="ocop-bat-buoc">*</span></label>
                <textarea id="ocop-noi-dung" name="noi_dung" rows="5" required><?php
                    echo esc_textarea($noi_dung ?? ''); ?></textarea>
            </p>
            <p class="ocop-form__hang">
                <button type="submit" class="ocop-btn">Gửi thư</button>
            </p>
        </form>
    </div>

</div>
<?php get_footer();
