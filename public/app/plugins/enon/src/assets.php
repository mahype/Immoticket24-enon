<?php
/**
 * Assets manager class
 *
 * @package Enon
 * @since 1.0.0
 */

namespace awsmug\Enon;

use Leaves_And_Love\Plugin_Lib\Assets as Assets_Base;
use Leaves_And_Love\Plugin_Lib\Traits\Hook_Service_Trait;

/**
 * Class for managing assets.
 *
 * @since 1.0.0
 */
class Assets extends Assets_Base {
	use Hook_Service_Trait;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $prefix The prefix for all AJAX actions.
	 * @param array  $args   {
	 *     Array of arguments.
	 *
	 *     @type callable $path_callback Callback to create a full plugin path from a relative path.
	 *     @type callable $url_callback  Callback to create a full plugin URL from a relative path.
	 * }
	 */
	public function __construct( $prefix, $args ) {
		parent::__construct( $prefix, $args );

		$this->setup_hooks();
	}

	/**
	 * Transforms a relative asset path into a full URL.
	 *
	 * The method also automatically handles loading a minified vs non-minified file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $src Relative asset path.
	 * @return string|bool Full asset URL, or false if the path
	 *                     is requested for a full $src URL.
	 */
	public function get_full_url( $src ) {
		return $this->get_full_path( $src, true );
	}

	/**
	 * Transforms a relative asset path into a full path.
	 *
	 * The method also automatically handles loading a minified vs non-minified file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $src Relative asset path.
	 * @param bool   $url Whether to return the URL instead of the path. Default false.
	 * @return string|bool Full asset path or URL, depending on the $url parameter, or false
	 *                     if the path is requested for a full $src URL.
	 */
	public function get_full_path( $src, $url = false ) {
		if ( preg_match( '/^(http|https):\/\//', $src ) || 0 === strpos( $src, '//' ) ) {
			if ( $url ) {
				return $src;
			}

			return false;
		}

		if ( '.js' !== substr( $src, -3 ) && '.css' !== substr( $src, -4 ) ) {
			if ( $url ) {
				return call_user_func( $this->url_callback, $src );
			}

			return call_user_func( $this->path_callback, $src );
		}

		return parent::get_full_path( $src, $url );
	}

	/**
	 * Renders an SVG icon.
	 *
	 * @since 1.0.0
	 *
	 * @param string $icon_id ID of the SVG icon to use.
	 * @param string $title   Optional. Alternative text for the SVG. If not, the element will be
	 *                        ignored by screen readers. Default empty string.
	 * @param string $class   Optional. Additional CSS class to use on the SVG element. Default
	 *                        empty string.
	 */
	public function render_icon( $icon_id, $title = '', $class = '' ) {
		$aria_hidden     = ' aria-hidden="true"';
		$aria_labelledby = '';

		if ( ! empty( $title ) ) {
			$unique_id = uniqid();

			$aria_hidden     = '';
			$aria_labelledby = ' aria-labelledby="title-' . esc_attr( $unique_id ) . '"';
		}

		?>
		<svg class="enon-icon <?php echo esc_attr( $class ); ?>"<?php echo $aria_hidden . $aria_labelledby; // WPCS: XSS OK. ?> role="img">
			<?php if ( ! empty( $title ) ) : ?>
				<title id="title-<?php echo esc_attr( $unique_id ); ?>"><?php echo esc_html( $title ); ?></title>
			<?php endif; ?>
			<use href="#<?php echo esc_attr( $icon_id ); ?>" xlink:href="#<?php echo esc_attr( $icon_id ); ?>"></use>
		</svg>
		<?php
	}

	/**
	 * Registers all default plugin assets.
	 *
	 * @since 1.0.0
	 */
	protected function register_assets() {
		$this->register_style(
			'frontend',
			'assets/dist/css/frontend.css',
			array(
				'deps' => array(),
				'ver'  => $this->plugin_version,
			)
		);

		$this->register_script(
			'util',
			'assets/dist/js/util.js',
			array(
				'deps'      => array( 'jquery', 'underscore', 'wp-util', 'wp-api' ),
				'ver'       => $this->plugin_version,
				'in_footer' => true,
			)
		);

		/**
		 * Fires after all default plugin assets have been registered.
		 *
		 * Do not use this action to actually enqueue any assets, as it is only
		 * intended for registering them.
		 *
		 * @since 1.0.0
		 *
		 * @param Assets $assets The assets manager instance.
		 */
		do_action( "{$this->get_prefix()}register_assets", $this );
	}

	/**
	 * Enqueues the icons stylesheet.
	 *
	 * @since 1.0.0
	 */
	protected function enqueue_icons() {
		$this->enqueue_style( 'admin-icons' );
	}

	/**
	 * Adds utility CSS classes to the admin body tag.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classes Optional. Admin body classes. Default empty string.
	 * @return string Modified admin body classes.
	 */
	protected function add_admin_utility_body_classes( $classes = '' ) {
		if ( ! empty( $classes ) ) {
			$classes .= ' ';
		}

		$classes .= 'no-clipboard';

		return $classes;
	}

	/**
	 * Prints the SVG icons to the page so that they are available to use.
	 *
	 * @since 1.0.0
	 */
	protected function load_icons() {
		$svg_icons = $this->get_full_path( 'assets/dist/img/icons.svg' );

		if ( file_exists( $svg_icons ) ) {
			require_once $svg_icons;
		}
	}

	/**
	 * Sets up all action and filter hooks for the service.
	 *
	 * This method must be implemented and then be called from the constructor.
	 *
	 * @since 1.0.0
	 */
	protected function setup_hooks() {
		$this->actions = array(
			array(
				'name'     => 'wp_enqueue_scripts',
				'callback' => array( $this, 'register_assets' ),
				'priority' => 1,
				'num_args' => 0,
			),
			array(
				'name'     => 'admin_enqueue_scripts',
				'callback' => array( $this, 'register_assets' ),
				'priority' => 1,
				'num_args' => 0,
			),
			array(
				'name'     => 'admin_enqueue_scripts',
				'callback' => array( $this, 'enqueue_icons' ),
				'priority' => 10,
				'num_args' => 0,
			),
			array(
				'name'     => 'admin_footer',
				'callback' => array( $this, 'load_icons' ),
				'priority' => 10,
				'num_args' => 0,
			),
		);

		$this->filters = array(
			array(
				'name'     => 'admin_body_class',
				'callback' => array( $this, 'add_admin_utility_body_classes' ),
				'priority' => 1,
				'num_args' => 1,
			),
		);
	}

	/**
	 * Parses the plugin version number.
	 *
	 * @since 1.0.0
	 * @static
	 *
	 * @param mixed $value The input value.
	 * @return string The parsed value.
	 */
	protected static function parse_arg_plugin_version( $value ) {
		if ( ! $value ) {
			return false;
		}

		return $value;
	}
}
