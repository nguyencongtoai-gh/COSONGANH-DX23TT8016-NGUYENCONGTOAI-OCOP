<?php
/**
 * File: wp-content/themes/ocoptv/template-parts/content-don-vi.php
 * Khung noi dung trang chi tiet don vi san xuat:
 * thong tin lien he, mo ta va luoi san pham OCOP cua don vi.
 */
$dv_id       = get_the_ID();
$dia_chi     = get_post_meta($dv_id, 'dia_chi', true);
$dien_thoai  = get_post_meta($dv_id, 'dien_thoai', true);
$thu_dien_tu = get_post_meta($dv_id, 'thu_dien_tu', true);
?>
<article <?php post_class('ocop-don-vi'); ?>>
    <header class="ocop-don-vi__head">
        <?php if (has_post_thumbnail()) : ?>
            <div class="ocop-don-vi__anh"><?php the_post_thumbnail('ocop-card'); ?></div>
        <?php endif; ?>
        <div class="ocop-don-vi__tieu-de">
            <h1 class="ocop-don-vi__ten"><?php the_title(); ?></h1>
            <div class="ocop-don-vi__phan-loai">
                <?php
                $nhom = get_the_term_list($dv_id, 'nhom_nganh_ocop', 'Nhóm ngành: ', ', ');
                if ($nhom && !is_wp_error($nhom)) { echo wp_kses_post($nhom); }
                $dp = get_the_term_list($dv_id, 'dia_phuong', ' &bull; Địa phương: ', ', ');
                if ($dp && !is_wp_error($dp)) { echo wp_kses_post($dp); }
                ?>
            </div>
        </div>
    </header>

    <?php if ($dia_chi || $dien_thoai || $thu_dien_tu) : ?>
    <table class="ocop-bang-thong-tin ocop-don-vi__lien-he">
        <?php if ($dia_chi) : ?>
        <tr><th>Địa chỉ</th><td><?php echo esc_html($dia_chi); ?></td></tr>
        <?php endif; ?>
        <?php if ($dien_thoai) : ?>
        <tr><th>Điện thoại</th><td><a href="tel:<?php echo esc_attr(preg_replace('/\s+/', '', $dien_thoai)); ?>"><?php echo esc_html($dien_thoai); ?></a></td></tr>
        <?php endif; ?>
        <?php if ($thu_dien_tu) : ?>
        <tr><th>Thư điện tử</th><td><a href="mailto:<?php echo esc_attr($thu_dien_tu); ?>"><?php echo esc_html($thu_dien_tu); ?></a></td></tr>
        <?php endif; ?>
    </table>
    <?php endif; ?>

    <div class="ocop-don-vi__mo-ta">
        <?php the_content(); ?>
    </div>

    <h2 class="ocop-don-vi__tieu-de-sp">Sản phẩm OCOP của đơn vị</h2>
    <?php
    $q = new WP_Query(array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'meta_query'     => array(array(
            'key'   => 'don_vi_san_xuat',
            'value' => $dv_id,
            'compare' => '=',
        )),
    ));
    if ($q->have_posts()) {
        echo '<div class="ocop-grid ocop-grid--4">';
        while ($q->have_posts()) {
            $q->the_post();
            set_query_var('ocop_hang_sao',
                get_post_meta(get_the_ID(), 'hang_sao_ocop', true));
            get_template_part('template-parts/card', 'san-pham');
        }
        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<p class="ocop-empty">Đơn vị hiện chưa có sản phẩm nào được công bố.</p>';
    }
    ?>
</article>
