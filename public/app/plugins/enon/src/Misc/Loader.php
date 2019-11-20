<?php

namespace Enon\Misc;

use Enon\Config\TaskLoader;

/**
 * Mis Script loader.
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
		$this->addTask( GoogleTagManager::class );
		$this->addTask( RemoveOptimizepress::class );
	}
}
