<?php
/**
 * Component interface.
 *
 * @category Interface
 * @package  Enon\Models
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models;

/**
 * Interface Component_Interface.
 *
 * @since 1.0.0
 */
interface Component_Interface {
	/**
	 * Executes code before passing any other function.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function load() : bool;

	/**
	 * Returns HTML code of component.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function html() : string;

	/**
	 * Returns JS code of component.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function js() : string;

	/**
	 * Returns CSS code of component.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function css() : string;
}
