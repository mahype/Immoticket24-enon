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
		$this->instantiate_core_services();
		$this->instantiate_db_object_managers();
		$this->setup_admin_pages();
	}

	/**
	 * Instantiates the plugin core services.
	 *
	 * @since 1.0.0
	 */
	protected function instantiate_core_services() {
		$this->error_handler = $this->instantiate_plugin_service( 'Error_Handler', $this->prefix, $this->instantiate_plugin_class( 'Translations\Translations_Error_Handler' ) );

		$this->db = $this->instantiate_plugin_service(
			'DB',
			$this->prefix,
			array(
				'options'       => $this->options,
				'error_handler' => $this->error_handler,
			),
			$this->instantiate_plugin_class( 'Translations\Translations_DB' )
		);

		$this->meta = $this->instantiate_library_service(
			'Meta',
			$this->prefix,
			array(
				'db'            => $this->db,
				'error_handler' => $this->error_handler,
			)
		);

		$this->assets = $this->instantiate_plugin_service(
			'Assets',
			$this->prefix,
			array(
				'path_callback'  => array( $this, 'path' ),
				'url_callback'   => array( $this, 'url' ),
				'plugin_version' => $this->version,
			)
		);

		$this->template = $this->instantiate_library_service(
			'Template',
			$this->prefix,
			array(
				'default_location' => $this->path( 'templates/' ),
			)
		);

		$this->ajax = $this->instantiate_library_service( 'AJAX', $this->prefix, $this->instantiate_plugin_class( 'Translations\Translations_AJAX' ) );

		$this->apiapi_config = $this->instantiate_plugin_class( 'APIAPI_Config', $this->prefix );
	}

	/**
	 * Instantiates the plugin DB object managers.
	 *
	 * @since 1.0.0
	 */
	protected function instantiate_db_object_managers() {
		$this->submission_values = $this->instantiate_plugin_service(
			'DB_Objects\Submission_Values\Submission_Value_Manager',
			$this->prefix,
			array(
				'capabilities'  => $this->instantiate_plugin_service( 'DB_Objects\Submission_Values\Submission_Value_Capabilities', $this->prefix ),
				'db'            => $this->db,
				'cache'         => $this->cache,
				'error_handler' => $this->error_handler,
			),
			$this->instantiate_plugin_class( 'Translations\Translations_Submission_Value_Manager' )
		);

		$this->db->set_version( 20180125 );
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
				'tools'               => $this->tools,
				'error_handler'         => $this->error_handler,
			)
		);
	}

	/**
	 * Instantiates the plugin component services.
	 *
	 * @since 1.0.0
	 */
	protected function instantiate_component_services() {
		$this->admin_pages = $this->instantiate_library_service(
			'Components\Admin_Pages',
			$this->prefix,
			array(
				'ajax'          => $this->ajax,
				'assets'        => $this->assets,
				'error_handler' => $this->error_handler,
			)
		);

		$this->extensions = $this->instantiate_plugin_service( 'Components\Extensions', $this->prefix, $this->instantiate_plugin_class( 'Translations\Translations_Extensions' ) );
		$this->extensions->set_plugin( $this );

		$this->template_tag_handlers = $this->instantiate_plugin_service( 'Components\Template_Tag_Handler_Manager', $this->prefix );

		$this->post_types = $this->instantiate_plugin_service(
			'DB_Objects\Post_Type_Manager',
			$this->prefix,
			array(
				'options'       => $this->options,
				'error_handler' => $this->error_handler,
			)
		);

		$this->taxonomies = $this->instantiate_plugin_service(
			'DB_Objects\Taxonomy_Manager',
			$this->prefix,
			array(
				'options'       => $this->options,
				'error_handler' => $this->error_handler,
			)
		);

		$this->form_uploads = $this->instantiate_plugin_service(
			'Components\Form_Upload_Manager',
			$this->prefix,
			array(
				'taxonomies'    => $this->taxonomies,
				'error_handler' => $this->error_handler,
			)
		);
	}

	/**
	 * Sets up capabilities for the plugin DB object managers.
	 *
	 * @since 1.0.0
	 */
	protected function setup_capabilities() {
		// Map form and its component capabilities to post capabilities.
		$this->forms->capabilities()->map_capabilities( 'posts' );

	}

	/**
	 * Connects the plugin DB object managers through hierarchical relationships.
	 *
	 * @since 1.0.0
	 */
	protected function connect_db_object_managers() {
		$this->forms->add_child_manager( 'form_categories', $this->form_categories );
		$this->forms->add_child_manager( 'containers', $this->containers );
		$this->forms->add_child_manager( 'submissions', $this->submissions );

		$this->form_categories->add_parent_manager( 'forms', $this->forms );

		$this->containers->add_parent_manager( 'forms', $this->forms );
		$this->containers->add_child_manager( 'elements', $this->elements );

		$this->elements->add_parent_manager( 'containers', $this->containers );
		$this->elements->add_child_manager( 'element_choices', $this->element_choices );
		$this->elements->add_child_manager( 'element_settings', $this->element_settings );

		$this->element_choices->add_parent_manager( 'elements', $this->elements );

		$this->element_settings->add_parent_manager( 'elements', $this->elements );

		$this->submissions->add_parent_manager( 'forms', $this->forms );
		$this->submissions->add_child_manager( 'submission_values', $this->submission_values );

		$this->submission_values->add_parent_manager( 'submissions', $this->submissions );
	}

	/**
	 * Sets up the admin pages.
	 *
	 * @since 1.0.0
	 */
	protected function setup_admin_pages() {
		if ( ! is_admin() ) {
			return;
		}

		$form_settings_class_name = 'Enon\DB_Objects\Forms\Form_Settings_Page';
		$form_settings_page       = new $form_settings_class_name( $this->admin_pages->get_prefix() . 'form_settings', $this->admin_pages, $this->forms );

		$this->admin_pages->add( 'form_settings', $form_settings_page, 'edit.php?post_type=torro_form', null, 'site' );
	}

	/**
	 * Adds the necessary plugin hooks.
	 *
	 * @since 1.0.0
	 */
	protected function add_hooks() {
		$this->add_core_service_hooks();
		$this->add_db_object_manager_hooks();
		$this->add_module_hooks();
		$this->add_component_service_hooks();
	}

	/**
	 * Adds the necessary plugin core service hooks.
	 *
	 * @since 1.0.0
	 */
	protected function add_core_service_hooks() {
		$this->options->add_hooks();
		$this->db->add_hooks();
		$this->assets->add_hooks();
		$this->ajax->add_hooks();
		$this->apiapi_config->add_hooks();
	}

	/**
	 * Adds the necessary plugin DB object manager hooks.
	 *
	 * @since 1.0.0
	 */
	protected function add_db_object_manager_hooks() {
		$this->forms->add_hooks();
		$this->form_categories->add_hooks();
		$this->containers->add_hooks();
		$this->elements->add_hooks();
		$this->element_choices->add_hooks();
		$this->element_settings->add_hooks();
		$this->submissions->add_hooks();
		$this->submission_values->add_hooks();

		$this->forms->capabilities()->add_hooks();
		$this->form_categories->capabilities()->add_hooks();
		$this->containers->capabilities()->add_hooks();
		$this->elements->capabilities()->add_hooks();
		$this->element_choices->capabilities()->add_hooks();
		$this->element_settings->capabilities()->add_hooks();
		$this->submissions->capabilities()->add_hooks();
		$this->submission_values->capabilities()->add_hooks();

		$this->post_types->add_hooks();
		$this->taxonomies->add_hooks();
	}

	/**
	 * Adds the necessary module hooks.
	 *
	 * @since 1.0.0
	 */
	protected function add_module_hooks() {
		$this->modules->add_hooks();
	}

	/**
	 * Adds the necessary plugin component service hooks.
	 *
	 * @since 1.0.0
	 */
	protected function add_component_service_hooks() {
		$this->admin_pages->add_hooks();
		$this->extensions->add_hooks();
	}
}
