<?php
// File: wp-content/themes/ocoptv/functions.php
// Child theme "OCOP TV" cho website gioi thieu san pham OCOP Tra Vinh.

define('OCOPTV_VERSION', '1.0.0');

// 1. Nap stylesheet cua theme cha truoc, stylesheet con sau
function ocoptv_enqueue_styles() {
    wp_enqueue_style('astra-parent',
        get_template_directory_uri() . '/style.css');
    wp_enqueue_style('ocoptv-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('astra-parent'), OCOPTV_VERSION);
    // Bo mau xanh - vang va bo cuc tang dinh cua do an
    wp_enqueue_style('ocoptv-custom',
        get_stylesheet_directory_uri() . '/assets/css/ocoptv.css',
        array('ocoptv-style'), OCOPTV_VERSION);
    wp_enqueue_script('ocoptv-script',
        get_stylesheet_directory_uri() . '/assets/js/ocoptv.js',
        array('jquery'), OCOPTV_VERSION, true);
}
add_action('wp_enqueue_scripts', 'ocoptv_enqueue_styles');

// 2. Nap cac module chuc nang tu viet cua child theme
require_once get_stylesheet_directory() . '/inc/custom-types.php';
require_once get_stylesheet_directory() . '/inc/phan-quyen.php';
require_once get_stylesheet_directory() . '/inc/kiem-duyet.php';
require_once get_stylesheet_directory() . '/inc/loc-san-pham.php';
require_once get_stylesheet_directory() . '/inc/trang-chi-tiet.php';
require_once get_stylesheet_directory() . '/shortcodes/ocop-noibat.php';
require_once get_stylesheet_directory() . '/shortcodes/ocop-danhsach.php';

// 3. Ho tro anh dai dien co kich thuoc rieng cho luoi san pham
add_theme_support('post-thumbnails');
add_image_size('ocop-card', 400, 300, true);
add_image_size('ocop-banner', 1200, 500, true);

// 4. Canh bao neu chua cai theme cha Astra
function ocoptv_canh_bao_theme_cha() {
    if (get_template() !== 'astra') {
        echo '<div class="notice notice-error"><p><strong>OCOP TV:</strong> '
            . 'child theme yeu cau theme cha <strong>Astra</strong> duoc cai dat truoc. '
            . 'Vao Appearance &rarr; Themes de cai dat Astra roi kich hoat lai OCOP TV.</p></div>';
    }
}
add_action('admin_notices', 'ocoptv_canh_bao_theme_cha');
