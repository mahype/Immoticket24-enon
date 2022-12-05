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
 * Class Post_Meta.
 *
 * @package Enon\WP\Models
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
	public function get( $field_name ) {
		if ( empty( $this->post_id ) ) {
			return null;
		}

		return get_field( $field_name, $this->post_id );
	}

	/**
	 * Set post field.
	 * 
	 * @param string $field_name Name of the field.
	 * @param mixed $value Value to set.
	 * 
	 * @return bool True if successfull, otherwise false.
	 * 
	 * @since 1.0.0
	 */
	public function set( $field_name, $value ) {
		if ( empty( $this->post_id ) ) {
			return null;
		}

		return update_field( $field_name, $value, $this->post_id );
	}

	/**
	 * Delete post field.
	 * 
	 * @param string $field_name Name of the field.
	 * 
	 * @return bool True if successfull, otherwise false.
	 * 
	 * @since 1.0.0
	 */
	public function delete( $field_name ) {
		if ( empty( $this->post_id ) ) {
			return null;
		}
		
		$deleted = delete_post_meta( $this->post_id, $field_name );
		return $deleted;
	}
}
