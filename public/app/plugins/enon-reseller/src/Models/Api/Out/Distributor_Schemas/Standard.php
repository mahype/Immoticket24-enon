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

namespace Enon\Models\Api\Out\Distributor_Schemas;

use WPENON\Model\Energieausweis as Energieausweis_Old;

/**
 * Class Distributor_Schema
 *
 * @since 1.0.0
 */
class Standard implements Distributor_Schema {
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
	 * @param array $data Data to filter.
	 * @return array Filtered data.
	 *
	 * @since 1.0.0
	 */
	public function filter_data( array $data ) : array {
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
