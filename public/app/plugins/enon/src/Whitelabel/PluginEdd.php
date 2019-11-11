<?php

namespace Enon\Whitelabel;

use Awsm\WP_Plugin\Building_Plans\Hooks_Filters;
use Awsm\WP_Plugin\Loaders\Hooks_Loader;
use Awsm\WP_Plugin\Loaders\Loader;

class PluginEdd implements Hooks_Filters {
	use Loader {
		load as load_definetly;
	}
	use Hooks_Loader;

	/**
	 * Customer object.
	 *
	 * @since 1.0.0
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
	}

	/**
	 * Adding fiilters.
	 *
	 * @since 1.0.0
	 */
	public static function add_filters() {
		add_filter( 'edd_get_checkout_uri', array( __CLASS__, 'filter_iframe_url'), 100 );
		add_filter( 'edd_get_success_page_uri', array( __CLASS__, 'filter_iframe_url'), 100 );
		add_filter( 'edd_get_failed_transaction_uri', array( __CLASS__, 'filter_iframe_url'), 100 );
		add_filter( 'edd_remove_fee_url', array( __CLASS__, 'filter_iframe_url'), 100 );
	}

	/**
	 * Filtering iframe URL.
	 *
	 * @param mixed $url Extra query args to add to the URI.
	 *
	 * @return string
	 */
	public static function filter_iframe_url( $url ) {
		$args = array(
			'iframe'       => true,
			'iframe_token' => self::$customer->get_token(),
		);

		return add_query_arg( $args, $url );
	}
}
