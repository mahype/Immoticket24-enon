<?php
/**
 * New Energieasweis class.
 *
 * @category Class
 * @package  Enon\Models\Enon
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 *
 * @todo Replacing old energieausweis class.
 */

namespace Enon\Models\Enon;

/**
 * Class Energieausweis
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller\Taks\Plugins
 */
class Energieausweis {
	/**
	 * Energieausweis id.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $id;

	/**
	 * Post object.
	 *
	 * @since 1.0.0
	 *
	 * @var \WP_Post
	 */
	private $post;

	/**
	 * Type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Standard.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $standard;

	/**
	 * Energieausweis constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id Energieausweis id.
	 */
	public function __construct( $id ) {
		$this->id = $id;

		$this->type     = get_post_meta( $this->id, 'wpenon_type', true );
		$this->standard = get_post_meta( $this->id, 'wpenon_standard', true );

		$this->post = get_post( $id );
	}

	/**
	 * Get energieausweis id.
	 *
	 * @since 1.0.0
	 *
	 * @return int Energieausweis id.
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get post object.
	 *
	 * @return array|\WP_Post|null
	 *
	 * @since 1.0.0
	 */
	public function get_post() {
		return $this->post;
	}

	/**
	 * Get energieausweis type.
	 *
	 * @since 1.0.0
	 *
	 * @return string Energieausweis type.
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get energieausweis standard.
	 *
	 * @since 1.0.0
	 *
	 * @return string Energieausweis standard.
	 */
	public function get_standard() {
		return $this->standard;
	}

	/**
	 * Get payment id.
	 *
	 * @return int Payment id.
	 *
	 * @since 1.0.0
	 */
	public function get_payment_id() {
		$payments = edd_get_payments( array(
			'output'   => 'payments',
			'download' => $this->id,
		) );

		if( count( $payments ) === 0 ){
			return false;
		}

		return $payments[ count( $payments ) -1 ]->ID; // Getting the last one
	}

	/**
	 * Get payment id.
	 *
	 * @return \EDD_Payment|Bool Edd Payment object, false if not found.
	 *
	 * @since 1.0.0
	 */
	public function get_payment() {
		$payment_id = $this->get_payment_id();
		if( ! $payment_id ) {
			return false;
		}

		return new \EDD_Payment( $payment_id );
	}

	/**
	 * Get access token for editing page.
	 *
	 * @since 1.0.0
	 *
	 * @return string $access_token      Token to use in URL.
	 */
	public function get_access_token() {
		$wpenon_email  = get_post_meta( $this->get_id(), 'wpenon_email', true );
		$wpenon_secret = get_post_meta( $this->get_id(), 'wpenon_secret', true );
		$access_token  = md5( $wpenon_email ) . '-' . $wpenon_secret;

		return $access_token;
	}
}
