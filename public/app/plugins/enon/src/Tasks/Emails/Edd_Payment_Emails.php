<?php

namespace Enon\Tasks\Emails;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;

use Enon\Models\Edd\Payment;

/**
 * Class Edd_Payment_Emails.
 *
 * @package Enon\Tasks\Emails
 *
 * @since 1.0.0
 */

class Edd_Payment_Emails implements Actions, Task
{
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
	 * Add actions.
	 * 
	 * @since 1.0.0
	 */
	public function add_actions()
	{
		add_action('edd_update_payment_status', array($this, 'payment_update'), 10, 2);
	}

	/**
	 * Payment update scripts.
	 * 
	 * @since 1.0.0
	 */
	public function payment_update(int $payment_id, string $status)
	{
		switch ($status) {
			case 'publish':
				$this->email_payment_complete($payment_id);
				break;
			default:
				return;
		}
	}

	/**
	 * Email which is sent on payment completion.
	 * 
	 * @param int Payment id.
	 * 
	 * @since 1.0.0
	 */
	public function email_payment_complete(int $payment_id)
	{
		$payment = new Payment($payment_id);
		$ec = $payment->get_energieausweis();

		$ec_email = $ec->get_contact_email();
		$ec_title = $ec->get_post()->post_title;
		$ec_url = $ec->get_access_url();
		$header = 'Zahlungseingang';
		
		$subject = sprintf( 'Zahlungseingang' );
		$message = sprintf('Sehr geehrter Kunde,
		
für den Energieausweis %s wurde ein Zahlungseingang festgestellt. Sie können den Ausweis ab sofort unter %s herunterladen.

Sollte der Energieausweis noch keine Registriernummer beinhalten, so befindet sich dieser noch in der finalen Prüfung und wird in Kürze freigeschaltet. Sobald die Freischaltung erfolgt ist, erhalten Sie eine Bestellbestätigungsmail an Ihre Email-Adresse gesendet.

Mit freundlichen Grüßen

Ihr Team von Immoticket24.de', $ec_title, $ec_url);

		$this->send_email( $ec_email, $subject, $header, $message);
	}

	/**
	 * Send email function.
	 * 
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * 
	 * @since 1.0.0
	 */
	protected function send_email($to, $subject, $header, $message)
	{
		$emails = \EDD()->emails;
		$emails->__set('heading', $header);
		return $emails->send($to, $subject, $message);
	}
}
