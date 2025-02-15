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
use Enon_Reseller\Models\Reseller;

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
		add_filter( 'enon_edd_emails_payment_fees', array( $this, 'filter_premium_bewertung_email_address' ), 10, 3 );
	}

	/**
	 * Filter premium bewertung email address.
	 *
	 * @param array Email addresses.
	 * @param array Payment fees.
	 * @param Energieausweis 
	 *
	 * @return array Filtered email addresses.
	 *
	 * @since 1.0.0
	 */
	public function filter_premium_bewertung_email_address( array $emails, $payment_fees, $energieausweis ) : array {
		if ( ! isset( $energieausweis->reseller_id ) ) {
			return $emails;
		}

		if ( ! $this->has_premium_bewertung( $payment_fees ) ) {
			return $emails;
		}

		$reseller = new Reseller( $energieausweis->reseller_id );
		$reseller_email = $reseller->data()->general->get_contact_email();

		if ( ! in_array( $reseller_email, $emails, true ) ) {
			$emails[] = $reseller_email;
		}

		$email = 'premiumbewertung-reseller@energieausweis-online-erstellen.de';

		if ( ! in_array( $email, $emails, true ) ) {
			$emails[] = $email;
		}

		return $emails;
	}

	/**
	 * Is premium bewertung selected?
	 * 
	 * @param int   Payment fees.
	 * @return bool True if was selected, false if not.
	 * 
	 * @since 1.0.0
	 */
	private function has_premium_bewertung( $payment_fees ) {
		foreach ( $payment_fees AS $payment_fee ) {
			if ( $payment_fee['id'] === 'premium_bewertung' ) {
				return true;
			}
		}

		return false;
	}
}
