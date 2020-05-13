<?php
/**
 * Google tag manager tasks.
 *
 * @category Class
 * @package  Enon\Misc\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Building_Plans\Hooks_Actions;
use Awsm\WP_Wrapper\Building_Plans\Service;
use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Awsm\WP_Wrapper\Loaders\Hooks_Loader;
use Awsm\WP_Wrapper\Loaders\Loader;

use Enon\Models\Popups\Premiumbewertung_Popup;

/**
 * Class Google_Tag_Manager
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Add_Popups implements Actions, Task {
	/**
	 * Premiumbewertung.
	 *
	 * @var Premiumbewertung_Popup
	 *
	 * @since 1.0.0
	 */
	private $popup_premiumbewertung;

	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->add_actions();
		$this->popup_premiumbewertung = new Premiumbewertung_Popup();
	}

	/**
	 * Load targeting scripts into hooks.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'wp_footer', array( $this, 'js' ) );
		add_action( 'edd_cart_footer_buttons', array( $this, 'html_premiumbewertung' ) );
	}

	/**
	 * Print out badge HTML.
	 *
	 * @since 1.0.0
	 */
	public function html_premiumbewertung() {
		// phpcs:ignore
		echo $this->popup_premiumbewertung->html();
	}

	/**
	 * Add JS.
	 *
	 * @since 1.0.0
	 */
	public function js() {
		// phpcs:ignore
		echo $this->popup_premiumbewertung->js();
	}
}
