<?php
// File: wp-content/themes/ocoptv/inc/phan-quyen.php
// Phan quyen ba nhom nguoi dung: quan tri vien, bien tap vien (editor)
// va don vi san xuat (vai tro tuy chinh dv_san_xuat).

// ---- 1. Dang ky vai tro tuy chinh "dv_san_xuat" ----
function ocoptv_dang_ky_vai_tro() {
    add_role('dv_san_xuat', 'Don vi san xuat', array(
        'read'                   => true,
        'edit_products'          => true,
        'edit_product'           => true,
        'publish_products'       => false, // khong tu cong bo
        'edit_others_products'   => false,
        'delete_others_products' => false,
        'upload_files'           => true,
        // Quyen tren kieu noi dung don vi san xuat
        'edit_don_vi_san_xuat'   => true,
        'edit_don_vi_san_xuats'  => true,
        'publish_don_vi_san_xuats'       => false,
        'edit_others_don_vi_san_xuats'   => false,
        'delete_others_don_vi_san_xuats' => false,
    ));
}
add_action('after_switch_theme', 'ocoptv_dang_ky_vai_tro');

// Dam bao vai tro ton tai ke ca khi du lieu duoc nhap lai (khong bat lai theme)
function ocoptv_kiem_tra_vai_tro() {
    if (get_role('dv_san_xuat') === null) {
        ocoptv_dang_ky_vai_tro();
    }
}
add_action('init', 'ocoptv_kiem_tra_vai_tro');

// ---- 2. San pham cua don vi luon chuyen sang trang thai cho kiem duyet ----
function ocoptv_ep_trang_thai_cho_duyet($data, $postarr) {
    $user = wp_get_current_user();
    if (isset($data['post_type'])
        && in_array($data['post_type'], array('product', 'don_vi_san_xuat'), true)
        && in_array('dv_san_xuat', (array) $user->roles, true)
        && isset($data['post_status'])
        && $data['post_status'] === 'publish') {
        $data['post_status'] = 'pending';
    }
    return $data;
}
add_filter('wp_insert_post_data', 'ocoptv_ep_trang_thai_cho_duyet', 10, 2);

// ---- 3. Don vi san xuat chi thay san pham cua chinh minh trong man hinh quan tri ----
function ocoptv_gioi_han_danh_sach_admin($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->base !== 'edit' || $screen->post_type !== 'product') {
        return;
    }
    if (!current_user_can('edit_others_products')) {
        $query->set('author', get_current_user_id());
    }
}
add_action('pre_get_posts', 'ocoptv_gioi_han_danh_sach_admin');

// ---- 4. Don vi san xuat khong duoc sua/xoa san pham cua nguoi khac ----
function ocoptv_chan_sua_san_pham_nguoi_khac($caps, $cap, $user_id, $args) {
    if (in_array($cap, array('edit_product', 'edit_post', 'delete_post'), true) && !empty($args)) {
        $post = get_post($args[0]);
        if ($post && in_array($post->post_type, array('product', 'don_vi_san_xuat'), true)
            && (int) $post->post_author !== (int) $user_id
            && !user_can($user_id, 'edit_others_products')) {
            $caps[] = 'do_not_allow';
        }
    }
    return $caps;
}
add_filter('map_meta_cap', 'ocoptv_chan_sua_san_pham_nguoi_khac', 10, 4);
