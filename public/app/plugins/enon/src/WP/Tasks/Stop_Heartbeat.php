<?php
/**
 * Dev tasks.
 *
 * @category Class
 * @package  Enon\WP\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\WP\Tasks;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Task;

/**
 * Class Google_Tag_Manager.
 *
 * @since 1.0.0
 */
class Stop_Heartbeat implements Actions, Task {

	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		if ( ! defined( 'WP_DEBUG' ) && ! WP_DEBUG ) {
			return;
		}

		$this->add_actions();
	}

	/**
	 * Load targeting scripts into hooks.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'init', array( $this, 'stop_heartbeat' ), 1 );
	}

	/**
	 * Stop WordPress hearbeat for better debugging.
	 *
	 * @since 1.0.0
	 */
	public function stop_heartbeat() {
		wp_deregister_script( 'heartbeat' );
	}
}
