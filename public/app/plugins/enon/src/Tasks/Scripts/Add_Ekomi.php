<?php
/**
 * Add scripts.
 *
 * @category Class
 * @package  Enon\Misc\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks\Scripts;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;
use EDD_Payment;
use Enon\Models\Enon\Energieausweis;

/**
 * Class Google_Tag_Manager
 *
 * @package awsmug\Enon\Tools
 *
 * @since 2022-02-08
 */
class Add_Ekomi implements Task, Actions {
	/**
	 * Running tasks.
	 *
	 * @return void
	 *
	 * @since 2022-02-08
	 */
	public function run() {
		$this->add_actions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 2022-02-08
	 */
	public function add_actions() {
		add_action( 'edd_complete_download_purchase', [ $this, 'send_to_ekomi' ], 100, 2 );
	}

	/**
	 * Send userdata to ekomi.
	 * 
	 * @param int Energy certificate id.
	 * @param int Payment id.
	 * 
	 * @since 2022-02-08
	 */
	public function send_to_ekomi( int $ec_id, int $payment_id ) {
		$ec = new Energieausweis( $ec_id );
		
		if ( ! $ec->contacting_allowed() ) {
			return;
		}

		$edd_payment = new EDD_Payment( $payment_id );
		$order_no = $edd_payment->number;

		$order_name = $edd_payment->first_name . ' ' . $edd_payment->last_name;
		$order_email = $edd_payment->email;

		$emails = \EDD()->emails;

		$to = '81266-energieausweis-online@connect.ekomi.de';
		$subject = 'Neue Bestellung';
		$message = "Hier finden sich die Daten zur neuen Bestellung:\n\n";
		$message .= "Vorgangskennung: " . $order_no . "\n";
		$message .= "Mailadresse des Kunden: " . $order_email . "\n";
		$message .= "Name des Kunden: " . $order_name . "\n";

		$emails->__set('heading', $subject);
		$result = $emails->send($to, $subject, $message);
		
		if ( ! $result ) {
			edd_record_log('', sprintf('Payment %d eKomi email could not be sent.', $payment_id), 0, 'templog');
		}

		update_post_meta($payment_id, 'it24_sent_to_ekomi', true);
	}
}
