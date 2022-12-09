<?php
/**
 * Filter for bill email "Zahlungsaufforderung" which is sent to the customer.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Filters;

use Awsm\WP_Wrapper\Interfaces\Filters;
use Awsm\WP_Wrapper\Interfaces\Task;
use Enon\Logger;

use Enon_Reseller\Models\Reseller;

/**
 * Class EnonEmailOrderConfirmation.
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller\WordPress
 */
class Filter_Bill_Email implements Task, Filters {
	/**
	 * Reseller object.
	 *
	 * @since 1.0.0
	 * @var Reseller;
	 */
	private $reseller;

	/**
	 * Wpenon constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Reseller $reseller Reseller object.
	 * @param Logger   $logger   Logger object.
	 */
	public function __construct( Reseller $reseller, Logger $logger ) {
		$this->reseller = $reseller;
		$this->logger   = $logger;
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->add_filters();
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		add_filter( 'wpenon_bill_sender_name', array( $this, 'filter_sender_name' ) );
		add_filter( 'wpenon_bill_sender_email', array( $this, 'filter_sender_email' ) );
		add_filter( 'wpenon_bill_subject', array( $this, 'filter_subject' ) );
		add_filter( 'wpenon_bill_content', array( $this, 'filter_content' ) );
		add_filter( 'wpenon_bill_recipients', array( $this, 'filter_recipients' ) );
	}

	/**
	 * Filter sender name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name From name.
	 *
	 * @return string From name.
	 */
	public function filter_sender_name( $name ) {
		$sender_name = $this->reseller->data()->billing_email->get_sender_name();

		if ( empty( $sender_name ) ) {
			return $name;
		}

		return $sender_name;
	}	

	/**
	 * Filter sender email.
	 *
	 * @since 1.0.0
	 *
	 * @param string $email Sender email.
	 *
	 * @return string Filtered sender email.
	 */
	public function filter_sender_email( $email ) {
		$sender_email = $this->reseller->data()->billing_email->get_sender_email();

		if ( empty( $sender_email ) ) {
			return $email;
		}

		return $sender_email;
	}

	/**
	 * Filter subject.
	 *
	 * @since 1.0.0
	 *
	 * @param string $subject Subject.
	 *
	 * @return string Filtered subject.
	 */
	public function filter_subject( $subject ) {
		$reseller_subject = $this->reseller->data()->billing_email->get_subject();

		if ( empty( $reseller_subject ) ) {
			return $subject;
		}

		return $reseller_subject;
	}

	/**
	 * Filter content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Content.
	 *
	 * @return string Filtered content.
	 */
	public function filter_content( $content ) {
		$reseller_content = $this->reseller->data()->billing_email->get_content();

		if ( empty( $reseller_content ) ) {
			return $content;
		}

		return $reseller_content;
	}

	/**
	 * Filter recipients.
	 *
	 * @since 1.0.0
	 *
	 * @param array $recipients Recipients.
	 *
	 * @return array Filtered recipients.
	 */
	public function filter_recipients( $recipients ) {
		// No recipients yet.
		return $recipients;
	}
}
