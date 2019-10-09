<?php
/**
 * Legal base class
 *
 * @package Enon
 * @since 1.0.0
 */

namespace awsmug\Enon\Modules\Legal;

use awsmug\Enon\Modules\Submodule;
use awsmug\Enon\Modules\Settings_Submodule_Interface;
use awsmug\Enon\Modules\Settings_Submodule_Trait;

/**
 * Base class for an Targeting submodule.
 *
 * @since 1.0.0
 */
abstract class Legal extends Submodule implements Settings_Submodule_Interface {
	use Settings_Submodule_Trait;
}
