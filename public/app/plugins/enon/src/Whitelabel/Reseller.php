<?php

namespace Enon\Whitelabel;

use Enon\Exceptions\Exception;
use Enon\Traits\Logger AS LoggerTrait;
use Enon\Logger;

/**
 * Class Reseller
 *
 * @since 1.0.0
 *
 * @package Enon\Whitelabel
 */
class Reseller {
	use LoggerTrait;

	/**
	 * Holds loaded reseller data.
	 *
	 * @since 1.0.0
	 *
	 * @var ResellerData
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
	 * @param ResellerData  $data   Reseller object.
	 * @param Logger        $logger Logger object.
	 */
	public function __construct( ResellerData $data, Logger $logger )
	{
		$this->data   = $data;
		$this->logger = $logger;
	}

	/**
	 * Get reseller values.
	 *
	 * @since 1.0.0
	 *
	 * @return ResellerData
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
			'iframe_token' => $this->data()->getToken(),
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
			'iframe_token' => $this->data()->getToken(),
			'access_token' => md5( get_post_meta( $energieausweis_id, 'wpenon_email', true ) ) . '-' . get_post_meta( $energieausweis_id, 'wpenon_secret', true ),
			'slug' => '',
		);

		if ( ! empty( $energieausweis_id ) ) {
			$post = get_post( $energieausweis_id );

			$query_args['access_token'] = $this->getAccessToken( $energieausweis_id );
			$query_args['slug']         = $post->post_name;
		}

		return add_query_arg( $query_args, trailingslashit( $url ) );
	}

	/**
	 * Get access token for editing page.
	 *
	 * @since 1.0.0
	 *
	 * @param int $energieausweis_id ID of energieausweis.
	 *
	 * @return string $access_token      Token to use in URL.
	 *
	 * @todo This has to go into new energieausweis class.
	 */
	public function getAccessToken( $energieausweis_id ) {
		return md5( get_post_meta( $energieausweis_id, 'wpenon_email', true ) ) . '-' . get_post_meta( $energieausweis_id, 'wpenon_secret', true );
	}
}
