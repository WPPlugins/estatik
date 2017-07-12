(function($) {
    'use strict';

    /**
     * Initialize admin property google map.
     */
    function esInitMetaboxMap() {
        var $lon = $('#es-longitude-input').val();
        var $lat = $('#es-latitude-input').val();
        var map = document.getElementById('es-property-map');

        if ($lon && $lat && map && EsGoogleMap != 'undefined') {
            var instance = new EsGoogleMap(map, $lon, $lat).init();
            instance.setMarker();

            map.classList.add('es-map-border');

            instance.getGeocoderInfo($lat, $lon, function(results, status) {
                if (status == 'OK') {
                    $('#es-address-input').val(results[0].formatted_address);
                    $('#es-address_components-input').val(JSON.stringify(results[0].address_components));
                }
            });
        } else {
            if (map) {
                map.classList.remove('es-map-border');
            }
        }
    }

    /**
     * Change display css param in $searchPagesContainer using $el value.
     *
     * @param $el
     * @param $searchPagesContainer
     */
    function esSwitchSearchPagesContainerState($el, $searchPagesContainer) {
        var val = $el.val();

        if (val == 'all') {
            $searchPagesContainer.removeClass('show');
        } else {
            $searchPagesContainer.addClass('show');
        }
    }

    $(function() {
        var $unitsTooltipFields = $( '#es-area-input, #es-lot_size-input, #es-area_unit-input, #es-lot_size_unit-input' );
        var map = document.getElementById('es-property-map');
        var addressInput = document.getElementById('es-address-input');
        var $propMetaBoxDataTabs = $('.property-data-tabs');
        var $dataManagerItems = $('.es-data-manager-item');
        var $themesDashboardSlider = $('.es-themes-slider');
        var $styledCheckboxes = $('.es-switch-input');
        var $layoutRadio = $('.js-es-layout-checkbox');
        var $scrollList = $('.es-scroll-list');
        var $confirmPopup = $('.es-confirm-popup');
        var $messagePopup = $('.es-message-popup');

        if ($confirmPopup.length) {
            $confirmPopup.esPopup({confirmPopup: true});
        }

        if ($messagePopup.length) {
            $messagePopup.esPopup();
        }

        // Dashboard custom scroll for list blocks.
        if ($scrollList.length) {
            $scrollList.mCustomScrollbar();
        }

        if ($layoutRadio.length) {
            $layoutRadio.change(function() {
                var $el = $(this);
                var $wrap = $el.closest('.es-layout-wrap');

                $wrap.find('.es-sprite').removeClass('es-sprite-active');
                $el.closest('.es-layout-box').find('.es-sprite').addClass('es-sprite-active');
            });
        }

        if ($styledCheckboxes.length) {
            $styledCheckboxes.esCheckbox({
                labelTrue: Estatik.tr.yes,
                labelFalse: Estatik.tr.no
            });
        }

        var $tabs = $('.nav-tab-wrapper');

        if ($tabs.length) {
            $tabs.tabs();
        }

        if ($themesDashboardSlider.length) {
            $themesDashboardSlider.slick({
                arrows: true,
                slidesToShow: 5,
                centerMode: true,
                centerPadding: '10px',
                responsive: [
                    {
                        breakpoint: 1550,
                        settings: {
                            slidesToShow: 4,
                            infinite: true,
                            dots: true
                        }
                    },
                    {
                        breakpoint: 1290,
                        settings: {
                            slidesToShow: 3,
                            infinite: true,
                            dots: true
                        }
                    },
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 2,
                            infinite: true,
                            dots: true
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 1
                        }
                    }
                ]
            });
        }

        if ($dataManagerItems.length) {
            $dataManagerItems.each(function() {
                $(this).dataManagerItem();
            })
        }

        var $searchWidgetFieldsList = $('.es-search-widget-fields');

        var $showPagesSearchField = $('.js-show-search-pages');
        var $searchPagesContainer = $('.js-search-pages');

        $(document).ajaxSuccess(function() {
            var $searchWidgetFieldsList = $('.es-search-widget-fields');
            var $showPagesSearchField = $('.js-show-search-pages');
            // var $searchPagesContainer = $('.js-search-pages');

            if ($searchWidgetFieldsList.length) {
                $searchWidgetFieldsList.sortable();
            }

            if ($showPagesSearchField.length) {
                $showPagesSearchField.each(function() {
                    var $el = $(this);
                    esSwitchSearchPagesContainerState($el, $el.closest('.widget').find('.js-search-pages'));
                });
            }
        });

        $(document).on('change', '.js-show-search-pages', function() {
            var $el = $(this);
            esSwitchSearchPagesContainerState($(this), $el.closest('.widget').find('.js-search-pages'));
        });

        if ($showPagesSearchField.length) {
            $showPagesSearchField.each(function() {
                var $el = $(this);
                esSwitchSearchPagesContainerState($el, $el.closest('.widget').find('.js-search-pages'));
            });
        }

        $(document).on('change', '.js-es-field-select', function() {
            var $el = $(this);
            var $option = $el.find('option:selected');
            var value = $el.val();
            var $searchWidgetFieldsList = $(this).closest('.widget-content').find('.es-search-widget-fields');
            var name = $(this).closest('.widget-content').find('.es-fields-name').attr('name');

            if (value) {
                if (!$searchWidgetFieldsList.find('li[data-field-name=' + value + ']').length) {
                    $searchWidgetFieldsList.append('' +
                        '<li data-field-name="' + value + '">' + $option.html() + '<a href="#" class="es-remove-field">×</a>' +
                        '<input type="hidden" name="' + name + '" value="' + value + '"></li>');
                }
            }
            $el.val('');
        });

        if ($searchWidgetFieldsList.length) {
            $searchWidgetFieldsList.sortable();

            $(document).on('click', '.es-remove-field', function() {
                $(this).closest('li').remove();

                return false;
            })
        }

        // Initialize property data meta box tabs.
        if ($propMetaBoxDataTabs.length) {
            $propMetaBoxDataTabs.tabs({
                activate: function( event, ui ) {
                    esInitMetaboxMap();
                }
            });

            // Initialize metabox map.
            esInitMetaboxMap();
        }

        $('.js-es-add-custom').click(function() {
            var value = $('[name=es-custom-field]').val();

            if (value) {
                var content = '<div class="es-field es-field-custom">' +
                    '<div class="es-field__label">' + value + '</div>' +
                    '<div class="es-field__content">' +
                    '<input type="text" name="es_custom_value[]"/>' +
                    '<input type="hidden" name="es_custom_key[]" value="' + value + '"/>' +
                    '<a href="#" class="js-es-remove-custom"><span class="es-sprite es-sprite-close"></span></a>' +
                    '</div>' +
                    '</div>';

                $('.es-property-custom-wrap').before(content);
            }

            $('[name=es-custom-field]').val('');

            return false;
        });

        $(document).on('click', '.js-es-remove-custom', function() {
            $(this).closest('.property-data-field, .es-field-custom').remove();

            return false;
        });

        // Reinit metabox map after change coordinates.
        $('#es-latitude-input, #es-longitude-input').change(esInitMetaboxMap);

        if (addressInput) {
            var autocomplete = new google.maps.places.Autocomplete(addressInput);

            // Reinit map when address changed.
            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                var result = autocomplete.getPlace();

                if (result != 'undefined') {
                    var location = result.geometry.location;
                    $('#es-latitude-input').val(location.lat());
                    $('#es-longitude-input').val(location.lng());

                    esInitMetaboxMap();
                }
            })
        }

        $unitsTooltipFields.on('change click focus', function () {
            var $content = $(this).closest('.es-field__content');
            var val = $content.find('input').val();
            var unit = $content.find('select').val();

            $('.tooltipstered').tooltipster({}).tooltipster('close');

            $.post(ajaxurl, {val: val, unit: unit, action: 'es_calculate_units'}, function(response) {
                response = response || {};

                if (response.status) {
                    $content.find('select').tooltipster({
                        contentAsHTML: true,
                        theme: 'tooltipster-borderless',
                        side: ['right'],
                        debug: false
                    }).tooltipster('content', response.content).tooltipster('open');
                }
            }, 'json');
        }).mouseleave(function() {
            $('.tooltipstered').tooltipster({}).tooltipster('close');
        });

    });
})(jQuery);
