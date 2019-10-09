<?php
/**
 * Module manager class
 *
 * @package Enon
 * @since 1.0.0
 */

namespace awsmug\Enon\Modules;

use Leaves_And_Love\Plugin_Lib\Service;
use Leaves_And_Love\Plugin_Lib\Traits\Container_Service_Trait;
use Leaves_And_Love\Plugin_Lib\Options;
use Leaves_And_Love\Plugin_Lib\Assets;
use Leaves_And_Love\Plugin_Lib\Error_Handler;

use awsmug\Enon\Error;
use awsmug\Enon\Modules\Legal\Module as Legal_Module;
use awsmug\Enon\Modules\Performance\Module as Performance_Module;
use awsmug\Enon\Modules\Targeting\Module as Targeting_Module;

use Psr\Log\LoggerInterface;

/**
 * Class for managing modules.
 *
 * @since 1.0.0
 *
 * @method Options                      options()
 * @method Assets                       assets()
 * @method LoggerInterface              logger()
 */
class Module_Manager extends Service {
	use Container_Service_Trait {
		Container_Service_Trait::__call as private __callService;
	}

	/**
	 * Registered modules.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $modules = array();

	/**
	 * Default modules definition.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $default_modules = array();

	/**
	 * Whether the hooks for the service have been added.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $hooks_added = false;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $prefix   The instance prefix.
	 * @param array  $services {
	 *     Array of service instances.
	 *
	 *     @type Options                      $options               The Option API instance.
	 *     @type Assets                       $assets                The Assets API instance.
	 *     @type Error_Handler                $error_handler         The error handler instance.
	 * }
	 */
	public function __construct( $prefix, $services ) {
		$this->set_prefix( $prefix );
		$this->set_services( $services );

		$this->default_modules = array(
			'legal'            => Legal_Module::class,
			'performance'      => Performance_Module::class,
			'targeting'        => Targeting_Module::class,
		);

		foreach ( $this->default_modules as $slug => $module_class_name ) {
			$this->register( $slug, $module_class_name );
		}
	}

	/**
	 * Magic call method.
	 *
	 * Supports retrieval of a module or an internally used service.
	 *
	 * @since 1.0.0
	 *
	 * @param string $method_name Method name. Should be the name of a service.
	 * @param array  $args        Method arguments. Unused here.
	 * @return Module|Service|null The module, service instance, or null
	 *                                                                                   if neither exist.
	 */
	public function __call( $method_name, $args ) {

		if ( 'logger' === $method_name ) {
			return enon()->logger();
		}

		if ( isset( $this->modules[ $method_name ] ) ) {
			return $this->modules[ $method_name ];
		}

		return $this->__callService( $method_name, $args );
	}

	/**
	 * Returns a specific registered module.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Module slug.
	 * @return Module|Error Module instance, or error object if module is not registered.
	 */
	public function get( $slug ) {
		if ( ! isset( $this->modules[ $slug ] ) ) {
			/* translators: %s: module slug */
			return new Error( $this->get_prefix() . 'module_not_exist', sprintf( __( 'A module with the slug %s does not exist.', 'enon' ), $slug ), __METHOD__, '1.0.0' );
		}

		return $this->modules[ $slug ];
	}

	/**
	 * Returns all registered modules.
	 *
	 * @since 1.0.0
	 *
	 * @return array Associative array of `$slug => $module_instance` pairs.
	 */
	public function get_all() {
		return $this->modules;
	}

	/**
	 * Returns all active modules.
	 *
	 * @since 1.0.0
	 *
	 * @return array Associative array of `$slug => $module_instance` pairs.
	 */
	public function get_all_active() {
		$options = $this->options()->get( 'general_settings', array() );
		if ( isset( $options['modules'] ) && is_array( $options['modules'] ) ) {
			return array_intersect_key( $this->modules, array_flip( $options['modules'] ) );
		}

		return $this->modules;
	}

	/**
	 * Registers a new module.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug              Module slug.
	 * @param string $module_class_name Module class name.
	 * @return bool|Error True on success, error object on failure.
	 */
	public function register( $slug, $module_class_name ) {
		if ( isset( $this->modules[ $slug ] ) ) {
			/* translators: %s: module slug */
			return new Error( $this->get_prefix() . 'module_already_exist', sprintf( __( 'A module with the slug %s already exists.', 'enon' ), $slug ), __METHOD__, '1.0.0' );
		}

		if ( ! class_exists( $module_class_name ) ) {
			/* translators: %s: module class name */
			return new Error( $this->get_prefix() . 'module_class_not_exist', sprintf( __( 'The class %s does not exist.', 'enon' ), $module_class_name ), __METHOD__, '1.0.0' );
		}

		if ( ! is_subclass_of( $module_class_name, Module::class ) ) {
			/* translators: %s: module class name */
			return new Error( $this->get_prefix() . 'module_class_not_allowed', sprintf( __( 'The class %s is not allowed for a module.', 'enon' ), $module_class_name ), __METHOD__, '1.0.0' );
		}

		$this->modules[ $slug ] = new $module_class_name(
			$this->get_prefix(),
			array(
				'manager'       => $this,
				'error_handler' => $this->error_handler(),
			)
		);

		return true;
	}

	/**
	 * Unregisters a new module.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Module slug.
	 * @return bool|Error True on success, error object on failure.
	 */
	public function unregister( $slug ) {
		if ( ! isset( $this->modules[ $slug ] ) ) {
			/* translators: %s: module slug */
			return new Error( $this->get_prefix() . 'module_not_exist', sprintf( __( 'A module with the slug %s does not exist.', 'enon' ), $slug ), __METHOD__, '1.0.0' );
		}

		if ( isset( $this->default_modules[ $slug ] ) ) {
			/* translators: %s: module slug */
			return new Error( $this->get_prefix() . 'module_is_default', sprintf( __( 'The default module %s cannot be unregistered.', 'enon' ), $slug ), __METHOD__, '1.0.0' );
		}

		unset( $this->modules[ $slug ] );

		return true;
	}

	/**
	 * Adds the service hooks.
	 *
	 * @since 1.0.0
	 */
	public function add_hooks() {
		if ( $this->hooks_added ) {
			return false;
		}

		foreach ( $this->get_all_active() as $module ) {
			$module->add_hooks();
		}

		$this->hooks_added = true;

		return true;
	}

	/**
	 * Removes the service hooks.
	 *
	 * @since 1.0.0
	 */
	public function remove_hooks() {
		if ( ! $this->hooks_added ) {
			return false;
		}

		foreach ( $this->get_all_active() as $module ) {
			$module->remove_hooks();
		}

		$this->hooks_added = false;

		return true;
	}
}
