<?php

namespace Enon\Whitelabel\Plugins;

use Awsm\WPWrapper\BuildingPlans\Task;
use Enon\Traits\Logger as LoggerTrait;
use Enon\Logger;
use Enon\Whitelabel\Customer;

/**
 * Class Wpenon.
 *
 * Running WordPress scripts
 *
 * @package Enon\Whitelabel
 */
class Wpenon implements Task {
	use LoggerTrait;

	/**
	 * Customer object.
	 *
	 * @since 1.0.0
	 * @var Customer;
	 */
	private $customer;

	/**
	 * Wpenon constructor.
	 *
	 * @param Customer $customer
	 * @param Logger $logger
	 */
	public function __construct( Customer $customer, Logger $logger )
	{
		$this->customer = $customer;
		$this->logger = $logger;
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		add_filter( 'wpenon_payment_success_url', [ $this, 'filterPaymentSuccessUrl' ] );
		add_filter( 'wpenon_payment_failed_url', [ $this, 'filterPaymentFailedUrl' ] );
	}

	/**
	 * Filtering payment success URL.
	 *
	 * @param string $old_url Old url.
	 *
	 * @return string
	 */
	public function filterPaymentSuccessUrl( $old_url ) {
		$url = $this->customer->getPaymentSuccesfulUrl();

		if ( empty( $url ) ) {
			$payment_successful_page = immoticketenergieausweis_get_option( 'it-theme', 'page_for_successful_payment' );

			if ( empty( $payment_successful_page ) ) {
				return $old_url;
			}

			$url = get_permalink( $payment_successful_page );
		}

		$url = $this->customer->getVerfiedUrl( $url );

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
		$url = $this->customer->getPaymentFailedUrl();

		if ( empty( $url ) ) {
			$payment_failed_page = immoticketenergieausweis_get_option( 'it-theme', 'page_for_failed_payment' );

			if ( empty( $payment_failed_page ) ) {
				return $old_url;
			}

			$url = get_permalink( $payment_failed_page );
		}

		$url = $this->customer->getVerfiedUrl( $url );

		return $url;
	}
}
