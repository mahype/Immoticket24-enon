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

namespace Enon_Reseller\Tasks\WP;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Traits\Logger as Logger_Trait;

use Enon_Reseller\Models\Reseller;

/**
 * Class Task_Frontend.
 *
 * @package Enon_Reseller\WordPress
 *
 * @since 1.0.0
 */
class Task_Frontend implements Task, Actions, Filters {
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
		add_action( 'enon_iframe_css', array( $this, 'add_css' ) );
		add_action( 'enon_iframe_js', array( $this, 'add_js' ) );
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
		return locate_template( array( 'energieausweis-iframe.php' ) );
	}

	/**
	 * Add reseller CSS.
	 *
	 * @since 1.0.0
	 */
	public function add_css() {
		$extra_css = $this->reseller->data()->get_extra_css();

		if ( empty( $extra_css ) ) {
			return;
		}

		echo esc_attr( $extra_css );
	}

	/**
	 * Add reseller CSS.
	 *
	 * @since 1.0.0
	 */
	public function add_js() {
		$extra_js = $this->reseller->data()->get_extra_js();

		if ( empty( $extra_js ) ) {
			return;
		}

		echo esc_attr( $extra_js );
	}
}
