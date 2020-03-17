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

namespace Enon\Models\Api\Out\Distributor_Schema_Interface;

use Enon\Models\Api\Out\Distributor_Schemas\Distributor_Schema_Interface;

use WPENON\Model\Energieausweis as Energieausweis_Old;

/**
 * Class Distributor_Schema
 *
 * @since 1.0.0
 */
class Standard implements Distributor_Schema_Interface {
	/**
	 * Checks if data will be sent.
	 *
	 * @param Energieausweis_Old $energieausweis Energy certificate.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function check( Energieausweis_Old $energieausweis ) : bool {
		return true;
	}

	/**
	 * Filter data.
	 *
	 * @param array              $data               Data to filter.
	 * @param Energieausweis_Old $energy_certificate Energy certificate object.
	 *
	 * @return array Filtered data.
	 *
	 * @since 1.0.0
	 */
	public function filter_data( array $data, Energieausweis_Old $energy_certificate ) : array {
		return $data;
	}

	/**
	 * Set as sent.
	 *
	 * @param Energieausweis_Old $energieausweis Engergieausweis object.
	 *
	 * @since 1.0.0
	 */
	public function set_sent( Energieausweis_Old $energieausweis ){}

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
