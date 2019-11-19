<?php
/**
 * This file contains Energieausweis-IFrame functionality.
 *
 * @package immoticketenergieausweis
 */

namespace Enon\Whitelabel;


use Awsm\WPWrapper\BuildingPlans\Task;
use Awsm\WPWrapper\Tasks\TaskRunner;

use Enon\Whitelabel\Plugins\Wpenon;
use WPENON\Model\Energieausweis;

use Enon\Logger;

/**
 * Whitelabel solution.
 */
class Loader implements Task{
	use TaskRunner;

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
	}

	/**
	 * Loading Scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$token = ( new Token() )->getToken();

		if( empty( $token ) ) {
			return;
		}

		$customer = new Customer( $token );

		$this->addTask(WordPress::class, $this->logger );
		$this->addTask(PluginAffiliateWP::class, $customer, $this->logger );
		$this->addTask(PluginEdd::class, $this->logger );
		$this->addTask(Wpenon::class, $this->logger );

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

