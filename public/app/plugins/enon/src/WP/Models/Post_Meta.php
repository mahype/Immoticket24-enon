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
 * @since 1.0.0
 *
 * @package Enon\Reseller\Taks\Plugins
 */
abstract class Post_Meta {
	/**
	 * Post Id.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	protected $post_id = null;

	/**
	 * Set post id.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id Post id.
	 */
	public function set_post_id( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * Get post id.
	 *
	 * @since 1.0.0
	 *
	 * @return int $post_id Post Id.
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	/**
	 * Get post field.
	 *
	 * @since 1.0.0
	 *
	 * @param string $field_name Name of the field.
	 *
	 * @return mixed/null Value if found, otherwhise null.
	 */
	public function get( $field_name ) {
		if ( empty( $this->post_id ) ) {
			return null;
		}

		return get_field( $field_name, $this->post_id );
	}
}
