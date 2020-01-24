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

use WPENON\Model\Energieausweis as Energieausweis_Old;

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
	 * @param array $data Data to filter.
	 * @return array Filtered data.
	 *
	 * @since 1.0.0
	 */
	public function filter_data( array $data ) : array {
		$data['sender'] = 'immoticket24'; // Required by Sparkasse Immobilien Heidelberg.

		return $data;
	}

	/**
	 * Get endpoint url.
	 *
	 * @return string Endpoint url.
	 *
	 * @since 1.0.0
	 */
	public function get_endpoint() : string {
		return 'https://postman-echo.com/post';
	}
}
