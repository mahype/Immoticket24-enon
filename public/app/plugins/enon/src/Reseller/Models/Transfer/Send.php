<?php

namespace Enon\Reseller\Models\Transfer;

use Enon\Logger;
use Enon\Traits\Logger As Logger_Trait;

/**
 * Class Send.
 *
 * @since 1.0.0
 */
abstract class Send {
	use Logger_Trait;

	/**
	 * Arguments for post.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $args = array();

	/**
	 * Endpoint to send data.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $endpoint;

	/**
	 * Send constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $endpoint
	 * @param Logger
	 */
	public function __construct( $endpoint, Logger $logger )
	{
		$this->endpoint = $endpoint;
		$this->logger = $logger;
	}

	/**
	 * Get body to send to endpoint.
	 *
	 * @return mixed
	 */
	abstract protected function getBody();

	/**
	 * Settuing up arguments.
	 *
	 * @since 1.0.0
	 */
	protected function setupArgs()
	{
		$body =  $this->getBody();

		$this->args = array(
			'body' => $body,
			'timeout' => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'cookies' => array()
		);
	}

	/**
	 * Sending data.
	 *
	 * @since 1.0.0
	 */
	public function send()
	{
		$this->setupArgs();
		// @todo Switch between request methods
		$response = wp_remote_post( $this->endpoint, $this->args );
		$status = wp_remote_retrieve_response_code( $response );

		$body = wp_remote_retrieve_body( $response );
		$bodyArr = json_decode( $body );

		switch ( $status ) {
			case 200:
				return true;
			default:
				$this->logger()->warning( sprintf( 'Error %s on sending data.', $response ));
				break;
		}
	}
}
