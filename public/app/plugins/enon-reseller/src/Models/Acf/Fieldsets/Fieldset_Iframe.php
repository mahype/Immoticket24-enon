<?php
/**
 * Reseller iframe settings fieldset.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Acf\Fieldsets;
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Acf\Fieldsets;

use Enon\Acf\Models\Fieldset;

/**
 * Class Reseller_Iframe.
 *
 * @since 1.0.0
 */
class Fieldset_Iframe implements Fieldset {
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
				'key' => 'field_elements',
				'label' => __( 'Elemente', 'enon' ),
				'name' => 'elements',
				'type' => 'checkbox',
				'choices' => array(
					'show_title'    => __( 'Titel anzeigen', 'enon' ),
					'show_description' => __( 'Beschreibung anzeigen', 'enon' ),
				),
				'default_value' => array(
					0 => 'show_headline',
				),
				'return_format' => 'value',
			),
			array(
				'key' => 'field_extra_css',
				'label' => __( 'Extra CSS', 'enon' ),
				'name' => 'extra_css',
				'type' => 'acf_code_field',
				'instructions' => __( 'Add some extra CSS for reseller.', 'enon' ),
				'mode' => 'css',
				'theme' => 'monokai',
			),
			array(
				'key' => 'field_extra_js',
				'label' => __( 'Extra JS', 'enon' ),
				'name' => 'extra_js',
				'type' => 'acf_code_field',
				'instructions' => __( 'Add some extra JavaScript for reseller.', 'enon' ),
				'mode' => 'javascript',
				'theme' => 'monokai',
			),
		];

		return $data;
	}
}
