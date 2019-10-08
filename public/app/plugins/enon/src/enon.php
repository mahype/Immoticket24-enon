<?php
/**
 * Plugin main class
 *
 * @package Enon
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main class for Enon.
 *
 * Takes care of initializing the plugin.
 *
 * This file must always be parseable by PHP 5.2.
 *
 * @since 1.0.0
 *
 * @method awsmug\Enon\Modules\Module_Manager modules()
 * @method Leaves_And_Love\Plugin_Lib\Options options()
 * @method Leaves_And_Love\Plugin_Lib\Meta    meta()
 * @method awsmug\Enon\Assets                 assets()
 */
class Enon extends Leaves_And_Love_Plugin {

	/**
	 * The Assets manager instance.
	 *
	 * @since 1.0.0
	 * @var awsmug\Enon\Assets
	 */
	protected $assets;

	/**
	 * The error handler instance.
	 *
	 * @since 1.0.0
	 * @var awsmug\Enon\Error_Handler
	 */
	protected $error_handler;

	/**
	 * The Option API instance.
	 *
	 * @since 1.0.0
	 * @var Leaves_And_Love\Plugin_Lib\Options
	 */
	protected $options;

	/**
	 * The module manager instance.
	 *
	 * @since 1.0.0
	 * @var awsmug\Enon\Modules\Module_Manager
	 */
	protected $modules;

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
		$this->minimum_php  = '7.1';
		$this->minimum_wp   = '5.2';
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
		if ( ! class_exists( 'awsmug\Enon\Error_Handler' ) ) {
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
		$this->instantiate_core_services();
		$this->instantiate_core_classes();

		$this->instantiate_modules();
	}

	/**
	 * Instantiates the plugin core services.
	 *
	 * @since 1.0.0
	 */
	protected function instantiate_core_services() {
		$this->error_handler = $this->instantiate_plugin_service( 'Error_Handler', $this->prefix, $this->instantiate_plugin_class( 'Translations\Translations_Error_Handler' ) );
		$this->options = $this->instantiate_library_service( 'Options', $this->prefix );
	}

	/**
	 * Instantiates the plugin core services.
	 *
	 * @since 1.0.0
	 */
	protected function instantiate_core_classes() {
	}

	/**
	 * Instantiates the module manager.
	 *
	 * @since 1.0.0
	 */
	protected function instantiate_modules() {
		$this->modules = $this->instantiate_plugin_service(
			'Modules\Module_Manager',
			$this->prefix,
			array(
				'options'               => $this->options,
				'assets'                => $this->assets,
				'error_handler'         => $this->error_handler,
			)
		);
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
