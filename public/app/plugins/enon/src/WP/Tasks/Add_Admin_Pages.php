<?php
/**
 * Load settings menu & page.
 *
 * @category Class
 * @package  Enon\WP\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\WP\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;
use Enon\Logger;

/**
 * Class Task_Settings_Page.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class Add_Admin_Pages implements Task, Actions {

	/**
	 * Add_Admin_Pages constructor.
	 *
	 * @param Logger $logger Logger object.
	 * @since 1.0.0
	 */
	public function __construct( Logger $logger ) {
		$this->logger = $logger;
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_actions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'admin_menu', array( $this, 'enon_menu' ) );
		add_action( 'admin_menu', array( $this, 'remove_affiliate_submenu' ), 11 );
	}

	/**
	 * Remove AffiliateWP submenu 'affiliate-wp-visits'
	 *
	 * @since 1.0.0
	 */
	public function remove_affiliate_submenu() {
		if ( ! is_admin() ) {
			remove_submenu_page( 'affiliate-wp', 'affiliate-wp-visits' );
		}
	}

	/**
	 * Register enon menu in admin.
	 *
	 * @since 1.0.0
	 */
	public function enon_menu() {
		add_menu_page( __( 'Enon', 'enon' ), __( 'Enon settings', 'enon' ), 'activate_plugins', 'enon', array( $this, 'options_page' ), 'dashicons-admin-multisite', 50 );
	}

	/**
	 * Options page content.
	 */
	public function options_page() {
		// Will be overwritten by added CPT's, so leave empty.
	}
}
