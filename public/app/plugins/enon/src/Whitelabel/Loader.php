<?php

namespace Enon\Whitelabel;

use Awsm\WPWrapper\BuildingPlans\Actions;
use Enon\TaskLoader;
use Enon\Exceptions\Exception;
use Enon\Whitelabel\WordPress\Core\TaskCPTReseller;
use Enon\Whitelabel\WordPress\Core\TaskFrontend;
use Enon\Whitelabel\WordPress\Enon\TaskEmailConfirmation;
use Enon\Whitelabel\WordPress\Enon\TaskEmailOrderConfirmation;
use Enon\Whitelabel\WordPress\Enon\TaskEnon;
use Enon\Whitelabel\WordPress\Enon\TaskSendEnergieausweis;
use Enon\Whitelabel\WordPress\Plugins\TaskACF;
use Enon\Whitelabel\WordPress\Plugins\TaskAffiliateWP;
use Enon\Whitelabel\WordPress\Plugins\TaskEdd;

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
		$this->addTask( TaskCPTReseller::class );
		$this->addTask( TaskACF::class, $this->logger() );

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

		$this->addTask( TaskSendEnergieausweis::class, $reseller, $this->logger() );
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

		$this->addTask( TaskFrontend::class );
		$this->addTask( TaskEnon::class, $reseller, $this->logger() );
		$this->addTask( TaskEmailConfirmation::class, $reseller, $this->logger() );
		$this->addTask( TaskEmailOrderConfirmation::class, $reseller, $this->logger() );
		$this->addTask( TaskSendEnergieausweis::class, $reseller, $this->logger() );
		$this->addTask( TaskAffiliateWP::class, $reseller, $this->logger() );
		$this->addTask( TaskEdd::class, $reseller, $this->logger() );

		$this->runTasks();;
	}
}

