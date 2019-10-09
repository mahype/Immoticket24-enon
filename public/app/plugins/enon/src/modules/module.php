<?php
/**
 * Module base class
 *
 * @package Enon
 * @since 1.0.0
 */

namespace awsmug\Enon\Modules;

use awsmug\Enon\Assets;
use Leaves_And_Love\Plugin_Lib\Service;
use Leaves_And_Love\Plugin_Lib\Traits\Container_Service_Trait;
use Leaves_And_Love\Plugin_Lib\Traits\Hook_Service_Trait;
use Leaves_And_Love\Plugin_Lib\Error_Handler;

/**
 * Base class for a module.
 *
 * @since 1.0.0
 *
 * @method Module_Manager manager()
 */
abstract class Module extends Service {
	use Container_Service_Trait, Hook_Service_Trait;

	/**
	 * The module slug. Must match the slug when registering the module.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $slug = '';

	/**
	 * The module title.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $title = '';

	/**
	 * The module description.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $description = '';

	/**
	 * Logging context for this module.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $logging_context = array();

	/**
	 * The module manager service definition.
	 *
	 * @since 1.0.0
	 * @static
	 * @var string
	 */
	protected static $service_manager = Module_Manager::class;

	/**
	 * Default submodules.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $default_submodules = array();

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $prefix   The instance prefix.
	 * @param array  $services {
	 *     Array of service instances.
	 *
	 *     @type Module_Manager $manager       The module manager instance.
	 *     @type Error_Handler  $error_handler The error handler instance.
	 * }
	 */
	public function __construct( $prefix, $services ) {
		$this->set_prefix( $prefix );
		$this->set_services( $services );

		$this->bootstrap();

		$this->logging_context = array(
			'module' => $this->get_slug(),
		);

		$this->setup_hooks();
	}

	/**
	 * Returns the module slug.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module slug.
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Returns the module title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module title.
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Returns the module description.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module description.
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Checks whether this module is active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the module is active, false otherwise.
	 */
	public function is_active() {
		$options = $this->manager()->options()->get( 'general_settings', array() );
		if ( isset( $options['modules'] ) && is_array( $options['modules'] ) ) {
			return in_array( $this->slug, $options['modules'], true );
		}

		return true;
	}

	/**
	 * Registers the default actions.
	 *
	 * The function also executes a hook that should be used by other developers to register their own actions.
	 *
	 * @since 1.0.0
	 */
	protected function register_defaults() {
		foreach ( $this->default_submodules as $slug => $class_name ) {
			$this->register( $slug, $class_name );
		}

		/**
		 * Fires when the default actions have been registered.
		 *
		 * This action should be used to register custom actions.
		 *
		 * @since 1.0.0
		 *
		 * @param \awsmug\Enon\Modules\Targeting\Module $actions Action manager instance.
		 */
		do_action( "{$this->get_prefix()}register_{$this->get_slug()}", $this );
	}

	/**
	 * Retrieves the value of a specific module option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $option  Name of the option to retrieve.
	 * @param mixed  $default Optional. Value to return if the option doesn't exist. Default false.
	 * @return mixed Value set for the option.
	 */
	public function get_option( $option, $default = false ) {
		$options = $this->get_options();

		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ];
		}

		return $default;
	}

	/**
	 * Retrieves the values of all module options.
	 *
	 * @since 1.0.0
	 *
	 * @return array Option values.
	 */
	public function get_options() {
		return $this->manager()->options()->get( $this->get_settings_identifier(), array() );
	}

	/**
	 * Adds settings subtabs, sections and fields for the module to the plugin settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param Form_Settings_Page $settings_page Settings page instance.
	 */
	final protected function add_settings( $settings_page ) {
		$subtabs = $this->get_settings_subtabs();
		if ( empty( $subtabs ) ) {
			return;
		}

		$sections = $this->get_settings_sections();
		$fields   = $this->get_settings_fields();

		$tab_id = $this->get_settings_identifier();

		$settings_page->add_tab(
			$tab_id,
			array(
				'title'            => $this->get_title(),
				/* translators: %s: module title */
				'rest_description' => sprintf( _x( 'Torro Forms %s module settings.', 'REST API description', 'torro-forms' ), $this->get_title() ),
			)
		);

		foreach ( $subtabs as $slug => $args ) {
			$args['tab'] = $tab_id;

			$settings_page->add_subtab( $slug, $args );
		}

		foreach ( $sections as $slug => $args ) {
			$settings_page->add_section( $slug, $args );
		}

		foreach ( $fields as $slug => $args ) {
			$type = 'text';
			if ( isset( $args['type'] ) ) {
				$type = $args['type'];
				unset( $args['type'] );
			}

			$settings_page->add_field( $slug, $type, $args );
		}
	}

	/**
	 * Returns the settings identifier for the module.
	 *
	 * This identifier must be used to access module options.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module settings identifier.
	 */
	final protected function get_settings_identifier() {
		return 'module_' . $this->slug;
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
				'name'     => 'init',
				'callback' => array( $this, 'register_defaults' ),
				'priority' => 100,
				'num_args' => 1,
			),
			array(
				'name'     => "{$this->get_prefix()}add_settings_content",
				'callback' => array( $this, 'add_settings' ),
				'priority' => 1,
				'num_args' => 1,
			),
			array(
				'name'     => "{$this->get_prefix()}register_assets",
				'callback' => array( $this, 'register_assets' ),
				'priority' => 1,
				'num_args' => 1,
			),
			array(
				'name'     => "{$this->get_prefix()}enqueue_settings_scripts",
				'callback' => array( $this, 'enqueue_settings_assets' ),
				'priority' => 1,
				'num_args' => 3,
			),
		);
	}

	/**
	 * Bootstraps the module by setting properties.
	 *
	 * @since 1.0.0
	 */
	abstract protected function bootstrap();

	/**
	 * Returns the available settings sub-tabs for the module.
	 *
	 * @since 1.0.0
	 *
	 * @return array Associative array of `$subtab_slug => $subtab_args` pairs.
	 */
	abstract protected function get_settings_subtabs();

	/**
	 * Returns the available settings sections for the module.
	 *
	 * @since 1.0.0
	 *
	 * @return array Associative array of `$section_slug => $section_args` pairs.
	 */
	abstract protected function get_settings_sections();

	/**
	 * Returns the available settings fields for the module.
	 *
	 * @since 1.0.0
	 *
	 * @return array Associative array of `$field_slug => $field_args` pairs.
	 */
	abstract protected function get_settings_fields();

	/**
	 * Registers the available module scripts and stylesheets.
	 *
	 * @since 1.0.0
	 *
	 * @param Assets $assets Assets API instance.
	 */
	abstract protected function register_assets( $assets );

	/**
	 * Enqueues the module's settings scripts and stylesheets.
	 *
	 * @since 1.0.0
	 *
	 * @param Assets $assets            Assets API instance.
	 * @param string $current_tab_id    Identifier of the current tab.
	 * @param string $current_subtab_id Identifier of the current sub-tab.
	 */
	abstract protected function enqueue_settings_assets( $assets, $current_tab_id, $current_subtab_id );
}
