<?php
/**
 * Tab class.
 *
 * @category Class
 * @package  Enon\Acf\Models
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Acf\Models;

/**
 * Class Tab.
 *
 * @since 1.0.0
 */
class Tab {
	/**
	 * Tab title.
	 *
	 * @var string
	 *
	 * @since 1.0.o
	 */
	private $title;

	/**
	 * Tab slug.
	 *
	 * @var string
	 *
	 * @since 1.0.o
	 */
	private $slug;

	/**
	 * Tab fieldset.
	 *
	 * @var Fieldset
	 *
	 * @since 1.0.o
	 */
	private $fieldset;

	/**
	 * Tab constructor.
	 *
	 * @param string   $slug     Slug for internal handling.
	 * @param string   $title    Title which appears in tab.
	 * @param Fieldset $fieldset Fieldset to add.
	 *
	 * @since 1.0.0
	 */
	public function __construct( string $slug, string $title, Fieldset $fieldset ) {
		$this->slug     = $slug;
		$this->title    = $title;
		$this->fieldset = $fieldset;
	}

	/**
	 * Get slug.
	 *
	 * @returns string $title Title of tab.
	 *
	 * @since 1.0.0
	 */
	public function get_slug() : string {
		return $this->slug;
	}

	/**
	 * Get title.
	 *
	 * @returns string $title Title of tab.
	 *
	 * @since 1.0.0
	 */
	public function get_title() : string {
		return $this->title;
	}

	/**
	 * Get fieldset.
	 *
	 * @returns Fieldset $title Title of tab.
	 *
	 * @since 1.0.0
	 */
	public function get_fieldset() : Fieldset {
		return $this->fieldset;
	}
}
