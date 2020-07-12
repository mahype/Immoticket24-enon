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

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Task;
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

	private $task_arguments = [];

	/* @var \WP_User*/
	private $user = null;

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		// bail early if not logged in
		if( !is_user_logged_in() ) return false;

		$this->user = wp_get_current_user();
		if(!is_super_admin( $this->user ->ID )) return false;

		$this->set_task_query_prefix('wpenon_csv');
		$this->task_arguments = $this->get_parsed_task_queries($_GET);

		if (!empty($this->task_arguments['reseller'])) {
			$this->add_actions();
		}
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {

		add_action('init', array($this, 'generate_csv'));
	}

	public function generate_csv() {

		$args = [
			'post_type'      => ['download'],
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
			'meta_query'     => [
				'relation' => 'AND',
				'reseller' => [
					'key'   => 'reseller_id',
					'value' => '321587',
				]
			]
		];

		$filename_aditional = '';

		if (!empty($this->task_arguments['date_ragne'])) {
			$range = explode('-', $this->task_arguments['date_ragne']);
			$from  = strtotime($range[0]);
			$to    = strtotime($range[1]);

			if (!empty($from) || !empty($to)) {
				$args['date_query'] = [
					'column'    => 'post_date',
					'after'     => [
						'year'  => date("Y", $from),
						'month' => date("m", $from),
						'day'   => date("d", $from),
					],
					'before'    => [
						'year'  => date("Y", $to),
						'month' => date("m", $to),
						'day'   => date("d", $to),
					],
					'inclusive' => TRUE,
				];

				$from_str = str_replace('/', '', $range[0]);
				$to_str   = str_replace('/', '', $range[1]);

				$filename_aditional .= '_daterange_' . $from_str . '_to_' . $to_str;
			}
		}

		if (!empty($this->task_arguments['certificate_checked'] && $this->task_arguments['certificate_checked'] === 1)) {
			$args['meta_query']['certificate_checked'] = [
				'key'   => 'wpenon_immoticket24_certificate_checked',
				'value' => '1',
			];

			$filename_aditional .= '_certificate_checked';
		}

		if (!empty($this->task_arguments['not_in_bussiness_range']) && $this->task_arguments['not_in_bussiness_range'] === 1) {
			$args['meta_query']['not_in_bussiness_range'] = [
				'key'     => 'adresse_plz',
				'value'   => ['06110', '69115, 69117, 69118, 69120, 69121, 69123, 69124, 69126, 69151, 69239, 69245, 69250, 69253, 69256, 69257, 69259, 69434, 74909, 74931, 68789, 69168, 69181, 69190, 69207, 69226, 69231, 69234, 69242, 69254, 74918, 68723, 68775, 68782, 69214, 68766, 68799, 68804, 68809'],
				'compare' => 'NOT IN'
			];

			$filename_aditional .= '_not_in_bussiness_range';
		}

		$the_query = query_posts($args);

		if ($the_query) {
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
				'Rechnungsadresse'                => ''
			];

			$result[0] = array_keys($meta_keys);

			foreach ($the_query as $post) {
				$invoice_id   = get_post_meta($post->ID, '_wpenon_attached_payment_id', TRUE);
				$invoice      = get_post($invoice_id);
				$invoiceMeta  = get_post_meta($invoice_id, '_edd_payment_meta', TRUE);
				$userIfno     = $invoiceMeta['user_info'];
				$payment_fees = edd_get_payment_fees($invoice_id, 'item');

				foreach ($meta_keys as $meta_key => $meta_value) {
					if ($meta_value === '') {
						$result[$post->ID][$meta_key] = '-';
						continue;
					}

					if ($meta_value === 'name') {
						$result[$post->ID][$meta_key] = $post->post_name;
						continue;
					}

					if ($meta_value === 'user_info_firstname') {
						$result[$post->ID][$meta_key] = $userIfno['first_name'];
						continue;
					}

					if ($meta_value === 'user_info_lastname') {
						$result[$post->ID][$meta_key] = $userIfno['last_name'];
						continue;
					}

					if ($meta_value === 'premium_bewertung') {
						if (!empty($payment_fees) && $payment_fees[0]['id'] === 'premium_bewertung') {
							$result[$post->ID][$meta_key] = 'ja';
						} else {
							$result[$post->ID][$meta_key] = 'nein';
						}
						continue;
					}

					$result[$post->ID][$meta_key] = get_post_meta($post->ID, $meta_value, TRUE);
				}

			}

			$filename = 'wpenon_csv_reseller_spk' . $filename_aditional . '.csv';

			$f = fopen('php://memory', 'w');

			foreach ($result as $line) {
				fputcsv($f, $line, ',');
			}
			fseek($f, 0);
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename="' . $filename . '";');
			fpassthru($f);


			die();
		}
	}

}
