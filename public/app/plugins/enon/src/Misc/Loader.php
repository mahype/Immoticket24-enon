<?php
/**
 * Loading misc tasks.
 *
 * @category Class
 * @package  Enon\Misc
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Misc;

use Enon\Misc\Tasks\Task_Dev;
use Enon\Misc\Tasks\Task_Google_Tag_Manager;
use Enon\Misc\Tasks\Task_Remove_Optimizepress;
use Enon\Misc\Tasks\Plugins\Task_Edd_Sparkasse_Discounts;

use Enon\Task_Loader;

/**
 * Mis Script loader.
 *
 * @since 1.0.0
 */
class Loader extends Task_Loader {

	/**
	 * Loading Scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_task( Task_Dev::class );
		$this->add_task( Task_Google_Tag_Manager::class );
		$this->add_task( Task_Remove_Optimizepress::class );
		$this->add_task( Task_Edd_Sparkasse_Discounts::class, $this->logger() );
		$this->run_tasks();
	}
}
