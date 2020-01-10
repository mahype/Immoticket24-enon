<?php

namespace Enon\Reseller\Models;

use Enon\Models\Enon\Energieausweis;
use Enon\Traits\Logger as Logger_Trait;
use Enon\Logger;

/**
 * Class Reseller
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller
 */
class Reseller {
	use Logger_Trait;

	/**
	 * Holds loaded reseller data.
	 *
	 * @since 1.0.0
	 *
	 * @var Reseller_Data
	 */
	private $data;

	/**
	 * Token.
	 *
	 * @since 1.0.0
	 *
	 * @var Token
	 */
	private $token;

	/**
	 * Reseller constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Reseller_Data $data   Reseller data object.
	 * @param Logger       $logger Logger object.
	 */
	public function __construct( Reseller_Data $data, Logger $logger ) {
		$this->data   = $data;
		$this->logger = $logger;
	}

	/**
	 * Get reseller values.
	 *
	 * @since 1.0.0
	 *
	 * @return Reseller_Data
	 */
	public function data() {
		return $this->data;
	}

	/**
	 * Filtering iframe URL.
	 *
	 * @param mixed $url Extra query args to add to the URI.
	 *
	 * @return string
	 */
	public function createIframeUrl( $url ) {
		$args = array(
			'iframe_token' => $this->data()->get_token(),
		);

		return add_query_arg( $args, $url );
	}

	/**
	 * Adds iframe and energeausweis parameters to url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url               URL where parameters have to be added.
	 * @param int    $energieausweis_id ID of energieausweis.
	 *
	 * @return string $url               URL with needed parameters.
	 */
	public function createVerfiedUrl( $url, $energieausweis_id = null ) {
		$query_args = array(
			'iframe'       => true,
			'iframe_token' => $this->data()->get_token(),
			'access_token' => md5( get_post_meta( $energieausweis_id, 'wpenon_email', true ) ) . '-' . get_post_meta( $energieausweis_id, 'wpenon_secret', true ),
			'slug' => '',
		);

		if ( ! empty( $energieausweis_id ) ) {
			$post = get_post( $energieausweis_id );

			$query_args['access_token'] = ( new Energieausweis( $energieausweis_id ) )->getAccessToken();
			$query_args['slug']         = $post->post_name;
		}

		return add_query_arg( $query_args, trailingslashit( $url ) );
	}
}
