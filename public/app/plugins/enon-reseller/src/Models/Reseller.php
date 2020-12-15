<?php
/**
 * Reseller object.
 *
 * @category Class
 * @package  Enon_Reseller\Models
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 *
 * @todo Replacing old energieausweis class.
 */

namespace Enon_Reseller\Models;

use Awsm\WP_Wrapper\Tools\Logger_Trait;
use Awsm\WP_Wrapper\Tools\Logger;

use Enon\Models\Enon\Energieausweis;
use Enon\Models\Edd\Payment;

/**
 * Class Reseller
 *
 * @package Enon_Reseller
 *
 * @since 1.0.0
 */
class Reseller {
	use Logger_Trait;

	/**
	 * Post id.
	 *
	 * @var int $id
	 *
	 * @since 1.0.0
	 */
	private $id;

	/**
	 * Holds loaded reseller data.
	 *
	 * @var Reseller_Data
	 *
	 * @since 1.0.0
	 */
	private $data;

	/**
	 * Token.
	 *
	 * @var Token
	 *
	 * @since 1.0.0
	 */
	private $token;

	/**
	 * Reseller constructor.
	 *
	 * @param int    $id    Reseller id.
	 * @param Logger $logger Logger object.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $id, Logger $logger ) {
		$this->logger = $logger;
		$this->set_id( $id );
	}

	/**
	 * Get id.
	 *
	 * @return int Post id of reseller.
	 *
	 * @since 1.0.0
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set post id.
	 *
	 * @param int $id Post id.
	 *
	 * @since 1.0.0
	 */
	public function set_id( $id ) {
		$this->id = $id;
		$this->data = new Reseller_Data( $id );
	}

	/**
	 * Get reseller values.
	 *
	 * @return Reseller_Data
	 *
	 * @since 1.0.0
	 */
	public function data() {
		return $this->data;
	}

	/**
	 * Adds iframe and energeausweis parameters to url.
	 *
	 * @param string $url               URL where parameters have to be added.
	 * @param int    $energieausweis_id ID of energy certificate.
	 *
	 * @return string $url               URL with needed parameters.
	 *
	 * @since 1.0.0
	 */
	public function add_iframe_params( $url, $energieausweis_id = null ) {
		$args = array(
			'iframe_token' => $this->data()->general->get_token(),
		);

		// If there is an existing energy certificate, get slug and access token.
		if ( ! empty( $energieausweis_id ) ) {
			$post = get_post( $energieausweis_id );

			$args['access_token'] = ( new Energieausweis( $energieausweis_id ) )->get_access_token();
			$args['slug']         = $post->post_name;
		}

		$this->logger()->notice( 'Adding iframe params.', $args );

		return add_query_arg( $args, $url );
	}
}
