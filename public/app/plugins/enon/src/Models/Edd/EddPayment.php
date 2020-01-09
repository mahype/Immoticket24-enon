<?php

namespace Enon\Models\Edd;

/**
 * Class EddPayment
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Taks\Plugins
 */
class EddPayment {
	/**
	 * Payment id.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $id;

	/**
	 * EddPayment constructor.
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
	public function getEnergieausweisId() {
		 $payment_meta = edd_get_payment_meta( $this->id );
		$item = array_shift( $payment_meta['cart_details'] );

		return $item['id'];
	}
}
