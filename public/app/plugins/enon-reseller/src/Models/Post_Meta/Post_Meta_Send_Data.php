<?php
/**
 * Class for getting resellers post meta send data.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Post_Meta
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Post_Meta;

use Enon\WP\Models\Post_Meta;

/**
 * Class Post_Meta_Send_Data.
 *
 * @since 1.0.0
 */
class Post_Meta_Send_Data extends Post_Meta {
	/**
	 * Get post endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @return string Post endpoint.
	 */
	public function get_post_endpoint() {
		return $this->get( 'post_endpoint' );
	}

	/**
	 * Get post data config class.
	 *
	 * @since 1.0.0
	 *
	 * @return string Post data config class.
	 */
	public function get_post_data_config_class() {
		return $this->get( 'post_data_config_class' );
	}
}
