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

namespace Enon_Reseller\Tasks\Filters;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Tools\Logger_Trait;

use Enon_Reseller\Models\Reseller;

/**
 * Class Logger_Trait.
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller\Tasks\Enon
 */
class Filter_Iframe implements Task, Actions, Filters {
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
		$this->add_filters();
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'wpenon_create_show_title', array( $this, 'filter_title' ) );
		add_filter( 'wpenon_create_show_description', array( $this, 'filter_description' ) );
		add_filter( 'immoticketenergieausweis_checkout_terms_checkboxes', array( $this, 'filter_newsletter_terms' ) );
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
	 * Filtering if title will be shown on energieausweis creation.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function filter_title() {
		$show = $this->reseller->data()->iframe->isset_element_title();
		return $show;
	}

	/**
	 * Filtering if description will be shown on energieausweis creation.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function filter_description() {
		$show = $this->reseller->data()->iframe->isset_element_description();
		return $show;
	}

	/**
	 * Filtering if newsletter terms will be shown on energieausweis creation.
	 *
	 * @param array $terms_checkboxes Terms checkboxes.
	 *
	 * @return array Filtered Terms checkboxes.
	 *
	 * @since 1.0.0
	 */
	public function filter_newsletter_terms( $terms_checkboxes ) {
		if ( ! $this->reseller->data()->iframe->isset_element_newsletter_terms() ) {
			unset( $terms_checkboxes['newsletter_terms'] );
		}

		return $terms_checkboxes;
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

		echo $extra_css;
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

		echo $extra_js;
	}
}
