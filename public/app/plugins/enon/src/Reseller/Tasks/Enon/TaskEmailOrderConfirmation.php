<?php

namespace Enon\Reseller\Tasks\Enon;

use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Enon\Logger;
use Enon\Traits\Logger as Logger_Trait;
use Enon\Reseller\Models\Reseller;

/**
 * Class EnonEmailOrderConfirmation.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class TaskEmailOrderConfirmation implements Task, Filters {
	use Logger_Trait;

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
	 */
	public function run() {
		$this->add_filters();
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'wpenon_order_confirmation_to_address', [ $this, 'filterToAddress' ] );
	}

	/**
	 * Returning token from email address.
	 *
	 * @since 1.0.0
	 *
	 * @param string $email To email address.
	 *
	 * @return string Reseller contact email address.
	 */
	public function filterToAddress( $email ) {
		$resellerContactEmail = $this->reseller->data()->getContactEmail();

		if( ! $this->reseller->data()->sendOrderToReseller() || empty( $resellerContactEmail ) ) {
			return $email;
		}
		return $resellerContactEmail;
	}
}
