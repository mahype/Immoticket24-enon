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

use Enon\Models\Enon\Energieausweis;
use Awsm\WP_Wrapper\Traits\Logger as Logger_Trait;
use Awsm\WP_Wrapper\Tools\Logger;

/**
 * Class Reseller
 *
 * @since 1.0.0
 *
 * @package Enon_Reseller
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
	 * @param Logger        $logger Logger object.
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
	public function create_iframe_url( $url ) {
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
	public function create_verfied_url( $url, $energieausweis_id = null ) {
		$query_args = array(
			'iframe'       => true,
			'iframe_token' => $this->data()->get_token(),
			'access_token' => md5( get_post_meta( $energieausweis_id, 'wpenon_email', true ) ) . '-' . get_post_meta( $energieausweis_id, 'wpenon_secret', true ),
			'slug' => '',
		);

		if ( ! empty( $energieausweis_id ) ) {
			$post = get_post( $energieausweis_id );

			$query_args['access_token'] = ( new Energieausweis( $energieausweis_id ) )->get_access_token();
			$query_args['slug']         = $post->post_name;
		}

		return add_query_arg( $query_args, trailingslashit( $url ) );
	}
}
