<?php
/**
 * This file contains Energieausweis-IFrame functionality.
 *
 * @package immoticketenergieausweis
 */

namespace Enon\Whitelabel;

use Awsm\WP_Plugin\Building_Plans\Hooks_Actions;
use Awsm\WP_Plugin\Building_Plans\Hooks_Filters;
use Awsm\WP_Plugin\Building_Plans\Service;
use Awsm\WP_Plugin\Loaders\Hooks_Loader;
use Awsm\WP_Plugin\Loaders\Loader;

use Awsm\WPWrapper\BuildingPlans\Actions;
use Awsm\WPWrapper\BuildingPlans\Filters;
use Awsm\WPWrapper\BuildingPlans\Task;
use WPENON\Model\Energieausweis;

use Enon\Exceptions\Enon_Exception;
use Monolog\Logger;

/**
 * Whitelabel solution.
 */
class WhitelabelLoader implements Actions, Filters, Task {
	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->addFilters();
	}

	/**
	 * Customer Object.
	 *
	 * @since 1.0.0
	 *
	 * @var Customer
	 */
	private $customer;

	/**
	 * Customer Token.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $token;

	/**
	 * Logger.
	 *
	 * @since 1.0.0
	 *
	 * @var Logger
	 */
	private $logger;

	/**
	 * WhitelabelLoader constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Logger $logger Logger object.
	 */
	public function __construct( Logger $logger ) {
		$this->logger = $logger;
		$this->setup();
	}

	/**
	 * Loading Scripts.
	 *
	 * @since 1.0.0
	 */
	public function setup() {
		$this->setup_token();
		$this->setup_customer();
		$this->setup_affiliate();
		$this->setup_shop();
		$this->setup_wp();
	}

	/**
	 * Setting up token.
	 *
	 * @since 1.0.0
	 *
	 * @throws Enon_Exception
	 */
	public function setup_token() {
		$this->token = $this->get_token_by_request();
	}

	/**
	 * Checks if current request is whitelabeled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if this is a whitelabel request
	 *
	 * @throws Enon_Exception If there was no iframe token.
	 */
	public function get_token_by_request() {
		if ( ! isset( $_REQUEST['iframe_token'] ) ) {
			throw new Enon_Exception( 'Could not get token from request.' );
		}

		$token = sanitize_text_field( wp_unslash( $_REQUEST['iframe_token'] ) );

		return $token;
	}

	/**
	 * Returns iframe token.
	 *
	 * @since 1.0.0
	 *
	 * @return string Iframe token.
	 */
	public function get_token() {
		return $this->token;
	}

	/**
	 * Setting customer.
	 *
	 * @since 1.0.0
	 *
	 * @param string $customer_token Customer token.
	 */
	private function setup_customer( $customer_token ) {
		try {
			$this->customer = new Customer( $customer_token );
		} catch ( Enon_Exception $e ) {
			$this->logger->error( 'Could not set Customer', array( 'exception' => $e ) );
		}
	}

	/**
	 * Settup affilliate.
	 *
	 * @since 1.0.0
	 */
	private function setup_affiliate() {
		PluginAffiliateWP::load( $this->customer );
	}

	/**
	 * Setup shop.
	 *
	 * @since 1.0.0
	 */
	private function setup_shop() {
		PluginEdd::load( $this->customer );
	}

	/**
	 * Initializing Actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'wpenon_confirmation_start', array( $this, 'setup_emails' ) );
	}

	/**
	 * Initializing Filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'plugins_loaded', array( $this, 'cleanup_wp' ) );
		add_filter( 'template_include', array( $this, 'filter_iframe_template' ) );
		add_filter( 'wpenon_filter_url', array( $this, 'filter_iframe_url' ), 100 );


		add_filter( 'wpenon_payment_success_url', array( $this, 'filter_payment_success_url' ) );
		add_filter( 'wpenon_payment_failed_url', array( $this, 'filter_payment_failed_url' ) );
	}

	/**
	 * Setting up Emails
	 *
	 * @since 1.0.0
	 *
	 * @param Energieausweis $ea Energieausweis object.
	 */
	public function setup_emails( Energieausweis $ea ) {
		$token = get_post_meta( $ea->id, 'whitelabel_token', true );

		if ( empty( $token ) ) {
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
	public function filter_iframe_template() {
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

