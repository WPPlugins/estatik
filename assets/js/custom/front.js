(function($) {
    'use strict';

    $(document).ready(function() {
        var resetFlag = true;

        $('.es-search__wrapper select').esDropDown();

        $('.es-search__wrapper form').on('reset', function() {
            var $selectBox = $('.js-es-location');
            $selectBox.find('option:first').attr('selected', 'selected');
            $selectBox.closest('.es-dropdown-wrap').find('li.active').removeClass('active');
            $selectBox.trigger('change');

            $('.es-search__field input').val('');

            $(this).submit();
        });
    });
})(jQuery);
