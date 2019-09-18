<?php
/**
 * Plugin main class
 *
 * @package Enon
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main class for Torro Forms.
 *
 * Takes care of initializing the plugin.
 *
 * This file must always be parseable by PHP 5.2.
 *
 * @since 1.0.0
 *
 * @method Enon\DB_Objects\Forms\Form_Manager                         forms()
 */
class Enon extends Leaves_And_Love_Plugin {

	/**
	 * The Tools manager instance.
	 *
	 * @since 1.0.0
	 * @var Enon\Tools
	 */
	protected $tools;
	protected $targeting_google;
	protected $targeting_bing;
	protected $targeting_performance;

	/**
	 * The database instance.
	 *
	 * @since 1.0.0
	 * @var Enon\DB
	 */
	protected $db;

	/**
	 * The Metadata API instance.
	 *
	 * @since 1.0.0
	 * @var Leaves_And_Love\Plugin_Lib\Meta
	 */
	protected $meta;

	/**
	 * The Assets manager instance.
	 *
	 * @since 1.0.0
	 * @var Enon\Assets
	 */
	protected $assets;

	/**
	 * The Template instance.
	 *
	 * @since 1.0.0
	 * @var Leaves_And_Love\Plugin_Lib\Template
	 */
	protected $template;

	/**
	 * The AJAX handler instance.
	 *
	 * @since 1.0.0
	 * @var Leaves_And_Love\Plugin_Lib\AJAX
	 */
	protected $ajax;

	/**
	 * The error handler instance.
	 *
	 * @since 1.0.0
	 * @var Enon\Error_Handler
	 */
	protected $error_handler;

	/**
	 * The plugin's logger instance.
	 *
	 * @since 1.0.0
	 * @var Psr\Log\LoggerInterface
	 */
	protected $logger;

	/**
	 * Returns the current version of Torro Forms.
	 *
	 * @since 1.0.0
	 *
	 * @return string Version number.
	 */
	public function version() {
		return $this->version;
	}

	/**
	 * Returns the plugin's logger instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Psr\Log\LoggerInterface The logger instance.
	 */
	public function logger() {
		if ( ! $this->logger ) {

			/**
			 * Filters initializing the plugin's logger instance.
			 *
			 * An implementation of Psr\Log\LoggerInterface may be returned to use
			 * that instead of the regular logger, which simply uses the typical
			 * PHP error handler controlled by WordPress.
			 *
			 * @since 1.0.0
			 *
			 * @param Psr\Log\LoggerInterface|null Logger instance to use, or null to not override (default).
			 */
			$logger = apply_filters( "{$this->prefix}set_logger", null );
			if ( $logger && is_a( $logger, 'Psr\Log\LoggerInterface' ) ) {
				$this->logger = $logger;
			} else {
				$this->logger = $this->instantiate_plugin_class( 'Logger' );
			}
		}

		return $this->logger;
	}

	/**
	 * Loads the base properties of the class.
	 *
	 * @since 1.0.0
	 */
	protected function load_base_properties() {
		$this->version      = '1.0.0';
		$this->prefix       = 'enon_';
		$this->vendor_name  = 'awsmug';
		$this->project_name = 'Enon';
		$this->minimum_php  = '5.6';
		$this->minimum_wp   = '4.8';
	}

	/**
	 * Loads the plugin's textdomain.
	 *
	 * @since 1.0.0
	 */
	protected function load_textdomain() {
		/** This filter is documented in wp-includes/l10n.php */
		$locale = apply_filters( 'plugin_locale', get_locale(), 'enon' );

		$mofile = WP_LANG_DIR . '/plugins/enon/enon-' . $locale . '.mo';
		if ( file_exists( $mofile ) ) {
			return load_textdomain( 'enon', $mofile );
		}

		$this->load_plugin_textdomain( 'enon' );
	}

	/**
	 * Loads the class messages.
	 *
	 * @since 1.0.0
	 */
	protected function load_messages() {
		$this->messages['cheatin_huh'] = __( 'Cheatin&#8217; huh?', 'enon' );

		/* translators: %s: PHP version number */
		$this->messages['outdated_php'] = __( 'Enon cannot be initialized because your setup uses a PHP version older than %s.', 'enon' );

		/* translators: %s: WordPress version number */
		$this->messages['outdated_wp'] = __( 'Enon cannot be initialized because your setup uses a WordPress version older than %s.', 'enon' );
	}

	/**
	 * Checks whether the dependencies have been loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the dependencies are loaded, false otherwise.
	 */
	protected function dependencies_loaded() {
		if ( ! interface_exists( 'Psr\Log\LoggerInterface' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Instantiates the plugin services.
	 *
	 * @since 1.0.0
	 */
	protected function instantiate_services() {
		$this->instantiate_core_classes();
	}

	/**
	 * Instantiates the plugin core services.
	 *
	 * @since 1.0.0
	 */
	protected function instantiate_core_classes() {
		if ( isset( $_REQUEST['iframe'] ) || 'true' === $_REQUEST['iframe'] ) {
			return;
		}
		$this->targeting_bing = $this->instantiate_plugin_class('Tools\Google_Tag_Manager' );
		$this->targeting_performance = $this->instantiate_plugin_class('Tools\Performance' );
	}

	/**
	 * Adds the necessary plugin hooks.
	 *
	 * @since 1.0.0
	 */
	protected function add_hooks() {
		$this->add_core_service_hooks();
	}

	/**
	 * Adds the necessary plugin core service hooks.
	 *
	 * @since 1.0.0
	 */
	protected function add_core_service_hooks() {
		// $this->tools->add_hooks();
	}
}
