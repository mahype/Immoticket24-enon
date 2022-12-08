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

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Task;
use Awsm\WP_Wrapper\Tools\Logger_Trait;

use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\SlackWebhookHandler;
use Monolog\Handler\StreamHandler;

use Enon\Logger;

/**
 * Class Log.
 *
 * @package Enon_Reseller\WordPress
 *
 * @since 1.0.0
 */
class Setup_Wonolog implements Task {
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

		$file = WP_LOG_DIR . '/System.log';

		$wonolog = \Inpsyde\Wonolog\bootstrap();

		$stream_handler = new StreamHandler( $file, Logger::WARNING );
		$wonolog->use_handler( $stream_handler );

		$slack_handler = new SlackWebhookHandler( 'https://hooks.slack.com/services/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', '#logs-enon', 'Monolog', true, null, false, false, Logger::WARNING );
		$wonolog->use_handler( $slack_handler );

		if ( WP_DEBUG && ! wp_doing_ajax() ) {
			$browserconsole_handler = new BrowserConsoleHandler();
			$wonolog->use_handler( $browserconsole_handler );
		}

		$wonolog->log_php_errors();
	}
}
