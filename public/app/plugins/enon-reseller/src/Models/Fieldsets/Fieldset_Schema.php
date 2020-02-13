<?php
/**
 * Reseller form settings fieldset.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Fieldsets;
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Fieldsets;

use Enon\Models\Fieldsets\Fieldset;

/**
 * Class Reseller_Form.
 *
 * @since 1.0.0
 */
class Fieldset_Schema implements Fieldset {
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
				'key'          => 'field_bw_schema_file',
				'label'        => __( 'Bedarfsausweis schema file', 'enon' ),
				'name'         => 'bw_schema_file',
				'type'         => 'text',
				'instructions' => __( 'Leave blank for standard file (bw.php).', 'enon' ),
			),
			array(
				'key'          => 'field_vw_schema_file',
				'label'        => __( 'Verbrauchsausweis schema file', 'enon' ),
				'name'         => 'vw_schema_file',
				'type'         => 'text',
				'instructions' => __( 'Leave blank for standard file (vw.php).', 'enon' ),
			),
		];

		return $data;
	}
}
