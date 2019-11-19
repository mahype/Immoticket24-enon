<?php

namespace Enon;

/**
 * Logger Wrapper.
 *
 * @since 1.0.0
 *
 * @package Enon
 */
class Logger extends \Monolog\Logger {

	public function alert($message, array $context = []): void
	{
		parent::alert($message, $context);
		wp_die( $message );
	}
}
