<?php
/**
 * Class for loading reseller tasks in admin.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Admin
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Admin;

use Awsm\WP_Wrapper\Interfaces\Task;
use Enon\Task_Loader;

/**
 * Admin scripts loader.
 *
 * @package Enon\Config
 *
 * @since 1.0.0
 */
class Loader extends Task_Loader implements Task {
	/**
	 * Loading Scripts
	 *
	 * @since 1.0.0
	 */
	public function run() {
		if ( ! is_admin() || wp_doing_ajax() ) {
			return;
		}

		if ( ! current_user_can( 'view_reseller_leads' ) ) {
			return;
		}

		$this->add_task( Config_Dashboard::class );
		$this->add_task( Config_Dashboard_Widgets::class );

		$this->run_tasks();
	}
}
