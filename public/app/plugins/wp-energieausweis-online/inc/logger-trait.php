<?php

/**
 * Trait logger_trait
 */
trait logger_trait {
	/**
	 * File to log to.
	 *
	 * @var $log_file
	 */
	private $log_file;

	/**
	 * Backtracing switched on/off.
	 *
	 * @var bool
	 */
	private $log_backtrace = false;

	/**
	 * Value delimiter.
	 *
	 * @var string
	 */
	private $log_delimiter = ';';

	/**
	 * Logging
	 *
	 * @param $message
	 *
	 * @return string|bool The line which has been written or false if file could not be opened.
	 */
	private function log_message( $message ) {
		if( $this->log_backtrace ) {
			ob_start();
			debug_print_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
			$trace = ob_get_contents();
			ob_end_clean();

			$message.= chr(13 ) . $trace;
		}

		$date_array = explode(' ', microtime());
		$time = date('Y-m-d H:i:s', $date_array[1] ) . ' ' . $date_array[0];

		$ip = $_SERVER['REQUEST_URI'];
		$url = $_SERVER['REMOTE_ADDR'];

		$line = $time . $this->log_delimiter . $url . $this->log_delimiter .  $message . chr(13 );
		$file = fopen( $this->log_file, 'a' );

		if( ! $file ) {
			return false;
		}

		fputs( $file, $line  );
		fclose( $file );

		return $line;
	}
}

/**
 * Class WP_Enon_logger
 */
class WP_Enon_logger {
	use logger_trait;

	/**
	 * Logging message.
	 *
	 * @param $message
	 * @param bool $backtrace
	 * @param string $delimiter
	 */
	public function log( $message, $backtrace = false, $delimiter = ';' ) {
		$this->log_file = dirname( dirname( ABSPATH ) ) . '/general.log';
		$this->log_delimiter = $delimiter;
		$this->log_backtrace = $backtrace;
		$this->log_message( $message );
	}
}

/**
 * Logger function.
 *
 * @param $message
 * @param bool $backtrace
 * @param string $delimiter
 */
function wp_enon_log( $message, $backtrace = false, $delimiter = ';' ) {
	$logger = new WP_Enon_logger();
	$logger->log( $message, $backtrace, $delimiter );
	unset( $logger );
}
