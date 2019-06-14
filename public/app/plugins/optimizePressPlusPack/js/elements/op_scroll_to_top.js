var op_asset_settings = (function($) {
    return {
        attributes: {
            step_1: {
                style: {
                    type: 'style-selector',
                    folder: 'previews',
                    addClass: 'op-disable-selected'
                },
                op_scroll_already_exist: {
                    addClass: 'op-scroll-top-top-exist op-scroll-top-top-exist--hidden',
                    type: 'microcopy',
                    text: 'op_scroll_already_exist'
                }
            },
            step_2: {
                hidden: {
                    text: 'scroll_to_top_microcopy_hidden',
                    type: 'hidden',
                    events: {
                        change: function() {
                            var script = $('#op-scroll-to-top-live-editor');
                            
                            if (script.length > 0) {
                                $('.micro-copy.op-scroll-top-top-exist').removeClass('op-scroll-top-top-exist--hidden');
                                $('#op_assets_addon_op_scroll_to_top_style_container').hide();

                                return;
                            } else {
                                $('.micro-copy.op-scroll-top-top-exist').addClass('op-scroll-top-top-exist--hidden');
                                $('#op_assets_addon_op_scroll_to_top_style_container').show();
                            }
                        }
                    }
                },
                shape: {
                    title: 'scroll_to_top_shape',
                    type: 'select',
                    values: { 'square': 'Square', 'circle': 'Circle' },
                    default_value: 'circle'
                },
                color: {
                    title: 'scroll_to_top_color',
                    type: 'color',
                    default_value: '#00b7e2',
                },
                icon: {
                    title: 'small_icon',
                    type: 'image-selector',
                    folder: "img",
                    selectorClass: 'scroll_to_top-icon-container',
                    default_value: 'chevron-up-light.svg'
                },
            }
        },
        insert_steps: {
            2: true
        }
    };
}(opjq));
