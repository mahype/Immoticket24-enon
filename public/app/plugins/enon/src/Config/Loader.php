<?php
/**
 * Loading Config tasks.
 *
 * @category Class
 * @package  Enon\Config
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Config;

use Enon\Config\Tasks\Task_Gutenberg;
use Enon\Config\Tasks\Task_Menu;

use Enon\Task_Loader;

/**
 * Config loader.
 *
 * @since 1.0.0
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
		 $this->add_task( Task_Gutenberg::class );
		$this->add_task( Task_Menu::class );
		$this->run_tasks();
		;
	}
}
