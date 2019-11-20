<?php

namespace Enon\Whitelabel;

use Enon\Config\TaskLoader;
use Enon\Exceptions\Exception;
use Enon\Whitelabel\WordPress\Core;
use Enon\Whitelabel\WordPress\Enon;
use Enon\Whitelabel\WordPress\EnonEmailConfirmation;
use Enon\Whitelabel\WordPress\EnonEmailOrderConfirmation;
use Enon\Whitelabel\WordPress\PluginAffiliateWP;
use Enon\Whitelabel\WordPress\PluginEdd;
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
			$reseller = new Reseller( $token, $this->logger() );
		} catch ( Exception $exception ) {
			$this->logger()->error( sprintf( 'Interrupting: %s', $exception->getMessage() ) );
		}

		$this->addTask( Core::class, $this->logger() );
		$this->addTask( PluginAffiliateWP::class, $reseller, $this->logger() );
		$this->addTask( PluginEdd::class, $reseller, $this->logger() );
		$this->addTask( Enon::class, $this->logger() );
		$this->addTask( EnonEmailConfirmation::class, $this->logger() );
		$this->addTask( EnonEmailOrderConfirmation::class, $this->logger() );
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
	public function get_reseller_token( Energieausweis $ea ) {
		$token = get_post_meta( $ea->id, 'whitelabel_token', true );

		if( empty( $token ) ) {
			return false;
		}

		return $token;
	}
}

