<?php
/**
 * File: wp-content/themes/ocoptv/shortcodes/ocop-danhsach.php
 * Shortcode: [ocop_danhsach]
 * Hien thi danh sach san pham OCOP theo dieu kien tuy chon.
 * Thuoc tinh: nhom_nganh, dia_phuong (slug), don_vi (ID),
 *            hang_sao (3/4, '' = moi hang), so_luong (mac dinh 12),
 *            cot (2-4, mac dinh 3), sap_xep (moi-nhat|gia-tang|gia-giam).
 * Vi du: [ocop_danhsach nhom_nganh="thu-cong-my-nghe" so_luong="6"]
 */
function ocoptv_shortcode_danhsach($atts) {
    $a = shortcode_atts(array(
        'nhom_nganh' => '',
        'dia_phuong' => '',
        'don_vi'     => 0,
        'hang_sao'   => '',
        'so_luong'   => 12,
        'cot'        => 3,
        'sap_xep'    => 'moi-nhat',
    ), $atts, 'ocop_danhsach');

    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => intval($a['so_luong']),
    );

    // Sap xep
    if ($a['sap_xep'] === 'gia-tang' || $a['sap_xep'] === 'gia-giam') {
        $args['orderby'] = 'meta_value_num';
        $args['meta_key'] = '_price';
        $args['order'] = ($a['sap_xep'] === 'gia-tang') ? 'ASC' : 'DESC';
    } else {
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
    }

    // Loc sieu du lieu: hang sao, don vi san xuat
    $meta = array();
    if ($a['hang_sao'] !== '') {
        $meta[] = array(
            'key'   => 'hang_sao_ocop',
            'value' => sanitize_text_field($a['hang_sao']),
            'compare' => '=',
        );
    }
    if (intval($a['don_vi']) > 0) {
        $meta[] = array(
            'key'   => 'don_vi_san_xuat',
            'value' => intval($a['don_vi']),
            'compare' => '=',
        );
    }
    if ($meta) {
        $args['meta_query'] = $meta;
    }

    // Loc phan loai: nhom nganh, dia phuong
    $tax = array();
    if ($a['nhom_nganh'] !== '') {
        $tax[] = array(
            'taxonomy' => 'nhom_nganh_ocop',
            'field'    => 'slug',
            'terms'    => sanitize_title($a['nhom_nganh']),
        );
    }
    if ($a['dia_phuong'] !== '') {
        $tax[] = array(
            'taxonomy' => 'dia_phuong',
            'field'    => 'slug',
            'terms'    => sanitize_title($a['dia_phuong']),
        );
    }
    if ($tax) {
        $args['tax_query'] = array_merge(array('relation' => 'AND'), $tax);
    }

    $q = new WP_Query($args);
    if (!$q->have_posts()) {
        return '<p class="ocop-empty">Khong co san pham OCOP phu hop.</p>';
    }

    ob_start();
    echo '<div class="ocop-grid ocoptv-cards cols-' . intval($a['cot']) . '">';
    while ($q->have_posts()) {
        $q->the_post();
        set_query_var('ocop_hang_sao',
            function_exists('get_field')
                ? get_field('hang_sao_ocop') : '');
        get_template_part('template-parts/card', 'san-pham');
    }
    echo '</div>';

    // Duong dan xem toan bo san pham
    $xem_tat_ca = get_post_type_archive_link('product');
    if ($xem_tat_ca) {
        echo '<p class="ocop-xem-tat-ca"><a class="ocop-btn" href="'
            . esc_url($xem_tat_ca) . '">Xem toàn bộ sản phẩm</a></p>';
    }

    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('ocop_danhsach', 'ocoptv_shortcode_danhsach');
