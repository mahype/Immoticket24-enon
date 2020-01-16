<?php

namespace Enon\Core;

use Enon\Task_Loader;
use Enon\Models\Exceptions\Exception;
use Enon\Core\Tasks\Task_Settings_ACF;
use Enon\Core\Tasks\Task_Settings_Page;

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
		$this->add_task( Task_Settings_ACF::class, $this->logger() );

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
		$this->add_task( Task_Settings_Page::class, $this->logger() );
	}

	/**
	 * Running frontend tasks.
	 *
	 * @since 1.0.0
	 */
	public function addFrontendTasks() {
	}
}

