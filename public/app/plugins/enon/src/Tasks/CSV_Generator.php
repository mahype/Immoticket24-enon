<?php
/**
 * Task which generates csv files.
 *
 * @category Class
 * @package  Enon\Tasks\Enon
 * @author   Rene Reimann
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;
use Awsm\WP_Wrapper\Tasks\Task_Query_Parser;
use Enon\Task_Loader;

/**
 * Class Setup_Enon.
 *
 * Running enon_csv_generator scripts
 *
 * @package Enon_Reseller\WordPress
 */
class CSV_Generator implements Task, Actions {
	use Task_Query_Parser;

	/**
	 * Task arguments.
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	private $task_arguments = [];

	/**
	 * User.
	 *
	 * @var \WP_User
	 *
	 * @since 1.0.0
	 */
	private $user;

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {

		if ( ! is_user_logged_in() ) {
			return false;
		}

		$this->user = wp_get_current_user();

		if ( ! is_super_admin( $this->user->ID ) || ! $this->user->has_cap( 'view_reseller_leads' ) ) {
			return false;
		}

		$this->set_task_query_prefix( 'reseller_leads' );
		$this->task_arguments = $this->get_parsed_task_queries( $_GET );

		if ( ! empty( $this->task_arguments['reseller'] ) ) {
			$this->add_actions();
		}
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'init', array( $this, 'generate_csv' ) );
	}

	/**
	 * Generates CSV.
	 *
	 * @since 1.0.0
	 */
	public function generate_csv() {
		$args = [
			'post_type'      => [ 'download' ],
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			'meta_query'     => [
				'relation' => 'AND',
				'reseller' => [
					'key'   => 'reseller_id',
					'value' => '321587',
				],
			],
		];

		$filename_aditional = '';

		if ( ! empty( $this->task_arguments['date_range'] ) ) {
			$range = explode( '-', $this->task_arguments['date_range'] );
			$from  = strtotime( $range[0] );
			$to    = strtotime( $range[1] );

			if ( ! empty( $from ) || ! empty( $to ) ) {
				$args['date_query'] = [
					'column'    => 'post_date',
					'after'     => [
						'year'  => date( 'Y', $from ),
						'month' => date( 'm', $from ),
						'day'   => date( 'd', $from ),
					],
					'before'    => [
						'year'  => date( 'Y', $to ),
						'month' => date( 'm', $to ),
						'day'   => date( 'd', $to ),
					],
					'inclusive' => true,
				];

				$from_str = str_replace( '/', '', $range[0] );
				$to_str   = str_replace( '/', '', $range[1] );

				$filename_aditional .= '_daterange_' . $from_str . '_to_' . $to_str;
			}
		}

		if ( ! empty( $this->task_arguments['certificate_checked'] && 1 === $this->task_arguments['certificate_checked'] ) ) {
			$args['meta_query']['certificate_checked'] = [
				'key'   => 'wpenon_immoticket24_certificate_checked',
				'value' => '1',
			];

			$filename_aditional .= '_certificate_checked';
		}

		if ( ! empty( $this->task_arguments['not_in_bussiness_range'] ) && 1 === $this->task_arguments['not_in_bussiness_range'] ) {
			$args['meta_query']['not_in_bussiness_range'] = [
				'key'     => 'adresse_plz',
				'value'   => [
					'06110',
					'69115, 69117, 69118, 69120, 69121, 69123, 69124, 69126, 69151, 69239, 69245, 69250, 69253, 69256, 69257, 69259, 69434, 74909, 74931, 68789, 69168, 69181, 69190, 69207, 69226, 69231, 69234, 69242, 69254, 74918, 68723, 68775, 68782, 69214, 68766, 68799, 68804, 68809',
				],
				'compare' => 'NOT IN',
			];

			$filename_aditional .= '_not_in_bussiness_range';
		}

		$the_query = query_posts( $args );

		if ( $the_query ) {
			$result = [];

			$meta_keys = [
				'Datum Beginn Eingabe'            => 'ausstellungsdatum',
				'Uhrzeit Beginn Eingabe'          => 'ausstellungszeit',
				'Energieausweis-Nr.'              => 'name',
				'Art (Verbrauch- oder Bedarf)'    => 'wpenon_type',
				'Grund'                           => 'anlass',
				'Gebäudetyp'                      => 'gebaeudetyp',
				'Datum + Uhrzeit Ausweis beendet' => '',
				'Bewertung erwünscht'             => 'premium_bewertung',
				'Preis'                           => 'edd_price',
				'Vorname'                         => 'user_info_firstname',
				'Name'                            => 'user_info_lastname',
				'Straße Objekt'                   => 'adresse_strassenr',
				'PLZ Objekt'                      => 'adresse_plz',
				'Ort Objekt'                      => 'adresse_ort',
				'Rechnungsadresse'                => '',
			];

			$result[0] = array_keys( $meta_keys );

			foreach ( $the_query as $post ) {
				$invoice_id   = get_post_meta( $post->ID, '_wpenon_attached_payment_id', true );
				$invoice      = get_post( $invoice_id );
				$invoice_meta  = get_post_meta( $invoice_id, '_edd_payment_meta', true );
				$user_info     = $invoice_meta['user_info'];
				$payment_fees = edd_get_payment_fees( $invoice_id, 'item' );

				foreach ( $meta_keys as $meta_key => $meta_value ) {
					if ( '' === $meta_value ) {
						$result[ $post->ID ][ $meta_key ] = '-';
						continue;
					}

					if ( 'name' === $meta_value ) {
						$result[ $post->ID ][ $meta_key ] = $post->post_name;
						continue;
					}

					if ( 'user_info_firstname' === $meta_value ) {
						$result[ $post->ID ][ $meta_key ] = $user_info['first_name'];
						continue;
					}

					if ( 'user_info_lastname' === $meta_value ) {
						$result[ $post->ID ][ $meta_key ] = $user_info['last_name'];
						continue;
					}

					if ( 'premium_bewertung' === $meta_value ) {
						if ( ! empty( $payment_fees ) && 'premium_bewertung' === $payment_fees[0]['id'] ) {
							$result[ $post->ID ][ $meta_key ] = 'ja';
						} else {
							$result[ $post->ID ][ $meta_key ] = 'nein';
						}
						continue;
					}

					$result[ $post->ID ][ $meta_key ] = get_post_meta( $post->ID, $meta_value, true );
				}
			}

			$filename = 'wpenon_csv_reseller_spk' . $filename_aditional . '.csv';

			$f = fopen( 'php://memory', 'w' );

			foreach ( $result as $line ) {
				fputcsv( $f, $line, ',' );
			}
			fseek( $f, 0 );
			header( 'Content-Type: application/csv' );
			header( 'Content-Disposition: attachment; filename="' . $filename . '";' );
			fpassthru( $f );

			die();
		}
	}
}
