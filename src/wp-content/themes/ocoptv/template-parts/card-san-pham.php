<?php
/**
 * File: wp-content/themes/ocoptv/template-parts/card-san-pham.php
 * The san pham OCOP dung chung cho moi luoi san pham
 * (shortcode [ocop_noibat], [ocop_danhsach], trang danh muc).
 */
$sao = get_query_var('ocop_hang_sao',
    get_post_meta(get_the_ID(), 'hang_sao_ocop', true));
$donvi_id = get_post_meta(get_the_ID(), 'don_vi_san_xuat', true);

$gia = '';
if (function_exists('wc_get_product')) {
    $p = wc_get_product(get_the_ID());
    if ($p) { $gia = $p->get_price(); }
}
if ($gia === '' || $gia === null) {
    $gia = get_post_meta(get_the_ID(), '_price', true);
}
if ($gia !== '' && $gia !== null) {
    $gia_hien = function_exists('wc_price')
        ? wc_price($gia)
        : number_format((float) $gia, 0, ',', '.') . ' ₫';
} else {
    $gia_hien = 'Liên hệ';
}
?>
<article class="ocop-card">
    <a href="<?php the_permalink(); ?>" class="ocop-card__img">
        <?php if (has_post_thumbnail()) {
            the_post_thumbnail('ocop-card');
        } else { ?>
            <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/logo.png"
                 alt="<?php the_title_attribute(); ?>">
        <?php } ?>
        <?php if ((int) $sao >= 4) : ?>
            <span class="ocop-badge"><?php echo esc_html($sao); ?> sao</span>
        <?php endif; ?>
    </a>
    <h3 class="ocop-card__title">
        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    </h3>
    <?php if ($donvi_id) : ?>
    <div class="ocop-card__don-vi">
        <a href="<?php echo esc_url(get_permalink($donvi_id)); ?>">
            <?php echo esc_html(get_the_title($donvi_id)); ?></a>
    </div>
    <?php endif; ?>
    <div class="ocop-card__stars">
        <?php echo esc_html(str_repeat('★', (int) $sao) . str_repeat('☆', 5 - (int) $sao)); ?>
    </div>
    <div class="ocop-card__price">
        <?php echo wp_kses_post($gia_hien); ?>
    </div>
    <div class="ocop-card__actions">
        <?php
        if (function_exists('woocommerce_template_loop_add_to_cart')) {
            woocommerce_template_loop_add_to_cart();
        } else { ?>
            <a class="ocop-btn" href="<?php the_permalink(); ?>">Xem chi tiết</a>
        <?php } ?>
    </div>
</article>
