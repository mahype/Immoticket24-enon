<?php
/**
 * Schema Standard
 *
 * @category Class
 * @package  Enon\Enon\Standards
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Enon\Standards;

use WPENON\Model\Energieausweis;

/**
 * Class Schema.
 *
 * @since 1.0.0
 *
 * @package Enon\Enon\Standards
 */
class Schema extends Standard {
	/**
	 * Get schema file.
	 *
	 * @since 1.0.0
	 *
	 * @param Energieausweis $energieausweis Energieausweis object
	 *
	 * @return string The location of the schema file.
	 */
	public function get_file( $energieausweis ) {
		$type = get_post_meta( $energieausweis->id, 'wpenon_type', true );

		$schema_file = $this->getPath() . '/schema/' . $type . '.php';
		return apply_filters( 'wpenon_schema_file', $schema_file, $this->getKey(), $energieausweis );
	}
}
