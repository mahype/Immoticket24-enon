<?php
/**
 * Fixing misleaded incvoices
 *
 * @category Class
 * @package  Enon\Misc\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Actions;
use Awsm\WP_Wrapper\Interfaces\Task;

/**
 * Class Fix_Misleaded_Invoices
 *
 * @package awsmug\Enon\Tools
 *
 * @since 2020-08-19
 */
class Fix_Misleaded_Invoices implements Actions, Task {
	/**
	 * Running tasks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run() {
		$this->add_actions();
	}

	/**
	 * Load targeting scripts into hooks.
	 *
	 * @since 1.0.0
	 */
	public function add_actions() {
		add_action( 'admin_notices', array( $this, 'show_misleaded_invoices' ) );
	}

	private function get_downloads_with_multiple_invoices() {
		global $wpdb;
		$sql = "SELECT p.ID, p.post_date, p.post_title, COUNT(ID) AS counter FROM `wpit24_posts` AS p, `wpit24_postmeta` AS pm WHERE pm.post_id=p.ID AND pm.meta_key='_wpenon_attached_payment_id' AND p.post_date > '2020-07-15' GROUP BY p.ID HAVING COUNT(ID) > 1";
		return $wpdb->get_results( $sql );
	}

	private function get_attached_payments( $download_id ) {
		global $wpdb;

		$sql = $wpdb->prepare( "SELECT pm.meta_id, pm.meta_value AS ID FROM `wpit24_posts` AS p, `wpit24_postmeta` AS pm WHERE pm.post_id=p.ID AND p.ID=%d AND pm.meta_key='_wpenon_attached_payment_id'", $download_id );
		return $wpdb->get_results( $sql );
	}

	private function payment_contains_download( $payment_id, $download_id ) {
		$payment           = edd_get_payment( $payment_id );
		$payment_downloads = $payment->downloads;

		foreach ( $payment_downloads as $payment_download ) {
			if ( (int) $payment_download['id'] === (int) $download_id ) {
				return true;
			}
		}

		return false;
	}

	private function download_has_incorrect_payment_ids( $download_id ) {
		$payments = $this->get_attached_payments( $download_id );

		foreach ( $payments as $payment ) {
			if ( ! $this->payment_contains_download( $payment->ID, $download_id ) ) {
				return true;
			}
		}

		return false;
	}

	private function fix_incorrect_payment_ids( $download_id ) {
		$payments = $this->get_attached_payments( $download_id );

		foreach ( $payments as $payment ) {
			if ( ! $this->payment_contains_download( $payment->ID, $download_id ) ) {
				$download_url = admin_url( sprintf( 'post.php?post=%d&action=edit', $download_id ) );
				$invoice_url = admin_url( sprintf( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=%d', $payment->ID ) );
				echo sprintf( '<span style="color:red">Payment %d have to be removed from download %d</span> - ', $payment->ID, $download_id );
				echo sprintf( '<a href="%s" target="_blank">Download</a> | ', $download_url );
				echo sprintf( '<a href="%s" target="_blank">Invoice</a><br />', $invoice_url );
			} else {
				$download_url = admin_url( sprintf( 'post.php?post=%d&action=edit', $download_id ) );
				$invoice_url = admin_url( sprintf( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=%d', $payment->ID ) );
				echo sprintf( 'Payment %d from download %d seems to be OK - ', $payment->ID, $download_id );
				echo sprintf( '<a href="%s" target="_blank">Download</a> | ', $download_url );
				echo sprintf( '<a href="%s" target="_blank">Invoice</a><br />', $invoice_url );
			}
		}
	}

	public function remove_payment_from_download( $payment_id, $download_id ) {
		global $wpdb;

		$data   = [
			'post_id' => $download_id,
			'_wpenon_attached_payment_id' => $payment_id,
		];
		$format = [ '%d', '%d' ];

		return $wpdb->delete( 'wpit24_postmeta', $data, $format );
	}

	/**
	 * Add JS.
	 *
	 * @since 1.0.0
	 */
	public function show_misleaded_invoices() {
		if ( ! array_key_exists( 'misleaded_invoices', $_GET ) ) {
			return;
		}

		$downloads = $this->get_downloads_with_multiple_invoices();

		foreach ( $downloads as $download ) {
			if( $this->download_has_incorrect_payment_ids( $download->ID ) ) {
				echo '<b>Inorrect invoices for ' .  $download->post_title . '</b><br />';
				$this->fix_incorrect_payment_ids( $download->ID );
			}
		}
	}
}
