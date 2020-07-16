<?php
/**
 * Class for configuring reseller dashboard.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Admin
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Admin;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class Config_Dashboard
 *
 * @package Enon_Reseller\Tasks\Admin.
 *
 * @since 1.0.0
 */
class Config_Dashboard implements Task, Actions {
	/**
	 * Run actions.
	 *
	 * @return mixed|void
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_actions();
	}

	/**
	 * Add Actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'wp_before_admin_bar_render', [ $this, 'remove_wp_logo' ], 0 );
	}

	/**
	 * Removing WP logo in admin.
	 *
	 * @since 1.0.0
	 */
	public function remove_wp_logo() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu( 'wp-logo' );
	}
}
