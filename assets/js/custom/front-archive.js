(function($) {

    function initGoogleSingleMap(lon, lat) {
        var map = document.getElementById('es-map-inner');

        if (map && lon && lat) {
            var instance = new EsGoogleMap(map, lon, lat).init();
            instance.setMarker();
        }
    }

    $(function() {
        var $listWrapper = $('.es-listing');
        var currentListClass = 'es-layout-' + Estatik.settings.layout;
        var resizeOptions = Estatik.settings.responsive;
        var resizeOptionsClassString = Object.keys(resizeOptions).join(' ');
        var currentResponsiveClass = currentListClass;

        $(window).on('resize', function() {
            // Property width.
            var contentWidth = $listWrapper.width();

            if (resizeOptions) {
                for (var layoutClassName in resizeOptions) {
                    var currentMin = resizeOptions[currentListClass].min;

                    var min = resizeOptions[layoutClassName].min;
                    var max = resizeOptions[layoutClassName].max;

                    if (contentWidth < currentMin || currentResponsiveClass != currentListClass) {
                        if (contentWidth > min && contentWidth < max) {
                            $listWrapper.removeClass(resizeOptionsClassString).addClass(layoutClassName);
                            currentResponsiveClass = layoutClassName;
                        }
                    }

                    if (contentWidth < 410) {
                        $listWrapper.addClass('es-col-1');
                    } else {
                        $listWrapper.removeClass('es-col-1');
                    }
                }
            }
        });

        $(window).trigger('resize');

        $('#es-map-popup').dialog({
            resizable: false,
            height: "auto",
            modal: true,
            autoOpen: false,
            width: '50%',
            closeText: '×',
            show: 500,
            hide: 500
        });

        $('.es-map-view-link').click(function () {
            var lon = parseFloat($(this).data('longitude')), lat = parseFloat($(this).data('latitude'));

            $('#es-map-popup').dialog('open');

            initGoogleSingleMap(lon, lat);

            return false;
        });

        $('.es-list-dropdown').esDropDown({
            defaultLabel: Estatik.tr.sorting
        });
    });
})(jQuery);
