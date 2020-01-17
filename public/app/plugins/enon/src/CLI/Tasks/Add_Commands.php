<?php
/**
 * Class for loading CLI commands
 *
 * @category Class
 * @package  Enon\CLI\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\CLI\Tasks;

use Awsm\WP_Wrapper\Building_Plans\Task;
use Enon\CLI\Commands\Scrub_Posts;

/**
 * Class Task_CPT_Reseller.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Tasks\Core
 */
class Add_Commands implements Task {
	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		\WP_CLI::add_command( 'scrub', 'Enon\CLI\Commands\Scrub_Posts' );
	}
}
