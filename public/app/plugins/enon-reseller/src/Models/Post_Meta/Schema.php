<?php
/**
 * Class for getting resellers post meta schema data.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Post_Meta
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models\Post_Meta;

use Enon\Acf\Models\Post_Meta;

/**
 * Class Schema.
 *
 * @since 1.0.0
 */
class Schema extends Post_Meta {
	/**
	 * Get Bedarsfausweis schema file.
	 *
	 * @since 1.0.0
	 *
	 * @return string Schema file.
	 */
	public function get_bw_schema_file() {
		return $this->get( 'bw_schema_file' );
	}

	/**
	 * Get Verbrauchsausweis schema file.
	 *
	 * @since 1.0.0
	 *
	 * @return string Schema file.
	 */
	public function get_vw_schema_file() {
		return $this->get( 'vw_schema_file' );
	}
}
