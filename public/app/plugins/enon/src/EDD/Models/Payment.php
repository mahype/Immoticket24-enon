<?php
/**
 * Wrapper class for Payment functions.
 *
 * @category Class
 * @package  Enon\Edd\Models
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Edd\Models;

use Enon\Models\Enon\Energieausweis;
use WPENON\Model\Energieausweis as Energieausweis_Old; // Old and have to be removed later.

/**
 * Class Edd_Payment
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Taks\Plugins
 */
class Payment {
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
	 * Get payment id.
	 *
	 * @return int Payment id.
	 *
	 * @since 1.0.0
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get the payment title.
	 *
	 * @return string Payment title.
	 *
	 * @since 1.0.0
	 */
	public function get_title() {
		return get_the_title( $this->id );
	}

	/**
	 * Get energieausweis id.
	 *
	 * @since 1.0.0
	 *
	 * @return int Energieausweis id.
	 */
	public function get_energieausweis_id() : int {
		$payment_meta = edd_get_payment_meta( $this->id );
		$item = array_shift( $payment_meta['cart_details'] );

		return $item['id'];
	}

	/**
	 * Get energieausweis.
	 *
	 * @return Energieausweis Energieausweis object.
	 *
	 * @since 1.0.0
	 */
	public function get_energieausweis() : Energieausweis {
		$energieausweis_id = $this->get_energieausweis_id();
		return Energieausweis( energieausweis_id );
	}

	/**
	 * Get old energieausweis object.
	 *
	 * @return Energieausweis_Old Energieausweis object.
	 *
	 * @since 1.0.0
	 */
	public function get_energieausweis_old() : Energieausweis_Old {
		$energieausweis_id = $this->get_energieausweis_id();
		return new Energieausweis_Old( $energieausweis_id );
	}

	/**
	 * Returns customer id.
	 *
	 * @param int $payment_id Payment id.
	 *
	 * @return id $customer Edd customer object.
	 *
	 * @since 1.0.0
	 */
	public function get_customer_id() {
		$customer_id = edd_get_payment_customer_id( $this->id );

		return $customer_id;
	}

	/**
	 * Returns customer.
	 *
	 * @param int $payment_id Payment id.
	 *
	 * @return \EDD_Customer $customer Edd customer object.
	 *
	 * @since 1.0.0
	 */
	public function get_customer() {
		$customer_id = $this->get_customer_id();
		$customer    = new \EDD_Customer( $customer_id );

		return $customer;
	}
}
