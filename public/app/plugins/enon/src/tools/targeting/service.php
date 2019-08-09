<?php

namespace EA\Tools\Targeting;

/**
 * Targetting service class.
 *
 * This class provides basic functionality for loading scripts to footer and at the end of purchasing an Energieausweis.
 *
 * @since 1.0.0
 */
abstract class Service {
	/**
	 * Loading nesesary properties and functions.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->load_hooks();
	}

	/**
	 * Load targeting scripts into hooks.
	 *
	 * @since 1.0.0
	 */
	private function load_hooks() {
		add_action( 'wp_head', array( $this, 'base_script' ), 1 );
		add_action( 'edd_payment_receipt_after_table', array( $this, 'load_conversions' ), 10, 2 );
	}

	/**
	 * Loads the base script on every site to the header.
	 *
	 * @param $payment
	 * @param $edd_receipt_args
	 *
	 * @since 1.0.0
	 */
	public function load_conversions() {
		if ( ! isset( $_SESSION['edd']['edd_purchase'] ) ) {
			return;
		}

		$purchase          = json_decode( $_SESSION['edd']['edd_purchase'] );
		$energieausweis_id = $purchase->downloads[0]->id;
		$type              = get_post_meta( $energieausweis_id, 'wpenon_type', true );

		if ( 'bw' === $type ) {
			$this->conversion_bedarfsausweis();
		}

		if ( 'vw' === $type ) {
			$this->conversion_verbrauchsausweis();
		}
	}

	/**
	 * Loads the base script on every site to the header.
	 *
	 * @since 1.0.0
	 */
	abstract public function base_script();

	/**
	 * Loads the scripts after a bedarfsausweis had a conversion.
	 *
	 * @since 1.0.0
	 */
	abstract protected function conversion_bedarfsausweis();

	/**
	 * Loads the scripts after a verbrauchsausweis had a conversion.
	 *
	 * @since 1.0.0
	 */
	abstract protected function conversion_verbrauchsausweis();
}
