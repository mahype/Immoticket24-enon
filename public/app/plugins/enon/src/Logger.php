<?php
/**
 * Enon Logger.
 *
 * @category Class
 * @package  Enon\Reseller\Tasks\Plugins
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon;

/**
 * Logger Wrapper.
 *
 * @since 1.0.0
 *
 * @package Enon
 */
class Logger extends \Monolog\Logger {

	/**
	 * Logging error
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Message to log and display.
	 * @param array  $context The log context.
	 */
	public function error( $message, array $context = array() ): void {
		parent::error( $message, $context );
		wp_die( $message );
	}
}
