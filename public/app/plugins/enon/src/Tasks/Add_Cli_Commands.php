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

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Task;
use Enon\Models\Cli\Scrub_Posts;

/**
 * Class Task_CPT_Reseller.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Tasks\Core
 */
class Add_Cli_Commands implements Task {
	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		if ( ! defined( 'WP_CLI' ) ) {
			return;
		}

		\WP_CLI::add_command( 'scrub', 'Enon\Models\Cli\Scrub_Posts' );
		\WP_CLI::add_command( 'dibt', 'Enon\Models\Cli\DIBT' );
	}
}
