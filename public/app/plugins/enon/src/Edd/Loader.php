<?php
/**
 * Edd loader.
 *
 * @category Class
 * @package  Enon\EDD
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Edd;

use Enon\Task_Loader;
use Enon\Models\Exceptions\Exception;

use Enon\Edd\Tasks\Setup_Edd;
use Enon\Edd\Tasks\Sparkasse_Discounts;

/**
 * Class loader.
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
		$this->add_task( Setup_Edd::class, $this->logger() );
		$this->add_task( Sparkasse_Discounts::class, $this->logger() );

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
	}

	/**
	 * Running frontend tasks.
	 *
	 * @since 1.0.0
	 */
	public function add_frontend_tasks() {
	}
}

