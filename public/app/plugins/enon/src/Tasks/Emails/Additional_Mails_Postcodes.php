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

use Awsm\WP_Wrapper\Interfaces\Filters;
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

class Additional_Mails_Postcodes implements Filters, Task {
	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() 
	{
		$this->add_filters();
	}

	public function add_filters()
	{
		add_action( 'edd_admin_sale_notice', 'mail_to_postcode_50_56', 10, 2 );
	}
}


function mail_to_postcode_50_56( $payment_id = 0, $payment_data = array() ) {
	$send_extra_mail = false;

	$payment = new Payment( $payment_id );
	$energieausweis_id = $payment->get_energieausweis_id();

	$energieausweis = new Energieausweis( $energieausweis_id );

	if ( $send_extra_mail ) {
		add_filter( 'edd_admin_notice_emails', 'enon_add_postcode_email' );
		edd_admin_email_notice( $payment_id, $payment_data );
		remove_filter( 'edd_admin_notice_emails', 'enon_add_postcode_email' );
	}
}