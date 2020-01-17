<?php
/**
 * CLI task loader.
 *
 * @category Class
 * @package  Enon\CLI
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\CLI;

use Enon\Task_Loader;
use Enon\CLI\Tasks\Add_Commands;


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
		if ( ! defined( 'WP_CLI' ) ) {
			return;
		}

		$this->add_task( Add_Commands::class );

		$this->run_tasks();
	}
}

