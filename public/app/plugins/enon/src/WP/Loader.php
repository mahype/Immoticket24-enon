<?php
/**
 * Whitelabel loader.
 *
 * @category Class
 * @package  Enon\WP
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\WP;

use Enon\Task_Loader;
use Enon\Models\Exceptions\Exception;

use Enon\WP\Tasks\Add_CPT_Reseller;
use Enon\WP\Tasks\Add_Admin_Pages;
use Enon\WP\Tasks\Setup_Gutenberg;
use Enon\WP\Tasks\Setup_WP_Nav;
use Enon\WP\Tasks\Stop_Heartbeat;

/**
 * Class loader.
 *
 * @package Enon\WP
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
		$this->add_task( Add_CPT_Reseller::class );

		if ( is_admin() ) {
			$this->add_admin_tasks();
		} else {
			$this->add_frontend_tasks();
		}

		$this->run_tasks();
	}

	/**
	 * Running admin tasks.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_tasks() {
		$this->add_task( Add_Admin_Pages::class, $this->logger() );
		$this->add_task( Setup_Gutenberg::class );
		$this->add_task( Stop_Heartbeat::class );
	}

	/**
	 * Running frontend tasks.
	 *
	 * @since 1.0.0
	 */
	public function add_frontend_tasks() {
		$this->add_task( Setup_WP_Nav::class );
	}
}

