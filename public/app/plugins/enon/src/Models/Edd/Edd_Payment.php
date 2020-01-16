<?php
/**
 * Wrapper class for EDD Payment functions.
 *
 * @category Class
 * @package  Enon\Models\Edd;
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Edd;

/**
 * Class Edd_Payment
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Taks\Plugins
 */
class Edd_Payment {
	/**
	 * Payment id.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $id;

	/**
	 * Edd_Payment constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id Payment id.
	 */
	public function __construct( $id ) {
		$this->id = $id;
	}

	/**
	 * Get energieausweis id.
	 *
	 * @since 1.0.0
	 *
	 * @return int Energieausweis id.
	 */
	public function get_energieausweis_id() {
		$payment_meta = edd_get_payment_meta( $this->id );
		$item = array_shift( $payment_meta['cart_details'] );

		return $item['id'];
	}
}
