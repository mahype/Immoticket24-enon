<?php

namespace Enon\Whitelabel;

use Enon\Config\TaskLoader;
use Enon\Exceptions\Exception;
use Enon\Whitelabel\Plugins\Wpenon;
use WPENON\Model\Energieausweis;

/**
 * Whitelabel loader.
 *
 * @package Enon\Config
 */
class Loader extends TaskLoader {
	/**
	 * Loading Scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$token = new Token();

		// No token, no action
		if( empty( $token->get() ) ) {
			return;
		}

		try {
			$customer = new Customer( $token, $this->logger() );
		} catch ( Exception $exception ) {
			$this->logger()->error( sprintf( 'Interrupting: %s', $exception->getMessage() ) );
		}

		$this->addTask(WordPress::class, $this->logger() );
		$this->addTask(PluginAffiliateWP::class, $customer, $this->logger() );
		$this->addTask(PluginEdd::class, $customer, $this->logger() );
		$this->addTask(Wpenon::class, $this->logger() );
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

