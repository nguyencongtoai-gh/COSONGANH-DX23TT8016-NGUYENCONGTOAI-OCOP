<?php
// File: wp-content/themes/ocoptv/inc/loc-san-pham.php
// Xu ly tim kiem - loc - phan loai san pham theo 5 tieu chi
// (ten, nhom nganh, dia phuong, don vi san xuat, hang sao OCOP)
// va loc khoang gia, sap xep tren trang danh muc san pham.

// ---- 1. Xu ly tham so URL bang hook pre_get_posts ----
function ocoptv_loc_san_pham($query) {
    if (is_admin() || !$query->is_main_query()) return;

    // Trang danh muc san pham, trang phan loai, hoac tim kiem gioi han cho san pham
    $la_trang_san_pham = is_post_type_archive('product')
        || is_tax('nhom_nganh_ocop') || is_tax('dia_phuong')
        || (is_search() && isset($_GET['post_type']) && 'product' === $_GET['post_type']);
    if (!$la_trang_san_pham) return;

    if (is_search()) {
        $query->set('post_type', 'product');
    }

    $meta = array();
    if (!empty($_GET['hang_sao'])) {
        $meta[] = array('key' => 'hang_sao_ocop',
            'value' => sanitize_text_field($_GET['hang_sao']));
    }
    if (!empty($_GET['don_vi'])) {
        $meta[] = array('key' => 'don_vi_san_xuat',
            'value' => intval($_GET['don_vi']));
    }
    if (!empty($_GET['min']) || !empty($_GET['max'])) {
        $meta[] = array('key' => '_price', 'type' => 'NUMERIC',
            'value' => array(intval($_GET['min'] ?: 0),
                             intval($_GET['max'] ?: 999999999)),
            'compare' => 'BETWEEN');
    }
    if ($meta) $query->set('meta_query', $meta);

    // Loc theo phan loai: nhom nganh va dia phuong (tax_query)
    $tax = array();
    if (!empty($_GET['dia_phuong'])) {
        $tax[] = array(
            'taxonomy' => 'dia_phuong',
            'field'    => 'slug',
            'terms'    => sanitize_title($_GET['dia_phuong']),
        );
    }
    if (!empty($_GET['nhom_nganh'])) {
        $tax[] = array(
            'taxonomy' => 'nhom_nganh_ocop',
            'field'    => 'slug',
            'terms'    => sanitize_title($_GET['nhom_nganh']),
        );
    }
    if ($tax) {
        $query->set('tax_query', array_merge(array('relation' => 'AND'), $tax));
    }

    // Sap xep: moi nhat / gia tang / gia giam
    $sx = isset($_GET['sap_xep']) ? sanitize_key($_GET['sap_xep']) : '';
    if ($sx === 'gia-tang' || $sx === 'gia-giam') {
        $query->set('orderby', 'meta_value_num');
        $query->set('meta_key', '_price');
        $query->set('order', $sx === 'gia-tang' ? 'ASC' : 'DESC');
    } else {
        $query->set('orderby', 'date');
        $query->set('order', 'DESC');
    }
}
add_action('pre_get_posts', 'ocoptv_loc_san_pham');

