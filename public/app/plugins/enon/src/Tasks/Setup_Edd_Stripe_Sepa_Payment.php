<?php
/**
 * Setup Eeasy digital downloads SEPA payment.
 *
 * @category Class
 * @package  Enon\Edd\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;
use Awsm\WP_Wrapper\Tools\Logger;

/**
 * Class Setup_Edd stripe payment.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class Setup_Edd_Stripe_Sepa_Payment implements Task, Actions {
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
		add_action( 'awsm_edd_payment_pending', [ $this, 'payment_pending' ], 10, 1 );
	}

	/**
	 * Setup pending payment.
	 *
	 * @since 1.0.0
	 */
	public function payment_pending( $payment_id ) {
		edd_update_payment_status( $payment_id, 'publish' );
	}
}
