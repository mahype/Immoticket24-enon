<?php
/**
 * Task which loads email confirmation scripts.
 *
 * @category Class
 * @package  Enon_Reseller\Tasks\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Tasks\Filters;

use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Tools\Logger_Trait;

use Enon_Reseller\Models\Reseller;
use WPENON\Model\Energieausweis as Energieausweis_Old;

/**
 * Class Filter_Confirmation_Email.
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller\WordPress
 */
class Filter_Confirmation_Email implements Task, Filters {
	use Logger_Trait;

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
		add_filter( 'wpenon_confirmation_sender_name', array( $this, 'filter_sender_name' ) );
		add_filter( 'wpenon_confirmation_sender_email', array( $this, 'filter_sender_email' ) );
		add_filter( 'wpenon_confirmation_subject', array( $this, 'filter_subject' ) );
		add_filter( 'wpenon_confirmation_content', array( $this, 'filter_content' ) );

		add_filter( 'wpenon_confirmation_energieausweis_link', array( $this, 'filter_link' ), 10, 2 );
	}

	/**
	 * Filter sender email.
	 *
	 * @param string $email Email from address to filter.
	 *
	 * @return string $email Filtered email address.
	 *
	 * @since 1.0.0
	 */
	public function filter_sender_email( $email ) {
		$sender_mail = $this->reseller->data()->confirmation_email->get_sender_email();

		if ( empty( $sender_mail ) ) {
			return $email;
		}

		return $sender_mail;
	}

	/**
	 * Filter sender name.
	 *
	 * @param string $name Name of the sender.
	 *
	 * @return string $name Filtered name of the sender.
	 *
	 * @since 1.0.0
	 */
	public function filter_sender_name( $name ) {
		$sender_name = $this->reseller->data()->confirmation_email->get_sender_name();

		if ( empty( $sender_name ) ) {
			return $name;
		}

		return $sender_name;
	}

	/**
	 * Filter subject.
	 *
	 * @param string $name Name of the sender.
	 *
	 * @return string $name Reseller sender name if given or global sender name.
	 *
	 * @since 1.0.0
	 */
	public function filter_subject( $name ) {
		$subject = $this->reseller->data()->confirmation_email->get_subject();

		if ( empty( $subject ) ) {
			return $name;
		}

		return $subject;
	}

	/**
	 * Filter content.
	 *
	 * @param string $content Content.
	 *
	 * @return string Filtered content.
	 *
	 * @since 1.0.0
	 */
	public function filter_content( $content ) {
		$reseller_content = $this->reseller->data()->confirmation_email->get_content();

		if ( empty( $reseller_content ) ) {
			return $content;
		}

		return $reseller_content;
	}



	/**
	 * Filter link.
	 *
	 * @param string $link Link.
	 * @param Energieausweis_Old $energy_certificate Energy certificate object.
	 *
	 * @return string Filtered link.
	 *
	 * @since 1.0.0
	 */
	public function filter_link( $link, $energy_certificate ) {
		$reseller_link = $this->reseller->data()->website->get_customer_edit_url();

		if ( empty( $reseller_link ) ) {
			return $link;
		}

		$reseller_link = $this->reseller->add_iframe_params( $reseller_link, $energy_certificate->id );

		return $reseller_link;
	}
}
