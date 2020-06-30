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

use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;
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
	use Logger_Trait;

	/**
	 * Reseller object.
	 *
	 * @since 1.0.0
	 * @var Reseller;
	 */
	private $reseller;

	/**
	 * Wpenon constructor.
	 *
	 * @param Reseller $reseller Reseller object.
	 * @param Logger   $logger   Logger object.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Reseller $reseller, Logger $logger ) {
		$this->reseller = $reseller;
		$this->logger = $logger;
	}

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

		$debug_values = array(
			'payment_id' => $payment_id,
		);

		$this->logger()->notice( 'Changing premium bewertung reseller email address of payment.', $debug_values );

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
	public function filter_premium_bewertung_email_address( array $emails ) : string {
		$new_email = 'premiumbewertung-reseller@energieausweis-online-erstellen.de';

		$debug_values = array(
			'emails' => $emails,
			'new_email' => $new_email,
		);

		$this->logger()->notice( 'Adding premium bewertung reseller email address.', $debug_values );

		if ( ! in_array( $new_email, $emails, true ) ) {
			$emails[] = $new_email;
		}

		return $emails;
	}
}
