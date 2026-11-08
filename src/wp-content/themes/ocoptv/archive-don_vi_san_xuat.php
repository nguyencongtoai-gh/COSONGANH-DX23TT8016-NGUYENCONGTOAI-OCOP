<?php
/**
 * File: wp-content/themes/ocoptv/archive-don_vi_san_xuat.php
 * Trang danh sach don vi san xuat (muc menu "Don vi san xuat").
 */
get_header(); ?>
<div class="ocop-wrap">
    <h1 class="ocop-tieu-de-trang">Đơn vị sản xuất OCOP Trà Vinh</h1>
    <p class="ocop-mo-ta-trang">
        Toàn bộ chủ thể có sản phẩm OCOP đang giới thiệu trên website,
        xếp theo tên đơn vị. Chọn một đơn vị để xem hồ sơ và danh sách sản phẩm.
    </p>

    <?php if (have_posts()) : ?>
    <div class="ocop-grid ocop-grid--3 ocop-don-vi-grid">
        <?php while (have_posts()) : the_post(); ?>
            <article class="ocop-don-vi-the">
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('ocop-card'); ?></a>
                <?php endif; ?>
                <h2 class="ocop-don-vi-the__ten">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                <div class="ocop-don-vi-the__dp">
                    <?php
                    $dp = get_the_term_list(get_the_ID(), 'dia_phuong', '<i class="ocop-ico-dia-danh"></i>', ', ');
                    if ($dp && !is_wp_error($dp)) { echo wp_kses_post($dp); }
                    ?>
                </div>
                <div class="ocop-don-vi-the__mo-ta"><?php the_excerpt(); ?></div>
                <a class="ocop-btn" href="<?php the_permalink(); ?>">Xem hồ sơ đơn vị</a>
            </article>
        <?php endwhile; ?>
    </div>
    <?php the_posts_pagination(array('mid_size' => 1)); ?>
    <?php else : ?>
        <p class="ocop-empty">Chưa có đơn vị sản xuất nào được công bố.</p>
    <?php endif; ?>
</div>
<?php get_footer();
