<?php
/**
 * Wrapper class for Payment fIlter coupons functions.
 *
 * @category Class
 * @package  Enon\Models\Edd
 * @author   Rene Reimann
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Edd;

use Awsm\WP_Wrapper\Interfaces\Filters;

/**
 * Class Payment_Used_Coupons
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Taks\Plugins
 */
class Payment_Query_Used_Coupons implements Filters {

	public function add_filters() {
		add_filter( 'posts_results', array( $this, 'add_coupon_meta_query' ), 20, 2 );
	}

	public function add_coupon_meta_query($post){
		global $pagenow, $wpdb;

		$result = [];


		if ( is_admin() && $pagenow == 'edit.php' && ! empty( $_GET['post_type'] ) && $_GET['post_type'] == 'download' && $_GET['page'] == 'edd-payment-history' && ! empty( $_GET['s'] ) ) {
			$request = 'SELECT SQL_CALC_FOUND_ROWS  ' . $wpdb->posts . '.* FROM ' . $wpdb->posts . ' INNER JOIN ' . $wpdb->postmeta . ' AS pm ON pm.post_id = ' . $wpdb->posts . '.ID AND  wpit24_posts.post_type = "edd_payment" AND pm.meta_key="_edd_payment_meta" AND pm.meta_value like "%' . sanitize_text_field( wp_unslash( $_GET['s'] ) ) . '%"';
			$result = $wpdb->get_results($request);
		}
		return array_merge($post, $result);
	}
}
