<?php

namespace Enon\Whitelabel;

use Awsm\WP_Plugin\Building_Plans\Hooks_Actions;
use Awsm\WP_Plugin\Loaders\Hooks_Loader;
use Awsm\WP_Plugin\Loaders\Loader;
use Enon\Exception;
use Monolog\Logger;

/**
 * Class PluginAffiliateWP
 *
 * @since 1.0.0
 *
 * @package Enon\Whitelabel
 */
class PluginAffiliateWP implements Hooks_Actions {
	use Loader {
		load as load_definetly;
	}
	use Hooks_Loader;

	/**
	 * Customer object.
	 *
	 * @since 1.1.0
	 *
	 * @var Customer
	 */
	private $customer;

	/**
	 * Logger object.
	 *
	 * @since 1.1.0
	 *
	 * @var Customer
	 */
	private $logger;

	/**
	 * Loading Plugin scripts.
	 *
	 * @since 1.0.0
	 *
	 * @param Customer $customer Customer object.
	 * @param Logger   $logger Logger object.
	 */
	public function load( Customer $customer, Logger $logger ) {
		$this->customer = $customer;
		self::load_definetly();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'template_redirect', array( $this, 'set_affiliatewp_referal' ), -10000, 0 );
	}

	/**
	 * Adjusting referal.
	 *
	 * @since 1.0.0
	 */
	public function set_affiliatewp_referal() {
		if ( ! self::is_activated() ) {
			return;
		}

		$email = $this->customer->get_email();

		if ( ! $email ) {
			wp_die( 'Referer can not be assigned.' );
		}

		try {
			$affiliate_id = self::get_affiliate_id_by_email( $email );
		} catch ( Exception $e ) {
			wp_die( 'Could not get afilliate id.' );
		}

		if ( ! isset( $affiliate_id ) ) {
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
	public static function is_activated() {
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
	 * @return string Affilate id of current token.
	 *
	 * @throws Exception If function for getting affiliate id not exists.
	 */
	public static function get_affiliate_id_by_email( $email ) {
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
