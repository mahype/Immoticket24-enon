<?php

namespace Enon\Misc\Tasks;

use Awsm\WPWrapper\BuildingPlans\Actions;
use Awsm\WPWrapper\BuildingPlans\Task;

/**
 * Class Google_Tag_Manager.
 *
 *
 * @since 1.0.0
 */
class TaskDev implements Actions, Task
{
    /**
     * Running tasks.
     *
     * @since 1.0.0
     */
    public function run()
    {
        if (!defined('WP_DEBUG') && !WP_DEBUG) {
            return;
        }

        $this->addActions();
    }

    /**
     * Load targeting scripts into hooks.
     *
     * @since 1.0.0
     */
    public function addActions()
    {
        add_action('init', array(__CLASS__, 'stop_heartbeat'), 1);
    }

    /**
     * Stop WordPress hearbeat for better debugging.
     *
     * @since 1.0.0
     */
    public function stop_heartbeat()
    {
        wp_deregister_script('heartbeat');
    }
}
