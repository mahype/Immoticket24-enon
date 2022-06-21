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

namespace Enon_Reseller\Models\Api\Out;

/**
 * Interface Distributor_Schema_Interface
 *
 * @since 1.0.0
 */
interface Sender_Interface {
	/**
	 * Checks if data have to be sent.
	 *
	 * @return bool True if check passed.
	 *
	 * @since 1.0.0
	 */
	public function check() : bool;

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
	 * Checks if data was sent.
	 *
	 * @return bool True if it was sent, false if not.
	 *
	 * @since 1.0.0
	 */
	public function is_sent() : bool;

	/**
	 * Send data
	 *
	 * @return bool True if it was sent, false if not.
	 *
	 * @since 1.0.0
	 */
	public function send() : bool;
}
