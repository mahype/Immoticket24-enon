var op_asset_settings = (function($) {

    if (typeof OptimizePress.map === 'undefined') {
        OptimizePress.map = {};
    }

    OptimizePress.map.liveEditorMap = '';
    OptimizePress.map.liveEditorMarker = '';
    OptimizePress.map.currentPosition = {
        'lat': 0,
        'lng': 0
    };

    OptimizePress.map.styles = [];
    OptimizePress.map.styles[0];
    OptimizePress.map.styles[1] = [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}];
    OptimizePress.map.styles[2] = [{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}];
    OptimizePress.map.styles[3] = [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]}];
    OptimizePress.map.styles[4] = [{"featureType":"administrative","elementType":"all","stylers":[{"saturation":"-100"}]},{"featureType":"administrative.province","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"saturation":-100},{"lightness":65},{"visibility":"on"}]},{"featureType":"poi","elementType":"all","stylers":[{"saturation":-100},{"lightness":"50"},{"visibility":"simplified"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":"-100"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"lightness":"30"}]},{"featureType":"road.local","elementType":"all","stylers":[{"lightness":"40"}]},{"featureType":"transit","elementType":"all","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#ffff00"},{"lightness":-25},{"saturation":-97}]},{"featureType":"water","elementType":"labels","stylers":[{"lightness":-25},{"saturation":-100}]}];
    OptimizePress.map.styles[5] = [{"featureType":"landscape","stylers":[{"hue":"#FFBB00"},{"saturation":43.400000000000006},{"lightness":37.599999999999994},{"gamma":1}]},{"featureType":"road.highway","stylers":[{"hue":"#FFC200"},{"saturation":-61.8},{"lightness":45.599999999999994},{"gamma":1}]},{"featureType":"road.arterial","stylers":[{"hue":"#FF0300"},{"saturation":-100},{"lightness":51.19999999999999},{"gamma":1}]},{"featureType":"road.local","stylers":[{"hue":"#FF0300"},{"saturation":-100},{"lightness":52},{"gamma":1}]},{"featureType":"water","stylers":[{"hue":"#0078FF"},{"saturation":-13.200000000000003},{"lightness":2.4000000000000057},{"gamma":1}]},{"featureType":"poi","stylers":[{"hue":"#00FF6A"},{"saturation":-1.0989010989011234},{"lightness":11.200000000000017},{"gamma":1}]}]

    return {
        attributes: {
            step_1: {
                gmaps_no_api_key: {
                    addClass: 'gmaps-no-api-key gmaps-no-api-key--hidden',
                    type: 'microcopy',
                    text: 'gmaps_no_api_key'
                },
                op_theme_version: {
                    addClass: 'op-theme-version op-theme-version--hidden',
                    type: 'microcopy',
                    text: 'op_theme_version'
                },
                style: {
                    type: 'style-selector',
                    folder: 'previews',
                    addClass: 'op-disable-selected',
                    events: {
                        change: function() {
                            setTimeout(function(){
                                var center = OptimizePress.map.liveEditorMap.getCenter();
                                google.maps.event.trigger(OptimizePress.map.liveEditorMap, "resize");
                                OptimizePress.map.liveEditorMap.setCenter(center);
                            }, 500);
                        }
                    }
                }
            },
            step_2: {
                hidden: {
                    text: 'gmaps_api_key_microcopy_hidden',
                    type: 'hidden',
                    events: {
                        change: function() {
                            //OptimizePress Theme version is not 2.5.10 or above
                            if (typeof OptimizePress.map.gmapsApiKey === 'undefined') {
                                $('.micro-copy.op-theme-version').removeClass('op-theme-version--hidden');
                                $('#op_assets_addon_op_map_style_container').hide();

                                return;
                            }

                            //Google Maps Api Key is not set in OP > Dashboard
                            if (OptimizePress.map.gmapsApiKey === '' || OptimizePress.map.gmapsApiKey === false) {
                                $('.micro-copy.gmaps-no-api-key').removeClass('gmaps-no-api-key--hidden');
                                $('#op_assets_addon_op_map_style_container').hide();

                                return;
                            }

                            //Reset map to default position when LE map is already instantiate
                            //and user want to add new map
                            if (typeof OptimizePress.map.liveEditorMap == 'object') {
                                OptimizePress.map.liveEditorMarker.setPosition({ lat: 51.509865, lng: -0.118092 });
                                OptimizePress.map.liveEditorMarker.setIcon(OptimizePress.oppp.path + 'images/elements/op_map/img/marker1.png');
                                OptimizePress.map.liveEditorMap.setCenter({ lat: 51.509865, lng: -0.118092 });
                                OptimizePress.map.liveEditorMap.setZoom(5);
                                OptimizePress.map.liveEditorMap.setMapTypeId("style_1");
                            }

                            //Instantiate new map if it't not
                            if (typeof OptimizePress.map.liveEditorMap == 'string') {
                                //Create map
                                OptimizePress.map.liveEditorMap = new google.maps.Map(document.getElementById('op-live-editor-map'), {
                                    center: {
                                        lat: 51.509865,
                                        lng: -0.118092
                                    },
                                    zoom: 5,
                                    disableDoubleClickZoom: true,
                                    mapTypeControl: false,
                                    streetViewControl: false
                                });

                                //Create Marker
                                OptimizePress.map.liveEditorMarker = new google.maps.Marker({
                                    position: {
                                        lat: 51.509865,
                                        lng: -0.118092
                                    },
                                    map: OptimizePress.map.liveEditorMap,
                                    draggable: true,
                                    title: "Drag me!",
                                    icon: OptimizePress.oppp.path + "images/elements/op_map/img/marker1.png",
                                });

                                //Create My Location button
                                var myLocationDiv = document.createElement('div');
                                myLocationDiv.className = 'op_map_my_location';

                                var locationBtnUI = document.createElement('div');
                                locationBtnUI.className = 'op_map_location_btn';
                                locationBtnUI.title = 'Click to set map and marker to your current location';
                                myLocationDiv.appendChild(locationBtnUI);

                                var locationBtnText = document.createElement('div');
                                locationBtnText.className = 'op_map_location_btn_text';
                                locationBtnText.innerHTML = 'My location';
                                locationBtnUI.appendChild(locationBtnText);

                                myLocationDiv.addEventListener('click', getMyLocationAndSetItToMap);
                                myLocationDiv.index = 1;
                                OptimizePress.map.liveEditorMap.controls[google.maps.ControlPosition.TOP_CENTER].push(myLocationDiv);

                                /**
                                 * Methods
                                 */
                                function getMyLocationAndSetItToMap() {
                                    if (window.location.protocol === 'http:' && /Chrome/.test(window.navigator.userAgent)) {
                                        alert("This feature is only available on https protocol");
                                        return;
                                    }

                                    if (navigator.geolocation) {
                                        navigator.geolocation.getCurrentPosition(function(location) {
                                            OptimizePress.map.liveEditorMap.setCenter({lat: location.coords.latitude, lng: location.coords.longitude});
                                            OptimizePress.map.liveEditorMarker.setPosition({lat: location.coords.latitude, lng: location.coords.longitude});
                                            markerWasMoved(OptimizePress.map.liveEditorMarker);
                                        });
                                    }
                                }

                                function markerWasMoved(event) {
                                    if (event.hasOwnProperty('latLng')) {
                                        $("#op_assets_addon_op_map_marker_position_lat").val(event.latLng.lat());
                                        $("#op_assets_addon_op_map_marker_position_lng").val(event.latLng.lng());
                                    }

                                    if (event.hasOwnProperty('position')) {
                                        $("#op_assets_addon_op_map_marker_position_lat").val(event.position.lat());
                                        $("#op_assets_addon_op_map_marker_position_lng").val(event.position.lng());
                                    }
                                }

                                function mapWasZoomed() {
                                    $("#op_assets_addon_op_map_map_zoom").val(OptimizePress.map.liveEditorMap.getZoom());
                                }

                                function mapWasDubleClicked(event) {
                                    OptimizePress.map.liveEditorMarker.setPosition(event.latLng);
                                }

                                function mapWasMoved(event) {
                                    $("#op_assets_addon_op_map_map_position_lat").val(OptimizePress.map.liveEditorMap.getCenter().lat());
                                    $("#op_assets_addon_op_map_map_position_lng").val(OptimizePress.map.liveEditorMap.getCenter().lng());
                                }

                                /**
                                 * Listeners
                                 */
                                OptimizePress.map.liveEditorMarker.addListener('dragend', markerWasMoved);
                                OptimizePress.map.liveEditorMap.addListener('dblclick', markerWasMoved);
                                OptimizePress.map.liveEditorMap.addListener('center_changed', mapWasMoved);
                                OptimizePress.map.liveEditorMap.addListener('zoom_changed', mapWasZoomed);
                                OptimizePress.map.liveEditorMap.addListener('dblclick', mapWasDubleClicked);

                                // Create the search box and link it to the UI element.
                                var input = document.getElementById('op-pac-input-map');
                                var searchBox = new google.maps.places.SearchBox(input);
                                OptimizePress.map.liveEditorMap.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
                                OptimizePress.map.liveEditorMap.addListener('bounds_changed', function() {
                                    searchBox.setBounds(OptimizePress.map.liveEditorMap.getBounds());
                                });

                                var markers = [];
                                // Listen for the event fired when the user selects a prediction and retrieve
                                // more details for that place.
                                searchBox.addListener('places_changed', function() {
                                    var places = searchBox.getPlaces();

                                    if (places.length == 0) {
                                        return;
                                    }

                                    // Clear out the old markers.
                                    markers.forEach(function(marker) {
                                        marker.setMap(null);
                                    });
                                    markers = [];

                                    // For each place, get the icon, name and location.
                                    var bounds = new google.maps.LatLngBounds();
                                    places.forEach(function(place) {
                                        if (!place.geometry) {
                                            console.log("Returned place contains no geometry");
                                            return;
                                        }

                                        OptimizePress.map.liveEditorMarker.setPosition(place.geometry.location);
                                        markerWasMoved(OptimizePress.map.liveEditorMarker);

                                        if (place.geometry.viewport) {
                                            // Only geocodes have viewport.
                                            bounds.union(place.geometry.viewport);
                                        } else {
                                            bounds.extend(place.geometry.location);
                                        }
                                    });
                                    OptimizePress.map.liveEditorMap.fitBounds(bounds);
                                });
                            }
                        }
                    }
                },
                map_container: {
                    title: 'map_container',
                    type: 'custom_html',
                    addClass: 'form_html_field form_html_info_field cf',
                    html: '<div id="op-live-editor-map-container">' +
                          '<input id="op-pac-input-map" class="controls op-pac-input" type="text" placeholder="Search Box">' +
                          '<div id="op-live-editor-map" class="oppp_map_container"></div>' +
                          '</div>',
                },
                map_style: {
                    title: 'Map Style',
                    type: 'select',
                    values: {
                        '1': 'Default',
                        '2': 'Ultra Light With Labels',
                        '3': 'Shades of Gray',
                        '4': 'Blue Water',
                        '5': 'Subtle Grayscale',
                        '6': 'Light Dream',
                    },
                    default_value: '1',
                    events: {
                        change: function(event) {
                            if (OptimizePress.map.liveEditorMap !== '') {
                                var choosenStyle = $(this).find(":selected").val();
                                OptimizePress.map.liveEditorMap.mapTypes.set('style_' + choosenStyle, new google.maps.StyledMapType(OptimizePress.map.styles[choosenStyle - 1]));
                                OptimizePress.map.liveEditorMap.setMapTypeId('style_' + choosenStyle);
                            }
                        }
                    }
                },
                marker_icon: {
                    title: 'map_marker',
                    type: 'image-selector',
                    selectorClass: 'icon-view-32',
                    class: 'blaasds',
                    folder: "img",
                    default_value: 'marker1.png',
                    events: {
                        change: function(value) {
                            if (OptimizePress.map.liveEditorMarker !== '') {
                                OptimizePress.map.liveEditorMarker.setIcon(window.oppp_path + 'images/elements/op_map/img/' + value);
                            }
                        }
                    },
                },
                disable_scroolwheel_zoom: {
                    title: 'disable_scroolwheel_zoom',
                    type: 'checkbox'
                },
                disable_satellite: {
                    title: 'disable_satellite',
                    type: 'checkbox'
                },
                disable_street_view: {
                    title: 'disable_street_view',
                    type: 'checkbox'
                },
                disable_zoom_buttons: {
                    title: 'disable_zoom_buttons',
                    type: 'checkbox'
                },
                disable_map_drag: {
                    title: 'disable_map_drag',
                    type: 'checkbox'
                },
                map_height: {
                    title: 'map_height',
                    default_value: 200,
                    addClass: 'form_html_map_height_field',
                },
                map_zoom: {
                    type: 'hidden',
                    default_value: 8,
                    addClass: 'form_html_map_zoom_field',
                },
                map_position_lat: {
                    type: 'hidden',
                    default_value: '51.509865',
                    addClass: 'form_html_map_position_field',
                },
                map_position_lng: {
                    type: 'hidden',
                    default_value: '-0.118092',
                },
                marker_position_lat: {
                    type: 'hidden',
                    default_value: '51.509865',
                    addClass: 'form_html_map_position_field',
                },
                marker_position_lng: {
                    type: 'hidden',
                    default_value: '-0.118092',
                    addClass: 'form_html_map_position_field',
                }
            }
        },
        insert_steps: { 2: true },
        customInsert: function(attrs) {
            var str = '',
                style = '';

            for (var i in attrs) {
                if (attrs.hasOwnProperty(i)) {
                    str = str + i + '=' + '"' + attrs[i] + '" ';
                }
            }

            str = '[op_map ' + str + '][/op_map]';
            OP_AB.insert_content(str);
            $.fancybox.close();
        },

        customSettings: function(attrs, steps) { 
            if (typeof OptimizePress.map.gmapsApiKey == 'undefined' || OptimizePress.map.gmapsApiKey == '') {
                setTimeout(function(){OptimizePress.LiveEditor.show_slide(2)}, '100');
                return;
            }

            //refresh map
            google.maps.event.trigger(OptimizePress.map.liveEditorMap, 'resize');

            //set map and marker position
            OptimizePress.map.liveEditorMarker.setPosition({ lat: parseFloat(attrs.attrs.marker_position_lat), lng: parseFloat(attrs.attrs.marker_position_lng) });
            OptimizePress.map.liveEditorMarker.setIcon(OptimizePress.oppp.path + 'images/elements/op_map/img/' + attrs.attrs.marker_icon);
            OptimizePress.map.liveEditorMap.setCenter({ lat: parseFloat(attrs.attrs.map_position_lat), lng: parseFloat(attrs.attrs.map_position_lng) });
            OptimizePress.map.liveEditorMap.setZoom(parseInt(attrs.attrs.map_zoom));
            OptimizePress.map.liveEditorMap.setMapTypeId("style_" + attrs.attrs.map_style);
            $('.field-map_style').find('select').val(attrs.attrs.map_style).trigger('change');


            OP_AB.set_selector_value('op_assets_addon_op_map_marker_icon_container', attrs.attrs.marker_icon);
            $("#op_assets_addon_op_map_map_height").val(attrs.attrs.map_height);

            //set hidden fields
            $("#op_assets_addon_op_map_map_position_lat").val(attrs.attrs.map_position_lat);
            $("#op_assets_addon_op_map_map_position_lng").val(attrs.attrs.map_position_lng);
            $("#op_assets_addon_op_map_marker_position_lat").val(attrs.attrs.marker_position_lat);
            $("#op_assets_addon_op_map_marker_position_lng").val(attrs.attrs.marker_position_lng);
            $("#op_assets_addon_op_map_map_zoom").val(attrs.attrs.map_zoom);

            //set checkboxes
            $("#op_assets_addon_op_map_disable_scroolwheel_zoom").attr('checked', (attrs.attrs.disable_scroolwheel_zoom == 'Y')).trigger('change');
            $("#op_assets_addon_op_map_disable_satellite").attr('checked', (attrs.attrs.disable_satellite == 'Y')).trigger('change');
            $("#op_assets_addon_op_map_disable_street_view").attr('checked', (attrs.attrs.disable_street_view == 'Y')).trigger('change');
            $("#op_assets_addon_op_map_disable_zoom_buttons").attr('checked', (attrs.attrs.disable_zoom_buttons == 'Y')).trigger('change');
            $("#op_assets_addon_op_map_disable_map_drag").attr('checked', (attrs.attrs.disable_map_drag == 'Y')).trigger('change');
        },
    }
}(opjq));
