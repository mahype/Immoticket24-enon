
(function($) {
    $(document).ready(function(event) {
        _init();
    });

    // When editing post, init map after tinymce is ready
    $(document).on('tinymce-editor-init', function(event, editor) {
        setTimeout(function(){
            if (typeof tinymce !== undefined && tinymce.activeEditor !== null) {
                var tinymceDocument = tinymce.activeEditor.contentDocument;
                var maps = tinymceDocument.getElementsByClassName('op-map');

                for (var i = 0; i < maps.length; i++) {
                    _init_map(maps[i]);
                }
            } 
        }, 1000);
    });

    /**
     * Get all maps and call initialization
     * @return {Void}
     */
    var _init = function() {
        var maps = $('.op-map');

        $.each(maps, function() {
            _init_map(this);
        });
    }

    /**
     * Initializate Map, Marker and set map style
     * @param  {[Object]} obj
     * @return {Void}
     */
    var _init_map = function(obj) {
        var jQueryObj = $(obj),
            data = jQueryObj.data('map');

        //create map
        var map = new google.maps.Map(obj, {
            center: {
                lat: parseFloat(data.map_position_lat),
                lng: parseFloat(data.map_position_lng)
            },

            zoom: parseFloat(data.map_zoom),
            zoomControl: Boolean(data.disable_zoom_buttons),
            scrollwheel: Boolean(data.disable_scroolwheel_zoom),
            mapTypeControl: Boolean(data.disable_satellite),
            streetViewControl: Boolean(data.disable_street_view),
            disableDoubleClickZoom: true,
            draggable: Boolean(data.disable_map_drag)
        });

        //set map style
        $.getJSON(OptimizePress.oppp.path + "/templates/elements/maps_styles/style_" + data.map_style + ".json", function(style){
            map.mapTypes.set("style_" + data.map_style , new google.maps.StyledMapType(style));
            map.setMapTypeId("style_" + data.map_style);
        });

        //create marker
        var marker = new google.maps.Marker({
            position: { 
                    lat: parseFloat(data.marker_position_lat), 
                    lng: parseFloat(data.marker_position_lng) 
            },
            map: map,
            icon: OptimizePress.oppp.path + "images/elements/op_map/img/" + data.marker_icon,
        });

        //on window resize keep map center
        $(window).on('resize', function (event) {
            var center = map.getCenter();
            google.maps.event.trigger(map, "resize");
            map.setCenter(center);
        });
    }

    // Expose the function for external use (when element is inserted into LE)
    $(document).on('op.afterLiveEditorParse',function() {
        // We wait to html to be rendered and shown (fadeout fast + fadein faset)
        setTimeout(function () {
            _init();
        }, 401);
    });
})(opjq, document);
