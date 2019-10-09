<?php
/**
 * Targeting module class
 *
 * @package Enon
 * @since 1.0.0
 */

namespace awsmug\Enon\Modules\Targeting;

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
		$this->slug        = 'targeting';
		$this->title       = __( 'Targeting', 'enon' );
		$this->description = __( 'Targeting functionalities.', 'enon' );

		$this->submodule_base_class = Targeting::class;

		$this->default_submodules   = array(
			'tag_manager' => Tag_Manager::class,
		);
	}
}
