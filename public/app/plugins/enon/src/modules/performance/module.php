<?php
/**
 * Performance module class
 *
 * @package Enon
 * @since 1.0.0
 */

namespace awsmug\Enon\Modules\Performance;

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
		$this->slug        = 'performance';
		$this->title       = __( 'Performance', 'enon' );
		$this->description = __( 'Performance functionalities.', 'enon' );

		$this->submodule_base_class = Performance::class;

		$this->default_submodules   = array(
			'performance' => Optimize_Press::class,
		);
	}
}
