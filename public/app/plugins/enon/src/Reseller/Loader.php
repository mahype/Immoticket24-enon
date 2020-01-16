<?php

namespace Enon\Reseller;

use Enon\Reseller\Models\Token;
use Enon\Reseller\Tasks\Enon\TaskReseller;
use Enon\Reseller\Tasks\Enon\TaskRouteUrls;
use Enon\Task_Loader;
use Enon\Models\Exceptions\Exception;

use Enon\Reseller\Models\Reseller;
use Enon\Reseller\Models\Reseller_Data;

use Enon\Reseller\Tasks\Core\Task_CPT_Reseller;
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
class Loader extends Task_Loader {
	/**
	 * Loading Scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_task( Task_CPT_Reseller::class );
		$this->add_task( TaskACF::class, $this->logger() );

		if ( is_admin() ) {
			$this->addAdminTasks();
		} else {
			$this->addFrontendTasks();
		}

		$this->run_tasks();
	}

	/**
	 * Running admin tasks.
	 *
	 * @since 1.0.0
	 */
	public function addAdminTasks() {
		try {
			$reseller_data = new Reseller_Data();
			$reseller = new Reseller( $reseller_data, $this->logger() );
		} catch ( Exception $exception ) {
			$this->logger()->error( sprintf( $exception->getMessage() ) );
		}

		$this->add_task( TaskReseller::class, $reseller, $this->logger() );
		$this->add_task( TaskSendEnergieausweis::class, $reseller, $this->logger() );
	}

	/**
	 * Running frontend tasks.
	 *
	 * @since 1.0.0
	 */
	public function addFrontendTasks() {
		$token = new Token();

		// No token, no action
		if ( empty( $token->get() ) ) {
			return;
		}

		try {
			$reseller_data = new Reseller_Data( $token );
			$reseller = new Reseller( $reseller_data, $this->logger() );
		} catch ( Exception $exception ) {
			$this->logger()->error( sprintf( $exception->getMessage() ) );
		}

		$this->add_task( TaskFrontend::class, $reseller );
		$this->add_task( TaskReseller::class, $reseller, $this->logger() );
		$this->add_task( TaskRouteUrls::class, $reseller, $this->logger() );
		$this->add_task( TaskEmailConfirmation::class, $reseller, $this->logger() );
		$this->add_task( TaskEmailOrderConfirmation::class, $reseller, $this->logger() );
		$this->add_task( TaskSendEnergieausweis::class, $reseller, $this->logger() );
		$this->add_task( TaskAffiliateWP::class, $reseller, $this->logger() );
		$this->add_task( TaskEdd::class, $reseller, $this->logger() );
	}
}

