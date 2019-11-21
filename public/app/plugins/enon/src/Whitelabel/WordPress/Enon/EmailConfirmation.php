<?php

namespace Enon\Whitelabel\WordPress\Enon;

use Awsm\WPWrapper\BuildingPlans\Filters;
use Awsm\WPWrapper\BuildingPlans\Task;
use Enon\Logger;
use Enon\Traits\Logger as LoggerTrait;
use Enon\Whitelabel\Reseller;

/**
 * Class EnonEmailConfirmation.
 *
 * @since 1.0.0
 *
 * @package Enon\Whitelabel\WordPress
 */
class EmailConfirmation implements Task, Filters
{
	use LoggerTrait;

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
	 * @param Logger $logger
	 */
	public function __construct( Reseller $reseller, Logger $logger )
	{
		$this->reseller = $reseller;
		$this->logger = $logger;
	}

	/**
	 * Running scripts.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->addFilters();
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function addFilters()
	{
		add_filter( 'wpenon_confirmation_from_address', [ $this, 'filterFromAddress' ] );
		add_filter( 'wpenon_confirmation_from_name',    [ $this, 'filterFromName' ] );
		add_filter( 'wpenon_confirmation_link',         [ $this, 'filterLink' ], 10, 2 );
		add_filter( 'wpenon_confirmation_site',         [ $this, 'filterSite' ], 10, 1 );

		add_filter( 'wpenon_email_legal',              [ $this, 'filterLegal' ] );
		add_filter( 'wpenon_alternative_email_footer', [ $this, 'filterAlternativeFooter' ] );
		add_filter( 'wpenon_email_signature',          [ $this, 'filterSignature' ], 20 );
	}

	/**
	 * Returning token from email address.
	 *
	 * @since 1.0.0
	 *
	 * @return string Reseller from email address.
	 */
	public function filterFromAddress() {
		return $this->reseller->getEmailFromAddress();
	}

	/**
	 * Returning token from email name.
	 *
	 * $since 1.0.0
	 *
	 * @return string Tokens from email name.
	 */
	public function filterFromName() {
		return $this->reseller->getEmailFromName();
	}

	/**
	 * Set site name to signature.
	 *
	 * $since 1.0.0
	 *
	 * @param string $link Link to filter.
	 * @param \WPENON\Model\Energieausweis Energieausweis
	 *
	 * @return string Filtered signature.
	 */
	public function filterSite() {
		return $this->reseller->getSitename();
	}

	/**
	 * Set site name to signature.
	 *
	 * @since 1.0.0
	 *
	 * @return string Filtered signature.
	 */
	public function filterLink( $link, $energieausweis ) {
		$redirect_url = $this->reseller->getVerfiedUrl( $this->reseller->getCustomerEditUrl(), $energieausweis->id );

		if( false === $redirect_url ) {
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
	public function filterAlternativeFooter() {
		$footer = '<div style="font-size:14px;">';
		$footer.= wpautop( $this->reseller->getEmailFooter() );
		$footer.= '</div>';
		$footer.= '<small>' . sprintf( __( 'Diese Email wurde automatisch von <a href="%s">%s</a> versendet.', 'wpenon' ), $this->reseller->getCustomerEditUrl(), $this->reseller->getSitename() ) . '</small>';
		return $footer;
	}

	/**
	 * Set legal.
	 *
	 * @since 1.0.0
	 *
	 * @return string Filtered footer.
	 */
	public function filterLegal() {
		return sprintf( __( 'Diese Email wurde automatisch von <a href="%s">%s</a> versendet.', 'wpenon' ), $this->reseller->getCustomerEditUrl(), $this->reseller->getSitename() );
	}

	/**
	 * Set signature.
	 *
	 * @since 1.0.0
	 *
	 * @return string Filtered signature.
	 */
	public function filterSignature() {
		$signature = sprintf( __( 'Mit freundlichen Grüßen,
		
		Ihr Team von %s.', 'wpenon' ), $this->reseller->getSitename() );
		return $signature;
	}
}
