<?php
/**
 * Class for getting resellers post meta general data.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Data
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Data;

use Enon\WP\Models\Post_Meta;

/**
 * Class Post_Meta_General.
 *
 * @since 1.0.0
 */
class Post_Meta_Page extends Post_Meta {
	/**
	 * Get extra CSS.
	 *
	 * @since 1.0.0
	 *
	 * @return string Extra CSS.
	 */
	public function get_extra_css() {
		return $this->get( 'extra_css' );
	}

	/**
	 * Get extra JS.
	 *
	 * @since 1.0.0
	 *
	 * @return string Extra JS.
	 */
	public function get_extra_js() {
		return $this->get( 'extra_js' );
	}
}
