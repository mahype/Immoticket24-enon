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

namespace Enon;

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
	 */
	public function __construct( string $name, array $handlers = [], array $processors = [], DateTimeZone $timezone = null ) {
		parent::__construct( $name, $handlers, $processors, $timezone );

		$slack_level = self::WARNING;

		if ( WP_DEBUG && ! wp_doing_ajax() ) {
			// $this->pushHandler( new BrowserConsoleHandler() );
		}

		if ( WP_DEBUG ) {
			$slack_level = self::NOTICE;
		}

		// phpcs:ignore
		$slack_handler = new SlackWebhookHandler( 'https://hooks.slack.com/services/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', '#logs-enon', 'Monolog', true, null, false, false, $slack_level );
		$this->pushHandler( $slack_handler );
	}

	/**
	 * Adding global data to record.
	 *
	 * @param int    $level
	 * @param string $message
	 * @param array  $context
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function addRecord( $level, $message, array $context = array() ) {
		$remote_addr       = $_SERVER['REMOTE_ADDR'];
		$request_uri       = $_SERVER['REQUEST_URI'];
		$remote_addr_parts = explode( '.', $remote_addr );

		if ( 4 === count( $remote_addr_parts ) ) {
			$remote_addr_parts[3] = '*';
			$remote_addr          = implode( '.', $remote_addr_parts );
		}

		$data = array(
			'remote_addr' => $remote_addr,
			'request_uri' => $request_uri,
		);

		if ( is_array( $context ) ) {
			$context = array_merge( $data, $context );
		} else {
			$context = array( $data, $context );
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
		return self::WARNING;
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
