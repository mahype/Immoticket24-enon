<?php

namespace Enon\Reseller\Tasks\Enon;

use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Enon\Logger;
use Enon\Traits\Logger as Logger_Trait;
use Enon\Reseller\Models\Reseller;

/**
 * Class EnonEmailConfirmation.
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\WordPress
 */
class TaskEmailConfirmation implements Task, Filters {

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
	 * @param Reseller $reseller
	 * @param Logger   $logger
	 */
	public function __construct( Reseller $reseller, Logger $logger ) {
		 $this->reseller = $reseller;
		$this->logger = $logger;
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
		add_filter( 'wpenon_confirmation_from_address', array( $this, 'filterFromAddress' ) );
		add_filter( 'wpenon_confirmation_from_name', array( $this, 'filterFromName' ) );
		add_filter( 'wpenon_confirmation_link', array( $this, 'filterLink' ), 10, 2 );
		add_filter( 'wpenon_confirmation_site', array( $this, 'filterSiteName' ), 10, 1 );

		add_filter( 'wpenon_email_legal', array( $this, 'filterLegal' ) );
		add_filter( 'wpenon_alternative_email_footer', array( $this, 'filterAlternativeFooter' ) );
		add_filter( 'wpenon_email_signature', array( $this, 'filterSignature' ), 20 );
	}

	/**
	 * Returning token from email address.
	 *
	 * @since 1.0.0
	 *
	 * @return string Reseller from email address.
	 */
	public function filterFromAddress( $email ) {
		$resellerEmail = $this->reseller->data()->getEmailSenderAddress();

		if ( empty( $resellerEmail ) ) {
			return $email;
		}

		return $resellerEmail;
	}

	/**
	 * Returning token from email name.
	 *
	 * $since 1.0.0
	 *
	 * @return string Tokens from email name.
	 */
	public function filterFromName( $name ) {
		$resellerName = $this->reseller->data()->getEmailSenderName();

		if ( empty( $resellerName ) ) {
			return $name;
		}

		return $resellerName;
	}

	/**
	 * Set site name to signature.
	 *
	 * $since 1.0.0
	 *
	 * @param string                                      $link Link to filter.
	 * @param \WPENON\Model\Energieausweis Energieausweis
	 *
	 * @return string Filtered signature.
	 */
	public function filterSiteName( $siteName ) {
		$resellerSiteName = $this->reseller->data()->getWebdsiteName();

		if ( empty( $resellerSiteName ) ) {
			return $siteName;
		}

		return $resellerSiteName;
	}

	/**
	 * Set site name to signature.
	 *
	 * @since 1.0.0
	 *
	 * @return string Filtered signature.
	 */
	public function filterLink( $link, $energieausweis ) {
		$customerEditUrl = $this->reseller->data()->getCustomerEditUrl();

		if ( empty( $customerEditUrl ) ) {
			return $link;
		}

		$redirect_url = $this->reseller->createVerfiedUrl( $customerEditUrl, $energieausweis->id );

		if ( false === $redirect_url ) {
			return $link;
		}

		return $redirect_url;
	}

	/**
	 * Set email footer.
	 *
	 * @since 1.0.0
	 *
	 * @return string $footer Footer content.
	 */
	public function filterAlternativeFooter( $footer ) {
		$resellerFooter = $this->reseller->data()->getEmailFooter();

		if ( empty( $resellerFooter ) ) {
			return $footer;
		}

		$footer = '<div style="font-size:14px;">';
		$footer .= wpautop( $resellerFooter );
		$footer .= '</div>';
		$footer .= '<small>' . sprintf( __( 'Diese Email wurde automatisch von <a href="%1$s">%2$s</a> versendet.', 'wpenon' ), $this->reseller->data()->getCustomerEditUrl(), $this->reseller->data()->getWebdsiteName() ) . '</small>';

		return $footer;
	}

	/**
	 * Set legal.
	 *
	 * @since 1.0.0
	 *
	 * @return string Filtered footer.
	 */
	public function filterLegal( $legal ) {
		$resellerCustomerEditUrl = $this->reseller->data()->getCustomerEditUrl();
		$resellerWebsiteName = $this->reseller->data()->getWebdsiteName();

		if ( empty( $resellerCustomerEditUrl ) ) {
			return $legal;
		}

		if ( empty( $resellerWebsiteName ) ) {
			$parsedUrl = parse_url( $resellerCustomerEditUrl );

			if ( ! $parsedUrl ) {
				$resellerWebsiteName = $resellerCustomerEditUrl;
			} else {
				$resellerWebsiteName = $parsedUrl['host'];
			}
		}

		return sprintf( __( 'Diese Email wurde automatisch von <a href="%1$s">%2$s</a> versendet.', 'wpenon' ), $resellerCustomerEditUrl, $resellerWebsiteName );
	}

	/**
	 * Set signature.
	 *
	 * @since 1.0.0
	 *
	 * @return string Filtered signature.
	 */
	public function filterSignature( $signature ) {
		$resellerWebsiteName = $this->reseller->data()->getWebdsiteName();

		if ( empty( $resellerWebsiteName ) ) {
			return $signature;
		}

		$signature = sprintf(
			__(
				'Mit freundlichen Grüßen,

		Ihr Team von %s.',
				'wpenon'
			),
			$resellerWebsiteName
		);
		return $signature;
	}
}
