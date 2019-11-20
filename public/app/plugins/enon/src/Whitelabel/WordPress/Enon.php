<?php

namespace Enon\Whitelabel\WordPress;

use Awsm\WPWrapper\BuildingPlans\Task;
use Enon\Traits\Logger as LoggerTrait;
use Enon\Logger;
use Enon\Whitelabel\Reseller;

/**
 * Class Wpenon.
 *
 * Running WordPress scripts
 *
 * @package Enon\Whitelabel\WordPress
 */
class Enon implements Task {
	use LoggerTrait;

	/**
	 * Reseller object.
	 *
	 * @since 1.0.0
	 * @var Reseller;
	 */
	private $reseller;

	/**
	 * Wpenon constructor.
	 *
	 * @param Reseller $reseller
	 * @param Logger $logger
	 */
	public function __construct( Reseller $reseller, Logger $logger )
	{
		$this->reseller = $reseller;
		$this->logger = $logger;
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		add_action( 'wpenon_confirmation_start', [ $this, 'setEnergieauseisToken' ] );

		add_filter( 'wpenon_filter_url', [ $this, 'filterIframeUrl' ] );
		add_filter( 'wpenon_payment_success_url', [ $this, 'filterPaymentSuccessUrl' ] );
		add_filter( 'wpenon_payment_failed_url', [ $this, 'filterPaymentFailedUrl' ] );
	}

	/**
	 * Updating token of energieausweis
	 *
	 * @since 1.0.0
	 *
	 * @param \WPENON\Model\Energieausweis $energieausweis Energieausweis object.
	 */
	public function updateEnergieausweisToken( $energieausweis ) {
		update_post_meta( $energieausweis->id, 'whitelabel_token', $this->reseller->getToken() );
	}

	/**
	 * Filtering iframe URL.
	 *
	 * @param mixed $url Extra query args to add to the URI.
	 *
	 * @return string
	 */
	public function filterIframeUrl( $url ) {
		return $this->reseller->createIframeUrl( $url );
	}

	/**
	 * Filtering payment success URL.
	 *
	 * @param string $old_url Old url.
	 *
	 * @return string
	 */
	public function filterPaymentSuccessUrl( $old_url ) {
		$url = $this->reseller->getPaymentSuccesfulUrl();

		if ( empty( $url ) ) {
			$payment_successful_page = immoticketenergieausweis_get_option( 'it-theme', 'page_for_successful_payment' );

			if ( empty( $payment_successful_page ) ) {
				return $old_url;
			}

			$url = get_permalink( $payment_successful_page );
		}

		$url = $this->reseller->getVerfiedUrl( $url );

		return $url;
	}

	/**
	 * Filtering payment success URL.
	 *
	 * @param string $old_url Old url.
	 *
	 * @return string
	 */
	public function filter_payment_failed_url( $old_url ) {
		$url = $this->reseller->getPaymentFailedUrl();

		if ( empty( $url ) ) {
			$payment_failed_page = immoticketenergieausweis_get_option( 'it-theme', 'page_for_failed_payment' );

			if ( empty( $payment_failed_page ) ) {
				return $old_url;
			}

			$url = get_permalink( $payment_failed_page );
		}

		$url = $this->reseller->getVerfiedUrl( $url );

		return $url;
	}
}
