(function($) {

    function initGoogleSingleMap() {
        var map = document.getElementById('es-google-map');

        if (map && Estatik.property.lat && Estatik.property.lon && typeof(EsGoogleMap) != 'undefined' ) {
            var instance = new EsGoogleMap(map, Estatik.property.lon, Estatik.property.lat).init();
            instance.setMarker();
        }
    }

    $(function () {
        var $singleSlickGallery = $('.es-gallery-image');
        var $singleSlickGalleryPager = $('.es-gallery-image-pager');
        var hash = document.location.hash.substring(1);

        var $nav = $('.es-single-tabs');

        var navPos = parseInt($nav.offset().top);
        var navPosLeft = parseInt($nav.offset().left);
        var navWidth = parseInt($nav.width());

        $(window).scroll(function (e) {
            if($(this).scrollTop() >= navPos){
                $nav.addClass('es-fixed');
                $nav.css({'left':navPosLeft+'px','width':navWidth+'px'});
            } else {
                $nav.removeClass('es-fixed');
                $nav.css({'left':'0px','width':'auto'});
            }

        });

        // if ($singleBxGallery.length) {

        if ($singleSlickGallery.length) {
            $singleSlickGallery.slick({
                arrows: false,
                asNavFor: $singleSlickGalleryPager
            });

            if ($singleSlickGallery.width() < 430) {
                var show = 4;
            } else {
                var show = 5;
            }

            $singleSlickGalleryPager.slick({
                asNavFor: $singleSlickGallery,
                slidesToScroll: 1,
                slidesToShow: show,
                focusOnSelect: true,
                // centerMode: true,
                nextArrow: $('.slick-next'),
                prevArrow: $('.slick-prev'),
                responsive: [
                    {
                        breakpoint: 1130,
                        settings: {
                            slidesToShow: 4
                        }
                    },
                    {
                        breakpoint: 780,
                        settings: {
                            slidesToShow: 3
                        }
                    },
                    {
                        breakpoint: 320,
                        settings: {
                            slidesToShow: 2
                        }
                    }
                ]
            });

            // $singleBxGallery.bxSlider({
            //     controls: false,
            //     pagerCustom: '.es-single-gallery-pager',
            //     adaptiveHeight: true,
            // });
            //
            // $('.es-single-gallery-pager').bxSlider({
            //     minSlides: 4,
            //     maxSlides: 4,
            //     slideWidth: 150,
            //     slideMargin: 15,
            //     pager: false,
            //     infiniteLoop: false,
            //     adaptiveHeight: true
            // });
        }

        initGoogleSingleMap();

        if (hash) {
            var $activeTab = $('.es-tab-' + hash).addClass('active');
        } else {
            $('.es-single-tabs').find('a').eq(0).addClass('active');
        }

        $('.es-single-tabs a').each(function() {
             if (!$($(this).attr('href')).length) {
                 $(this).hide();
             }
        });

        $('.es-single-tabs a, .es-top-link').click(function() {
            $('.es-single-tabs a').removeClass('active');
            $(this).addClass('active');

            var target = $(this).attr('href') == '#es-info' ? 'body' : $(this).attr('href');

            $('html, body').animate({
                scrollTop: $(target).offset().top - 50
            }, 600);

            return false;
        });
    });
})(jQuery);
