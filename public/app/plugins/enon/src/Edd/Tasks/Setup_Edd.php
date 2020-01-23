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

namespace Enon\Edd\Tasks;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Task;

/**
 * Class Setup_Edd.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class Setup_Edd implements Task, Actions {
	/**
	 * Running tasks.
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
		add_action( 'edd_customer_post_attach_payment', [ $this, 'disable_email_on_existing_user_account' ], 1 );
	}

	/**
	 * Disabling outgoing emails to guest orders if email adress exists for an user.
	 *
	 * @since 1.0.0
	 */
	public function disable_email_on_existing_user_account() {
		remove_action( 'edd_customer_post_attach_payment', 'edd_connect_guest_customer_to_existing_user', 10, 4 );
	}
}
