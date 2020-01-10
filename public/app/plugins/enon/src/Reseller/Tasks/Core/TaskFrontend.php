<?php

namespace Enon\Reseller\Tasks\Core;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Enon\Logger;
use Enon\Reseller\Models\Reseller;
use Enon\Traits\Logger as Logger_Trait;

/**
 * Class WordPress.
 *
 * Running WordPress scripts
 *
 * @package Enon\Reseller\WordPress
 */
class TaskFrontend implements Task, Actions, Filters {
	/**
	 * Reseller object.
	 *
	 * @since 1.0.0
	 * @var Reseller;
	 */
	private $reseller;

	/**
	 * TaskFrontend constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Reseller $reseller
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
		 $extraCss = $this->reseller->data()->get_extra_css();

		if ( empty( $extraCss ) ) {
			return;
		}

		$source = $this->reseller->data()->get_extra_css();
		echo $source;
	}
}
