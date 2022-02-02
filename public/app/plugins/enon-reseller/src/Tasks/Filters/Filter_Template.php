<?php
/**
 * Class for loading frontend scripts.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Core
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Filters;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;

use Enon_Reseller\Models\Reseller;

/**
 * Class Task_Frontend.
 *
 * @package Enon_Reseller\WordPress
 *
 * @since 1.0.0
 */
class Filter_Template implements Task, Actions, Filters {
	/**
	 * Reseller object.
	 *
	 * @since 1.0.0
	 * @var Reseller;
	 */
	private $reseller;

	/**
	 * Task_Frontend constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Reseller $reseller Reseller object.
	 */
	public function __construct( Reseller $reseller ) {
		$this->reseller = $reseller;
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_actions();
		$this->add_filters();

		remove_action( 'wp_footer', 'immoticketenergieausweis_trusted_shops_badge_script', 100 );
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'wp_head', 'wp_no_robots' );
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'template_include', array( $this, 'filter_iframe_template' ) );
	}

	/**
	 * Filtering iframe template.
	 *
	 * @since 1.0.0
	 *
	 * @return string $template The path of the template to include.
	 */
	public function filter_iframe_template() {
		return locate_template( array( 'iframe.php' ) );
	}
}
