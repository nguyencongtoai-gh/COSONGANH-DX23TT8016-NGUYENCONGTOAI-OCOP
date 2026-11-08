<?php
// File: wp-content/themes/ocoptv/inc/trang-chi-tiet.php
// Khung "Thong tin san pham OCOP" (bay thuoc tinh bat buoc)
// va khoa "Mua tu don vi san xuat" tren trang chi tiet san pham.

function ocoptv_khung_thong_tin_ocop() {
    $id    = get_the_ID();
    $sao   = get_post_meta($id, 'hang_sao_ocop', true);
    $nguon = get_post_meta($id, 'nguon_goc', true);
    $dv    = intval(get_post_meta($id, 'don_vi_san_xuat', true));
    if (!$sao && !$nguon && !$dv) {
        return;
    }

    $gia = '';
    if (function_exists('wc_get_product')) {
        $p = wc_get_product($id);
        if ($p) { $gia = $p->get_price(); }
    }
    if ($gia === '' || $gia === null) {
        $gia = get_post_meta($id, '_price', true);
    }
    if ($gia !== '' && $gia !== null) {
        $gia_hien = function_exists('wc_price')
            ? wc_price($gia)
            : number_format((float) $gia, 0, ',', '.') . ' ₫';
    } else {
        $gia_hien = '—';
    }
    ?>
    <div class="ocop-thong-tin">
        <h2>Thông tin sản phẩm OCOP</h2>
        <table class="ocop-bang-thong-tin">
            <tr>
                <th>Hạng sao OCOP</th>
                <td><?php echo $sao
                    ? esc_html(str_repeat('★', (int) $sao) . ' (' . $sao . ' sao)') : '—'; ?></td>
            </tr>
            <tr>
                <th>Nguồn gốc</th>
                <td><?php echo $nguon ? esc_html($nguon) : '—'; ?></td>
            </tr>
            <tr>
                <th>Đơn vị sản xuất</th>
                <td><?php if ($dv) : ?>
                    <a href="<?php echo esc_url(get_permalink($dv)); ?>"><?php echo esc_html(get_the_title($dv)); ?></a>
                <?php else : ?>—<?php endif; ?></td>
            </tr>
            <tr>
                <th>Giá bán</th>
                <td><?php echo wp_kses_post($gia_hien); ?></td>
            </tr>
            <?php
            $nhom = get_the_term_list($id, 'nhom_nganh_ocop', '', ', ');
            if ($nhom && !is_wp_error($nhom)) : ?>
            <tr><th>Nhóm ngành</th><td><?php echo wp_kses_post($nhom); ?></td></tr>
            <?php endif;
            $dp = get_the_term_list($id, 'dia_phuong', '', ', ');
            if ($dp && !is_wp_error($dp)) : ?>
            <tr><th>Địa phương</th><td><?php echo wp_kses_post($dp); ?></td></tr>
            <?php endif; ?>
        </table>
        <?php if ($dv) : ?>
        <p class="ocop-mua-tu-don-vi">Mua trực tiếp từ đơn vị sản xuất:
            <a class="ocop-btn" href="<?php echo esc_url(get_permalink($dv)); ?>">Xem hồ sơ đơn vị</a>
        </p>
        <?php endif; ?>
    </div>
    <?php
}

// Neu WooCommerce dang hoat dong: gan khung vao tom tat trang chi tiet
add_action('woocommerce_single_product_summary', 'ocoptv_khung_thong_tin_ocop', 25);

// Neu khong co WooCommerce: phu luu bang bo loc the_content cho trang chi tiet
function ocoptv_noi_dung_chi_tiet_khong_wc($content) {
    if (class_exists('WooCommerce') || !is_singular('product') || !in_the_loop() || !is_main_query()) {
        return $content;
    }
    ob_start();
    ocoptv_khung_thong_tin_ocop();
    return $content . ob_get_clean();
}
add_filter('the_content', 'ocoptv_noi_dung_chi_tiet_khong_wc', 25);
