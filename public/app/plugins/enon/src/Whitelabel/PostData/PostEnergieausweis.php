<?php

namespace Enon\Whitelabel\PostData;

use \WPENON\Model\Energieausweis;
use Enon\Logger;

/**
 * Class ResellerSendEnergieausweis
 *
 * @since 1.0.0
 */
abstract class PostEnergieausweis {
	/**
	 * Arguments for post.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $args = array();

	/**
	 * Energieausweis Object.
	 *
	 * @since 1.0.0
	 *
	 * @var Energieausweis
	 */
	protected $energieausweis;

	/**
	 * Endpoint to send data.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $endpoint;

	/**
	 * Logger.
	 *
	 * @since 1.0.0
	 *
	 * @var Logger
	 */
	protected $logger;

	/**
	 * ResellerSendEnergieausweis constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $endpoint
	 * @param Energieausweis $energieausweis
	 * @param Logger
	 */
	public function __construct( $endpoint, Energieausweis $energieausweis, Logger $logger )
	{
		$this->energieausweis = $energieausweis;
		$this->endpoint = $endpoint;
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
	public function setup()
	{
		$this->setupArgs();
		$response = wp_remote_post( $this->endpoint, $this->args );
		$status = wp_remote_retrieve_response_code( $response );

		switch ( $status ) {
			case 200:
				return true;
			default:
				$this->logger->warning( sprintf( 'Error %s on sending data.', $response ));
				break;
		}
	}
}
