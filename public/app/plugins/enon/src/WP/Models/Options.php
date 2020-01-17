<?php
/**
 * Class for getting enon settings/options.
 *
 * @category Class
 * @package  Enon\WP\Model
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\WP\Models;

/**
 * Class ACF_Setting.
 *
 * @since 1.0.0
 */
abstract class Options {
	/**
	 * Get value.
	 *
	 * @param string $field_name Field name.
	 *
	 * @since 1.0.0
	 */
	public function get( $field_name ) {
		return get_field( $field_name, 'option' );
	}
}
