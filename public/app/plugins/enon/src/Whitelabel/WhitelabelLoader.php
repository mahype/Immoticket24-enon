<?php
/**
 * This file contains Energieausweis-IFrame functionality.
 *
 * @package immoticketenergieausweis
 */

namespace Enon\Whitelabel;

use Awsm\WP_Plugin\Building_Plans\Hooks_Actions;
use Awsm\WP_Plugin\Building_Plans\Hooks_Filters;
use Awsm\WP_Plugin\Loaders\Hooks_Loader;
use Awsm\WP_Plugin\Loaders\Loader;

use WPENON\Model\Energieausweis;

use Enon\Exception;
use Monolog\Logger;



/**
 * Whitelabel solution.
 */
class WhitelabelLoader implements Hooks_Actions, Hooks_Filters {
	use Hooks_Loader, Loader {
		load as load_definetly;
	}

	/**
	 * Customer Object.
	 *
	 * @since 1.0.0
	 *
	 * @var Customer
	 */
	private static $customer;

	/**
	 * Customer Token.
	 *
	 * @since 1.0.0
	 *
	 * @var Customer
	 */
	private static $token;

	/**
	 * Logger.
	 *
	 * @since 1.0.0
	 *
	 * @var Logger
	 */
	private static $logger;

	/**
	 * Loading Scripts.
	 *
	 * @since 1.0.0
	 */
	public static function load( Logger $logger ) {
		if( ! self::fetch_request() ) {
			return;
		}

		self::$logger = $logger;

		self::set_customer( self::$token );
		self::setup_affilliate();
		self::setup_shop();

		self::load_definetly();
	}

	/**
	 * Checks if current request is whitelabeled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if this is a whitelabel request
	 */
	public static function fetch_request() {
		if ( empty( $_REQUEST['iframe_token'] ) ) {
			return false;
		}

		self::$token = htmlentities( wp_unslash( $_REQUEST['iframe_token'] ) );

		return true;
	}

	/**
	 * Setting customer.
	 *
	 * @since 1.0.0
	 *
	 * @param $customer_token
	 */
	private static function set_customer( $customer_token ) {
		try{
			self::$customer = new Customer( $customer_token );
		} catch ( Exception $e ) {
			self::$logger->error( 'Could not set Customer', array( 'exception' => $e ) );
		}
	}

	/**
	 * Settup affilliate.
	 *
	 * @since 1.0.0
	 */
	private static function setup_affilliate() {
		PluginAffiliateWP::load( self::$customer );
	}

	/**
	 * Setup shop.
	 *
	 * @since 1.0.0
	 */
	private static function setup_shop() {
		PluginEdd::load( self::$customer );
	}

	/**
	 * Initializing Actions.
	 *
	 * @since 1.0.0
	 */
	public static function add_actions() {
		add_action( 'wpenon_confirmation_start', array( __CLASS__, 'setup_emails' ) );
	}

	/**
	 * Initializing Filters.
	 *
	 * @since 1.0.0
	 */
	public static function add_filters() {
		add_filter( 'plugins_loaded', array( __CLASS__, 'cleanup_wp'  ) );
		add_filter( 'template_include', array( __CLASS__, 'load_iframe_template'  ) );
		add_filter( 'wpenon_filter_url', array( __CLASS__, 'filter_iframe_url'), 100 );


		add_filter( 'wpenon_payment_success_url', array( __CLASS__, 'filter_payment_success_url' ) );
		add_filter( 'wpenon_payment_failed_url', array( __CLASS__, 'filter_payment_failed_url' ) );
	}

	/**
	 * Setting up Emails
	 *
	 * @since 1.0.0
	 *
	 * @param Energieausweis $ea Energieausweis object.
	 */
	public static function setup_emails( Energieausweis $ea ) {
		$token = get_post_meta( $ea->id, 'whitelabel_token', true );

		if( empty( $token ) ) {
			return;
		}

		// $this->confirmation_email = new EA_Whitelabel_Confirmation_Email( $this );
		// $this->order_confirmation_email = new EA_Whitelabel_Order_Confirmation_Email( $this );
	}


	/**
	 * Loading iframe.
	 *
	 * @param string $template The path of the template to include.
	 *
	 * @return string $template The path of the template to include.
	 */
	public static function load_iframe_template() {
		return locate_template( array( 'energieausweis-iframe.php' ) );
	}


	/**
	 * Cleanung scripts which must not start in whitelabel.
	 *
	 * @since 1.0.0
	 */
	public static function cleanup_wp () {
		add_action( 'wp_head', 'wp_no_robots' );
		remove_action( 'wp_footer', 'immoticketenergieausweis_trusted_shops_badge_script', 100 );
	}

	/**
	 * Setting information that Energieausweis was registered white labeled.
	 *
	 * @since 1.0.0
	 *
	 * @param Energieausweis $energieausweis Energieausweis object.
	 */
	public function save_whitelabel_token( Energieausweis $ea ) {
		if( self::$token === null ) {
			return;
		}

		update_post_meta( $ea->id, 'whitelabel_token', self::$token );
	}

	/**
	 * Checks if Energieausweis was white labeled.
	 *
	 * @param Energieausweis $energieausweis Energieausweis object.
	 *
	 * @return bool True if Energieausweis was created white labeled.
	 */
	public function get_customer_token( Energieausweis $ea ) {
		$token = get_post_meta( $ea->id, 'whitelabel_token', true );

		if( empty( $token ) ) {
			return false;
		}

		return $token;
	}
}

