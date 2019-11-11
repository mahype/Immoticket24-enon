<?php

namespace Enon\Whitelabel;

use Awsm\WP_Plugin\Building_Plans\Hooks_Actions;
use Awsm\WP_Plugin\Building_Plans\Hooks_Filters;
use Awsm\WP_Plugin\Loaders\Hooks_Loader;
use Awsm\WP_Plugin\Loaders\Loader;
use Enon\Exception;

class PluginAffiliateWP implements Hooks_Actions {
	use Loader {
		load as load_definetly;
	}
	use Hooks_Loader;

	/**
	 * Customer object
	 *
	 * @since 1.1.0
	 *
	 * @var Customer
	 */
	private static $customer;

	/**
	 * Loading Plugin scripts.
	 *
	 * @since 1.0.0
	 *
	 * @param Customer $customer
	 */
	public static function load( $customer ) {
		self::$customer = $customer;
		self::load_definetly();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'template_redirect', array( __CLASS__, 'set_afiilliatewp_referal' ), -10000, 0 );
	}

	/**
	 * Adjusting referal.
	 *
	 * @since 1.0.0
	 */
	public function set_afiilliatewp_referal() {
		if ( ! self::is_activated() ) {
			return;
		}

		$email = self::$customer->get_email();

		if( ! $email ) {
			wp_die( 'Referer can not be assigned.' );
		}

		try {
			$affiliate_id = self::get_affiliate_id_by_email( $email );
		} catch ( Exception $e ) {
			wp_die( 'Ursprunng des Referals kann nicht zugeordnet werden.' );
		}

		if ( ! $affiliate_id ) {
			return;
		}

		affiliate_wp()->tracking->referral = $affiliate_id;
	}

	public function is_activated() {
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
	 * @throws Exception
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
