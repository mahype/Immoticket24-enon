<?php

namespace Enon\Config;

use Enon\Config\Tasks\TaskGutenberg;
use Enon\Config\Tasks\TaskMenu;

use Enon\TaskLoader;

/**
 * Config loader.
 *
 * @since 1.0.0
 *
 * @package Enon\Config
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
		$this->add_task( TaskGutenberg::class );
		$this->add_task( TaskMenu::class );
		$this->run_tasks();;
	}
}
