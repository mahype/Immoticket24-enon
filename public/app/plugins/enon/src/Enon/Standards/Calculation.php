<?php
/**
 * Calculation Standard.
 *
 * @category Class
 * @package  Enon\Enon\Standards
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Enon\Standards;

use Enon\Models\Exceptions\Exception;

/**
 * Class Calculations.
 *
 * @since 1.0.0
 *
 * @package Enon\Enon\Standards
 */
class Calculation extends Standard {
	/**
	 * Is file on loading required once?
	 *
	 * @var bool
	 *
	 * @since 1.0.0
	 */
	protected $require_once = false;

	/**
	 * Get calculation file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Type of Energieausweis (vw/bw).
	 *
	 * @return string The location of the schema file.
	 */
	public function get_file( $type ) {
		$schema_file = $this->get_path() . '/calculations/' . $type . '.php';
		return apply_filters( 'wpenon_calculation_file', $schema_file, $this->get_key(), $type );
	}
}
