<?php

namespace Enon\Misc;

use Enon\Misc\Tasks\Plugins\TaskEddSparkasseDiscounts;
use Enon\Misc\Tasks\TaskDev;
use Enon\Misc\Tasks\TaskGoogleTagManager;
use Enon\Misc\Tasks\TaskRemoveOptimizepress;
use Enon\TaskLoader;

/**
 * Mis Script loader.
 *
 * @since 1.0.0
 */
class Loader extends TaskLoader {

	/**
	 * Loading Scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_task( TaskDev::class );
		$this->add_task( TaskGoogleTagManager::class );
		$this->add_task( TaskRemoveOptimizepress::class );
		$this->add_task( TaskEddSparkasseDiscounts::class, $this->logger() );
		$this->run_tasks();
	}
}
