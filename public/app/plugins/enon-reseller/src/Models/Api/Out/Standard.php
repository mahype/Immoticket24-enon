<?php
/**
 * Standard Schema.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Transfer
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Api\Out;

use WPENON\Model\Energieausweis as Energieausweis_Old;

/**
 * Class Distributor_Schema
 *
 * @since 1.0.0
 */
class Standard extends Sender {
	/**
	 * Checks if data will be sent.
	 *
	 * @param Energieausweis_Old $energieausweis Energy certificate.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function check() : bool {
		// Sending data is only relevant if one of the following fields is filled out.
		$values_to_check = array(
			'modernisierung_baeder',
			'modernisierung_innenausbau',
			'verbesserung_grundrissgestaltung',
			'qualitaet_generell',
			'grundstuecksflaeche',
		);

		foreach ( $values_to_check as $value ) {
			if ( ! empty( $this->energieausweis->$value ) ) {
				return true;
			}
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
	public function get_subject() : string {
		return 'Lead Immobilienbewertung';
	}
}
