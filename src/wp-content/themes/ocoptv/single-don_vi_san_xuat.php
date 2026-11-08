<?php
/**
 * File: wp-content/themes/ocoptv/single-don_vi_san_xuat.php
 * Khuon mau trang chi tiet don vi san xuat (theo phan cap khuon mau:
 * single-{post_type}.php cua child theme duoc uu tien truoc archive.php).
 */
get_header(); ?>
<div class="ocop-wrap">
    <?php
    while (have_posts()) :
        the_post();
        get_template_part('template-parts/content', 'don-vi');
    endwhile;
    ?>
</div>
<?php get_footer();
