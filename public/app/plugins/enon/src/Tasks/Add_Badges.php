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

use Enon\Models\Badges\CO2_Badge;
use \Enon\Models\Badges\Ekomi_Badge;
use Enon\Models\Badges\Trusted_Shops_Badge;
use Enon\Models\Badges\Tuev_Badge;

/**
 * Class Google_Tag_Manager
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Add_Badges implements Actions, Task {
	/**
	 * Badges.
	 *
	 * @var Trusted_Shops_Badge
	 *
	 * @since 1.0.0
	 */
	private $badge_trusted_shops;

	/**
	 * Badges.
	 *
	 * @var Tuev_Badge
	 *
	 * @since 1.0.0
	 */
	private $badge_tuev;

	/**
	 * Badges.
	 *
	 * @var Ekomi_Badge
	 *
	 * @since 1.0.0
	 */
	private $badge_ekomi;

	/**
	 * Badges.
	 *
	 * @var CO2_Badge
	 *
	 * @since 1.0.0
	 */
	private $badge_co2;

	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->add_actions();

		$this->badge_ekomi = new Ekomi_Badge();
		$this->badge_trusted_shops = new Trusted_Shops_Badge();
		$this->badge_co2 = new CO2_Badge();
		$this->badge_tuev = new Tuev_Badge();
	}

	/**
	 * Load targeting scripts into hooks.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'wp_head', array( $this, 'css' ) );
		add_action( 'wp_footer', array( $this, 'js' ) );
		add_action( 'enon_header_badges', array( $this, 'badges' ) );
	}

	/**
	 * Print out badge HTML.
	 *
	 * @since 1.0.0
	 */
	public function badges() {
		// phpcs:ignore
		echo $this->badge_trusted_shops->html();
		echo $this->badge_ekomi->html();
		echo $this->badge_co2->html();
		echo $this->badge_tuev->html();
	}

	/**
	 * Add CSS.
	 *
	 * @since 1.0.0
	 */
	public function css() {
		// phpcs:ignore
		echo $this->badge_ekomi->css();
		echo $this->badge_tuev->css();
	}

	/**
	 * Add JS.
	 *
	 * @since 1.0.0
	 */
	public function js() {
		// phpcs:ignore
		echo $this->badge_ekomi->js();
		echo $this->badge_tuev->js();
		echo $this->badge_trusted_shops->js();
	}
}
