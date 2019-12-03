<?php

namespace Enon\Whitelabel\WordPress\Plugins;

use Awsm\WPWrapper\BuildingPlans\Actions;
use Awsm\WPWrapper\BuildingPlans\Task;

use Enon\Exceptions\Exception;
use Enon\Traits\Logger as LoggerTrait;
use Enon\Logger;
use Enon\Whitelabel\Reseller;

/**
 * Class PluginAffiliateWP
 *
 * @since 1.0.0
 *
 * @package Enon\Whitelabel\WordPress
 */
class TaskAffiliateWP implements Task, Actions
{
	use LoggerTrait;

	/**
	 * Reseller object.
	 *
	 * @since 1.1.0
	 *
	 * @var Reseller
	 */
	private $reseller;

	/**
	 * AffiliateWP constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Reseller $reseller Reseller object.
	 * @param Logger $logger Logger object.
	 */
	public function __construct( Reseller $reseller,  Logger $logger )
	{
		$this->reseller = $reseller;
		$this->logger = $logger;
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run()
	{
		$this->addActions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function addActions()
	{
		add_action( 'template_redirect', array( $this, 'setAffiliatewpReferal' ), -10000, 0 );
	}

	/**
	 * Adjusting referal.
	 *
	 * @since 1.0.0
	 */
	public function setAffiliatewpReferal()
	{
		if ( ! self::isActivated() )
		{
			$this->logger->alert('Affiliate WP seems not to be activated.');
			return;
		}

		$affiliateId = $this->reseller->data()->getAffiliateId();

		if( empty( $affiliateId ) ) {
			$this->logger->error( 'Could not set affiliate id.' );
			return;
		}

		affiliate_wp()->tracking->referral = $affiliateId;
	}

	/**
	 * Is activated.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Is Affiliate WP activated.
	 */
	public static function isActivated() {
		if ( ! function_exists( 'affiliate_wp' ) ) {
			return false;
		}

		return true;
	}
}
