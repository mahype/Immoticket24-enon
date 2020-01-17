<?php
/**
 * Class for managing tabs.
 *
 * @category Class
 * @package  Enon\ACF\Models
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\ACF\Models;

/**
 * Class Tabs.
 *
 * @since 1.0.0
 */
class Tabs {
	/**
	 * Added tabs.
	 *
	 * @var array An array with Tab objects.
	 *
	 * @since 1.0.0
	 */
	private $tabs = array();

	/**
	 * Get fieldset.
	 *
	 * @return array Returns an array with ACF fields.
	 *
	 * @since 1.0.0
	 */
	public function get() : array {
		$tabs = array();

		foreach ( $this->tabs as $slug => $tab ) {
			$tab_setting = [
				array(
					'key'   => 'tab_' . $tab->get_slug(),
					'label' => $tab->get_title(),
					'type'  => 'tab',
				),
			];

			$fieldset = $tab->get_fieldset();

			$tabs = array_merge( $tabs, $tab_setting, $fieldset->get() );
		}

		return $tabs;
	}

	/**
	 * Add a tab.
	 *
	 * @param Tab $tab Tab to add.
	 *
	 * @return Tabs Tabs object chaining functionality.
	 *
	 * @since 1.0.0
	 */
	public function add( Tab $tab ) : Tabs {
		$this->tabs[ $tab->get_slug() ] = $tab;

		return $this;
	}
}
