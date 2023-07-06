<?php
/**
 * Adds support to affwp JS namespace.
 *
 * @package     AffiliateWP
 * @subpackage  Core
 * @copyright   Copyright (c) 2023, Awesome Motive, Inc
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.15.0
 */

namespace AffiliateWP;

/**
 * Scripts class.
 *
 * @since 2.15.0
 */
final class Scripts {

	/**
	 * The script namespace.
	 *
	 * @since 2.15.0
	 * @access private
	 * @var string
	 */
	private string $namespace = 'affiliatewp';

	/**
	 * The JS folder path.
	 *
	 * @since 2.15.0
	 * @access private
	 * @var string
	 */
	private string $path = '';

	/**
	 * Script suffix, can be `.min` or empty string.
	 *
	 * @since 2.15.0
	 * @access private
	 * @var string
	 */
	private string $suffix = '';

	/**
	 * The file version.
	 *
	 * @since 2.15.0
	 * @access private
	 * @var string
	 */
	private string $version = '';

	/**
	 * Initialize props and hooks.
	 *
	 * @since 2.15.0
	 */
	public function __construct() {

		// Set default properties.
		$this->path    = sprintf( '%sassets/js/', AFFILIATEWP_PLUGIN_URL );
		$this->suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$this->version = AFFILIATEWP_VERSION;

		// Set all hooks.
		$this->hooks();
	}

	/**
	 * Register all hooks.
	 *
	 * @since 2.15.0
	 *
	 * @return void
	 */
	private function hooks() : void {

		// It should hook in a priority greater than 10, so our filter affwp_extend_js_vars can run properly.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_namespace' ), 100 );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_namespace' ), 100 );

		// Register and enqueue other scripts, extending our global.
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
	}

	/**
	 * Register and enqueue all necessary styles and scripts.
	 *
	 * Scripts registered here will live under our namespace.
	 *
	 * @since 2.15.0
	 *
	 * @return void
	 */
	public function load_scripts() : void {

		// Restrict to affiliate area and admin only.
		if ( ! ( affwp_is_affiliate_area() || affwp_is_admin_page() ) ) {
			return; // Do not enqueue.
		}

		// Register our modal dependencies.
		wp_register_style( 'fancybox-css', "{$this->path}vendor/fancybox/fancybox.css", array(), $this->version );
		wp_register_script( 'fancybox', "{$this->path}vendor/fancybox/fancybox.umd.js", array(), $this->version, true );

		// Enqueue the modal script `affiliatewp.modal`.
		$this->enqueue( 'modal', array( 'fancybox', 'fancybox-css' ) );

	}

	/**
	 * Register the namespace.
	 *
	 * @since 2.15.0
	 *
	 * @return void
	 */
	public function register_namespace() : void {

		wp_register_script(
			$this->namespace,
			"{$this->path}{$this->namespace}{$this->suffix}.js",
			array(),
			$this->version,
			true
		);

	}

	/**
	 * Handle script enqueuing, extending it into our namespace.
	 *
	 * Use this method instead of normal wp_enqueue_script function to extend our global object.
	 * It handles automatically script dependencies, and it can be also used to pass default settings to
	 * the new object through the namespace API.
	 *
	 * @since 2.15.0
	 *
	 * @param string $script_name The name of te script, without the namespace prefix.
	 * @param array  $dependencies Additional dependencies. Can be both scripts or styles.
	 * @param string $src Optional file source. Overrides the default source path.
	 * @return void
	 */
	public function enqueue( string $script_name, array $dependencies = array(), string $src = '' ) : void {

		// Scripts within our namespace will always have the namespace added as prefix automatically.
		$handle = sprintf(
			'%s-%s',
			$this->namespace,
			ltrim( $script_name, "{$this->namespace}-" )
		);

		// Prevent duplicated dependencies.
		$dependencies = array_unique( $dependencies );

		// Check for styles dependencies, enqueue if find any and remove from the dependencies array.
		foreach ( $dependencies as $k => $dependency ) {
			if ( wp_style_is( $dependency, 'registered' ) ) {
				wp_enqueue_style( $dependency );
				unset( $dependencies[ $k ] );
			}
		}

		// Enqueue the script.
		wp_enqueue_script(
			$handle,
			empty( $src )
				? "{$this->path}{$handle}{$this->suffix}.js"
				: $src,
			array_merge(
				array( $this->namespace ),
				$dependencies
			),
			$this->version,
			true
		);

	}

}
