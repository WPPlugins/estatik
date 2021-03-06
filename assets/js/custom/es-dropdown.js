(function( $ ) {
    'use strict';

    $.fn.esDropDown = function(options) {

        var methods = {};
        options = options || {};
        var icon = options.icon || '<i class="fa fa-caret-down" aria-hidden="true"></i>';

        /**
         * Wrap selected element.
         *
         * @param $el
         */
        methods.wrapElement = function($el) {
            if ($el.prop('tagName') == 'SELECT') {
                $el.wrap('<div class="es-dropdown-wrap es-dropdown-hide"></div>').before('<span class="es-dropdown-label"><span class="es-label">' + methods.getLabel($el)  + "</span>" + icon + '</span>' +
                    '<div class="es-dropdown-list"><ul></ul></div>');
            } else {
                $el.wrap('<div class="es-dropdown-wrap es-dropdown-hide"></div>').before('<span class="es-dropdown-label"><span class="es-label">' + methods.getLabel($el) + '</span>' + icon + '</span>');
            }
            $el.hide();
        };

        /**
         * Return field label.
         *
         * @param $el
         * @returns {*}
         */
        methods.getLabel = function($el) {
            var dataLabel = $el.data('dropdown-label');
            var linkLabel = $el.find('.active a').html();
            var valueLabel = $el.find('option:selected').html();
            var linkFirstValue = $el.find('a:first-child').html();
            var selectValue = $el.find('option:first-child').html();

            // console.log(dataLabel, linkLabel, valueLabel)

            return dataLabel || valueLabel || linkLabel || selectValue ||linkFirstValue;
        };

        /**
         * Build html list.
         *
         * @param $el
         */
        methods.buildList = function($el) {
            if ($el.prop('tagName') == 'SELECT') {
                var $options = $el.find('option');

                var $list = $el.closest('.es-dropdown-wrap').find('.es-dropdown-list ul');
                $list.html('');

                $options.each(function() {
                    var $option = $(this);
                    $list.append('<li data-dropdown-value="' + $option.attr('value') + '"><a href="#">' + $option.html() + '</a></li>');
                });

            }
        };

        /**
         * Hide dropdown method.
         *
         * @param $el
         */
        methods.hideDropDown = function($el) {
            $el.closest('.es-dropdown-wrap').removeClass('show').addClass('es-dropdown-hide');
        };

        /**
         * Rebuild element.
         *
         * @param $el
         */
        methods.rebuild = function($el) {
            methods.buildList($el);
            methods.setLabel($el);
        };

        /**
         * Set label to the element.
         *
         * @param $el
         */
        methods.setLabel = function($el) {
            var label = methods.getLabel($el);
            $el.closest('.es-dropdown-wrap').find('.es-label').html(label);
        };

        /**
         * Initialize element.
         *
         * @param $el
         */
        methods.init = function($el) {
            this.wrapElement($el);
            this.buildList($el);

            var $wrap = $el.closest('.es-dropdown-wrap');

            $wrap.on('click', function() {
                var $elWrap = $(this);
                if ($elWrap.hasClass('es-dropdown-hide')) {
                    $elWrap.addClass('show').removeClass('es-dropdown-hide');
                } else {
                    $elWrap.addClass('es-dropdown-hide').removeClass('show');
                }
            });

            $wrap.on('click', 'a', function() {
                if ($el.prop('tagName') == 'SELECT') {
                    $el.closest('.es-dropdown-wrap').find('li').removeClass('active');
                    $(this).closest('li').addClass('active');
                    $el.val($(this).closest('li').data('dropdown-value'));
                    $el.trigger('change');
                    methods.setLabel($el);

                    methods.hideDropDown($el);

                    return false;
                }
            });

            $el.on('change', function() {
                methods.setLabel($el);
            });

            $wrap.mouseleave(function() {
                methods.hideDropDown($el);
            });
        };

        /**
         * Initialize / rebuild list or each item.
         */
        this.each(function() {
            var $el = $(this);

            if (options == 'rebuild') {
                methods.rebuild($el);
            } else {
                methods.init($el);
            }
        });
    };

})(jQuery);
