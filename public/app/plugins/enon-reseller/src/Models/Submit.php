<?php
/**
 * Parent class for sending data.
 *
 * @category Class
 * @package  Enon_Reseller\Models\Transfer
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon_Reseller\Models;

use Awsm\WP_Wrapper\Tools\Logger;
use Awsm\WP_Wrapper\Traits\Logger as Logger_Trait;

/**
 * Class Send.
 *
 * @since 1.0.0
 */
abstract class Submit {
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
	 * @param string $endpoint URL where data have to be sent.
	 * @param Logger $logger   Logger object.
	 */
	public function __construct( $endpoint, Logger $logger ) {
		$this->endpoint = $endpoint;
		$this->logger   = $logger;
	}

	/**
	 * Get body to send to endpoint.
	 *
	 * @return mixed
	 */
	abstract protected function get_body();

	/**
	 * Settuing up arguments.
	 *
	 * @since 1.0.0
	 */
	protected function setup_args() {
		$body = $this->get_body();

		$this->args = array(
			'body' => $body,
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
	public function submit() {
		$this->setup_args();
		// @todo Switch between request methods
		$response = wp_remote_post( $this->endpoint, $this->args );
		$status = wp_remote_retrieve_response_code( $response );

		$body = wp_remote_retrieve_body( $response );

		switch ( $status ) {
			case 200:
				return true;
			default:
				$this->logger()->warning( sprintf( 'Error %s on sending data.', $response ) );
				break;
		}
	}
}
