<?php
/**
 * Scritps
 *
 * @category Class
 * @package  Enon\CLI\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class Test_Scrtipt.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Tasks\Core
 */
class Test_Script implements Task {
	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		if( isset( $_GET ) && array_key_exists( 'dododo', $_GET ) ) {
			$dododo = $_GET['dododo'];
			switch( $dododo ) {
				case 'payments':
					add_action( 'init', [ $this, 'dododo' ] );
					break;
			}
			
		}		
	}

	public function dododo() {
		$args =  [
			'status'     => 'publish',
			'start_date' => '2022-02-02 22:00:00',
			'end_date'   => date( 'Y-m-d H:i:s', time() ),
		];

		$payment_query = new \EDD_Payments_Query( $args );
		$payments      = $payment_query->get_payments();
		$filtered      = [];

		foreach( $payments AS $payment ) {
			$fees = $payment->get_fees();
			$found = false;

			foreach( $fees as $fee ) {
				if( $fee['id'] == 'sendung_per_post' ) {
					$found = true;
				}
			}

			if( $found ) {
				$url = get_site_url() . '/core/wp-admin/edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $payment->ID;

				$filtered[] = [
					'payment_id' => $payment->ID,
					'url'        => $url,
				];

				// print_r( $payment );

				echo $payment->date . ' - <a href="' . $url . '" target="_blank">' . $url . '</a><br />' . chr(13);
			}
		}		
		
		exit;
	}
}