// ---- 2. Giao dien bo loc (nam o cot trai trang danh muc) ----
function ocoptv_bo_loc_html() {
    $nhom   = get_terms(array('taxonomy' => 'nhom_nganh_ocop', 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC'));
    $dp     = get_terms(array('taxonomy' => 'dia_phuong', 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC'));
    $donvis = get_posts(array('post_type' => 'don_vi_san_xuat', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC'));

    $gia_tri = function ($k) {
        return isset($_GET[$k]) ? esc_attr(wp_unslash($_GET[$k])) : '';
    };
    $chon = function ($cur, $v) {
        return ((string) $cur === (string) $v) ? ' selected' : '';
    };

    $link_danh_muc = is_post_type_archive('product')
        ? get_post_type_archive_link('product') : home_url('/');

    ob_start();
    ?>
    <form class="ocop-filter" method="get" action="<?php echo esc_url($link_danh_muc); ?>">
        <h3>Bộ lọc sản phẩm</h3>

        <p class="ocop-filter__hang">
            <label for="ocop-f-s">Tìm theo tên</label>
            <input type="search" id="ocop-f-s" name="s" placeholder="Tên sản phẩm…"
                   value="<?php echo $gia_tri('s'); ?>">
            <input type="hidden" name="post_type" value="product">
        </p>

        <p class="ocop-filter__hang">
            <label for="ocop-f-nhom">Nhóm ngành</label>
            <select id="ocop-f-nhom" name="nhom_nganh">
                <option value="">Tất cả nhóm ngành</option>
                <?php if (!is_wp_error($nhom)) foreach ($nhom as $t) : ?>
                    <option value="<?php echo esc_attr($t->slug); ?>"<?php echo $chon($gia_tri('nhom_nganh'), $t->slug); ?>>
                        <?php echo esc_html($t->name); ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p class="ocop-filter__hang">
            <label for="ocop-f-dp">Địa phương</label>
            <select id="ocop-f-dp" name="dia_phuong">
                <option value="">Tất cả địa phương</option>
                <?php if (!is_wp_error($dp)) foreach ($dp as $t) : ?>
                    <option value="<?php echo esc_attr($t->slug); ?>"<?php echo $chon($gia_tri('dia_phuong'), $t->slug); ?>>
                        <?php echo esc_html($t->name); ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p class="ocop-filter__hang">
            <label for="ocop-f-dv">Đơn vị sản xuất</label>
            <select id="ocop-f-dv" name="don_vi">
                <option value="">Tất cả đơn vị</option>
                <?php foreach ($donvis as $d) : ?>
                    <option value="<?php echo esc_attr($d->ID); ?>"<?php echo $chon($gia_tri('don_vi'), $d->ID); ?>>
                        <?php echo esc_html($d->post_title); ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p class="ocop-filter__hang">
            <label for="ocop-f-sao">Hạng sao OCOP</label>
            <select id="ocop-f-sao" name="hang_sao">
                <option value="">Mọi hạng sao</option>
                <option value="3"<?php echo $chon($gia_tri('hang_sao'), '3'); ?>>3 sao</option>
                <option value="4"<?php echo $chon($gia_tri('hang_sao'), '4'); ?>>4 sao</option>
            </select>
        </p>

        <p class="ocop-filter__hang ocop-filter__gia">
            <label>Giá (đồng)</label>
            <input type="number" name="min" min="0" placeholder="Từ"
                   value="<?php echo $gia_tri('min'); ?>">
            <input type="number" name="max" min="0" placeholder="Đến"
                   value="<?php echo $gia_tri('max'); ?>">
        </p>

        <p class="ocop-filter__hang">
            <label for="ocop-f-sx">Sắp xếp</label>
            <select id="ocop-f-sx" name="sap_xep">
                <option value="moi-nhat"<?php echo $chon($gia_tri('sap_xep') ?: 'moi-nhat', 'moi-nhat'); ?>>Mới nhất</option>
                <option value="gia-tang"<?php echo $chon($gia_tri('sap_xep'), 'gia-tang'); ?>>Giá tăng dần</option>
                <option value="gia-giam"<?php echo $chon($gia_tri('sap_xep'), 'gia-giam'); ?>>Giá giảm dần</option>
            </select>
        </p>

        <p class="ocop-filter__nut">
            <button type="submit" class="ocop-btn">Áp dụng lọc</button>
            <a class="ocop-btn ocop-btn--xoa ocop-xoa-loc"
               href="<?php echo esc_url($link_danh_muc); ?>">Xóa lọc</a>
        </p>
    </form>
    <?php
    return ob_get_clean();
}

// Hien thi bo loc phia tren luoi san pham (WooCommerce);
// dong thoi dang ky shortcode [ocop_bo_loc] de dat o bat ky vi tri nao.
function ocoptv_hien_bo_loc() {
    echo ocoptv_bo_loc_html();
}
add_action('woocommerce_before_shop_loop', 'ocoptv_hien_bo_loc', 15);
add_shortcode('ocop_bo_loc', function () {
    return ocoptv_bo_loc_html();
});
