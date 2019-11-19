<?php

namespace Enon\Whitelabel;

use Awsm\WPWrapper\BuildingPlans\Filters;
use Awsm\WPWrapper\BuildingPlans\Task;

use Enon\Traits\Logger as LoggerTrait;
use Enon\Logger;

/**
 * Class PluginEdd.
 *
 * @since 1.0.0
 *
 * @package Enon\Whitelabel
 */
class PluginEdd implements Task, Filters {
	use LoggerTrait;

	/**
	 * Customer object.
	 *
	 * @since 1.0.0
	 *
	 * @var Customer Customer object.
	 */
	private $customer;

	/**
	 * Loading Plugin scripts.
	 *
	 * @since 1.0.0
	 *
	 * @param Customer $customer Customer object.
	 * @param Logger   $logger   Logger object.
	 */
	public function __construct( Customer $customer, Logger $logger ) {
		$this->customer = $customer;
		$this->logger = $logger;
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|void
	 */
	public function run() {
		$this->addFilters();
	}

	/**
	 * Adding fiilters.
	 *
	 * @since 1.0.0
	 */
	public function addFilters() {
		add_filter( 'edd_get_checkout_uri', array( $this, 'filterIframeUrl' ), 100 );
		add_filter( 'edd_get_success_page_uri', array( $this, 'filterIframeUrl' ), 100 );
		add_filter( 'edd_get_failed_transaction_uri', array( $this, 'filterIframeUrl' ), 100 );
		add_filter( 'edd_remove_fee_url', array( $this, 'filterIframeUrl' ), 100 );
	}

	/**
	 * Filtering iframe URL.
	 *
	 * @param mixed $url Extra query args to add to the URI.
	 *
	 * @return string
	 */
	public function filterIframeUrl( $url ) {
		$args = array(
			'iframe'       => true,
			'iframe_token' => $this->customer->getToken()
		);

		return add_query_arg( $args, $url );
	}
}
