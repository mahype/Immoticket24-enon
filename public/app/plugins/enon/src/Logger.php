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

/**
 * Logger Wrapper.
 *
 * @since 1.0.0
 */
class Logger {
	private $id;

	/**
	 * Logger constructor.
	 *
	 * @param string $id ID of logger. Used for file name.
	 * 
	 * @since 1.0.0
	 */
	public function __construct( string $id ) {
		$this->id = $id;
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
	public function addRecord( $message, $context = array() ) {
		$remote_addr       = $_SERVER['REMOTE_ADDR'];
		$request_uri       = $_SERVER['REQUEST_URI'];
		$remote_addr_parts = explode( '.', $remote_addr );

		if ( 4 === count( $remote_addr_parts ) ) {
			$remote_addr_parts[3] = '*';
			$remote_addr          = implode( '.', $remote_addr_parts );
		}

		$line = sprintf( '%s %s %s %s', $remote_addr, $request_uri, $message, wp_json_encode( $context ) );

		$filename = $this->get_logging_path() . '/' . $this->id . '.log';

		$fp = fopen( $filename, 'a' );
		fwrite( $fp, $line . PHP_EOL );
		fclose( $fp );
	}

	/**
	 * Getting one dir higher for logs.
	 *
	 * @return string Loggong path.
	 *
	 * @since 1.0.0
	 */
	protected function get_logging_path() {
		return WP_LOG_DIR;;
	}

	public function alert( $message, $context = array() ) {
		$this->addRecord( $message, $context );
	}

	public function critical( $message, $context = array() ) {
		$this->addRecord( $message, $context );
	}

	public function debug( $message, $context = array() ) {
		$this->addRecord( $message, $context );
	}

	public function notice( $message, $context = array() ) {
		$this->addRecord( $message, $context );
	}
}
