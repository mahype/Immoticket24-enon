<?php

namespace Enon\Reseller\Tasks\Enon;

use Awsm\WP_Wrapper\Building_Plans\Actions;
use Awsm\WP_Wrapper\Building_Plans\Filters;
use Awsm\WP_Wrapper\Building_Plans\Task;
use Enon\Traits\Logger as Logger_Trait;
use Enon\Logger;
use Enon\Reseller\Models\Reseller;
use WPENON\Model\Energieausweis;

/**
 * Class Wpenon.
 *
 * Running WordPress scripts
 *
 * @package Enon\Reseller\WordPress
 */
class TaskEnon implements Task, Actions, Filters
{
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
	public function run()
	{
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adding actions.
	 *
	 * @since 1.0.0
	 */
	public function add_actions()
	{
		add_action( 'wpenon_confirmation_start', [ $this, 'updateEnergieausweisToken' ] );
		add_action( 'wpenon_energieausweis_create', [ $this, 'updateRessellerId' ] );
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters()
	{
		add_filter( 'wpenon_schema_file',         [ $this, 'filterSchemafile' ], 10, 3 );
		add_filter( 'wpenon_filter_url',          [ $this, 'filterIframeUrl' ] );
		add_filter( 'wpenon_payment_success_url', [ $this, 'filterPaymentSuccessUrl' ] );
		add_filter( 'wpenon_payment_failed_url',  [ $this, 'filterPaymentFailedUrl' ] );
	}

	/**
	 * Updating token of energieausweis
	 *
	 * @since 1.0.0
	 *
	 * @param \WPENON\Model\Energieausweis $energieausweis Energieausweis object.
	 */
	public function updateEnergieausweisToken( $energieausweis ) {
		update_post_meta( $energieausweis->id, 'whitelabel_token', $this->reseller->data()->getToken() );
	}

	/**
	 * Updating reseller id.
	 *
	 * @since 1.0.0
	 *
	 * @param Energieausweis $energieausweis Energieausweis object.
	 */
	public function updateRessellerId( $energieausweis ) {
		update_post_meta( $energieausweis->id, 'reseller_id', $this->reseller->data()->getPostId() );
	}

	/**
	 * Filtering schema file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file Path to file.
	 * @param string $type Schema typ.
	 *
	 * @return string Filtered schema file.
	 */
	public function filterSchemafile( $file, $standard, $type ) {
		switch ( $type ) {
			case 'bw':
				$schema_file = trim( $this->reseller->data()->getBwSchemaFile() );
				break;
			case 'vw':
				$schema_file = trim( $this->reseller->data()->getVwSchemaFile() );
				break;
		}

		if( empty( $schema_file ) ) {
			return $file;
		}

		$schema_file = WPENON_DATA_PATH . '/' . $standard . '/schema/' . $schema_file;

		return $schema_file;
	}

	/**
	 * Filtering iframe URL.
	 *
	 * @param mixed $url Extra query args to add to the URI.
	 *
	 * @return string
	 */
	public function filterIframeUrl( $url ) {
		return $this->reseller->createIframeUrl( $url );
	}

	/**
	 * Filtering payment success URL.
	 *
	 * @param string $old_url Old url.
	 *
	 * @return string
	 */
	public function filterPaymentSuccessUrl( $old_url ) {
		$url = $this->reseller->data()->getPaymentSuccesfulUrl();

		if ( empty( $url ) ) {
			$payment_successful_page = immoticketenergieausweis_get_option( 'it-theme', 'page_for_successful_payment' );

			if ( empty( $payment_successful_page ) ) {
				return $old_url;
			}

			$url = get_permalink( $payment_successful_page );
		}

		$url = $this->reseller->createVerfiedUrl( $url );

		return $url;
	}

	/**
	 * Filtering payment failed URL.
	 *
	 * @param string $old_url Old url.
	 *
	 * @return string
	 */
	public function filterPaymentFailedUrl( $old_url ) {
		$url = $this->reseller->data()->getPaymentFailedUrl();

		if ( empty( $url ) ) {
			$payment_failed_page = immoticketenergieausweis_get_option( 'it-theme', 'page_for_failed_payment' );

			if ( empty( $payment_failed_page ) ) {
				return $old_url;
			}

			$url = get_permalink( $payment_failed_page );
		}

		$url = $this->reseller->createVerfiedUrl( $url );

		return $url;
	}
}
