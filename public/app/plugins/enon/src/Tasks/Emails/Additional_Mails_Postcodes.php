<?php
/**
 * Google tag manager tasks.
 *
 * @category Class
 * @package  Enon\Misc\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks\Emails;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;
use Enon\Models\Edd\Payment;
use WPENON\Model\Energieausweis;

/**
 * Class Google_Tag_Manager
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */

class Additional_Mails_Postcodes implements Actions, Task {

	/**
	 * Email address used on sending email
	 * 
	 * @since 1.0.0
	 */
	private $email_to;

	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() 
	{
		$this->add_actions();
	}

	/**
	 * Add Actions
	 * 
	 * @since 1.0.0
	 */
	public function add_actions()
	{
		add_action( 'edd_admin_sale_notice', [ $this, 'mail' ], 10, 2 );
	}

	/**
	 * Mail to postcode areas.
	 * 
	 * @param int   Payment ID.
	 * @param array Payment data.
	 * 
	 * @since 1.0.0
	 */
	public function mail( $payment_id = 0, $payment_data = array() ) {
		$payment = new Payment( $payment_id );
		$energieausweis_id = $payment->get_energieausweis_id();

		$energieausweis = new Energieausweis( $energieausweis_id );
		$energieausweis->adresse_plz;

		$postcode_areas = [
			[
				// 'email_to'  => 'kwe@immoticket24.de',
				'email_to'  => 'sven@awesome.ug',
				'postcodes' => [ '50', '51', '52', '53', '54', '55', '56' ]
			]
		];

		foreach ( $postcode_areas AS $postcode_area ) {
			$send_extra_mail = false;			

			$this->email_to  = $postcode_area['email_to'];						
			$postcodes       = $postcode_area['postcodes'];

			foreach( $postcodes AS $postcode ) {
				$compare_postcode = substr( $energieausweis->adresse_plz, 0, strlen( $postcode ) );

				if ( $compare_postcode == $postcode ) {
					$send_extra_mail = true;
				}
			}

			if ( $send_extra_mail ) {
				add_filter( 'edd_admin_notice_emails', [ $this, 'add_email_to' ] );
				edd_admin_email_notice( $payment_id, $payment_data );
				remove_filter( 'edd_admin_notice_emails', [ $this,  'add_email_to' ] );
			}		
		}
	}

	/**
	 * Adding recipient to email.
	 * 
	 * @var array Email addresses.
	 * 
	 * @since 1.0.0
	 */
	public function add_email_to( $emails = array() ) {
		$emails[] = $this->email_to;
		return $emails;
	}
}