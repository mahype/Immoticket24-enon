<?php
/**
 * Class for use of ACF
 *
 * @category Class
 * @package  Enon\WP\Model
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Acf\Models;

use Enon\Acf\Models;

/**
 * Class ACF_Setting.
 *
 * @since 1.0.0
 */
class ACF {
	/**
	 * Detection if ACF is activated.
	 *
	 * @throws Exception ACF must be activated.
	 *
	 * @since 1.0.0
	 */
	public static function detect() {
		if ( ! self::is_activated() ) {
			throw new Exception( 'ACF must be acitvated! ' );
		}
	}

	/**
	 * Is activated.
	 *
	 * @return bool Is Affiliate WP activated.
	 *
	 * @since 1.0.0
	 */
	public static function is_activated() {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return false;
		}

		return true;
	}
}
