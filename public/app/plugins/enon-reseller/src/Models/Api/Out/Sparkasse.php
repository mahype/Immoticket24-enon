<?php

/**
 * Sparkasse Schema.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Transfer
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Api\Out;

use Enon\Models\Api\Out\Request;
use Enon\Models\Enon\Energieausweis;

/**
 * Class Distributor_Schema
 *
 * @since 1.0.0
 */
class Sparkasse extends Sender
{
	/**
	 * Checks if data can be sent.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function check(): bool
	{
		if (321587 !== (int) $this->energieausweis->reseller_id) {
			$debug_values = array(
				'energy_certificate_id' => (int) $this->energieausweis->id,
				'reseller_id'           => (int) $this->energieausweis->reseller_id,
			);

			$this->logger->notice('Stopped sending data to sparkasse server. Reseller id is wrong.', $debug_values);

			return false;
		}

		if ($this->is_sent()) {
			$debug_values = array(
				'energy_certificate_id' => (int) $this->energieausweis->id,
				'reseller_id'           => (int) $this->energieausweis->reseller_id,
			);

			$this->logger->notice('Stopped sending data to sparkasse server. Data already sent.', $debug_values);

			return false;
		}

		// Sending data is only relevant if one of the following fields is filled out.
		$values_to_check = array(
			'modernisierung_baeder',
			'modernisierung_innenausbau',
			'verbesserung_grundrissgestaltung',
			'qualitaet_generell',
			'grundstuecksflaeche',
		);

		foreach ($values_to_check as $value) {
			if (!empty($this->energieausweis->$value)) {
				return true;
			}
		}

		$debug_values = array(
			'energy_certificate_id' => (int) $this->energieausweis->id,
			'reseller_id'           => (int) $this->energieausweis->reseller_id,
			'values_to_check'       => $values_to_check,
		);

		$this->logger->notice('Stopped sending data to sparkasse server. Value check not passed..', $debug_values);

		return false;
	}

	/**
	 * Filter data.
	 *
	 * @param array              $estate_data            Estate data.
	 *
	 * @return array Filtered data.
	 *
	 * @since 1.0.0
	 */
	private function filter_data(array $estate_data): array
	{
		$energy_certificate = new Energieausweis($this->energieausweis->id);
		$payment            = new \Edd_Payment($energy_certificate->get_payment_id());

		$imageID            = get_post_meta($this->energieausweis->ID, '_thumbnail_id', true);
		$image              = wp_get_attachment_image_url($imageID, 'enon-energieausweiss-image');

		$customer_data = array(
			'name' => $payment->first_name . ' ' . $payment->last_name,
		);

		$estate_address_data = array(
			'email'              => $this->energieausweis->wpenon_email,
			'adresse_strassenr'  => $this->energieausweis->adresse_strassenr,
			'adresse_plz'        => $this->energieausweis->adresse_plz,
			'adresse_ort'        => $this->energieausweis->adresse_ort,
			'adresse_bundesland' => $this->energieausweis->adresse_bundesland,
		);

		$data = array(
			'customer'       => $customer_data,
			'image'          => $image,
			'estate_address' => $estate_address_data,
			'estate'         => $estate_data,
			'sender'         => 'immoticket24', // Required by Sparkasse Immobilien Heidelberg.
		);

		$debug_values = array(
			'energy_certificate_id' => (int) $this->energieausweis->id,
			'reseller_id'           => (int) $this->energieausweis->reseller_id,
			'data'                  => $data,
		);

		$this->logger->notice('Prepared data.', $debug_values);

		return $data;
	}

	/**
	 * Preparing data.
	 *
	 * @since 1.0.0
	 */
	public function get_content()
	{
		$fields = $this->energieausweis->getSchema()->getFields();

		$data = array();

		foreach ($fields as  $key => $field) {
			$data[$key] = $this->energy_certificate->$key;
		}

		return $this->filter_data($data);
	}

	/**
	 * Get endpoint url.
	 *
	 * @return string Endpoint url.
	 *
	 * @since 1.0.0
	 */
	public function get_endpoint(): string
	{
		// phpcs:ignore
		$server_name = $_SERVER['SERVER_NAME'];

		switch ($server_name) {
			case 'enon.test':
			case 'staging.energieausweis-online-erstellen.de':
			case 'develop.energieausweis-online-erstellen.de':
				$receipient_server = 'https://postman-echo.com/post';
				break;
			default:
				$receipient_server = 'https://www.immobilienwertanalyse.de/iwapro/import/importData.php';
				break;
		}

		return $receipient_server;
	}

	/**
	 * Send data to sparkasse endpoint.
	 * 
	 * @return bool True if sent, false if not.
	 * 
	 * @since 1.0.0
	 */
	public function send(): bool
	{
		$debug_values = array(
			'energy_certificate_id' => (int) $this->energieausweis->id,
			'reseller_id'           => (int) $this->energieausweis->reseller_id
		);

		if($this->is_sent()) {
			$this->logger->notice('Stopped sending data. Mail already sent.', $debug_values );
			return false;
		}

		if(!$this->check()) {
			$this->logger->notice('Stopped sending data. Check not passed.', $debug_values );
			return false;
		}

		$request = new Request($this->get_endpoint(), $this->logger);
		$request->set_content($this->get_content());

		if(!$request->post()) {
			return false;
		}

		return true;
	}
}
