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

namespace Enon_Reseller\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;
use Awsm\WP_Wrapper\Tasks\Task_Query_Parser;

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

		if ( ! is_super_admin( $this->user->ID ) && ! $this->user->has_cap( 'view_reseller_leads' ) ) {
			return false;
		}

		$this->set_query_parameter_prefix( 'reseller_leads' );
		$this->set_query( $_GET );

		if ( $this->has_query_values() ) {
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
	 *
	 * @todo Has to go to into models and to be abstracted.
	 */
	public function generate_csv() {
		$reseller_id = (int) get_user_meta( $this->user->ID, 'reseller_id', true );

		if ( empty( $reseller_id ) ) {
			wp_die( 'No reseller id given.' );
		}

		$args = [
			'post_type'      => [ 'download' ],
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			'meta_query'     => [
				'relation' => 'AND',
				'reseller' => [
					'key'   => 'reseller_id',
					'value' => $reseller_id,
				],
			],
		];
		$values = $this->get_query_values();

		$filename_aditional = '';

		if ( ! empty( $values['date_range'] ) ) {
			$range = explode( '|', $values['date_range'] );
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

		if ( ! empty( $values['certificate_checked'] && 1 === $values['certificate_checked'] ) ) {
			$args['meta_query']['certificate_checked'] = [
				'key'   => 'wpenon_immoticket24_certificate_checked',
				'value' => '1',
			];

			$filename_aditional .= '_certificate_checked';
		}

		/**
		 * Filter query args for reseller leads data.
		 *
		 * @param array $args Query arguments.
		 * @param array $values Task query values.
		 *
		 * @since 1.0.0
		 */
		$args = apply_filters( 'enon_reseller_leads_quer_args', $args, $values );

		$posts = query_posts( $args );

		if ( $posts ) {
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

			foreach ( $posts as $post ) {
				$invoice_id   = get_post_meta( $post->ID, '_wpenon_attached_payment_id', true );
				$invoice_meta = get_post_meta( $invoice_id, '_edd_payment_meta', true );
				$user_info    = $invoice_meta['user_info'];
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

			$filename = 'reseller_leads' . $filename_aditional . '.csv';

			$f = fopen( 'php://memory', 'w' );

			foreach ( $result as $line ) {
				$line = mb_convert_encoding( $line, 'UTF-16LE', 'UTF-8'));
				fputcsv( $f, $line, ';' );
			}
			fseek( $f, 0 );
			header( 'Content-Type: application/csv; charset=Windows-1252' );
			header( 'Content-Disposition: attachment; filename="' . $filename . '";' );
			fpassthru( $f );

			die();
		}
	}
}
