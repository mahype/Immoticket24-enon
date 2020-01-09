<?php

namespace Enon;

use Awsm\WP_Wrapper\Building_Plans\Task;
use Awsm\WP_Wrapper\Tasks\Task_Runner;

/**
 * Config loader.
 */
abstract class TaskLoader implements Task
{
	use Task_Runner;

	/**
	 * Logger.
	 *
	 * @since 1.0.0
	 *
	 * @var Logger
	 */
	private $logger;

	/**
	 * WhitelabelLoader constructor.
	 *
	 * @param Logger $logger Logger object.
	 * @since 1.0.0
	 *
	 */
	public function __construct(Logger $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * Returns loggger.
	 *
	 * @since 1.0.0
	 *
	 * @return Logger
	 */
	protected function logger() {
		return $this->logger;
	}

	/**
	 * Loading Scripts.
	 *
	 * @since 1.0.0
	 */
	abstract public function run();
}
