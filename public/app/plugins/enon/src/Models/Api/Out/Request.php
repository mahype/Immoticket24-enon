<?php
/**
 * Class for sending data.
 *
 * @category Class
 * @package  Enon\Models\Api\Out
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Models\Api\Out;

use Enon\Logger;

/**
 * Class Send.
 *
 * @since 1.0.0
 */
class Request {

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
	 * Content to send.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $content;

	/**
	 * Send constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $endpoint URL where data have to be sent.
	 * @param Logger $logger   Logger object.
	 */
	public function __construct( $endpoint, Logger $logger ) {
		$this->endpoint = $endpoint;
		$this->logger   = $logger;
	}

	/**
	 * Set body content
	 *
	 * @param mixed
	 * 
	 * @since 1.0.0
	 */
	public function set_content( $content ) {
		$this->content = $content;
	}

	/**
	 * Settuing up arguments.
	 *
	 * @since 1.0.0
	 */
	protected function setup_args() {
		$this->args = array(
			'body' => $this->content,
			'timeout' => '5',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'cookies' => array(),
		);
	}

	/**
	 * Sending data.
	 *
	 * @since 1.0.0
	 */
	public function post() {
		$this->setup_args();
		$response = wp_remote_post( $this->endpoint, $this->args );
		$status = wp_remote_retrieve_response_code( $response );

		switch ( $status ) {
			case 200:
				$debug_data = array(
					'endpoint' => $this->endpoint,
					'response' => $response,
					'args'     => $this->args,
				);

				$this->logger()->notice( 'Response successful.', $debug_data );
				return true;
			default:
				if ( is_wp_error( $response ) ) {
					$values = array(
						'error_message' => $response->get_error_message(),
					);
				} else {
					$values = $response;
				}

				$this->logger()->warning( 'Response failed.', $values );
				return false;
				break;
		}
	}
}
