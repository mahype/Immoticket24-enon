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
		$payment_ids = get_post_meta( $this->id, '_wpenon_attached_payment_id' );

		if ( count( $payment_ids ) < 1 ) {
			return false;
		}

		return $payment_ids[0];
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
