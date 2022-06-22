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
use Enon\Models\Enon\Energieausweis;

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
				case 'ec':
					add_action( 'init', [ $this, 'dododo' ] );
					break;
			}
			
		}		
	}

	public function dododo() {
		$ec = new Energieausweis(653050);
		$url = $ec->get_access_url();
		
		exit;
	}
}