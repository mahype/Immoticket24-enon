<?php

namespace Enon\Tasks\Emails;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;

use Enon\Models\Edd\Payment;
use WPENON\Model\Energieausweis;

/**
 * Class Edd_Payment_Emails.
 *
 * @package Enon\Tasks\Emails
 *
 * @since 1.0.0
 */
class Edd_Payment_Emails implements Actions, Filters, Task
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
		$this->add_filters();
	}

	/**
	 * Add actions.
	 * 
	 * @since 1.0.0
	 */
	public function add_actions()
	{
		add_action('edd_update_payment_status', array($this, 'payment_update'), 10, 2);
		add_action('current_screen', array($this, 'bulk_execute'), 20, 1);
	}

	public function add_filters()
	{
		add_filter( 'edd_payments_table_bulk_actions', array($this, 'bulk_actions') );
	}

	/**
	 * Add bulk actions.
	 * 
	 * @since 1.0.0
	 */
	public function bulk_actions($actions)
	{
		$actions['send_payment_reminder'] = 'Zahlungserinnerung versenden';
		return $actions;
	}

	/**
	 * Execute bulk actions.
	 * 
	 * @since 1.0.0
	 */
	public function bulk_execute($screen) 
	{
		if ( $screen->post_type !== 'download' || empty($_REQUEST['action'] ) ) {
			return;
		}

		if( $_REQUEST['action'] === 'send_payment_reminder' ) {			
			$payments = $_REQUEST['payment'];
			foreach ($payments as $payment_id) {
				$this->email_payment_reminder($payment_id);
			}
			return;
		}
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

	public function email_payment_reminder(int $payment_id)
	{
		$payment = new Payment($payment_id);

		if( $payment->get_status() !== 'pending' ) {
			return;
		}

		$ec = $payment->get_energieausweis();
		$ec_old = new Energieausweis($ec->get_post()->ID);

		$to_email = $ec->get_contact_email();
		$address = $ec_old->adresse_strassenr . ', ' . $ec_old->adresse_plz . ' ' . $ec_old->adresse_ort;
		$payment_nr = $payment->get_title();
		$payment_date = $payment->get_date();
		$due_date = date('d.m.Y', strtotime('+6 days'));		

		$subject = "{$payment_nr} - Zahlungserinnerung";



		$message = "Sehr geehrter Kunde,

		ich melde mich, da wir bislang noch keinen Zahlungseingang für den Energieausweis für das Gebäude <strong>{$address}</strong> mit der Rechnungsnummer {$payment_nr} vom {$payment_date} feststellen konnten. Bitte überweisen Sie den Rechnungsbetrag bis <strong><u>{$due_date}</u></strong>.
		
		Beigefügt erhalten Sie im Anhang noch einmal die Rechnung.
		
		Sollten Sie den Rechnungsbetrag bereits überwiesen haben, bitten wir Sie um kurze Rückmeldung, da wir Ihre Zahlung in dem Fall nicht zu ordnen konnten.
		
		Viele Grüße
		Christian Esch
		
		<p style=\"text-align: center; font-size: 12px;\"><strong>Immoticket24.de GmbH</strong>
		Krufter Straße 5
		56753 Welling
		Telefon: 0 26 54 - 8 80 11 99
		Email: info(at)immoticket24.de</p>
		<p style=\"text-align: center; font-size: 12px;\">Geschäftsführer: Christian Esch, Karl-Werner Esch
		Amtsgericht Koblenz, HRB 23918
		USt-Identifikationsnummer: DE292996626</p>
		<p style=\"text-align: center; font-size: 12px;\">Diese Email wurde automatisch von <a href=\"https://www.energieausweis-online-erstellen.de\">https://www.energieausweis-online-erstellen.de</a> versendet.</p>
		";

		$bill_path = $this->create_bill_file($payment_id);
		$this->send_email( $to_email, $subject, "Zahlungserinnerung", $message, array($bill_path) );

		unlink($bill_path);
	}

	public function create_bill_file( $payment_id ) {
		$post = get_post($payment_id);

        $details = new \stdClass();

        $payment_id = $post->ID;

        $details->ID = $payment_id;
        $details->date = $post->post_date;
        $details->post_status = $post->post_status;
        $details->total = edd_get_payment_amount($payment_id);
        $details->subtotal = edd_get_payment_subtotal($payment_id);
        $details->tax = edd_get_payment_tax($payment_id);
        $details->fees = edd_get_payment_fees($payment_id);
        $details->key = edd_get_payment_key($payment_id);
        $details->gateway = edd_get_payment_gateway($payment_id);
        $details->user_info = edd_get_payment_meta_user_info($payment_id);
        $details->cart_details = edd_get_payment_meta_cart_details($payment_id, true);

        if (edd_get_option('enable_sequential')) {
            $details->payment_number = edd_get_payment_number($payment_id);
        }

		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['basedir'];
		$filename = get_the_title($payment_id);

        $receipt = new \WPENON\Model\ReceiptPDF( $filename );
		$receipt->create($details);
		$receipt->finalize('F', $upload_dir );

		return $upload_dir . '/' . $filename . '.pdf';
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
	protected function send_email($to, $subject, $header, $message, $attachments = array())
	{
		$emails = \EDD()->emails;
		$emails->__set('heading', $header);		
		return $emails->send($to, $subject, $message, $attachments);
	}
}
