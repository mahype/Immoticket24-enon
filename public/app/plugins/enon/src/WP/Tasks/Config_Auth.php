<?php
/**
 * Config auth
 *
 * @category Class
 * @package  Enon\WP\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\WP\Tasks;

use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class Config_Auth.
 *
 * @since 1.0.0
 */
class Config_Auth implements Filters, Task {

	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_filters();
	}

	/**
	 * Load filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		$no_cookie_timout_domains = [ 'enon.test', 'staging.energieausweis.de', '2021.energieausweis-online-erstellen.de' ];

		$current_domain = array_key_exists( 'HTTP_HOST', $_SERVER ) ? $_SERVER['HTTP_HOST'] : 'enon.test';

		if( ! in_array( $current_domain, $no_cookie_timout_domains ) )
		{
			add_filter( 'auth_cookie_expiration', [ $this, 'set_auth_cookie_expiration' ] );
		}        
	}

	/**
	 * Setting auth expirations for cookies.
	 *
	 * @since 1.0.0
	 */
	public function set_auth_cookie_expiration( $length ) {
		return HOUR_IN_SECONDS / 2; // 
	}
}
