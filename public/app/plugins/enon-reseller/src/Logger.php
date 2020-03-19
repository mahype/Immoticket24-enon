<?php
/**
 * Logger.
 *
 * @category Class
 * @package  Enon;
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller;

use DateTimeZone;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\SlackWebhookHandler;

/**
 * Logger Wrapper.
 *
 * @since 1.0.0
 */
class Logger extends \Awsm\WP_Wrapper\Tools\Logger {
	/**
	 * Logger constructor.
	 *
	 * @param string             $name       The logging channel, a simple descriptive name that is attached to all log records.
	 * @param HandlerInterface[] $handlers   Optional stack of handlers, the first one in the array is called first, etc.
	 * @param callable[]         $processors Optional array of processors.
	 * @param DateTimeZone|null  $timezone   Optional timezone, if not provided date_default_timezone_get() will be used.
	 *
	 * @since 1.0.0
	 */
	public function __construct( string $name, array $handlers = [], array $processors = [], DateTimeZone $timezone = null ) {
		parent::__construct( $name, $handlers, $processors, $timezone );

		// phpcs:ignore
		$slack_handler = new SlackWebhookHandler( 'https://hooks.slack.com/services/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', '#logs-enon', 'Monolog', true, null, false, false, self::WARNING );
		$this->pushHandler( $slack_handler );

		if ( WP_DEBUG && ! wp_doing_ajax() ) {
			$this->pushHandler( new BrowserConsoleHandler() );
		}
	}

	/**
	 * Adding global data to record.
	 *
	 * @param int $level
	 * @param string $message
	 * @param array $context
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function addRecord( $level, $message, array $context = array() ) {
		$remote_addr       = $_SERVER['REMOTE_ADDR'];
		$remote_addr_parts = explode( '.', $remote_addr );

		if ( 4 === count( $remote_addr_parts ) ) {
			$remote_addr_parts[3] = '*';
			$remote_addr          = implode( '.', $remote_addr_parts );
		}

		$remote_addr_data = array( 'remote_addr' => $remote_addr );

		if ( is_array( $context ) ) {
			$context = array_merge( $remote_addr_data, $context );
		} else {
			$context = array( $remote_addr_data, $context );
		}

		return parent::addRecord( $level, $message, $context );
	}

	/**
	 * Setting debug level.
	 *
	 * @return int|string Debug level.
	 *
	 * @since 1.0.0
	 */
	protected function get_debug_level() {
		return self::DEBUG;
	}

	/**
	 * Getting one dir higher for logs.
	 *
	 * @return string Loggong path.
	 *
	 * @since 1.0.0
	 */
	protected function get_logging_path() {
		return dirname( parent::get_logging_path() );
	}
}
