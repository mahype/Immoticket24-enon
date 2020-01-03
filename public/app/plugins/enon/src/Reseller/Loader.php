<?php

namespace Enon\Reseller;

use Enon\Reseller\Models\Token;
use Enon\Reseller\Tasks\Enon\TaskReseller;
use Enon\Reseller\Tasks\Enon\TaskRouteUrls;
use Enon\TaskLoader;
use Enon\Models\Exceptions\Exception;

use Enon\Reseller\Models\Reseller;
use Enon\Reseller\Models\ResellerData;

use Enon\Reseller\Tasks\Core\TaskCPTReseller;
use Enon\Reseller\Tasks\Core\TaskFrontend;
use Enon\Reseller\Tasks\Enon\TaskEmailConfirmation;
use Enon\Reseller\Tasks\Enon\TaskEmailOrderConfirmation;
use Enon\Reseller\Tasks\Enon\TaskEnon;
use Enon\Reseller\Tasks\Enon\TaskSendEnergieausweis;
use Enon\Reseller\Tasks\Plugins\TaskACF;
use Enon\Reseller\Tasks\Plugins\TaskAffiliateWP;
use Enon\Reseller\Tasks\Plugins\TaskEdd;

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

		if( wp_doing_ajax() ) {
			return;
		}

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

		$this->addTask( TaskReseller::class, $reseller, $this->logger() );
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

		$this->addTask( TaskFrontend::class, $reseller );
		$this->addTask( TaskReseller::class, $reseller, $this->logger() );
		$this->addTask( TaskRouteUrls::class, $reseller, $this->logger() );
		$this->addTask( TaskEmailConfirmation::class, $reseller, $this->logger() );
		$this->addTask( TaskEmailOrderConfirmation::class, $reseller, $this->logger() );
		$this->addTask( TaskSendEnergieausweis::class, $reseller, $this->logger() );
		$this->addTask( TaskAffiliateWP::class, $reseller, $this->logger() );
		$this->addTask( TaskEdd::class, $reseller, $this->logger() );

		$this->runTasks();;
	}
}

