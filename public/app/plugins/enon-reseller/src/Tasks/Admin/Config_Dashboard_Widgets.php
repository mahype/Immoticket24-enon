<?php
/**
 * Class for configuring dashboard widgets.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Admin
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Admin;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class Config_Dashboard_Widgets
 *
 * @package Enon_Reseller\Tasks\Admin
 *
 * @since 1.0.0
 */
class Config_Dashboard_Widgets implements Task, Actions {
	/**
	 * Run actions.
	 *
	 * @return mixed|void
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_actions();
	}

	/**
	 * Add actions.
	 *
	 * @return mixed|void
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'wp_dashboard_setup', [ $this, 'remove' ], 10000 );
		add_action( 'wp_dashboard_setup', [ $this, 'add' ] );
	}

	/**
	 * Remove existing meta boxes.
	 *
	 * @since 1.0.0
	 */
	public function remove() {
		global $wp_meta_boxes;

		unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity'] );
		unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
		unset( $wp_meta_boxes['dashboard']['normal']['core']['op_optin_stats_widget'] );
	}

	/**
	 * Add own meta boxes.
	 *
	 * @since 1.0.0
	 */
	public function add() {
		wp_add_dashboard_widget( 'lead_export', 'Lead export', [ $this, 'widget_lead_export' ] );
	}

	/**
	 * Widget for lead export.
	 *
	 * @since 1.0.0
	 */
	public function widget_lead_export() {
		$csv_all = admin_url( '?reseller_leads_download' );

		$date_start_last_month = date("Y-m-d", strtotime("first day of previous month"));
		$date_end_last_month   = date("Y-m-d", strtotime("last day of previous month"));

		$csv_last_month = admin_url( '?reseller_leads_date_range=' . $date_start_last_month . '|' . $date_end_last_month );

		$date_start_this_month = date("Y-m-d", strtotime("first day of this month"));
		$date_end_this_month   = date("Y-m-d", time() );

		$csv_this_month = admin_url( '?reseller_leads_date_range=' . $date_start_this_month . '|' . $date_end_this_month );

		echo sprintf( '<p>Alle: <a href="%s">CSV</a></p>', $csv_all );
		echo sprintf( '<p>Letzter Monat: <a href="%s">CSV</a></p>', $csv_last_month );
		echo sprintf( '<p>Dieser Monat: <a href="%s">CSV</a></p>', $csv_this_month );

		do_action( 'enon_widget_lead_export_end' );
	}
}
