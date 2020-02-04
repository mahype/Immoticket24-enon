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
use Monolog\Handler\FirePHPHandler;

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

		if ( WP_DEBUG ) {
			$this->pushHandler( new FirePHPHandler() );
			$this->notice( 'WP Debug is switched on. Fire PHP is now activated.' );
		}
	}
}
