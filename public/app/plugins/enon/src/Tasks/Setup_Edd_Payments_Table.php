<?php
/**
 * Setting up EDD bill tables.
 *
 * @category Class
 * @package  Enon\Config\Tasks
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Tasks;

use Awsm\WP_Wrapper\Interfaces\Hooks_Actions;
use Awsm\WP_Wrapper\Interfaces\Service;
use Awsm\WP_Wrapper\Loaders\Hooks_Loader;
use Awsm\WP_Wrapper\Loaders\Loader;
use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;
use Enon\Models\Edd\Payment;
use Enon\Models\Enon\Energieausweis;

/**
 * Class Setup_Gutenberg.
 *
 * @package awsmug\Enon\Tools
 *
 * @since 1.0.0
 */
class Setup_Edd_Payments_Table implements Filters, Task {
	/**
	 * Running tasks.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_filters();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'edd_payment_row_actions', array( $this, 'payment_rows' ), 10, 2 );
	}

	/**
	 * Setup payment rows.
	 *
	 * @param array        $row_actions All row actions.
	 * @param \EDD_Payment $edd_payment     Contains all the data of the payment.
	 *
	 * @return array Filtered row actions.
	 *
	 * @since 1.0.0
	 */
	public function payment_rows( $row_actions, $edd_payment ) {
		unset( $row_actions['email_links'] );
		unset( $row_actions['delete'] );

		$payment = new Payment( $edd_payment->ID );
		$energy_certificate_id = $payment->get_energieausweis_id();
		$energy_certificate_permalink = get_permalink( $energy_certificate_id );

		$row_actions['energy_certificate']     = '<a href="' . admin_url( "post.php?post={$energy_certificate_id}&action=edit&classic-editor" ) . '">' . __( 'Energieausweis bearbeiten', 'enon' ) . '</a>';
		$row_actions['energy_certificate_pdf'] = '<a href="' . add_query_arg( 'action', 'pdf-view', $energy_certificate_permalink ) . '">' . __( 'Energieausweis PDF', 'enon' ) . '</a>';

		return $row_actions;
	}

}
