<?php
/**
 * Add CSV sparkasse functionalities.
 *
 * @category Class
 * @package  Enon\Misc
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Sparkasse;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class PluginEdd.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class Add_CSV_Export implements Task, Actions, Filters {
	/**
	 * Run task.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$user = wp_get_current_user();
		$reseller_id = (int) get_user_meta( $user->ID, 'reseller_id', true );

		// Only this reseller has access.
		if ( 321587 !== $reseller_id ) {
			return;
		}

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Add Actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'enon_widget_lead_export_end', [ $this, 'add_export_links' ] );
	}

	/**
	 * Add Filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'enon_reseller_leads_quer_args', [ $this, 'filter_query_args' ], 10, 2 );
	}

	/**
	 * Add export links.
	 *
	 * @since 1.0.0
	 */
	public function add_export_links() {
		$csv_in_range = admin_url( '?reseller_leads_spk_in_range=1' );
		$csv_out_range = admin_url( '?reseller_leads_spk_in_range=0' );

		echo sprintf( '<p>Alle: <a href="%s">Im Geschäftsbereich</a> | <a href="%s">Außerhalb des Geschäftsbereichs</a></p>', $csv_in_range, $csv_out_range );
	}

	/**
	 * Filter query for additional data.
	 *
	 * @param array $query_args Query arguments.
	 * @param array $query_values Query values.
	 *
	 * @return array Filtered query arguments.
	 */
	public function filter_query_args( $query_args, $query_values ) {
		$postcodes = array(
			'heidelberg' => [ 69115, 69117, 69118, 69120, 69121, 69123, 69124, 69126 ],
			'neckargemuend' => [ 69151, 69239, 69245, 69250, 69253, 69256, 69257, 69259, 69434, 74909, 74931 ],
			'walldorf_wiesloch' => [ 68789, 69168, 69181, 69190, 69207, 69226, 69231, 69234, 69242, 69254, 74918 ],
			'schwetzingen' => [ 68723, 68775, 68782, 69214 ],
			'hockenheim' => [ 68766, 68799, 68804, 68809 ],
		);

		$postcodes = array_merge( $postcodes['heidelberg'], $postcodes['neckargemuend'], $postcodes['walldorf_wiesloch'], $postcodes['schwetzingen'], $postcodes['hockenheim'] );

		if ( ! empty( $query_values['spk_in_range'] ) && 1 === (int) $query_values['spk_in_range'] ) {
			$query_args['meta_query']['spk_in_range'] = [
				'key'     => 'adresse_plz',
				'value'   => $postcodes,
				'compare' => 'IN',
			];
		}

		if ( ! empty( $query_values['spk_in_range'] ) && 0 === (int) $query_values['spk_in_range'] ) {
			$query_args['meta_query']['spk_in_range'] = [
				'key'     => 'adresse_plz',
				'value'   => $postcodes,
				'compare' => 'NOT IN',
			];
		}

		return $query_args;
	}
}
