<?php

namespace Enon;

/**
 * Logger Wrapper.
 *
 * @since 1.0.0
 *
 * @package Enon
 */
class Logger extends \Monolog\Logger
{
	/**
	 * Logging error
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Message to log and display.
	 * @param array $context The log context.
	 */
	public function error( $message, array $context = [] ): void {
		parent::error( $message, $context );
		wp_die( $message );
	}
}
