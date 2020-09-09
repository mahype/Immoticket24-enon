<?php

class OptimizePress_Elements_ScrollToTop
{
    /**
     * @var string
     */
    protected $tag = 'op_scroll_to_top';

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
        add_filter('op_assets_oppp_element_styles', array($this, 'elementOpppStyles'), 11, 2);
        add_filter('wp_print_styles', array($this, 'inLiveEditorRenderCSS'));

        /*
         * Actions
         */
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueStyles'));

        add_shortcode($this->tag, array($this, 'shortcode'));
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
            $styles = array_merge($styles, array(1));
        }

        return $styles;
    }

    /**
     * Enqueue styles
     * @return void
     */
    public function enqueueStyles()
    {
        // Different dependencies when OP_SCRIPT_DEBUG is turned on and off (all styles are concatenated into opplus-front-all.css & opplus-back-all for production)
        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_style('oppp-scroll-to-top', OPPP_BASE_URL . 'css/elements/op_scroll_to_top' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }
    }

    /**
     * Enqueue scripts
     * @return void
     */
    public function enqueueScripts()
    {
        // Different dependencies when OP_SCRIPT_DEBUG is turned on and off
        if (OP_SCRIPT_DEBUG === '') {
            wp_enqueue_script('init-op_scroll_to_top-scropt', OPPP_BASE_URL . 'js/elements/op_scroll_to_top' . OP_SCRIPT_DEBUG . '.js', array(OP_SN . '-noconflict-js'), OPPP_VERSION, false);
        }
    }

    /**
     * Parses op_scroll_to_top
     * @param  array $atts
     * @param  string $content
     * @return string
     */
    public function shortcode($atts, $content)
    {
        // Decode encoded chars
        $atts = op_urldecode($atts);

        //Get the attributes from the shortcode
        $data = shortcode_atts(array(
            'style' => 1,
            'color' => '',
            'shape' => '',
            'position_select' => '',
            'icon' => ''
        ), $atts);

        $data['icon'] = OPPP_BASE_URL . '/images/elements/op_scroll_to_top/img/' . urldecode($data['icon']);

        if ($data['shape'] === 'circle') {
            $data['shape'] = '50%';
        } else {
            $data['shape'] = '3px';
        }

        if (defined('OP_LIVEEDITOR')) {
            return '<div id="op-scroll-to-top-live-editor" style="position:absolute;bottom:0;">!!! SCROLL TO TOP ELEMENT !!!</div>';
        }

        //Clear the template
        _op_tpl('clear');

        //Process the new template and load it
        return _op_tpl('_load_file', OPPP_BASE_DIR . 'templates/elements/scroll_to_top/style_' . $data['style'] . '.php',$data,true);
    }

    /**
     * Adds calendar_date CSS in Live Editor
     * @return void
     */
    public function inLiveEditorRenderCSS()
    {
        if (defined('OP_LIVEEDITOR')) {
            //wp_enqueue_style(OP_SN.'-calendar_date', OPPP_BASE_URL . 'css/elements/calendar_date' . OP_SCRIPT_DEBUG . '.css', array(), OPPP_VERSION, 'all');
        }
    }

    /**
     * Adds new element to asset list
     * @param  array $assets
     * @return array
     */
    public function addToAssetList($assets)
    {
        $assets['addon'][$this->tag] = array(
            'title'         => __('Scroll To Top', 'optimizepress-plus-pack'),
            'description'   => __('Use this element to make it easy for your website visitors to scroll back to the top of your webpage.', 'optimizepress-plus-pack'),
            'settings'      => 'Y',
            'image'         => OPPP_BASE_URL . 'images/elements/' . $this->tag . '/' . $this->tag . '.png',
            'base_path'     => OPPP_BASE_URL . 'js/elements/',
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
        $strings['scroll_to_top_color']     = __('Scroll To Top Element Color', 'optimizepress-plus-pack');
        $strings['op_scroll_already_exist'] = __('On this Live Editor page Scroll To Top Element already exist', 'optimizepress-plus-pack');
        $strings['scroll_to_top_shape'] = __('Scroll To Top Shape', 'optimizepress-plus-pack');

        return $strings;
    }

    /**
     * Adds new element to parse list
     * @param  array $assets
     * @return array
     */
    public function addToParseList($assets)
    {
        $assets[$this->tag] = array(
            'asset' => 'addon/' . $this->tag,
        );

        return $assets;
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
}

new OptimizePress_Elements_ScrollToTop();
