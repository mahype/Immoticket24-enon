<?php
/**
 * Reseller send data settings fieldset.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Fieldsets;
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Fieldsets;

use Enon\Acf\Models\Fieldset;

/**
 * Class Reseller_Send_Data.
 *
 * @since 1.0.0
 */
class Fieldset_Send_Data implements Fieldset {
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
				'key'          => 'field_post_data_config class',
				'label'        => __( 'Data config class.', 'enon' ),
				'name'         => 'post_data_config_class',
				'type'         => 'text',
				'instructions' => '',
				'placeholder'  => 'Send_Energieausweis_Standard',
				'prepend'      => '',
				'append'       => '',
				'maxlength'    => '',
			),
		];

		return $data;
	}
}
