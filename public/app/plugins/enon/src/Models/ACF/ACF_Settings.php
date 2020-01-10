<?php
/**
 * Class for getting enon settings/options.
 *
 * @category Class
 * @package  Enon\Core\Model
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\ACF;

/**
 * Class ACF_Setting.
 *
 * @since 1.0.0
 */
abstract class ACF_Settings {
	/**
	 * Get value.
	 *
	 * @param string $field_name Field name.
	 *
	 * @throws Exception ACF must be activated.
	 *
	 * @since 1.0.0
	 */
	public function get( $field_name ) {
		ACF::detect();

		return get_field( $field_name, 'option' );
	}

	/**
	 * Fieldset for ACF.
	 *
	 * @since 1.0.0
	 *
	 * @return array $fieldset ACF options page Fieldset.
	 */
	abstract public function fieldset();
}
