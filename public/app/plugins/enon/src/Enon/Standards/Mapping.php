<?php
/**
 * Mapping Standard.
 *
 * @category Class
 * @package  Enon\Enon\Standards
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Enon\Standards;

/**
 * Class Mapping.
 *
 * @since 1.0.0
 *
 * @package Enon\Enon\Standards
 */
class Mapping extends Standard {
	/**
	 * Get Mapping file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Type of mapping (pdf/xml-datenerfassung/xml-zusatzdatenerfassung)
	 *
	 * @return string The location of the mapping file.
	 */
	public function get_file( $type ) {
		$mapping_file = $this->getPath() . '/mappings/' . $type . '-mappings.php';
		return apply_filters( 'wpenon_mapping_file', $mapping_file, $this->getKey(), $type );
	}
}
