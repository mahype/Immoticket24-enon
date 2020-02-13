<?php
/**
 * Tasks loader.
 *
 * @category Class
 * @package  Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Enon\Task_Loader;

/**
 * Tasks loader.
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
		$this->add_task( Add_Options_General::class, $this->logger() );
		$this->add_task( Add_Page_Fields::class, $this->logger() );
		$this->add_task( Add_Cli_Commands::class );

		$this->add_task( Setup_Edd::class, $this->logger() );
		$this->add_task( Setup_Wonolog::class, $this->logger() );

		if ( is_admin() && ! wp_doing_ajax() ) {
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
		$this->add_task( Add_Google_Tag_Manager::class );
		$this->add_task( Add_Page_Scripts::class );
		$this->add_task( Remove_Optimizepress::class );
	}
}

