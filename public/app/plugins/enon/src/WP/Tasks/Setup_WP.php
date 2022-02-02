<?php
/**
 * Setting up general WP options
 *
 * @category Class
 * @package  Enon\Config\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\WP\Tasks;

use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class Setup_Gutenberg.
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Setup_WP implements Filters, Task {
	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->add_filters();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'styles_inline_size_limit', '__return_zero' );
	}
}
