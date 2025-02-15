<?php
/**
 * Wrapper class for Payment functions.
 *
 * @category Class
 * @package  Enon\Models\Edd
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Edd;

use Enon\Models\Enon\Energieausweis;
use WPENON\Model\Energieausweis as Energieausweis_Old; // Old and have to be removed later.
use WPENON\Util\PaymentMeta as Payment_Meta_Old;

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
	 * Get the payment date.
	 * 
	 * @return string Payment date.
	 * 
	 * @since 2022-02-08
	 */
	public function get_date() {
		return get_the_date( 'd.m.Y', $this->id );
	}

	/**
	 * Get energieausweis id.
	 *
	 * @since 1.0.0
	 *
	 * @return int|null Energieausweis id.
	 */
	public function get_energieausweis_id() : ?int {
		$id = null;

		$payment = new \Edd_Payment( $this->id );
		$cart_details = $payment->cart_details;

		if(empty($cart_details)){
			return $id;
		}

		$item = array_shift( $cart_details );
		$id = $item['id'];

		return $id;
	}

	/**
	 * Get discount code.
	 * 
	 * @return string|null Discount code.
	 */
	public function get_discount_code() {
		$payment = new \Edd_Payment( $this->id );
		$discounts = $payment->discounts;

		if(empty($discounts)){
			return null;
		}

		if( is_array( $discounts) ) {
			$code = array_shift( $discounts );
		} else {
			$code = $discounts;
		}

		return $code;
	}

	/**
	 * Has discount code.
	 * 
	 * @return bool True if has discount code.
	 */
	public function has_discount_code() {
		$code = $this->get_discount_code();
		return $code !== 'none';
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
		return new Energieausweis( $energieausweis_id );
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
	 * @return \EDD_Customer $customer Edd customer object.
	 *
	 * @since 1.0.0
	 */
	public function get_customer() {
		$customer_id = $this->get_customer_id();
		$customer    = new \EDD_Customer( $customer_id );

		return $customer;
	}

	/**
	 * Get seller meta.
	 *
	 * @return array Seller meta.
	 *
	 * @since 1.0.0
	 */
	public function get_seller_meta() {
		return Payment_Meta_Old::instance()->getSellerMeta( $this->id );
	}

	/**
	 * Get amount.
	 *
	 * @return int Amount of payment.
	 *
	 * @since 1.0.0
	 */
	public function get_amount() {
		return edd_get_payment_amount( $this->id );
	}

	/**
	 * Get payment status.
	 *
	 * @return string Payment status.
	 *
	 * @since 1.0.0
	 */
	public function get_status() {
		return edd_get_payment_status( $this->id, false );
	}
}
