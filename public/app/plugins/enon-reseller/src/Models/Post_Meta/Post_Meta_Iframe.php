<?php
/**
 * Class for getting resellers post meta iframe data.
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
 * Class Post_Meta_Iframe.
 *
 * @since 1.0.0
 */
class Post_Meta_Iframe extends Post_Meta {
	/**
	 * Get reseller extra CSS.
	 *
	 * @since 1.0.0
	 *
	 * @return string Reseller extra CSS.
	 */
	public function get_extra_css() {
		return $this->get( 'extra_css' );
	}

	/**
	 * Get reseller extra JS.
	 *
	 * @since 1.0.0
	 *
	 * @return string Reseller extra JS.
	 */
	public function get_extra_js() {
		return $this->get( 'extra_js' );
	}
}
