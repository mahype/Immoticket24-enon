<?php
/**
 * Class for handling reseller data.
 *
 * @category Class
 * @package  Enon\Reseller\Models
 * @author   Sven Wagener
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://awesome.ug
 */

namespace Enon\Reseller\Models;

use Enon\Models\Exceptions\Exception;

use Enon\Models\ACF\PostData;
use Enon\Models\Edd\EddPayment;

/**
 * Class ACFResellerFiels
 *
 * @since 1.0.0
 *
 * @package Enon\Reseller
 */
class Reseller_Data extends PostData {

	/**
	 * Reseller_Data constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param \Enon\Reseller\Models\Token $token Token object.
	 *
	 * @throws Exception Token was not found.
	 */
	public function __construct( Token $token = null ) {
		if ( ! empty( $token ) ) {
			$this->set_token( $token );
		} elseif ( is_admin() ) {
			$this->set_post();
		}
	}

	/**
	 * Set token.
	 *
	 * @since 1.0.0
	 *
	 * @param Token $token Token object.
	 *
	 * @throws Exception Token was not found.
	 */
	public function set_token( Token $token ) {
		$post_id = $this->get_post_id_by_token( $token );

		if ( empty( $post_id ) ) {
			throw new Exception( sprintf( 'Invalid token "%s".', $token->get() ) );
		}

		$this->set_post_id( $post_id );
	}

	/**
	 * Gett post id by token.
	 *
	 * @since 1.0.0
	 *
	 * @param Token $token Reseller token.
	 *
	 * @return int/bool Post id if found or false. Returns the first token which was found.
	 */
	private function get_post_id_by_token( Token $token ) {
		$args = array(
			'post_type'     => 'reseller',
			'meta_query'        => array(
				array(
					'key'       => 'token',
					'value'     => $token->get(),
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
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private function set_post() {
		global $pagenow;

		if ( 'edit.php' !== $pagenow ) {
			return;
		}

		if ( 'download' !== $_REQUEST['post_type'] || 'edd-payment-history' !== $_REQUEST['page'] || 'view-order-details' !== $_REQUEST['view'] || ! isset( $_REQUEST['id'] ) ) {
			return;
		}

		$energieausweis_id = ( new EddPayment( $_REQUEST['id'] ) )->getenergieausweis_id();

		// @todo Move to new energieausweis object getreseller_id function
		$reseller_id = get_post_meta( $energieausweis_id, 'reseller_id', true );

		$this->set_post_id( $reseller_id );
	}

	/**
	 * Get company name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string of current token.
	 */
	public function get_company_name() {
		return $this->get( 'company_name' );
	}

	/**
	 * Get contact name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string of current token.
	 */
	public function get_contact_name() {
		return $this->get( 'contact_name' );
	}

	/**
	 * Get contact email.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string of current token.
	 */
	public function get_contact_email() {
		return $this->get( 'contact_email' );
	}

	/**
	 * Checks if order has to be sent to reseller.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string of current token.
	 */
	public function send_order_to_reseller() {
		$send_order_to_reseller = $this->get( 'send_order_to_reseller' );

		if ( in_array( 'send_order_to_reseller', $send_order_to_reseller ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get afiliate id.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed
	 */
	public function get_affiliate_id() {
		return $this->get( 'affiliate_id' );
	}

	/**
	 * Get token.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string of current token.
	 */
	public function get_token() {
		return $this->get( 'token' );
	}

	/**
	 * Get Website Name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Sitename of current token.
	 */
	public function get_website_name() {
		return $this->get( 'website_name' );
	}

	/**
	 * Get customer edit url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Url where users are redirected for editing entry.
	 */
	public function get_customer_edit_url() {
		return trim( $this->get( 'customer_edit_url' ) );
	}

	/**
	 * Get payment successful url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Url where users are redirected after succesful payment.
	 */
	public function get_payment_successful_url() {
		return trim( $this->get( 'payment_successful_url' ) );
	}

	/**
	 * Get payment failed url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Url where users are redirected after failed payment.
	 */
	public function get_payment_failed_url() {
		 return trim( $this->get( 'payment_failed_url' ) );
	}

	/**
	 * Get user interface values.
	 *
	 * @since 1.0.0
	 *
	 * @return array User interface values.
	 */
	public function get_user_interface_values() {
		return $this->get( 'user_interface' );
	}

	/**
	 * Get reseller extra CSS.
	 *
	 * @since 1.0.0
	 *
	 * @return string Reseller extra CSS.
	 */
	public function get_extra_css() {
		 return $this->get( 'extra_css' );
	}

	/**
	 * Get technical values.
	 *
	 * @since 1.0.0
	 *
	 * @return string Technical values.
	 */
	public function get_technical_values() {
		return $this->get( 'technical_values' );
	}

	/**
	 * Get Bedarsfausweis schema file.
	 *
	 * @since 1.0.0
	 *
	 * @return string Schema file.
	 */
	public function get_bw_schema_file() {
		return $this->get( 'bw_schema_file' );
	}

	/**
	 * Get Verbrauchsausweis schema file.
	 *
	 * @since 1.0.0
	 *
	 * @return string Schema file.
	 */
	public function get_vw_schema_file() {
		return $this->get( 'vw_schema_file' );
	}

	/**
	 * Get Email From Address.
	 *
	 * @since 1.0.0
	 *
	 * @return string Email from address.
	 */
	public function get_email_sender_address() {
		return $this->get( 'email_sender_address' );
	}

	/**
	 * Get Email From Name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Email from name.
	 */
	public function get_email_sender_name() {
		return $this->get( 'email_sender_name' );
	}

	/**
	 * Get Email Footer.
	 *
	 * @since 1.0.0
	 *
	 * @return string Email footer.
	 */
	public function get_email_footer() {
		return $this->get( 'email_footer' );
	}

	/**
	 * Get iframe url.
	 *
	 * @since 1.0.0
	 *
	 * @return string iframe url.
	 */
	public function get_iframe_bedarfsausweis_url() {
		return get_home_url() . '/energieausweis2/bedarfsausweis-wohngebaeude/?iframe_token=' . $this->get_token();
	}

	/**
	 * Get iframe url.
	 *
	 * @since 1.0.0
	 *
	 * @return string iframe url.
	 */
	public function get_iframe_verbrauchsausweis_url() {
		return get_home_url() . '/energieausweis2/verbrauchsausweis-wohngebaeude/?iframe_token=' . $this->get_token();
	}

	/**
	 * Get Post endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @return string iframe url.
	 */
	public function get_post_endpoint() {
		return $this->get( 'post_endpoint' );
	}

	/**
	 * Get Post data config class.
	 *
	 * @since 1.0.0
	 *
	 * @return string iframe url.
	 */
	public function get_post_data_config_class() {
		$config_file = $this->get( 'post_data_config_class' );

		if ( empty( $config_file ) ) {
			return 'SendEnergieausweisStandard';
		}

		return $config_file;
	}
}
