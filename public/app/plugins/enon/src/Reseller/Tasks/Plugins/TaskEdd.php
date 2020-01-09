<?php

namespace Enon\Reseller\Tasks\Plugins;

use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;

use Enon\Traits\Logger as Logger_Trait;
use Enon\Logger;
use Enon\Reseller\Models\Reseller;

/**
 * Class PluginEdd.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class TaskEdd implements Task, Filters {
	use Logger_Trait;

	/**
	 * Reseller object.
	 *
	 * @since 1.0.0
	 *
	 * @var Reseller Reseller object.
	 */
	private $reseller;

	/**
	 * Loading Plugin scripts.
	 *
	 * @since 1.0.0
	 *
	 * @param Reseller $reseller Reseller object.
	 * @param Logger   $logger   Logger object.
	 */
	public function __construct( Reseller $reseller, Logger $logger ) {
		$this->reseller = $reseller;
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
		$this->add_filters();
	}

	/**
	 * Adding fiilters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
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
		return $this->reseller->createIframeUrl( $url );
	}
}
