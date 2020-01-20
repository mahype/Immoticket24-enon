<?php
/**
 * Task which loads iframe filters.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Enon;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Traits\Logger as Logger_Trait;

use Enon_Reseller\Models\Reseller;

/**
 * Class Logger_Trait.
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller\Tasks\Enon
 */
class Filter_Iframe implements Task, Actions {
	use Logger_Trait;

	/**
	 * Reseller object.
	 *
	 * @since 1.0.0
	 * @var Reseller;
	 */
	private $reseller;

	/**
	 * Wpenon constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Reseller $reseller Reseller object.
	 * @param Logger   $logger   Logger object.
	 */
	public function __construct( Reseller $reseller, Logger $logger ) {
		$this->reseller = $reseller;
		$this->logger   = $logger;
	}

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
		add_action( 'enon_iframe_css', array( $this, 'add_css' ) );
		add_action( 'enon_iframe_js', array( $this, 'add_js' ) );
	}

	/**
	 * Add reseller CSS.
	 *
	 * @since 1.0.0
	 */
	public function add_css() {
		$extra_css = $this->reseller->data()->iframe->get_extra_css();

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
		$extra_js = $this->reseller->data()->iframe->get_extra_js();

		if ( empty( $extra_js ) ) {
			return;
		}

		echo esc_attr( $extra_js );
	}
}
