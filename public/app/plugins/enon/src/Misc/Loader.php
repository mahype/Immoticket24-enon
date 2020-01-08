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
class Loader extends TaskLoader
{
    /**
     * Loading Scripts.
     *
     * @since 1.0.0
     */
    public function run()
    {
        $this->addTask(TaskDev::class);
        $this->addTask(TaskGoogleTagManager::class);
        $this->addTask(TaskRemoveOptimizepress::class);
        $this->addTask(TaskEddSparkasseDiscounts::class, $this->logger());
        $this->runTasks();
    }
}
