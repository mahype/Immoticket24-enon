<?php
/**
 * Post data parent class.
 *
 * @category Class
 * @package  Enon\Mis\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\WP\Models;

/**
 * Class Post_Data.
 *
 * @package Enon\Reseller\Taks\Plugins
 *
 * @since 1.0.0
 */
abstract class Post_Meta {
	/**
	 * Post Id.
	 *
	 * @var int
	 *
	 * @since 1.0.0
	 */
	protected $post_id = null;

	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * Get post id.
	 *
	 * @return int $post_id Post Id.
	 *
	 * @since 1.0.0
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	/**
	 * Get post field.
	 *
	 * @param string $field_name Name of the field.
	 *
	 * @return mixed/null Value if found, otherwhise null.
	 *
	 * @since 1.0.0
	 */
	protected function get( $field_name ) {
		if ( empty( $this->post_id ) ) {
			return null;
		}

		return get_field( $field_name, $this->post_id );
	}
}
