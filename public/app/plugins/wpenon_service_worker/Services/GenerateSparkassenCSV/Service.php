<?php
namespace WPENON\ServiceWorker\GenerateSparkassenCSV;

use WPENON\ServiceWorker\Interfaces;
use WPENON\ServiceWorker;

class Service extends ServiceWorker\Services implements Interfaces\Action {

	/**
	 * @var array
	 */
	private $serviceArgument;

	public function __construct() {
		$this::setServiceName('wpenon_csv');
		$this->serviceArgument = $this::getParseServiceArguments($_GET);

		if(!empty($this->serviceArgument['reseller'])){
			add_action('init', [$this, 'initAction'], 2, 1);
		}
	}

	public function initAction() {


		$args = [
			'post_type'     => ['download'],
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'meta_query' => [
				'relation' => 'AND',
				'reseller' =>[
					'key' => 'reseller_id',
					'value' => '321587',
				]
			]
		];

		$filename_aditional = '';

		if(!empty($this->serviceArgument['range_30_days'])){
			$args['date_query'] = [
				'column'  => 'post_date',
				'after'   => '- 30 days'
			];

			$filename_aditional .= '_daterange_30';
		}

		if(!empty($this->serviceArgument['certificate_checked'])){
			$args['meta_query']['certificate_checked'] = [
				'key' => 'wpenon_immoticket24_certificate_checked',
				'value' => '1',
			];

			$filename_aditional .= '_certificate_checked';
		}

		if(!empty($this->serviceArgument['not_in_bussiness_range'])){
			$args['meta_query']['not_in_bussiness_range'] = [
				'key'       => 'adresse_plz',
				'value'     => ['69115, 69117, 69118, 69120, 69121, 69123, 69124, 69126, 69151, 69239, 69245, 69250, 69253, 69256, 69257, 69259, 69434, 74909, 74931, 68789, 69168, 69181, 69190, 69207, 69226, 69231, 69234, 69242, 69254, 74918, 68723, 68775, 68782, 69214, 68766, 68799, 68804, 68809'],
				'compare'   => 'NOT IN'
			];

			$filename_aditional .= '_not_in_bussiness_range';
		}

		$the_query = query_posts($args);

		if($the_query){
			$result = [];

			$meta_keys = [
				'Datum Beginn Eingabe' => 'ausstellungsdatum',
				'Uhrzeit Beginn Eingabe' => 'ausstellungszeit',
				'Energieausweis-Nr.' => 'name',
				'Art (Verbrauch- oder Bedarf)' => 'wpenon_type',
				'Grund' => 'anlass',
				'Gebäudetyp' => 'gebaeudetyp',
				'Datum + Uhrzeit Ausweis beendet' => 'ausstellungszeit',
				'Bewertung erwünscht' => '',
				'Preis' => 'edd_price',
				'Vorname' => 'user_info_firstname',
				'Name' => 'user_info_lastname',
				'Straße Objekt' => 'adresse_strassenr',
				'PLZ Objekt' => 'adresse_plz',
				'Ort Objekt' => 'adresse_ort',
				'Rechnungsadresse' => ''
			];

			$result[0] = array_keys($meta_keys);

			foreach($the_query as $post){
				$invoice_id = get_post_meta($post->ID, '_wpenon_attached_payment_id', true);
				$invoice    = get_post($invoice_id);
				$invoiceMeta = get_post_meta($invoice_id, '_edd_payment_meta', true);
				$userIfno = $invoiceMeta['user_info'];

				foreach ($meta_keys as $meta_key => $meta_value){
					if($meta_value === ''){
						$result[$post->ID][$meta_key] = '-';
						continue;
					}

					if($meta_value === 'name'){
						$result[$post->ID][$meta_key] = $post->post_name;
						continue;
					}

					if($meta_value === 'user_info_firstname'){
						$result[$post->ID][$meta_key] = $userIfno['first_name'];
						continue;
					}

					if($meta_value === 'user_info_lastname'){
						$result[$post->ID][$meta_key] = $userIfno['last_name'];
						continue;
					}

					$result[$post->ID][$meta_key] = get_post_meta($post->ID, $meta_value, true);
				}

			}

			$filename = 'wpenon_csv_reseller_spk' . $filename_aditional . '.csv';

			$f = fopen('php://memory', 'w');

			foreach ($result as $line) {
				fputcsv($f, $line, ',');
			}
			fseek($f, 0);
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename="'.$filename.'";');
			fpassthru($f);
			die();
		}
	}

}
