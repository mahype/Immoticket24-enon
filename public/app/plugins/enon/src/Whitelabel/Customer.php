<?php

namespace Enon\Whitelabel;

use Enon\Exceptions\Exception;
use Enon\Traits\Logger AS LoggerTrait;
use Enon\Logger;

/**
 * Class Customer
 *
 * @since 1.0.0
 *
 * @package Enon\Whitelabel
 */
class Customer {
	use LoggerTrait;

	/**
	 * Holds loaded customer data.
	 *
	 * @since 1.0.0
	 *
	 * @var array
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
	 * Customer constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Token  $token  Token object.
	 * @param Logger $logger Logger object.
	 *
	 * @throws Exception
	 */
	public function __construct( Token $token, Logger $logger )
	{
		$this->logger = $logger;
		$this->token = $token;

		if( ! $this->setCustomerByToken( $token->get() ) ) {
			throw new Exception( sprintf( 'Could not find any customer for token "%s".', $token->get() ) );
		}
	}

	/**
	 * Setting customer by token.
	 *
	 * @since 1.0.0
	 *
	 * @param string $token Token.
	 *
	 * @return bool
	 */
	private function setCustomerByToken( $token )
	{
		$saved_tokens = immoticketenergieausweis_get_option( 'it-iframe', 'tokens' );

		if ( ! is_array( $saved_tokens ) ) {
			$this->logger()->alert( 'No token data found to set customer.' );
		}

		foreach ( $saved_tokens as $saved_token ) {
			if ( $token === $saved_token['token'] && 'yes' === $saved_token['active'] ) {
				$this->data = $saved_token;
				return true;
			}
		}

		return false;
	}

	/**
	 * Gets a token value.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $name  Name of the token value to return.
	 *
	 * @return string Token value.
	 */
	private function getValue( $name )
	{
		if ( null === $this->data || empty( $this->data[ $name ] ) ) {
			return false;
		}

		return $this->data[ $name ];
	}

	/**
	 * Get token.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string of current token.
	 */
	public function getToken() {
		return $this->getValue( 'token' );
	}

	/**
	 * Get Email
	 *
	 * @since 1.0.0
	 *
	 * @return string Email of current token.
	 */
	public function getEmail() {
		return $this->getValue( 'email' );
	}

	/**
	 * Get Email From Address.
	 *
	 * @since 1.0.0
	 *
	 * @return string Email from address of current token.
	 */
	public function getEmailFromAddress() {
		return $this->getValue( 'email_from_address' );
	}

	/**
	 * Get Email From Name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Email from name of current token.
	 */
	public function getEmailFromName() {
		return $this->getValue( 'email_from_name' );
	}

	/**
	 * Get Email Footer.
	 *
	 * @since 1.0.0
	 *
	 * @return string Email from name of current token.
	 */
	public function getEmailFooter() {
		return $this->getValue( 'email_footer' );
	}

	/**
	 * Get Site Name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Sitename of current token.
	 */
	public function getSitename() {
		return $this->getValue( 'sitename' );
	}

	/**
	 * Get Redirect URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string Redirect url of current token.
	 */
	public function getCustomerEditUrl() {
		return trim( $this->getValue( 'customer_edit_url' ) );
	}

	/**
	 * Get URL for site after successful payment.
	 *
	 * @since 1.0.0
	 *
	 * @return string Redirect successful payment url.
	 */
	public function getPaymentSuccesfulUrl() {
		return trim( $this->getValue( 'payment_successful_url' ) );
	}

	/**
	 * Get URL for site after failed payment.
	 *
	 * @since 1.0.0
	 *
	 * @return string Redirect failed payment url.
	 */
	public function getPaymentFailedUrl() {
		return trim( $this->getValue( 'payment_failed_url' ) );
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
	public function getVerfiedUrl( $url, $energieausweis_id = null ) {
		$query_args = array(
			'iframe'       => true,
			'iframe_token' => $this->getToken(),
			'access_token' => md5( get_post_meta( $energieausweis_id, 'wpenon_email', true ) ) . '-' . get_post_meta( $energieausweis_id, 'wpenon_secret', true ),
			'slug' => '',
		);

		if ( ! empty( $energieausweis_id ) ) {
			$post = get_post( $energieausweis_id );

			$query_args['access_token'] = $this->getAccessToken( $energieausweis_id );
			$query_args['slug']         = $post->post_name;
		}

		return add_query_arg( $query_args, trailingslashit( $url ) );
	}

	/**
	 * Get access token for editing page.
	 *
	 * @since 1.0.0
	 *
	 * @param int $energieausweis_id ID of energieausweis.
	 *
	 * @return string $access_token      Token to use in URL.
	 */
	public function getAccessToken( $energieausweis_id ) {
		return md5( get_post_meta( $energieausweis_id, 'wpenon_email', true ) ) . '-' . get_post_meta( $energieausweis_id, 'wpenon_secret', true );
	}

	/**
	 * Get affiliate id.
	 *
	 * @since 1.0.0
	 *
	 * @return string Affilate id of current token.
	 *
	 * @throws Exception
	 */
	public function getAffiliateId() {
		if ( ! function_exists( 'affwp_get_affiliate_id' ) ) {
			$this->logger()->alert( 'Function affwp_get_affiliate_id not found.' );
			throw new Exception( 'Function affwp_get_affiliate_id not found.' );
		}

		$email = $this->getEmail();

		if ( ! isset( $email ) ) {
			$this->logger()->alert( 'Could not get affiliate id, because email is not set.' );
			return false;
		}

		$user = get_user_by( 'email', $email );
		if ( ! $user ) {
			$this->logger()->alert( sprintf( 'Could not get user, because email address "%s" not found.', $email ) );
			return false;
		}

		return affwp_get_affiliate_id( $user->ID );
	}


}
