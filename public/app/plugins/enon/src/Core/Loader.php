<?php

namespace Enon\Core;

use Enon\TaskLoader;
use Enon\Models\Exceptions\Exception;
use Enon\Core\Tasks\Plugins\Task_ACF;

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
		$this->add_task( Task_ACF::class, $this->logger() );

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
	}

	/**
	 * Running frontend tasks.
	 *
	 * @since 1.0.0
	 */
	public function addFrontendTasks() {

	}
}

