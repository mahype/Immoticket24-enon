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

namespace Enon_Reseller\Models\Api\Out\Distributor_Schemas;

use Enon\Models\Api\Out\Distributor_Schemas\Distributor_Schema;

use Enon\Models\Enon\Energieausweis;
use WPENON\Model\Energieausweis as Energieausweis_Old;

/**
 * Class Distributor_Schema
 *
 * @since 1.0.0
 */
class Sparkasse extends Distributor_Schema {
	/**
	 * Checks if data can be sent.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function check() : bool {
		if ( 321587 !== (int) $this->energieausweis->reseller_id ) {
			$debug_values = array(
				'energy_certificate_id' => (int) $this->energieausweis->id,
				'reseller_id'           => (int) $this->energieausweis->reseller_id,
			);

			$this->logger()->notice('Stopped sending data to sparkasse server. Reseller id is wrong.', $debug_values );

			return false;
		}

		if ( $this->already_sent() ) {
			$debug_values = array(
				'energy_certificate_id' => (int) $this->energieausweis->id,
				'reseller_id'           => (int) $this->energieausweis->reseller_id,
			);

			$this->logger()->notice('Stopped sending data to sparkasse server. Data already sent.', $debug_values );

			return false;
		}

		// Sending data is only relevant if one of the following fields is filled out.
		$values_to_check = array(
			'modernisierung_baeder',
			'modernisierung_innenausbau',
			'verbesserung_grundrissgestaltung',
			'qualitaet_mauerwerk',
			'qualitaet_dach',
			'qualitaet_gebaeudedaemmung',
			'qualitaet_fenster',
			'qualitaet_bodenbelaege',
			'qualitaet_heizung',
			'qualitaet_baeder_sanitaer',
			'grundstuecksflaeche',
		);

		foreach ( $values_to_check as $value ) {
			if ( ! empty( $this->energieausweis->$value ) ) {
				return true;
			}
		}

		$debug_values = array(
			'energy_certificate_id' => (int) $this->energieausweis->id,
			'reseller_id'           => (int) $this->energieausweis->reseller_id,
			'values_to_check'       => $values_to_check,
		);

		$this->logger()->notice('Stopped sending data to sparkasse server. Value check not passed..', $debug_values );

		return false;
	}

	/**
	 * Set as sent.
	 *
	 * @return int|bool The new meta field ID if a field with the given key didn't exist and was
     *                  therefore added, true on successful update, false on failure.
	 *
	 * @since 1.0.0
	 */
	public function set_sent() {
		return update_post_meta( $this->energieausweis->id, 'sent_to_sparkasse', true );
	}

	/**
	 * Checks if Engergieausweis was sent to sparkasse.
	 *
	 * @return bool True if was already sent to sparkasse, false if not.
	 *
	 * @since 1.0.0
	 */
	public function already_sent() {
		$is_sent = (bool) get_post_meta( $this->energieausweis->id, 'sent_to_sparkasse' );

		if ( true === $is_sent ) {
			return true;
		}

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
	public function filter_data( array $estate_data ) : array {
		$energy_certificate = new Energieausweis( $this->energieausweis->id );
		$payment            = new \Edd_Payment( $energy_certificate->get_payment_id() );

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
			'estate_address' => $estate_address_data,
			'estate'         => $estate_data,
			'sender'         => 'immoticket24', // Required by Sparkasse Immobilien Heidelberg.
		);

		// $data = $this->encode_data_recursive( 'utf8_encode', $data );
		$debug_values = array(
			'energy_certificate_id' => (int) $this->energieausweis->id,
			'reseller_id'           => (int) $this->energieausweis->reseller_id,
			'data'                  => $data,
		);

		$this->logger()->notice('Prepared data.', $debug_values );

		return $data;
	}

	/**
	 * Encoding data recursive.
	 *
	 * @param string $callback Callback function for encoding.
	 * @param array  $data     Data to encode.
	 *
	 * @return array Encoded tata.
	 *
	 * @since 1.0.0
	 */
	private function encode_data_recursive( $callback, $data ) {
		$function = function ( $item ) use ( &$function, &$callback ) {
			return is_array( $item ) ? array_map( $function, $item ) : call_user_func( $callback, $item );
		};

		return array_map( $function, $data );
	}

	/**
	 * Get endpoint url.
	 *
	 * @return string Endpoint url.
	 *
	 * @since 1.0.0
	 */
	public function get_endpoint() : string {
		// phpcs:ignore
		$server_name = $_SERVER['SERVER_NAME'];

		switch( $server_name ) {
			case 'enon.test':
			case 'staging.energieausweis-online-erstellen.de':
			case 'sparkasse.energieausweis-online-erstellen.de':
				$receipient_server = 'https://postman-echo.com/post';
				break;
			default:
				$receipient_server = 'https://www.immobilienwertanalyse.de/iwapro/import/importData.php';
				break;
		}

		return $receipient_server;
	}
}
