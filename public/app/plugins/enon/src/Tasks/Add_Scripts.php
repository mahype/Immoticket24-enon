<?php
/**
 * Add scripts.
 *
 * @category Class
 * @package  Enon\Misc\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Assets\Frontend_Script;
use Awsm\WP_Wrapper\Tools\Class_Location;
use Awsm\WP_Wrapper\Building_Plans\Task;

/**
 * Class Google_Tag_Manager
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Add_Scripts implements Task {
	use Class_Location;

	/**
	 * Running tasks.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$bundle_js = $this->get_assets_url( 'bundle.js' );
		new Frontend_Script( 'enon-bundle-js', $bundle_js, array(), '1.0.0', true );
	}

	/**
	 * Get assets url.
	 *
	 * @return string Assets dir url.
	 *
	 * @throws \ReflectionException Thrown if there was a problem on getting file information.
	 *
	 * @since 1.0.0
	 */
	private function get_assets_url( $filename ) {
		$assets_dir = dirname( dirname( self::get_file() ) ) . '/Assets/Dist/';
		return plugin_dir_url( $assets_dir . $filename ) . $filename;
	}
}
