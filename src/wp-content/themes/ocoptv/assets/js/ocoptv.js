/**
 * File: wp-content/themes/ocoptv/assets/js/ocoptv.js
 * Tuong tac giao dien cua child theme OCOP TV:
 *  1. Bo loc tu dong gui khi doi select;
 *  2. Lightbox anh cho thu vien;
 *  3. Nut ve dau trang.
 */
/* global jQuery */
jQuery(function ($) {
    'use strict';

    // 1. Bo loc san pham: doi select -> tu dong ap dung (khong can nhan nut)
    $('.ocop-filter').on('change', 'select', function () {
        $(this).closest('form').submit();
    });

    // 2. Lightbox don gian cho thu vien anh / anh dai dien
    var $box = null;

    function moLightbox(src) {
        if (!$box) {
            $box = $('<div class="ocop-lightbox"><img alt="Xem ảnh" /></div>').appendTo('body');
            $box.on('click', function () {
                $(this).removeClass('open');
            });
        }
        $box.find('img').attr('src', src);
        $box.addClass('open');
    }

    $(document).on('click', '.ocop-gallery a, a[href$=".png"], a[href$=".jpg"], a[href$=".jpeg"], a[href$=".webp"]', function (e) {
        var href = $(this).attr('href') || '';
        var laAnhTrong = $(this).find('img').length > 0;
        // Chi mo anh trong noi dung website (tranh chan lien ket tai nguyen ngoai)
        if (laAnhTrong && /\.(png|jpe?g|gif|webp)$/i.test(href)) {
            e.preventDefault();
            moLightbox(href);
        }
    });

    // 3. Nut ve dau trang
    var $nut = $('<button class="ocop-ve-dau" aria-label="Về đầu trang">&uarr;</button>').appendTo('body');
    $(window).on('scroll', function () {
        if ($(window).scrollTop() > 480) {
            $nut.addClass('hien');
        } else {
            $nut.removeClass('hien');
        }
    });
    $nut.on('click', function () {
        $('html, body').animate({ scrollTop: 0 }, 320);
    });
});
