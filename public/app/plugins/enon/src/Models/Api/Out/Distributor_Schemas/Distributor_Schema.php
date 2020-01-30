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

namespace Enon\Models\Api\Out\Distributor_Schemas;

use WPENON\Model\Energieausweis as Energieausweis_Old;

/**
 * Class Distributor_Schema
 *
 * @since 1.0.0
 */
interface Distributor_Schema {
	/**
	 * Checks if data have to be sent.
	 *
	 * @param Energieausweis_Old $energy_certificate Energy certificate object.
	 *
	 * @return bool True if check passed.
	 *
	 * @since 1.0.0
	 */
	public function check( Energieausweis_Old $energy_certificate ) : bool;

	/**
	 * Filter the data which will be sent.
	 *
	 * @param array              $data               Data array of energy certificate.
	 * @param Energieausweis_Old $energy_certificate Energy certificate object.
	 *
	 * @return array Filtered data.
	 *
	 * @since 1.0.0
	 */
	public function filter_data( array $data, Energieausweis_Old $energy_certificate ) : array;

	/**
	 * Get endpoint url.
	 *
	 * @return string Endpoint url.
	 *
	 * @since 1.0.0
	 */
	public function get_endpoint() : string;
}
