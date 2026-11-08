<?php
/**
 * File: wp-content/themes/ocoptv/shortcodes/ocop-noibat.php
 * Shortcode: [ocop_noibat]
 * Hien thi luoi san pham OCOP noi bat.
 * Thuoc tinh: so_luong (mac dinh 8), hang_sao ('' = moi hang),
 *            nhom_nganh (slug, '' = moi nhom), cot (2-4).
 */
function ocoptv_shortcode_noibat($atts) {
    $a = shortcode_atts(array(
        'so_luong'   => 8,
        'hang_sao'   => '4',
        'nhom_nganh' => '',
        'cot'        => 4,
    ), $atts, 'ocop_noibat');

    // Dieu kien truy van san pham
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => intval($a['so_luong']),
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    // Loc theo hang sao OCOP (truong ACF hang_sao_ocop)
    if ($a['hang_sao'] !== '') {
        $args['meta_query'] = array(array(
            'key'     => 'hang_sao_ocop',
            'value'   => sanitize_text_field($a['hang_sao']),
            'compare' => '=',
        ));
    }

    // Loc theo nhom nganh OCOP (taxonomy)
    if ($a['nhom_nganh'] !== '') {
        $args['tax_query'] = array(array(
            'taxonomy' => 'nhom_nganh_ocop',
            'field'    => 'slug',
            'terms'    => sanitize_title($a['nhom_nganh']),
        ));
    }

    $q = new WP_Query($args);
    if (!$q->have_posts()) {
        return '<p class="ocop-empty">Chua co san pham OCOP noi bat.</p>';
    }

    // Sinh HTML bang bo dem ob_start
    ob_start();
    echo '<div class="ocop-grid ocoptv-cards cols-' . intval($a['cot']) . '">';
    while ($q->have_posts()) {
        $q->the_post();
        // Truyen hang sao sang khuon mau the san pham
        set_query_var('ocop_hang_sao',
            function_exists('get_field')
                ? get_field('hang_sao_ocop') : '');
        get_template_part('template-parts/card', 'san-pham');
    }
    echo '</div>';
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('ocop_noibat', 'ocoptv_shortcode_noibat');
