<?php

namespace Enon\Reseller\Tasks\Core;

use Awsm\WPWrapper\BuildingPlans\Actions;
use Awsm\WPWrapper\BuildingPlans\Filters;
use Awsm\WPWrapper\BuildingPlans\Task;
use Enon\Logger;
use Enon\Reseller\Models\Reseller;
use Enon\Traits\Logger as LoggerTrait;

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
	public function __construct( Reseller $reseller )
	{
		$this->reseller = $reseller;
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run()
	{
		$this->addActions();
		$this->addFilters();

		remove_action( 'wp_footer', 'immoticketenergieausweis_trusted_shops_badge_script', 100 );
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function addActions()
	{
		add_action( 'wp_head', 'wp_no_robots' );
		add_action( 'enon_iframe_css', [ $this, 'add_css' ] );
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function addFilters()
	{
		add_filter( 'template_include', [ $this, 'filter_iframe_template' ] );
	}

	/**
	 * Filtering iframe template.
	 *
	 * @since 1.0.0
	 *
	 * @return string $template The path of the template to include.
	 */
	public function filter_iframe_template()
	{
		return locate_template( array( 'energieausweis-iframe.php' ) );
	}

	/**
	 * Add reseller CSS.
	 *
	 * @since 1.0.0
	 */
	public function add_css()
	{
		$extraCss = $this->reseller->data()->getExtraCSS();

		if( empty ( $extraCss ) ) {
			return;
		}

		$source = $this->reseller->data()->getExtraCSS();
		echo $source;
	}
}
