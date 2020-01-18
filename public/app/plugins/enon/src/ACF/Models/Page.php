<?php
/**
 * Page class.
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
class Page {
	/**
	 * Page slug.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	private $slug;

	/**
	 * Menu title.
	 *
	 * @var string
	 *
	 * @since 1.0.o
	 */
	private $menu_title;

	/**
	 * Page title.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	private $page_title;

	/**
	 * Capability for accessing page.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	private $capability;

	/**
	 * Contents objects array
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	private $contents;

	/**
	 * Tab constructor.
	 *
	 * @param string $slug       Slug for internal handling.
	 * @param string $menu_title Title which appears in menu.
	 * @param string $page_title Title which appears on top of page.
	 * @param string $capability Capability for accessing page.
	 *
	 * @since 1.0.0
	 */
	public function __construct( string $slug, string $menu_title, string $page_title, string $capability = 'edit_posts' ) {
		$this->slug       = $slug;
		$this->menu_title = $menu_title;
		$this->page_title = $page_title;
		$this->capability = $capability;
	}

	/**
	 * Registering page.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$page = acf_add_options_page(
			array(
				'page_title'  => $this->page_title,
				'menu_title'  => $this->menu_title,
				'menu_slug'   => $this->slug,
				'capability'  => $this->capability,
				'parent_slug' => 'enon',
				'redirect'    => false,
			)
		);

		acf_add_options_page( $page );

		$this->register_content();
	}

	/**
	 * Registering content.
	 *
	 * @since 1.0.0
	 */
	private function register_content() {
		$data = array();

		foreach ( $this->contents as $content ) {
			$data = array_merge( $data, $content->get() );
		}

		$field_group = array(
			'key'                   => 'settings',
			'title'                 => 'Email Einstellungen',
			'fields'                => $data,
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'location'              => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => $this->slug,
					),
				),
			),
		);

		acf_add_local_field_group( $field_group );
	}

	/**
	 * Add tabs.
	 *
	 * @param Tabs $tabs Tabs object.
	 *
	 * @return Page $this Page object for chaining functionality.
	 *
	 * @since 1.0.0
	 */
	public function add_tabs( Tabs $tabs ) {
		$this->contents[] = $tabs;

		return $this;
	}

	/**
	 * Add fieldset.
	 *
	 * @param Fieldset $fieldset Fieldset object.
	 *
	 * @return Page $this Page object for chaining functionality.
	 *
	 * @since 1.0.0
	 */
	public function add_fieldset( Fieldset $fieldset ) {
		$this->contents[] = $fieldset;

		return $this;
	}
}
