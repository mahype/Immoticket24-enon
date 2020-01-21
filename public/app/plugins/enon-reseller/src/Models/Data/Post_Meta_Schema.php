<?php
/**
 * Class for getting resellers post meta form data.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Data
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Data;

use Enon\WP\Models\Post_Meta;

/**
 * Class Post_Meta_Schema.
 *
 * @since 1.0.0
 */
class Post_Meta_Schema extends Post_Meta {
	/**
	 * Get bedarfsausweis schema file.
	 *
	 * @since 1.0.0
	 *
	 * @return string Bedarfsausweis schema file.
	 */
	public function get_bw_schema_file() {
		return $this->get( 'bw_schema_file' );
	}

	/**
	 * Get verbrauchsausweis schema file.
	 *
	 * @since 1.0.0
	 *
	 * @return string Verbrauchssausweis schema file.
	 */
	public function get_vw_schema_file() {
		return $this->get( 'vw_schema_file' );
	}
}
