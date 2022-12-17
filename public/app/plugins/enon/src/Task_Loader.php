<?php
/**
 * Enon Task loader.
 *
 * @category Class
 * @package  Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon;

use Awsm\WP_Wrapper\Interfaces\Task;
use Awsm\WP_Wrapper\Tasks\Task_Runner;

/**
 * Class Task_Loader.
 *
 * @since 1.0.0
 */
abstract class Task_Loader implements Task {
	use Task_Runner;

	/**
	 * Logger.
	 *
	 * @since 1.0.0
	 *
	 * @var Logger
	 */
	protected $logger;

	/**
	 * WhitelabelLoader constructor.
	 *
	 * @param Logger $logger Logger object.
	 * @since 1.0.0
	 */
	public function __construct( Logger $logger ) {
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
