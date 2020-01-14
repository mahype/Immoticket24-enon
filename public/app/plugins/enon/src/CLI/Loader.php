<?php

namespace Enon\CLI;

use Enon\TaskLoader;
use Enon\CLI\Tasks\Task_Commands;


/**
 * CLI loader.
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
		if ( ! defined( 'WP_CLI' ) ) {
			return;
		}

		$this->add_task( Task_Commands::class );

		$this->run_tasks();
	}
}

