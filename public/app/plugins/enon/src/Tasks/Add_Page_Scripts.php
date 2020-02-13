<?php
/**
 * Loading ACF Options.
 *
 * @category Class
 * @package  Enon\ACF
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Awsm\WP_Wrapper\Tools\Logger_Trait;
use Awsm\WP_Wrapper\Tools\Logger;

use Enon\Models\Data\Post_Meta_Page;

/**
 * Class Add_Options.
 *
 * @package Enon\Config
 */
class Add_Page_Scripts implements Task, Actions {
	use Logger_Trait;

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_actions();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'enon_page_js', [ $this, 'add_js' ] );
	}

	/**
	 * Adding JavaScript to page.
	 *
	 * @since 1.0.0
	 */
	public function add_js() {
		$page_id   = get_the_ID();
		$post_meta = new Post_Meta_Page( $page_id );

		$extra_js = $post_meta->get_extra_js();

		if ( empty( $extra_js ) ) {
			return;
		}

		// phpcs:ignore
		echo $extra_js;
	}
}
