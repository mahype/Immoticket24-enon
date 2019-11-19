<?php

namespace Enon\Whitelabel;

use Awsm\WPWrapper\BuildingPlans\Task;

use Enon\Exceptions\Exception;
use Enon\Traits\Logger as LoggerTrait;
use Enon\Logger;

/**
 * Class PluginAffiliateWP
 *
 * @since 1.0.0
 *
 * @package Enon\Whitelabel
 */
class PluginAffiliateWP implements Task {
	use LoggerTrait;

	/**
	 * Customer object.
	 *
	 * @since 1.1.0
	 *
	 * @var Customer
	 */
	private $customer;

	public function __construct( Customer $customer,  Logger $logger )
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
		$this->add_actions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'template_redirect', array( $this, 'setAffiliatewpReferal' ), -10000, 0 );
	}

	/**
	 * Adjusting referal.
	 *
	 * @since 1.0.0
	 */
	public function setAffiliatewpReferal() {
		if ( ! self::isActivated() ) {
			$this->logger->alert('Affiliate WP seems not to be activated.');
			return;
		}

		$email = $this->customer->get_email();

		if ( ! $email ) {
			$this->logger->alert('Could not get customer email.');
		}

		try {
			$affiliate_id = self::getAffiliateIdByEmail( $email );
		} catch ( Exception $exception ) {
			$this->logger->alert( sprintf( 'Could not get afilliate id by email "%s" with message "%s".', $email, $exception->getMessage() ) );
		}

		if ( ! isset( $affiliate_id ) ) {
			$this->logger->alert( sprintf( 'Afilliate ins unset.' ) );
			return;
		}

		affiliate_wp()->tracking->referral = $affiliate_id;
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

	/**
	 * Get affilliate id.
	 *
	 * @since 1.0.0
	 *
	 * @param string $email Email address of customer.
	 *
	 * @return string Affilate id of current token.
	 *
	 * @throws Exception If function for getting affiliate id not exists.
	 */
	public static function getAffiliateIdByEmail( $email ) {
		if ( ! function_exists( 'affwp_get_affiliate_id' ) ) {
			throw new Exception( 'Function affwp_get_affiliate_id does not exist.' );
		}

		$user = get_user_by( 'email', $email );
		if ( ! $user ) {
			return false;
		}

		return affwp_get_affiliate_id( $user->ID );
	}
}
