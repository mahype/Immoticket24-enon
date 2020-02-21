<?php
/**
 * Components parent class.
 *
 * @category Interface
 * @package  Enon\Models
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */
namespace Enon\Models;

/**
 * Class Component
 *
 * @package Enon\Models
 *
 * @since 1.0.0
 */
abstract class Component implements Component_Interface {
	/**
	 * Place to load js.
	 *
	 * @var string Allowed values are header, footer and, html.
	 *
	 * @since 1.0.0
	 */
	protected $load_js_location = 'footer';

	/**
	 * Executes code before passing any other function.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function load(): bool {
		// TODO: Implement load() method.
		return true;
	}

	/**
	 * Returns HTML code of component.
	 *
	 * @return string HTML content.
	 *
	 * @since 1.0.0
	 */
	public function html(): string {
		// TODO: Implement html() method.
		return '';
	}

	/**
	 * Returns JS code of component.
	 *
	 * @return string JS content.
	 *
	 * @since 1.0.0
	 */
	public function js(): string {
		// TODO: Implement js() method.
		return '';
	}

	/**
	 * Returns CSS code of component.
	 *
	 * @return string CSS content.
	 *
	 * @since 1.0.0
	 */
	public function css(): string {
		// TODO: Implement css() method.
		return '';
	}
}
