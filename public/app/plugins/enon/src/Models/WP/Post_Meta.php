<?php
/**
 * Post meta class.
 *
 * @category Class
 * @package  Enon\Models\WP
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\WP;

use Enon\Models\Fieldsets\Fieldset;

/**
 * Class Tab.
 *
 * @since 1.0.0
 */
class Post_Meta {
	/**
	 * Contents objects array.
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	private $contents;

	/**
	 * Post type.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	private $post_type;

	/**
	 * Tab constructor.
	 *
	 * @param string $post_type Post type.
	 *
	 * @since 1.0.0
	 */
	public function __construct( string $post_type ) {
		$this->post_type = $post_type;
	}

	/**
	 * Registering content.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$data = array();

		foreach ( $this->contents as $content ) {
			$data = array_merge( $data, $content->get() );
		}

		$field_group = array(
			'key'                   => 'settings_' . $this->post_type,
			'title'                 => __( 'Einstellungen', 'enon' ),
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
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => $this->post_type,
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
	 * @return Post_Meta $this Page object for chaining functionality.
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
	 * @return Post_Meta $this Page object for chaining functionality.
	 *
	 * @since 1.0.0
	 */
	public function add_fieldset( Fieldset $fieldset ) {
		$this->contents[] = $fieldset;

		return $this;
	}
}
