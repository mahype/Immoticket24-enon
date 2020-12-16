<?php
/**
 * Class for loading frontend scripts.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Core
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Filters;

use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;
use Awsm\WP_Wrapper\Tools\Logger_Trait;

use Enon_Reseller\Logger;
use Enon_Reseller\Models\Reseller;
use Enon_Reseller\Models\Reseller_Payment;

/**
 * Class Filter_Payment_Fee_Email.
 *
 * @package Enon_Reseller\WordPress
 *
 * @since 1.0.0
 */
class Filter_Payment_Fee_Email implements Task, Filters {
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
		add_filter( 'filter_payment_fee_email_address_callback', array( $this, 'filter_premium_bewertung_email_address_callback' ), 10, 3 );
	}

	/**
	 * Email premium bewerung email address callback.
	 *
	 * @param string $filter_callback Name of the callback.
	 * @param string $payment_fee_id  Payment fee id.
	 * @param int    $payment_id      Payment id.
	 *
	 * @return string Filtered callback name.
	 *
	 * @since 1.0.0
	 */
	public function filter_premium_bewertung_email_address_callback( string $filter_callback, string $payment_fee_id, int $payment_id ) {
		if ( 'premium_bewertung' !== $payment_fee_id ) {
			return $filter_callback;
		}

		$reseller_payment = new Reseller_Payment( $payment_id );

		if ( 0 === $reseller_payment->get_reseller_id() ) {
			return $filter_callback;
		}

		$filter_callback = array( $this, 'filter_premium_bewertung_email_address' );

		return $filter_callback;
	}

	/**
	 * Filter premium bewertung email address.
	 *
	 * @param array $emails Email addresses.
	 *
	 * @return array Filtered email addresses.
	 *
	 * @since 1.0.0
	 */
	public function filter_premium_bewertung_email_address( array $emails ) : array {
		$new_email = 'premiumbewertung-reseller@energieausweis-online-erstellen.de';
		$new_email2 = 'premiumbewertung@energieausweis-online-erstellen.de';

		if ( ! in_array( $new_email, $emails, true ) ) {
			$emails[] = $new_email;
			$emails[] = $new_email2;
		}

		return $emails;
	}
}
