<?php
/**
 * Distributor schema interface.
 *
 * @category Interface
 * @package  Enon_Reseller\Models\Transfer
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Api\Out\Distributor_Schemas;

/**
 * Interface Distributor_Schema_Interface
 *
 * @since 1.0.0
 */
interface Distributor_Schema_Interface {
	/**
	 * Checks if data have to be sent.
	 *
	 * @return bool True if check passed.
	 *
	 * @since 1.0.0
	 */
	public function check() : bool;

	/**
	 * Filter the data which will be sent.
	 *
	 * @param array              $data               Data array of energy certificate.
	 *
	 * @return array Filtered data.
	 *
	 * @since 1.0.0
	 */
	public function filter_data( array $data ) : array;

    /**
	 * Set as sent.
	 *
	 * @return int|bool The new meta field ID if a field with the given key didn't exist and was
     *                  therefore added, true on successful update, false on failure.
	 *
	 * @since 1.0.0
	 */
	public function set_sent();

	/**
	 * Get endpoint url.
	 *
	 * @return string Endpoint url.
	 *
	 * @since 1.0.0
	 */
	public function get_endpoint() : string;
}
