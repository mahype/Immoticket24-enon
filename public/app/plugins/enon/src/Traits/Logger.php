<?php

namespace Enon\Traits;

/**
 * Trait Logger.
 *
 * @since 1.0.0
 *
 * @package Enon\Traits
 */
trait Logger {
	/**
	 * Logger object.
	 *
	 * @since 1.0.0
	 *
	 * @var \Enon\Logger
	 */
	private $logger;

	/**
	 * Logger fuunction.
	 *
	 * @since 1.0.0
	 *
	 * @return \Enon\Logger
	 */
	protected function logger() {
		return $this->logger;
	}
}
