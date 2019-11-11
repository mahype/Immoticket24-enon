<?php

namespace Enon\Whitelabel;

use Awsm\WP_Plugin\Building_Plans\Hooks_Filters;
use Awsm\WP_Plugin\Loaders\Hooks_Loader;
use Awsm\WP_Plugin\Loaders\Loader;
use Enon\Exception;

/**
 * Class Customer
 *
 * @package Enon\Whitelabel
 */
class Customer implements Hooks_Filters {
	use Loader {
		load as load_definetly;
	}
	use Hooks_Loader;

	/**
	 * Holds loaded customer data.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $data;

	/**
	 * Customer constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string|int $identifier Customer Token.
	 *
	 * @throws Exception
	 */
	public function __construct( $identifier ) {
		if( ! is_int( $identifier ) ) {
			$this->set_customer_by_token( $identifier );
		}

		$this->load();
	}

	/**
	 * Adding filters.
	 *
	 * @since 1.0.0
	 */
	public function add_filters() {
		// TODO: Implement add_filters() method.
	}

	/**
	 * Setting customer by token.
	 *
	 * @since 1.0.0
	 *
	 * @param string $customer_token Customer Token.
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function set_customer_by_token( $customer_token ) {
		$tokens = immoticketenergieausweis_get_option( 'it-iframe', 'tokens' );
		if ( ! is_array( $tokens ) ) {
			throw new Exception( 'No token data found.' );
		}

		foreach ( $tokens as $token ) {
			if ( $customer_token === $token['token'] && 'yes' === $token['active'] ) {
				$this->data = $token;
				return true;
			}
		}

		throw new Exception( 'No customer data found.' );
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
	private function get_value( $name ) {
		if( $this->data === null || empty( $this->data[$name] ) ) {
			return false;
		}

		return $this->data[$name];
	}

	/**
	 * Get token.
	 *
	 * @since 1.0.0
	 *
	 * @return string Token string of current token.
	 */
	public function get_token() {
		$token = $this->get_value('token' );
		return $token;
	}

	/**
	 * Get Email
	 *
	 * @since 1.0.0
	 *
	 * @return string Email of current token.
	 */
	public function get_email() {
		$email = $this->get_value('email' );
		return $email;
	}

	/**
	 * Get Email From Address.
	 *
	 * @since 1.0.0
	 *
	 * @return string Email from address of current token.
	 */
	public function get_email_from_address() {
		$email_from_address = $this->get_value('email_from_address' );
		return $email_from_address;
	}

	/**
	 * Get Email From Name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Email from name of current token.
	 */
	public function get_email_from_name() {
		$email_from_name = $this->get_value('email_from_name' );
		return $email_from_name;
	}

	/**
	 * Get Email Footer.
	 *
	 * @since 1.0.0
	 *
	 * @return string Email from name of current token.
	 */
	public function get_email_footer() {
		$email_footer = $this->get_value('email_footer' );
		return $email_footer;
	}

	/**
	 * Get Site Name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Sitename of current token.
	 */
	public function get_sitename() {
		$sitename = $this->get_value('sitename' );
		return $sitename;
	}

	/**
	 * Get Redirect URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string Redirect url of current token.
	 */
	public function get_customer_edit_url() {
		$redirect_url = trim( $this->get_value('customer_edit_url' ) );
		return $redirect_url;
	}

	/**
	 * Get URL for site after successful payment.
	 *
	 * @since 1.0.0
	 *
	 * @return string Redirect successful payment url.
	 */
	public function get_payment_succesful_url() {
		return trim( $this->get_value('payment_successful_url' ) );
	}

	/**
	 * Get URL for site after failed payment.
	 *
	 * @since 1.0.0
	 *
	 * @return string Redirect failed payment url.
	 */
	public function get_payment_failed_url() {
		return trim( $this->get_value('payment_failed_url' ) );
	}

	/**
	 * Adds iframe and energeausweis parameters to url.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $url               URL where parameters have to be added.
	 * @param int     $energieausweis_id ID of energieausweis.
	 *
	 * @return string $url               URL with needed parameters.
	 */
	public function get_verfied_url( $url, $energieausweis_id = null ) {
		$query_args = array(
			'iframe'       => true,
			'iframe_token' => $this->get_current_token(),
			'access_token' => md5( get_post_meta( $energieausweis_id, 'wpenon_email', true ) ) . '-' . get_post_meta( $energieausweis_id, 'wpenon_secret', true ),
			'slug' => $post->post_name,
		);

		if( ! empty( $energieausweis_id ) ) {
			$post = get_post( $energieausweis_id );

			$query_args['access_token'] = $this->get_access_token( $energieausweis_id );
			$query_args['slug']         = $post->post_name;
		}

		return add_query_arg( $query_args, trailingslashit( $url ) );
	}

	/**
	 * Get access token for editing page.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $energieausweis_id ID of energieausweis.
	 *
	 * @return string $access_token      Token to use in URL.
	 */
	public function get_access_token( $energieausweis_id ) {
		$access_token = md5( get_post_meta( $energieausweis_id, 'wpenon_email', true ) ) . '-' . get_post_meta( $energieausweis_id, 'wpenon_secret', true );
		return $access_token;
	}


	/**
	 * Get affilliate id.
	 *
	 * @since 1.0.0
	 *
	 * @return string Affilate id of current token.
	 */
	public function get_affiliate_id() {
		$email = $this->get_email();

		if( ! $email ) {
			return false;
		}

		$user = get_user_by( 'email', $email );
		if ( ! $user ) {
			return false;
		}

		if ( ! function_exists( 'affwp_get_affiliate_id' ) ) {
			return false;
		}

		return affwp_get_affiliate_id( $user->ID );
	}

	/**
	 * Filtering payment success URL.
	 *
	 * @param $url
	 *
	 * @return string
	 */
	public static function filter_payment_success_url( $old_url ) {
		$url = self::$customer->get_payment_succesful_url();

		if( empty( $url ) ) {
			$payment_successful_page = immoticketenergieausweis_get_option( 'it-theme', 'page_for_successful_payment' );

			if( empty( $payment_successful_page ) ) {
				return $old_url;
			}

			$url = get_permalink( $payment_successful_page );
		}

		$url =  self::$customer->get_verfied_url( $url );

		return $url;
	}

	/**
	 * Filtering payment success URL.
	 *
	 * @param string $old_url
	 *
	 * @return string
	 */
	public static function filter_payment_failed_url( $old_url ) {
		$url =  self::get_payment_failed_url();

		if( empty( $url ) ) {
			$payment_failed_page = immoticketenergieausweis_get_option( 'it-theme', 'page_for_failed_payment' );

			if( empty( $payment_failed_page ) ) {
				return $old_url;
			}

			$url = get_permalink( $payment_failed_page );
		}

		$url =  self::$customer->get_verfied_url( $url );

		return $url;
	}
}
