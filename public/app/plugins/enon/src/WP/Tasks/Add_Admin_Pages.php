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

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Task;

use Enon\Acf\Models\ACF;
use Awsm\WP_Wrapper\Traits\Logger as Logger_Trait;
use Awsm\WP_Wrapper\Tools\Logger;

/**
 * Class Task_Settings_Page.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class Add_Admin_Pages implements Task, Actions {
	/**
	 * AffiliateWP constructor.
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
		if ( ! ACF::is_activated() ) {
			$this->logger->warning( 'Advanced custom fields seems not to be activated.' );
			return;
		}

		$this->add_actions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'admin_menu', array( $this, 'enon_menu' ) );
	}

	/**
	 * Register enon menu in admin.
	 *
	 * @since 1.0.0
	 */
	public function enon_menu() {
		add_menu_page( __('Enon', 'enon' ), __('Enon settings', 'enon' ), 'administrator', 'enon', [ $this, 'options_page' ], 'dashicons-admin-multisite', 50 );
	}

	/**
	 * Options page content.
	 */
	public function options_page() {
		// Will be overwritten by added CPT's, so leave empty.
	}
}
