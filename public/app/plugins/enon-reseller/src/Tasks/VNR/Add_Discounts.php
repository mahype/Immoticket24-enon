<?php
/**
 * Loading ubego discount code tasks.
 *
 * @category Class
 * @package  Enon\Misc
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\VNR;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;

use Enon\Logger;
use Enon_Reseller\Models\Reseller;

/**
 * Class PluginEdd.
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller\Tasks\Sparkasse
 */
class Add_Discounts implements Task, Actions{
	/**
	 * Discount_Types
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	private $discount_types = array();

	/**
	 * Discount_Amounts
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	private $discount_amounts = array();

	/**
	 * Glory coupon codes are valid without further validation.
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	private $glory_codes = array();

	/**
	 * Allowed zip areas.
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	private $allowed_zip_areas = array();

	/**
	 * Allowed zip cities.
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	private $allowed_zip_cities = array();

	/**
	 * Loading Plugin scripts.
	 *
	 * @param Reseller $reseller Logger object.
	 * @param Logger   $logger   Logger object.
	 *
	 * @since 1.0.0
	 */
	public function __construct( Logger $logger ) {
        $this->logger = $logger;
	}

	/**
	 * Running scripts.
	 *
	 * @return mixed|void
	 *
	 * @since 1.0.0
	 */
	public function run() {
        $this->add_actions();
	}
    
    public function add_actions() {
		add_action( 'admin_init', array( $this, 'add_new_discount_codes' ) );
		add_action( 'admin_init', array( $this, 'remove_discounts' ) );
    }


	public function add_new_discount_codes() {
		if( ! array_key_exists('add-vnr-discounts', $_GET) ) {
			return;
		}

		$prefix = 'vnr';
		$num    = 250;
		$code_length = 8;

		for( $i = 0; $i < $num; $i++ ) {
			$hash = substr( md5( $prefix . $i . time() ), 0, $code_length );
			$discount_code = $prefix . '-' . $hash;
			$name = 'vnr ' . ( $i+1 );

			$code_args = [
				'name' => $name,
				'code' => $discount_code,
				'type' => 'percent',
				'amount' => 100,
				'uses' => 0,
				'use_once' => 0,
				'max' => 1,
				'edd-max-uses'
			];

			edd_store_discount( $code_args );

			echo $name . ',' . $discount_code . PHP_EOL;
		}
		exit;
	}

	public function remove_discounts() {
		if( ! array_key_exists('delete-vnr-discounts', $_GET) ) {
			return;
		}

		global $wpdb;

		$prefix = 'vnr';
		$table_name = $wpdb->prefix . 'posts'; // Adjust the table name if needed

		$query = $wpdb->prepare("
			SELECT *
			FROM $table_name
			WHERE post_title LIKE %s
		", $prefix . '%');

		$results = $wpdb->get_results($query, ARRAY_A);

		foreach( $results as $result ) {
			edd_remove_discount( $result['ID'] );
		}
	}
}
