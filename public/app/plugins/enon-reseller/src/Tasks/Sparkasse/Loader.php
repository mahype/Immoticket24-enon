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

namespace Enon_Reseller\Tasks\Sparkasse;

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
		$this->add_task( Sparkasse_Setup_Edd::class, $reseller, $this->logger() );
        $this->add_task( Add_Sparkasse_Discounts::class, $reseller, $this->logger() );

		$this->run_tasks();
    }
    
    public function add_frontent_tasks() {

    }

    public function add_backend_tasks() {
        
    }
}
