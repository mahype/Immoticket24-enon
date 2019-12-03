<?php

namespace Enon\Reseller\Tasks\Enon;

use Awsm\WPWrapper\BuildingPlans\Filters;
use Awsm\WPWrapper\BuildingPlans\Task;
use Enon\Logger;
use Enon\Traits\Logger as LoggerTrait;
use Enon\Reseller\Models\Reseller;

/**
 * Class EnonEmailOrderConfirmation.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class TaskEmailOrderConfirmation implements Task, Filters {
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
		$this->addFilters();
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function addFilters() {
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
	public function filterToAddress() {
		return $this->reseller->data()->getContactEmail();
	}
}
