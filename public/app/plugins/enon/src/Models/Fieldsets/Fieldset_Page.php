<?php
/**
 * Page fieldset.
 *
 * @category Class
 * @package  Enon\Acf\Fieldsets
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Fieldsets;

/**
 * Class Fieldset_Page.
 *
 * @since 1.0.0
 */
class Fieldset_Page implements Fieldset {
	/**
	 * Get the fieldset.
	 *
	 * @return array $data Fieldset data.
	 *
	 * @since 1.0.0
	 */
	public function get() : array {
		$data = [
			array(
				'key' => 'field_extra_css',
				'label' => __( 'Extra CSS', 'enon' ),
				'name' => 'extra_css',
				'type' => 'acf_code_field',
				'instructions' => __( 'Add some extra CSS.', 'enon' ),
				'mode' => 'css',
				'theme' => 'monokai',
			),
			array(
				'key' => 'field_extra_js',
				'label' => __( 'Extra JS', 'enon' ),
				'name' => 'extra_js',
				'type' => 'acf_code_field',
				'instructions' => __( 'Add some extra JavaScript.', 'enon' ),
				'mode' => 'javascript',
				'theme' => 'monokai',
			),
		];

		return $data;
	}
}
