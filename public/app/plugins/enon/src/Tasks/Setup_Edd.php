<?php
/**
 * Setup Eeasy digital downloads.
 *
 * @category Class
 * @package  Enon\Edd\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;
use Awsm\WP_Wrapper\Tools\Logger;

use Enon\Models\Enon\Prevent_Completion;

/**
 * Class Setup_Edd.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class Setup_Edd implements Task, Filters {
	/**
	 * Loading Plugin scripts.
	 *
	 * @since 1.0.0
	 *
	 * @param Logger $logger Logger object.
	 */
	public function __construct( Logger $logger ) {
		$this->logger = $logger;
	}

	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 */
	public function run() {
        $this->add_filters();
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'plugins_loaded', array( Prevent_Completion::class, 'init' ), 20 );
	}
}
