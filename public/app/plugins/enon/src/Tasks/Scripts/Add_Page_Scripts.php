<?php
/**
 * Loading Scripts
 *
 * @category Class
 * @package  Enon\ACF
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks\Scripts;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;

use Enon\Models\Data\Post_Meta_Page;

/**
 * Class Add_Options.
 *
 * @package Enon\Config
 */
class Add_Page_Scripts implements Task, Actions {

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
		add_action( 'wp_head',   [ $this, 'add_css' ] );
		add_action( 'wp_footer', [ $this, 'add_js' ] );	
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

	/**
	 * Adding CSS to page.
	 *
	 * @since 1.0.0
	 */
	public function add_css() {
		$page_id   = get_the_ID();
		$post_meta = new Post_Meta_Page( $page_id );

		$extra_css = $post_meta->get_extra_css();

		if ( empty( $extra_css ) ) {
			return;
		}

		// phpcs:ignore
		echo $extra_css;
	}
}
