<?php
/**
 * Task which loads email order confirmation scripts.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Enon;

use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Traits\Logger as Logger_Trait;

use Enon_Reseller\Models\Reseller;

/**
 * Class Filter_Order_Confirmation_Email.
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller\WordPress
 */
class Filter_Order_Confirmation_Email extends Task_Email {

	/**
	 * Running scripts.
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
		add_filter( 'wpenon_order_confirmation_to_address', array( $this, 'filter_to_address' ) );
	}

	/**
	 * Returning token from email address.
	 *
	 * @since 1.0.0
	 *
	 * @param string $email To email address.
	 *
	 * @return string Reseller contact email address.
	 */
	public function filter_to_address( $email ) {
		$reseller_contact_email = $this->reseller->data()->get_contact_email();

		if ( ! $this->reseller->data()->send_order_to_reseller() || empty( $reseller_contact_email ) ) {
			return $email;
		}
		return $reseller_contact_email;
	}
}
