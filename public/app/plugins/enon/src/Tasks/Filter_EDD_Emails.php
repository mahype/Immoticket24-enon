<?php
/**
 * Filtering emails
 *
 * @category Class
 * @package  Enon\Tasks\Filters
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;
use Enon\Models\Edd\Payment;

use WPENON\Model\Energieausweis;

/**
 * Class Filter_Mails_For_Postcodes.
 *
 * @since 1.0.0
 */
class Filter_EDD_Emails implements Task, Actions {	
	/**
	 * Payment object.
	 * 
	 * @var Payment
	 * 
	 * @since 1.0.0
	 */
	private $payment;

	/**
	 * Energieausweis object.
	 * 
	 * @var Energieausweis
	 * 
	 * @since 1.0.0
	 */
	private $energieausweis;

	/**
	 * Payment fees.
	 * 
	 * @since 1.0.0
	 */
	private $payment_fees = [];

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_actions();
	}	

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
        add_action( 'edd_admin_sale_notice', [ $this, 'add_email_callbacks' ]  );
	}	

	/**
	 * Add Email callbacks.
	 * 
	 * @param int $payment_id Payment id.
	 * 
	 * @since 1.0.0
	 */
	public function add_email_callbacks( $payment_id ) {
		if ( ! edd_is_payment_complete( $payment_id ) ) {
			return;
		}

		$this->payment        = new Payment( $payment_id );		
		$this->payment_fees   = edd_get_payment_fees( $payment_id, 'item' );
		$this->energieausweis = new Energieausweis( $this->payment->get_energieausweis_id() );

		if ( $this->has_emails_for_postcodes() ) {
			add_filter( 'edd_admin_notice_emails', [ $this, 'add_emails_for_postcodes' ] );
		}

		if ( $this->has_emails_for_payment_fees() ) {
			add_filter( 'edd_admin_notice_emails', [ $this, 'add_emails_for_payment_fees' ] );
		}
	}

	/**
	 * Are there any emails to send for postode areas?
	 * 
	 * @return bool True if emails have to be sent, false if not.
	 * 
	 * @since 1.0.0
	 */
	private function has_emails_for_postcodes() {		
		$postcodes         = [ '53', '54', '55', '56' ];			

		foreach( $postcodes AS $postcode ) {
			$compare_postcode = substr( $this->energieausweis->adresse_plz, 0, strlen( $postcode ) );

			if ( $compare_postcode == $postcode ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Are there any emails to send for payment fees?
	 * 
	 * @param int $payment_id Payment id.
	 * @return bool True if emails have to be sent, false if not.
	 * 
	 * @since 1.0.0
	 */
	private function has_emails_for_payment_fees() {	
		return count( $this->payment_fees ) > 0;
	}

	/**
	 * Add email address for emails, which have a recipient for different poscodes.
	 * 
	 * @param  array $emails Email adresses for sale notification.
	 * @return array $emails Filtered email adresses for sale notification.
	 * 
	 * @since 1.0.0
	 */
	public function add_emails_for_postcodes( $emails ) {
		$emails[] = 'kwe@immoticket24.de';
		return $emails;
	}

	/**
	 * Add email addresses for emails, which have to be sent on selected payment fees.
	 * 
	 * @param  array $emails Email adresses for sale notification.
	 * @return array $emails Filtered email adresses for sale notification.
	 */
	public function add_emails_for_payment_fees( $emails ) {
		$payment_fee_emails = [
			'experten_check'             => 'angabencheck@energieausweis-online-erstellen.de',
			'sendung_per_post'           => 'postversand@energieausweis-online-erstellen.de',			
			'energieausweis_besprechung' => 'angabencheck@energieausweis-online-erstellen.de',
			'premium_bewertung'          => 'premiumbewertung@energieausweis-online-erstellen.de',
		];

		foreach ( $this->payment_fees as $payment_fee ) {
			if ( ! array_key_exists( $payment_fee['id'], $payment_fee_emails ) ) {
				continue;
			}

			$email = $payment_fee_emails[ $payment_fee['id'] ];

			if ( in_array( $email, $emails, true ) ) {
				continue;
			}

			if( $payment_fee['id'] === 'premium_bewertung' && $this->has_emails_for_postcodes() ) {
				continue;
			}

			$emails[] = $email;
		}

		return $emails;
	}
}
