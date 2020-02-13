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

use Enon\Models\Edd\Payment;
use Enon\Models\Api\Out\Distributor_Schemas\Distributor_Schema;

use Enon\Models\Enon\Energieausweis;
use WPENON\Model\Energieausweis as Energieausweis_Old;
use WPENON\Util\PaymentMeta;

/**
 * Class Distributor_Schema
 *
 * @since 1.0.0
 */
class Sparkasse implements Distributor_Schema {
	/**
	 * Checks if data can be sent.
	 *
	 * @param Energieausweis_Old $energieausweis Energy certificate.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function check( Energieausweis_Old $energieausweis ) : bool {
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
			if ( ! empty( $energieausweis->$value ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Filter data.
	 *
	 * @param array              $estate_data            Estate data.
	 * @param Energieausweis_Old $energy_certificate_old Energy certificate object.
	 *
	 * @return array Filtered data.
	 *
	 * @since 1.0.0
	 */
	public function filter_data( array $estate_data, Energieausweis_Old $energy_certificate_old ) : array {
		$energy_certificate = new Energieausweis( $energy_certificate_old->id );
		$payment            = new \Edd_Payment( $energy_certificate->get_payment_id() );

		$customer_data = array(
			'name' => $payment->first_name . ' ' . $payment->last_name,
		);

		$estate_address_data = array(
			'email'              => $energy_certificate_old->wpenon_email,
			'adresse_strassenr'  => $energy_certificate_old->adresse_strassenr,
			'adresse_plz'        => $energy_certificate_old->adresse_plz,
			'adresse_ort'        => $energy_certificate_old->adresse_ort,
			'adresse_bundesland' => $energy_certificate_old->adresse_bundesland,
		);

		$data = array(
			'customer'       => $customer_data,
			'estate_address' => $estate_address_data,
			'estate'         => $estate_data,
			'sender'         => 'immoticket24', // Required by Sparkasse Immobilien Heidelberg.
		);

		// $data = $this->encode_data_recursive( 'utf8_encode', $data );

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
		if ( 'enon.test' === $_SERVER['SERVER_NAME'] ) {
		// 	return 'https://postman-echo.com/post';
		}

		return 'https://www.immobilienwertanalyse.de/iwapro/import/importData.php';
	}
}
