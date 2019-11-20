<?php

namespace Enon\Config;

use Awsm\WPWrapper\BuildingPlans\Task;
use Awsm\WPWrapper\Tasks\TaskRunner;

use Enon\Logger;

/**
 * Config loader.
 */
abstract class TaskLoader implements Task
{
	use TaskRunner;

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
