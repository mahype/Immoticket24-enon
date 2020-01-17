<?php
/**
 * CLI task loader.
 *
 * @category Class
 * @package  Enon\ACF
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\ACF;

use Enon\Task_Loader;

use Enon\Acf\Tasks\Task_ACF;
use Enon\Acf\Tasks\Task_Settings_ACF;


/**
 * Class Loader.
 *
 * @package Enon\Config
 */
class Loader extends Task_Loader {
	/**
	 * Loading Scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_task( Task_ACF::class, $this->logger() );
		$this->add_task( Task_Settings_ACF::class );

		$this->run_tasks();
	}
}

