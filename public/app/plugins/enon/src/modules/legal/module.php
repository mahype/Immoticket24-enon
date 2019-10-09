<?php
/**
 * Actions module class
 *
 * @package TorroForms
 * @since 1.0.0
 */

namespace awsmug\Enon\Modules\Actions;

use awsmug\Enon\Modules\Legal\Cookie_Consent;
use awsmug\Enon\Modules\Legal\Legal;
use awsmug\Enon\Modules\Module as Module_Base;
use awsmug\Enon\Modules\Submodule_Registry_Interface;
use awsmug\Enon\Modules\Submodule_Registry_Trait;

/**
 * Class for the Actions module.
 *
 * @since 1.0.0
 */
class Module extends Module_Base implements Submodule_Registry_Interface {
	use Submodule_Registry_Trait;

	/**
	 * Bootstraps the module by setting properties.
	 *
	 * @since 1.0.0
	 */
	protected function bootstrap() {
		$this->slug        = 'legal';
		$this->title       = __( 'Legal', 'torro-forms' );
		$this->description = __( 'Legal Module.', 'torro-forms' );

		$this->submodule_base_class = Legal::class;

		$this->default_submodules   = array(
			'cookie_consent' => Cookie_Consent::class,
		);
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
		do_action( "{$this->get_prefix()}register_legal", $this );
	}

	/**
	 * Sets up all action and filter hooks for the service.
	 *
	 * @since 1.0.0
	 */
	protected function setup_hooks() {
		parent::setup_hooks();

		$this->actions[] = array(
			'name'     => 'init',
			'callback' => array( $this, 'register_defaults' ),
			'priority' => 100,
			'num_args' => 0,
		);
	}
}
