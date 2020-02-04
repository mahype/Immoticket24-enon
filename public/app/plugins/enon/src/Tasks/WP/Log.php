<?php
/**
 * Class for handling WP log.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Core
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks\WP;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Awsm\WP_Wrapper\Tools\Logger_Trait;

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\SlackWebhookHandler;

use Enon\Logger;

/**
 * Class Log.
 *
 * @package Enon_Reseller\WordPress
 *
 * @since 1.0.0
 */
class Log implements Task {
	use Logger_Trait;

	/**
	 * Log constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Logger $logger Logger object.
	 */
	public function __construct( Logger $logger ) {
		$this->logger = $logger;
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		if ( ! defined( 'Inpsyde\Wonolog\LOG' ) ) {
			$this->logger()->warning( 'Could not load Wonolog. Please take care that it is included by composer.' );
			return;
		}

		$wonolog = \Inpsyde\Wonolog\bootstrap();

		foreach ( $this->logger()->getHandlers() as $handler ) {
			$wonolog->use_handler( $handler );
		}

		$wonolog->log_php_errors();
	}
}
