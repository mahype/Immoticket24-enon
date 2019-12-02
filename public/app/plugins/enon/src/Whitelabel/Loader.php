<?php

namespace Enon\Whitelabel;

use Awsm\WPWrapper\BuildingPlans\Actions;
use Enon\TaskLoader;
use Enon\Exceptions\Exception;
use Enon\Whitelabel\WordPress\Core\CPTReseller;
use Enon\Whitelabel\WordPress\Core\Frontend;
use Enon\Whitelabel\WordPress\Enon\EmailConfirmation;
use Enon\Whitelabel\WordPress\Enon\EmailOrderConfirmation;
use Enon\Whitelabel\WordPress\Enon\Enon;
use Enon\Whitelabel\WordPress\Enon\SendEnergieausweis;
use Enon\Whitelabel\WordPress\Plugins\ACF;
use Enon\Whitelabel\WordPress\Plugins\AffiliateWP;
use Enon\Whitelabel\WordPress\Plugins\Edd;

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
		$this->addTask( CPTReseller::class );
		$this->addTask( ACF::class, $this->logger() );

		if( is_admin() ) {
			$this->runAdminTasks();
		} else {
		    $this->runFrontendTasks();
		}
	}

	/**
	 * Running admin tasks.
	 *
	 * @since 1.0.0
	 */
	public function runAdminTasks()
	{
		try {
			$resellerData = new ResellerData();
			$reseller = new Reseller( $resellerData,  $this->logger() );
		} catch ( Exception $exception ) {
			$this->logger()->error( sprintf( $exception->getMessage() ) );
		}

		$this->addTask( SendEnergieausweis::class, $reseller, $this->logger() );
		$this->runTasks();
	}

	/**
	 * Running frontend tasks.
	 *
	 * @since 1.0.0
	 */
	public function runFrontendTasks() {
		$token = new Token();

		// No token, no action
		if( empty( $token->get() ) ) {
			return;
		}

		try {
			$resellerData = new ResellerData( $token );
			$reseller = new Reseller( $resellerData,  $this->logger() );
		} catch ( Exception $exception ) {
			$this->logger()->error( sprintf( $exception->getMessage() ) );
		}

		$this->addTask( Frontend::class );
		$this->addTask( Enon::class, $reseller, $this->logger() );
		// $this->addTask( EmailConfirmation::class, $reseller, $this->logger() );
		// $this->addTask( EmailOrderConfirmation::class, $reseller, $this->logger() );
		// $this->addTask( SendEnergieausweis::class, $reseller, $this->logger() );
		// $this->addTask( AffiliateWP::class, $reseller, $this->logger() );
		// $this->addTask( Edd::class, $reseller, $this->logger() );

		$this->runTasks();;
	}
}

