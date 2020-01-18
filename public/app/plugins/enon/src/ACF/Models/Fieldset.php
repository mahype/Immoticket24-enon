<?php
/**
 * Interface for a fieldset.
 *
 * @category Interface
 * @package  Enon\Acf\Models
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Acf\Models;

/**
 * Interface Fieldset.
 *
 * @since 1.0.0
 */
interface Fieldset {
	/**
	 * Get fieldset.
	 *
	 * @return array Returns an array with ACF fields.
	 *
	 * @since 1.0.0
	 */
	public function get() : array;
}
