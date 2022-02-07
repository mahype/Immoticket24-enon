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
			'number'     => 20,
			'status'     => 'publish',
			'start_date' => '2022-02-02 22:00:00'
		];
		
		$payments     = new \EDD_Payments_Query( $args );
		$last_payment = $payments->get_payments();
        
        print_r( $args );
		print_r( $last_payment );
		exit;
	}
}