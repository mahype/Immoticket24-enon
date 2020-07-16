<?php
/**
 * Configuring user.
 *
 * @category Class
 * @package  Enon\Misc\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;
use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class Config_User
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Config_User implements Filters, Actions, Task {

	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->add_filters();
		$this->add_actions();
	}

	/**
	 * Add filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'user_contactmethods', array( $this, 'remove_contactmethods' ), 10000 );
	}

	/**
	 * Add actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'admin_head', array( $this, 'remove_color_scheme' ) );
	}

	/**
	 * Removing contact methods.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function remove_contactmethods() {
		return array();
	}

	/**
	 * Removing color schemes.
	 *
	 * @since 1.0.0
	 */
	public function remove_color_scheme() {
		global $_wp_admin_css_colors;
		$_wp_admin_css_colors = 0;
	}
}
