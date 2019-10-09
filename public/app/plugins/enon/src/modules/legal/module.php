<?php
/**
 * Actions module class
 *
 * @package TorroForms
 * @since 1.0.0
 */

namespace awsmug\Enon\Modules\Legal;
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
		$this->description = __( 'Legal functionalities.', 'torro-forms' );

		$this->submodule_base_class = Legal::class;

		$this->default_submodules   = array(
			'cookie_consent' => Cookie_Consent::class,
		);
	}
}
