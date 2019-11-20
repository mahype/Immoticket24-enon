<?php

namespace Enon\Whitelabel\WordPress;

use Awsm\WPWrapper\BuildingPlans\Task;
use Enon\Logger;
use Enon\Traits\Logger as LoggerTrait;
use Enon\Whitelabel\Reseller;

/**
 * Class EnonEmailOrderConfirmation.
 *
 * @since 1.0.0
 *
 * @package Enon\Whitelabel\WordPress
 */
class EnonEmailOrderConfirmation implements Task
{
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
		add_filter( 'wpenon_order_confirmation_to_address', [ $this, 'filterToAddress' ] );
	}

	/**
	 * Returning token from email address.
	 *
	 * @param string $email
	 *
	 * @return string Tokens from email address.
	 */
	public function filterToAddress() {
		$email = $this->reseller->getEmail();
		return $email;
	}
}
