<?php

namespace Enon\Config;

use Enon\Logger;

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
		$this->addTask( Gutenberg::class );
		$this->addTask( Menu::class );
	}
}
