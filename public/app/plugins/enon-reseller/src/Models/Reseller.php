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
	 * @var int $post_id
	 *
	 * @since 1.0.0
	 */
	private $post_id;

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
	 * @param Token  $token  Token object.
	 * @param Logger $logger Logger object.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $token, Logger $logger ) {
		$this->logger = $logger;

		if ( ! empty( $token ) ) {
			$this->set_post_id_by_token( $token );
		} else {
			$this->set_post_id_by_admin_url();
		}
	}

	/**
	 * Get post id.
	 *
	 * @return int Post id of reseller.
	 *
	 * @since 1.0.0
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	/**
	 * Set post id.
	 *
	 * @param int $post_id Post id.
	 *
	 * @since 1.0.0
	 */
	public function set_post_id( $post_id ) {
		$this->post_id = $post_id;
		$this->data = new Reseller_Data( $post_id );
	}

	/**
	 * Set post id by token.
	 *
	 * @param Token $token Token object.
	 *
	 * @throws Exception Token was not found.
	 *
	 * @since 1.0.0
	 */
	private function set_post_id_by_token( Token $token ) {
		$post_id = $this->get_post_id_by_token( $token );

		if ( empty( $post_id ) ) {
			wp_die(
				sprintf( 'Invalid token "%s".', esc_attr( $token->get() ) ),
				sprintf( 'Invalid token "%s".', esc_attr( $token->get() ) ),
				[ 'response' => 404 ],
			);
		}

		$this->set_post_id( $post_id );
	}

	/**
	 * Gett post id by token.
	 *
	 * @param Token $token Reseller token.
	 *
	 * @return int/bool Post id if found or false. Returns the first token which was found.
	 *
	 * @since 1.0.0
	 */
	private function get_post_id_by_token( Token $token ) {
		$args = array(
			'post_type'  => 'reseller',
			'meta_query' => array(
				array(
					'key'   => 'token',
					'value' => $token->get(),
				),
			),
		);

		$posts = \get_posts( $args );

		foreach ( $posts as $post ) {
			return $post->ID; // There can only be one, the first is returned.
		}

		return false;
	}

	/**
	 * Get post id automatically.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	private function set_post_id_by_admin_url() {
		global $pagenow;

		if ( 'edit.php' !== $pagenow ) {
			return;
		}

		// phpcs:ignore Getting vars from wordpress admin url cant set nonce.
		if ( ! isset( $_GET['post_type'] ) || ! isset( $_GET['page'] ) || ! isset( $_GET['view'] ) ) {
			return;
		}

		// phpcs:ignore Getting vars from wordpress admin url cant set nonce.
		if ( 'download' !== $_GET['post_type'] || 'edd-payment-history' !== $_GET['page'] || 'view-order-details' !== $_GET['view'] || ! isset( $_GET['id'] ) ) {
			return;
		}

		// phpcs:ignore Getting vars from wordpress admin url cant set nonce.
		$payment_id = intval( $_GET['id'] );

		$energieausweis_id = ( new Payment( $payment_id ) )->get_energieausweis_id();

		// @todo Move to new energieausweis object get_reseller_id function
		$reseller_id = get_post_meta( $energieausweis_id, 'reseller_id', true );

		$this->set_post_id( $reseller_id );
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
