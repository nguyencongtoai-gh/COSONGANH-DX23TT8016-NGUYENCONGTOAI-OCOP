<?php
/**
 * File: wp-content/themes/ocoptv/inc/custom-types.php
 * Dang ky kieu noi dung "don_vi_san_xuat" va hai phan loai
 * "nhom_nganh_ocop" (4 nhom nganh OCOP), "dia_phuong" (huyen/thi xa/TP).
 * Kem theo: dinh nghia truong ACF bang PHP (neu plugin ACF dang hoat dong)
 * va hop nhap lieu du phong khi chua cai ACF.
 */

// ---- 1. Dang ky phan loai va kieu noi dung ----
function ocoptv_dang_ky_custom_types() {

    // Phan loai nhom nganh OCOP (4 nhom, gan cho san pham va don vi)
    register_taxonomy('nhom_nganh_ocop', array('product', 'don_vi_san_xuat'), array(
        'labels' => array(
            'name'          => 'Nhóm ngành OCOP',
            'singular_name' => 'Nhóm ngành OCOP',
            'menu_name'     => 'Nhóm ngành OCOP',
        ),
        'hierarchical'      => true,
        'public'            => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => array('slug' => 'nhom-nganh'),
    ));

    // Phan loai dia phuong (huyen / thi xa / thanh pho Tra Vinh)
    register_taxonomy('dia_phuong', array('product', 'don_vi_san_xuat'), array(
        'labels' => array(
            'name'          => 'Địa phương',
            'singular_name' => 'Địa phương',
            'menu_name'     => 'Địa phương',
        ),
        'hierarchical'      => true,
        'public'            => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => array('slug' => 'dia-phuong'),
    ));

    // Kieu noi dung don vi san xuat
    register_post_type('don_vi_san_xuat', array(
        'labels' => array(
            'name'          => 'Đơn vị sản xuất',
            'singular_name' => 'Đơn vị sản xuất',
            'add_new'       => 'Thêm đơn vị',
            'add_new_item'  => 'Thêm đơn vị sản xuất',
            'edit_item'     => 'Sửa đơn vị sản xuất',
        ),
        'public'        => true,
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-store',
        'menu_position' => 26,
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'  => true,
        'rewrite'       => array('slug' => 'don-vi-san-xuat'),
    ));
}
add_action('init', 'ocoptv_dang_ky_custom_types');

// Cap nhat duong dan lanh dep lan dau kich hoat theme
function ocoptv_flush_rewrite_khi_bat_theme() {
    ocoptv_dang_ky_custom_types();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'ocoptv_flush_rewrite_khi_bat_theme');

// ---- 2. Dinh nghia truong ACF bang PHP (hang sao, nguon goc, don vi) ----
function ocoptv_dang_ky_truong_acf() {
    if (!function_exists('acf_add_local_field_group')) {
        return; // chua cai ACF: dung hop nhap lieu du phong o phan duoi
    }

    // Nhom truong "Thong tin san pham OCOP" tren kieu noi dung product
    acf_add_local_field_group(array(
        'key'      => 'group_ocop_san_pham',
        'title'    => 'Thông tin sản phẩm OCOP',
        'location' => array(
            array(array('param' => 'post_type', 'operator' => '==', 'value' => 'product')),
        ),
        'position' => 'side',
        'fields'   => array(
            array(
                'key'      => 'field_hang_sao_ocop',
                'label'    => 'Hạng sao OCOP',
                'name'     => 'hang_sao_ocop',
                'type'     => 'select',
                'choices'  => array('3' => '3 sao', '4' => '4 sao'),
                'required' => 1,
            ),
            array(
                'key'          => 'field_nguon_goc',
                'label'        => 'Nguồn gốc',
                'name'         => 'nguon_goc',
                'type'         => 'text',
                'instructions' => 'Xã, huyện nơi sản xuất nguyên liệu',
                'required'     => 1,
            ),
            array(
                'key'           => 'field_don_vi_san_xuat',
                'label'         => 'Đơn vị sản xuất',
                'name'          => 'don_vi_san_xuat',
                'type'          => 'post_object',
                'post_type'     => array('don_vi_san_xuat'),
                'return_format' => 'id',
                'required'      => 1,
            ),
        ),
    ));

    // Nhom truong thong tin lien he cua don vi san xuat
    acf_add_local_field_group(array(
        'key'      => 'group_ocop_don_vi',
        'title'    => 'Thông tin đơn vị sản xuất',
        'location' => array(
            array(array('param' => 'post_type', 'operator' => '==', 'value' => 'don_vi_san_xuat')),
        ),
        'position' => 'side',
        'fields'   => array(
            array('key' => 'field_dia_chi', 'label' => 'Địa chỉ', 'name' => 'dia_chi', 'type' => 'text'),
            array('key' => 'field_dien_thoai', 'label' => 'Điện thoại', 'name' => 'dien_thoai', 'type' => 'text'),
            array('key' => 'field_thu_dien_tu', 'label' => 'Thư điện tử', 'name' => 'thu_dien_tu', 'type' => 'email'),
        ),
    ));
}
add_action('acf/init', 'ocoptv_dang_ky_truong_acf');

