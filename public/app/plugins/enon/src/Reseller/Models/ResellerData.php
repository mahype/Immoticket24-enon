<?php

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
class ResellerData extends PostData {

	/**
	 * ResellerData constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param \Enon\Reseller\Models\Token $token Token object.
	 *
	 * @throws Exception Token was not found.
	 */
	public function __construct( Token $token = null ) {
		if ( ! empty( $token ) ) {
			$this->setToken( $token );
		} elseif ( is_admin() ) {
			$this->setPost();
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
	public function setToken( Token $token ) {
		$postId = $this->getPostIdByToken( $token );

		if ( empty( $postId ) ) {
			throw new Exception( sprintf( 'Invalid token "%s".', $token->get() ) );
		}

		$this->setPostId( $postId );
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
	private function getPostIdByToken( Token $token ) {
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
	private function setPost() {
		global $pagenow;

		if ( 'edit.php' !== $pagenow ) {
			return;
		}

		if ( 'download' !== $_REQUEST['post_type'] || 'edd-payment-history' !== $_REQUEST['page'] || 'view-order-details' !== $_REQUEST['view'] || ! isset( $_REQUEST['id'] ) ) {
			return;
		}

		$energieausweisId = ( new EddPayment( $_REQUEST['id'] ) )->getEnergieausweisId();

		// @todo Move to new energieausweis object getResellerId function
		$resellerId = get_post_meta( $energieausweisId, 'reseller_id', true );

		$this->setPostId( $resellerId );
	}

	/**
	 * Get company name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string of current token.
	 */
	public function getCompanyName() {
		return $this->get( 'company_name' );
	}

	/**
	 * Get contact name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string of current token.
	 */
	public function getContactName() {
		return $this->get( 'contact_name' );
	}

	/**
	 * Get contact email.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string of current token.
	 */
	public function getContactEmail() {
		return $this->get( 'contact_email' );
	}

	/**
	 * Checks if order has to be sent to reseller.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string of current token.
	 */
	public function sendOrderToReseller() {
		$sendOrderToReseller = $this->get( 'send_order_to_reseller' );

		if ( in_array( 'send_order_to_reseller', $sendOrderToReseller ) ) {
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
	public function getAffiliateId() {
		return $this->get( 'affiliate_id' );
	}

	/**
	 * Get token.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string of current token.
	 */
	public function getToken() {
		return $this->get( 'token' );
	}

	/**
	 * Get Website Name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Sitename of current token.
	 */
	public function getWebdsiteName() {
		return $this->get( 'website_name' );
	}

	/**
	 * Get customer edit url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Url where users are redirected for editing entry.
	 */
	public function getCustomerEditUrl() {
		return trim( $this->get( 'customer_edit_url' ) );
	}

	/**
	 * Get payment successful url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Url where users are redirected after succesful payment.
	 */
	public function getPaymentSuccesfulUrl() {
		return trim( $this->get( 'payment_successful_url' ) );
	}

	/**
	 * Get payment failed url.
	 *
	 * @since 1.0.0
	 *
	 * @return string Url where users are redirected after failed payment.
	 */
	public function getPaymentFailedUrl() {
		 return trim( $this->get( 'payment_failed_url' ) );
	}

	/**
	 * Get user interface values.
	 *
	 * @since 1.0.0
	 *
	 * @return array User interface values.
	 */
	public function getUserInterfaceValues() {
		return $this->get( 'user_interface' );
	}

	/**
	 * Get reseller extra CSS.
	 *
	 * @since 1.0.0
	 *
	 * @return string Reseller extra CSS.
	 */
	public function getExtraCSS() {
		 return $this->get( 'extra_css' );
	}

	/**
	 * Get technical values.
	 *
	 * @since 1.0.0
	 *
	 * @return string Technical values.
	 */
	public function getTechnicalValues() {
		return $this->get( 'technical_values' );
	}

	/**
	 * Get Bedarsfausweis schema file.
	 *
	 * @since 1.0.0
	 *
	 * @return string Schema file.
	 */
	public function getBwSchemaFile() {
		return $this->get( 'bw_schema_file' );
	}

	/**
	 * Get Verbrauchsausweis schema file.
	 *
	 * @since 1.0.0
	 *
	 * @return string Schema file.
	 */
	public function getVwSchemaFile() {
		return $this->get( 'vw_schema_file' );
	}

	/**
	 * Get Email From Address.
	 *
	 * @since 1.0.0
	 *
	 * @return string Email from address.
	 */
	public function getEmailSenderAddress() {
		return $this->get( 'email_sender_address' );
	}

	/**
	 * Get Email From Name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Email from name.
	 */
	public function getEmailSenderName() {
		return $this->get( 'email_sender_name' );
	}

	/**
	 * Get Email Footer.
	 *
	 * @since 1.0.0
	 *
	 * @return string Email footer.
	 */
	public function getEmailFooter() {
		return $this->get( 'email_footer' );
	}

	/**
	 * Get iframe url.
	 *
	 * @since 1.0.0
	 *
	 * @return string iframe url.
	 */
	public function getIframeBedarfsausweisUrl() {
		return get_home_url() . '/energieausweis2/bedarfsausweis-wohngebaeude/?iframe_token=' . $this->getToken();
	}

	/**
	 * Get iframe url.
	 *
	 * @since 1.0.0
	 *
	 * @return string iframe url.
	 */
	public function getIframeVerbrauchsausweisUrl() {
		return get_home_url() . '/energieausweis2/verbrauchsausweis-wohngebaeude/?iframe_token=' . $this->getToken();
	}

	/**
	 * Get Post endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @return string iframe url.
	 */
	public function getPostEndpoint() {
		return $this->get( 'post_endpoint' );
	}

	/**
	 * Get Post data config class.
	 *
	 * @since 1.0.0
	 *
	 * @return string iframe url.
	 */
	public function getPostDataConfigClass() {
		$configFile = $this->get( 'post_data_config_class' );

		if ( empty( $configFile ) ) {
			return 'SendEnergieausweisStandard';
		}

		return $configFile;
	}
}
