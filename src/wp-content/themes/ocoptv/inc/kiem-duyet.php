<?php
// File: wp-content/themes/ocoptv/inc/kiem-duyet.php
// Quy trinh gui - kiem duyet - cong bo san pham OCOP.

// ---- 1. Gui thu thong bao cho bien tap vien khi co san pham cho duyet ----
function ocoptv_thong_bao_cho_duyet($new, $old, $post) {
    if ($post->post_type !== 'product'
        || $new !== 'pending' || $old === 'pending') return;
    $editors = get_users(array('role' => 'editor'));
    foreach ($editors as $ed) {
        wp_mail($ed->user_email,
            'OCOP Tra Vinh: co san pham cho kiem duyet',
            'San pham "' . $post->post_title .
            '" vua duoc gui kiem duyet.');
    }
}
add_action('transition_post_status',
    'ocoptv_thong_bao_cho_duyet', 10, 3);

// ---- 2. Gui thu ket qua cho don vi sau khi duyet cong bo hoac tra lai ----
function ocoptv_thong_bao_ket_qua($new, $old, $post) {
    if ($post->post_type !== 'product') return;
    $tac_gia = get_userdata($post->post_author);
    if (!$tac_gia) return;

    if ($new === 'publish' && ($old === 'pending' || $old === 'draft')) {
        wp_mail($tac_gia->user_email,
            'OCOP Tra Vinh: san pham da duoc cong bo',
            'San pham "' . $post->post_title .
            '" da duoc bien tap vien duyet va cong bo tren website.');
    } elseif ($new === 'draft' && $old === 'pending') {
        wp_mail($tac_gia->user_email,
            'OCOP Tra Vinh: san pham bi tra lai chinh sua',
            'San pham "' . $post->post_title .
            '" bi tra lai. Vui long dang nhap, xem y kien cua bien tap vien, chinh sua roi gui lai.');
    }
}
add_action('transition_post_status',
    'ocoptv_thong_bao_ket_qua', 10, 3);

// ---- 3. Bo sung cot "Hang sao" va "Don vi san xuat" trong danh sach san pham ----
function ocoptv_cot_admin_san_pham($columns) {
    $moi = array();
    foreach ($columns as $k => $v) {
        $moi[$k] = $v;
        if ($k === 'name') {
            $moi['ocop_hang_sao'] = 'Hạng sao';
            $moi['ocop_don_vi']   = 'Đơn vị sản xuất';
        }
    }
    return $moi;
}
add_filter('manage_product_posts_columns', 'ocoptv_cot_admin_san_pham');

function ocoptv_noi_dung_cot_admin($col, $id) {
    if ($col === 'ocop_hang_sao') {
        $sao = get_post_meta($id, 'hang_sao_ocop', true);
        echo $sao ? esc_html($sao . ' sao') : '—';
    }
    if ($col === 'ocop_don_vi') {
        $dv = intval(get_post_meta($id, 'don_vi_san_xuat', true));
        echo $dv ? esc_html(get_the_title($dv)) : '—';
    }
}
add_action('manage_product_posts_custom_column', 'ocoptv_noi_dung_cot_admin', 10, 2);
