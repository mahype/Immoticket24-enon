<?php

/**
 * Distributor schema parent class.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Transfer
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Api\Out;

use Enon\Logger;
use Enon_Reseller\Models\Reseller;
use WPENON\Model\Energieausweis as Energieausweis_Old;

/**
 * Class Distributor_Schema
 *
 * @since 1.0.0
 */
abstract class Sender implements Sender_Interface
{
	/**
	 * Energieausweis object.
	 *
	 * @var Energieausweis_Old
	 *
	 * @socne 1.0.0
	 */
	protected Energieausweis_Old $energieausweis;

	/**
	 * Reseller object.
	 *
	 * @var Reseller
	 *
	 * @since 1.0.0
	 */
	protected Reseller $reseller;

	/**
	 * Payment id.
	 * 
	 * @var int
	 * 
	 * @since 1.0.0
	 */
	protected int $payment_id;

	/**
	 * Distrubutor schema constructor
	 *
	 * @param Logger $logger Logger object.
	 *
	 * @since 1.0.0
	 */
	public function __construct(Logger $logger, Energieausweis_Old $energieausweis, Reseller $reseller, int $payment_id)
	{
		$this->logger = $logger;
		$this->energieausweis = $energieausweis;
		$this->reseller = $reseller;
		$this->payment_id = $payment_id;
	}

	/**
	 * Checks if data will be sent.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function check(): bool
	{
		return true;
	}

	/**
	 * Set as sent.
	 *
	 * @return int|bool The new meta field ID if a field with the given key didn't exist and was
	 *                  therefore added, true on successful update, false on failure.
	 *
	 * @since 1.0.0
	 */
	public function set_sent()
	{
		return update_post_meta($this->energieausweis->id, 'sent_to_reseller', true);
	}

	/**
	 * Checks if Engergieausweis was sent to sparkasse.
	 *
	 * @return bool True if was already sent to sparkasse, false if not.
	 *
	 * @since 1.0.0
	 */
	public function is_sent(): bool
	{
		$is_sent = (bool) get_post_meta($this->energieausweis->id, 'sent_to_reseller');

		if (true === $is_sent) {
			return true;
		}

		return false;
	}

	/**
	 * Get subject.
	 * 
	 * @return string
	 * 
	 * @since 1.0.0
	 */
	public function get_subject(): string
	{
		return 'Neuer Energieausweis';
	}

	/**
	 * Get content.
	 * 
	 * @return mixed
	 * 
	 * @since 1.0.0
	 */
	public function get_content()
	{
		return wpenon_immoticket24_email_tag_certificate_data($this->payment_id);
	}

	/**
	 * Send data.
	 * 
	 * @return bool True if data have been sent, false if not.
	 * 
	 * @since 1.0.0
	 */
	public function send(): bool
	{
		$debug_values = array(
			'energy_certificate_id' => (int) $this->energieausweis->id,
			'reseller_id'           => (int) $this->energieausweis->reseller_id
		);

		if ($this->is_sent()) {
			$this->logger->notice('Stopped sending data. Mail already sent.', $debug_values);
			return false;
		}

		if (!$this->check()) {
			$this->logger->notice('Stopped sending data. Check not passed.', $debug_values);
			return false;
		}

		$recipient_email = $this->reseller->data()->general->get_contact_email();

		$this->send_email($recipient_email, $this->get_subject(), $this->get_content());

		$this->set_sent();

		return true;
	}

	/**
	 * Send email function.
	 * 
	 * @param string $to
	 * @param string subject
	 * @param string $message
	 * 
	 * @since 1.0.0
	 */
	protected function send_email($to, $subject, $message)
	{
		$emails = \EDD()->emails;
		$emails->__set('heading', $subject);
		return $emails->send($to, $subject, $message);
	}
}
