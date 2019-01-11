<?php

class OptimizePress_Elements_Map
{

    /**
     * @var string
     */
    protected $tag = 'op_map';

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */    
    protected $api_key = '';

    /**
     * Registering actions and filters
     */
    public function __construct()
    {
        /*
         * Filters
         */
        add_filter('op_assets_after_addons', array($this, 'addToAssetList'));
        add_filter('op_assets_parse_list', array($this, 'addToParseList'));
        add_filter('op_assets_lang_list', array($this, 'addToLangList'));
        add_filter('op_assets_addons_path', array($this, 'elementPath'), 10, 2);
        add_filter('op_assets_addons_url', array($this, 'elementUrl'), 10, 2);
        add_filter('op_cacheable_elements', array($this, 'addToCacheableElementsList'));
        add_filter('op_assets_oppp_element_styles', array($this, 'elementOpppStyles'), 10, 2);

        /**
         * Actions
         */
        $this->getGmapsApiDataFromOpDashboard();
        add_action('op_assets_after_shortcode_init', array($this, 'initShortcodes'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
    }

    /**
     * Add OPPP exclusive element styles to list for style picker to style differently.
     * @param  array $styles
     * @param  string $id
     * @return array
     */
    public function elementOpppStyles($styles, $id)
    {
        if ('op_assets_addon_' . $this->tag . '_style' === $id) {
            $styles = array_merge($styles, array('1'));
        }

        return $styles;
    }

    /**
     * Enqueue scripts
     * @return void
     */
    public function enqueueScripts($attr) 
    { 
        if ($this->checkIfApiKeyIsEmpty()) {
            return;
        }

        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_script(OP_SN . '-google-maps-api', 'https://maps.googleapis.com/maps/api/js?key=' . $this->api_key . '&libraries=places', array(OP_SN . '-noconflict-js'), OPPP_VERSION, 'all');
            wp_enqueue_script(OP_SN . '-google-maps-init', OPPP_BASE_URL . 'js/elements/init_map' . OP_SCRIPT_DEBUG . '.js', array(OP_SN . '-noconflict-js', OP_SN . '-google-maps-api'), OPPP_VERSION, false);
        } else {
            wp_enqueue_script(OP_SN . 'google-maps-api', 'https://maps.googleapis.com/maps/api/js?key=' . $this->api_key . '&libraries=places', array(OP_SN . '-op-jquery-base-all'), OPPP_VERSION, 'all');
        }
     }

    /**
     * Enqueue styles
     * @return void
     */
    public function enqueueStyles()
    {
        wp_enqueue_style('op_map_custom', OPPP_BASE_URL . 'css/elements/op_map' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
    }


    /**
     * Initialize shortcodes (both with op prefix and without it)
     * @return void
     */
    public function initShortcodes()
    {   
        add_shortcode($this->tag, array($this, 'shortcode'));
        add_shortcode('op_' . $this->tag, array($this, 'shortcode'));
    }

    /**
     * Parses guarantee_box and op_guarantee_box shortcode
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function shortcode($atts, $content)
    {
        if ($this->checkIfApiKeyIsEmpty()) {
            return '<div style="height:35px">'.
                        (defined('OP_LIVEEDITOR') ? ' -- It seemes that there is no Google Maps Api key. Please add your Google Maps Api key in OptimizePress Dashboard -- ':'')
                    .'</div>';
        }

        // Element ID used for caching
        $elementId = 'el_map_' . md5(serialize($atts));

        // Cache busting
        if (function_exists('wp_using_ext_object_cache')) {
            $extCache = wp_using_ext_object_cache();
            if (true === $extCache) {
                wp_using_ext_object_cache(false);
            }
        }

        $output = '';

        if (false === $data = get_transient($elementId)) {

            //Decode encoded chars
            $atts = op_urldecode($atts);

            //Initialize variables
            $data = shortcode_atts(array(
                'style' => '',
                'map_position_lat' => '',
                'map_position_lng' => '',
                'map_zoom' => '',
                'map_style' => '',
                'marker_position_lat' => '',
                'marker_position_lng' => '',
                'marker_icon' => '',
                'map_height' => '200',
                'disable_scroolwheel_zoom' => '',
                'disable_satellite' => '',
                'disable_street_view' => '',
                'disable_zoom_buttons' => '',
                'disable_map_drag' => ''
            ),$atts);

            $this->id = $this->tag . '_' . op_generate_id();
            $data['element_id'] = $this->id;

            $data = op_sl_parse('map', $data);
            if (false === is_array($data) && empty($data)) {
                return;
            }

            set_transient($elementId, $data, OP_SL_ELEMENT_CACHE_LIFETIME);
        }

        // Append output if defined
        if (isset($data['markup'])) {
            $output .= $data['markup'];
        }

        // Cache busting
        if (function_exists('wp_using_ext_object_cache')) {
            wp_using_ext_object_cache($extCache);
        }

        return $output;
    }


    /**
     * Adds new element to asset list
     * @param  array $assets
     * @return array
     */
    public function addToAssetList($assets)
    {
        $assets['addon'][$this->tag] = array(
            'title'         => __('Map element', 'optimizepress-plus-pack'),
            'description'   => __('Use this map element to share your business location or to promote an event location.', 'optimizepress-plus-pack'),
            'settings'      => 'Y',
            'image'         => OPPP_BASE_URL . 'images/elements/' . $this->tag . '/' . $this->tag . '.png',
            'base_path'     => OPPP_BASE_URL . 'js/elements/',
        );

        return $assets;
    }

    /**
     * Adds new element to parse list
     * @param  array $assets
     * @return array
     */
    public function addToParseList($assets)
    {
        $assets[$this->tag] = array(
            'asset' => 'addon/' . $this->tag
        );

        return $assets;
    }

    /**
     * Add additional strings for translation
     * @param array $strings
     * @return array
     */
    public function addToLangList($strings)
    {
        $strings['map_container'] = __('Google Map', 'optimizepress-plus-pack');
        $strings['map_marker'] = __('Marker Icon', 'optimizepress-plus-pack');
        $strings['map_height'] = __('Map Height (px)', 'optimizepress-plus-pack');
        $strings['disable_scroolwheel_zoom'] = __('Disable map zoom on scroll wheel', 'optimizepress-plus-pack');
        $strings['disable_satellite'] = __('Disable satellite preview', 'optimizepress-plus-pack');
        $strings['disable_street_view'] = __('Disable street view', 'optimizepress-plus-pack');
        $strings['disable_zoom_buttons'] = __('Disable zoom buttons', 'optimizepress-plus-pack');
        $strings['disable_map_drag'] = __('Disable map draging', 'optimizepress-plus-pack');
        $strings['gmaps_api_key_microcopy'] = __('In order to use this element you need to generate Google Maps API key and save it to OptimizePress Dashboard > Google API key. You can create your API key following this <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">link</a>.', 'optimizepress-plus-pack');
        $strings['gmaps_no_api_key'] = __('In order to use this element you need to provide Google Maps API Key in <a href="/wp-admin/admin.php?page=optimizepress-dashboard" target="_blank">OptimizePress Dashboard</a>', 'optimizepress-plus-pack');
        $strings['op_theme_version'] = __('In order to use this element update OptimizePress Theme to version 2.5.10 or above', 'optimizepress-plus-pack');
        return $strings;
    }

    /**
     * Returns changed images path
     * @param  string $path
     * @param  string $tag
     * @return string
     */
    public function elementPath($path, $tag)
    {
        if ($tag === $this->tag) {
            $path = OPPP_BASE_DIR . 'images/elements/';
        }
        return $path;
    }

    /**
     * Returns changed images URL
     * @param  string $url
     * @param  string $tag
     * @return string
     */
    public function elementUrl($url, $tag)
    {
        if ($tag === $this->tag) {
            $url = OPPP_BASE_URL . 'images/elements/';
        }
        return $url;
    }

    /**
     * Get google maps api key stored in OptimizePress Dashboard
     */
    public function getGmapsApiDataFromOpDashboard() {
        $gmaps_api_data = get_option('optimizepress_gmaps_api_key');

        if ($gmaps_api_data != '') {
            $this->api_key = $gmaps_api_data;
        }
    }

    public function checkIfApiKeyIsEmpty() {
        if ($this->api_key === '') {
            return true;
        }

        return false;
    }

}

new OptimizePress_Elements_Map();