// ---- 3. Hop nhap lieu du phong khi chua cai ACF ----
function ocoptv_them_metabox_du_phong() {
    if (function_exists('acf_add_local_field_group')) {
        return; // ACF dang hoat dong, khong can metabox du phong
    }
    add_meta_box('ocoptv_ocop', 'Thông tin sản phẩm OCOP', 'ocoptv_render_metabox_ocop', 'product', 'side', 'default');
    add_meta_box('ocoptv_don_vi', 'Thông tin đơn vị sản xuất', 'ocoptv_render_metabox_don_vi', 'don_vi_san_xuat', 'side', 'default');
}
add_action('add_meta_boxes', 'ocoptv_them_metabox_du_phong');

function ocoptv_render_metabox_ocop($post) {
    wp_nonce_field('ocoptv_luu_truong', 'ocoptv_nonce');
    $sao   = get_post_meta($post->ID, 'hang_sao_ocop', true);
    $nguon = get_post_meta($post->ID, 'nguon_goc', true);
    $dv    = get_post_meta($post->ID, 'don_vi_san_xuat', true);
    ?>
    <p><label for="ocoptv_sao"><strong>Hạng sao OCOP</strong></label><br>
        <select name="ocoptv_hang_sao" id="ocoptv_sao" style="width:100%">
            <option value="">— Chọn —</option>
            <option value="3" <?php selected($sao, '3'); ?>>3 sao</option>
            <option value="4" <?php selected($sao, '4'); ?>>4 sao</option>
        </select></p>
    <p><label for="ocoptv_nguon"><strong>Nguồn gốc</strong></label><br>
        <input type="text" name="ocoptv_nguon_goc" id="ocoptv_nguon" style="width:100%"
               value="<?php echo esc_attr($nguon); ?>" placeholder="Xã, huyện nơi sản xuất"></p>
    <p><label for="ocoptv_dv"><strong>Đơn vị sản xuất</strong></label><br>
        <select name="ocoptv_don_vi" id="ocoptv_dv" style="width:100%">
            <option value="0">— Chọn —</option>
            <?php
            $don_vis = get_posts(array('post_type' => 'don_vi_san_xuat', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));
            foreach ($don_vis as $d) {
                printf('<option value="%d"%s>%s</option>', $d->ID, selected($dv, $d->ID, false), esc_html($d->post_title));
            }
            ?>
        </select></p>
    <?php
}

function ocoptv_render_metabox_don_vi($post) {
    wp_nonce_field('ocoptv_luu_truong', 'ocoptv_nonce');
    ?>
    <p><label><strong>Địa chỉ</strong></label><br>
        <input type="text" name="ocoptv_dia_chi" style="width:100%"
               value="<?php echo esc_attr(get_post_meta($post->ID, 'dia_chi', true)); ?>"></p>
    <p><label><strong>Điện thoại</strong></label><br>
        <input type="text" name="ocoptv_dien_thoai" style="width:100%"
               value="<?php echo esc_attr(get_post_meta($post->ID, 'dien_thoai', true)); ?>"></p>
    <p><label><strong>Thư điện tử</strong></label><br>
        <input type="email" name="ocoptv_thu_dien_tu" style="width:100%"
               value="<?php echo esc_attr(get_post_meta($post->ID, 'thu_dien_tu', true)); ?>"></p>
    <?php
}

function ocoptv_luu_truong_du_phong($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['ocoptv_nonce']) || !wp_verify_nonce($_POST['ocoptv_nonce'], 'ocoptv_luu_truong')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (get_post_type($post_id) === 'product') {
        if (isset($_POST['ocoptv_hang_sao'])) {
            update_post_meta($post_id, 'hang_sao_ocop', sanitize_text_field(wp_unslash($_POST['ocoptv_hang_sao'])));
        }
        if (isset($_POST['ocoptv_nguon_goc'])) {
            update_post_meta($post_id, 'nguon_goc', sanitize_text_field(wp_unslash($_POST['ocoptv_nguon_goc'])));
        }
        if (isset($_POST['ocoptv_don_vi'])) {
            update_post_meta($post_id, 'don_vi_san_xuat', intval($_POST['ocoptv_don_vi']));
        }
    }
    if (get_post_type($post_id) === 'don_vi_san_xuat') {
        foreach (array('ocoptv_dia_chi' => 'dia_chi', 'ocoptv_dien_thoai' => 'dien_thoai', 'ocoptv_thu_dien_tu' => 'thu_dien_tu') as $k => $meta) {
            if (isset($_POST[$k])) {
                update_post_meta($post_id, $meta, sanitize_text_field(wp_unslash($_POST[$k])));
            }
        }
    }
}
add_action('save_post', 'ocoptv_luu_truong_du_phong');
