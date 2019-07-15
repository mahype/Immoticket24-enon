<?php
/**
 * This file contains Energieausweis-IFrame functionality.
 *
 * @package immoticketenergieausweis
 */

/**
 * Whitelabel solution.
 */
class EA_Whitelabel{

	/**
	 * Wether iframe is set or not.
	 *
	 * @var string
	 */
	private $iframe = false;

	/**
	 * Whitelabel customer token.
	 *
	 * @var string
	 */
	private $token = null;

	/**
	 * Whitelabel customer token data.
	 *
	 * @var string
	 */
	private $token_data = null;

	/**
	 * Confirmationn Email Object.
	 *
	 * @var EA_Whitelabel_Confirmation_Email
	 */
	private $confirmation_email;

	/**
	 * Order Confirmationn Email Object.
	 *
	 * @var EA_Whitelabel_Confirmation_Email
	 */
	private $order_confirmation_email;

	/**
	 * EA_Whitelabel constructor.
	 */
	public function __construct() {
		$this->init();
		add_action( 'wpenon_confirmation_start', array( $this, 'init' ) );
	}

	/**
	 * Initializing hooks and filters
	 *
	 * @param \WPENON\Model\Energieausweis Energieausweis object.
	 */
	public function init( $energieausweis = null ) {
		if ( ! $this->is_whitelabeled_request() && ! $this->get_whitelabel_token( $energieausweis ) ) {
			return;
		}

		if( $energieausweis !== null && $token = $this->get_whitelabel_token( $energieausweis ) ) {
			$this->set_current_token( $token );
		}

		$this->iframe = true;

		$this->confirmation_email = new EA_Whitelabel_Confirmation_Email( $this );
		$this->order_confirmation_email = new EA_Whitelabel_Order_Confirmation_Email( $this );

		add_filter( 'template_include', array( $this, 'maybe_load_iframe'  ) );
		add_action( 'wpenon_confirmation_start', array( $this, 'set_whitelabel_token' ) );
		add_action( 'template_redirect', array( $this, 'adjust_fallback_track_referral' ), -10000, 0 );

		add_filter( 'wpenon_filter_url', array( $this, 'maybe_filter_iframe_url'), 100 );
		add_filter( 'edd_get_checkout_uri', array( $this, 'maybe_filter_iframe_url'), 100 );
		add_filter( 'edd_get_success_page_uri', array( $this, 'maybe_filter_iframe_url'), 100 );
		add_filter( 'edd_get_failed_transaction_uri', array( $this, 'maybe_filter_iframe_url'), 100 );
		add_filter( 'edd_remove_fee_url', array( $this, 'maybe_filter_iframe_url'), 100 );
	}

	/**
	 * Checks if current request is whitelabeled.
	 *
	 * @return bool True if this is a whitelabel request
	 */
	public function is_whitelabeled_request() {
		if ( ! isset( $_REQUEST['iframe'] ) || 'true' !== $_REQUEST['iframe'] ) {
			$this->iframe = false;
			return false;
		}

		if ( ! empty( $_REQUEST['iframe_token'] ) ) {
			if( ! $this->set_current_token( wp_unslash( $_REQUEST['iframe_token'] ) ) ) {
				wp_die( __( 'Fehlerhaftes IFrame-Token.', 'immoticketenergieausweis' ) );
			}
		}

		return true;
	}

	/**
	 * Loading iframe.
	 *
	 * @param string $template The path of the template to include.
	 *
	 * @return string $template The path of the template to include.
	 */
	public function maybe_load_iframe( $template ) {
		if ( ! $this->iframe ) {
			return $template;
		}

		$privacy_page    = immoticketenergieausweis_get_option( 'it-theme', 'page_for_privacy' );
		$terms_page      = immoticketenergieausweis_get_option( 'it-theme', 'page_for_terms' );
		$withdrawal_page = immoticketenergieausweis_get_option( 'it-theme', 'page_for_withdrawal' );

		if ( is_page( array( $privacy_page, $terms_page, $withdrawal_page ) ) ) {
			remove_action( 'wp_footer', 'immoticketenergieausweis_userlike_script', 100 );
			remove_action( 'wp_footer', 'immoticketenergieausweis_ekomi_widget_script', 100 );
			remove_action( 'wp_footer', 'immoticketenergieausweis_google_remarketing_tag_script', 100 );
			remove_action( 'wp_footer', 'immoticketenergieausweis_bing_ads_uet_tag_script', 100 );
			remove_action( 'wp_footer', 'immoticketenergieausweis_trusted_shops_badge_script', 100 );

			return locate_template( array( 'energieausweis-iframe.php' ) );
		}

		if ( ! class_exists( 'WPENON\Controller\Frontend' ) ) {
			if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
				return $template;
			}

			if ( ! edd_is_checkout() && ! edd_is_success_page() && ! edd_is_failed_transaction_page() ) {
				return $template;
			}
		}

		$view = \WPENON\Controller\Frontend::instance()->getView();
		if ( ! $view ) {
			if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
				return $template;
			}

			if ( ! edd_is_checkout() && ! edd_is_success_page() && ! edd_is_failed_transaction_page() ) {
				return $template;
			}
		}

		if ( ! current_user_can( 'manage_options' ) || ! empty( $_REQUEST['iframe_token'] ) ) {
			$email = $this->get_email();
			if ( ! $email ) {
				wp_die( __( 'Fehlerhaftes IFrame-Token.', 'immoticketenergieausweis' ) );
			}
		}

		add_action( 'wp_head', 'wp_no_robots' );
		remove_action( 'wp_footer', 'immoticketenergieausweis_adcell_retargeting_script', 10 );
		remove_action( 'wp_footer', 'immoticketenergieausweis_userlike_script', 100 );
		remove_action( 'wp_footer', 'immoticketenergieausweis_ekomi_widget_script', 100 );
		remove_action( 'wp_footer', 'immoticketenergieausweis_google_remarketing_tag_script', 100 );
		remove_action( 'wp_footer', 'immoticketenergieausweis_bing_ads_uet_tag_script', 100 );
		remove_action( 'wp_footer', 'immoticketenergieausweis_trusted_shops_badge_script', 100 );

		return locate_template( array( 'energieausweis-iframe.php' ) );
	}

	/**
	 * Filtering iframe URL.
	 *
	 * @param mixed $url Extra query args to add to the URI.
	 *
	 * @return string
	 */
	public function maybe_filter_iframe_url( $url ) {
		$args = array();
		if ( ! empty( $_GET['iframe'] ) ) {
			$args['iframe'] = wp_unslash( $_GET['iframe'] );
		}
		if ( ! empty( $_GET['iframe_token'] ) ) {
			$args['iframe_token'] = wp_unslash( $_GET['iframe_token'] );
		}

		if ( empty( $args ) ) {
			return $url;
		}

		return add_query_arg( $args, $url );
	}


	/**
	 * Setting information that Energieausweis was registered white labeled.
	 *
	 * @param \WPENON\Model\Energieausweis $energieausweis Energieausweis object.
	 */
	public function set_whitelabel_token( $energieausweis ) {
		if( $this->get_current_token() === null ) {
			return;
		}

		update_post_meta( $energieausweis->id, 'whitelabel_token', $this->get_current_token() );
	}

	/**
	 * Checks if Energieausweis was white labeled.
	 *
	 * @param \WPENON\Model\Energieausweis $energieausweis Energieausweis object.
	 *
	 * @return bool True if Energieausweis was created white labeled.
	 */
	public function get_whitelabel_token( $energieausweis ) {
		if( $energieausweis === null ) {
			return false;
		}

		$token = get_post_meta( $energieausweis->id, 'whitelabel_token', true );

		if( empty( $token ) ) {
			return false;
		}

		return $token;
	}

	/**
	 * Setting current token.
	 *
	 * @param string $token_string
	 *
	 * @return bool True if token could be loaded, false if not.
	 */
	private function set_current_token( $token_string ) {
		$tokens = immoticketenergieausweis_get_option( 'it-iframe', 'tokens' );
		if ( ! is_array( $tokens ) ) {
			return false;
		}

		foreach ( $tokens as $token ) {
			if ( $token_string === $token['token'] && 'yes' === $token['active'] ) {
				$this->token = $token['token'];
				$this->token_data = $token;
				return true;
			}
		}

		return false;
	}

	/**
	 * Get current token.
	 *
	 * @return string $token Current token.
	 */
	public function get_current_token() {
		return $this->token;
	}

	/**
	 * Gets a token value.
	 *
	 * @param  string $name  Name of the token value to return.
	 *
	 * @return string Token value.
	 */
	private function get_token_value( $name ) {
		if( $this->token_data === null || empty( $this->token_data[$name] ) ) {
			return false;
		}

		return $this->token_data[$name];
	}

	/**
	 * Get token
	 *
	 * @return string Token string of current token.
	 */
	public function get_token() {
		return $this->get_token_value('token' );
	}

	/**
	 * Get Email
	 *
	 * @return string Email of current token.
	 */
	public function get_email() {
		return $this->get_token_value('email' );
	}

	/**
	 * Get Email From Address.
	 *
	 * @return string Email from address of current token.
	 */
	public function get_email_from_address() {
		return $this->get_token_value('email_from_address' );
	}

	/**
	 * Get Email From Name.
	 *
	 * @return string Email from name of current token.
	 */
	public function get_email_from_name() {
		return $this->get_token_value('email_from_name' );
	}

	/**
	 * Get Email Footer.
	 *
	 * @return string Email from name of current token.
	 */
	public function get_email_footer() {
		return $this->get_token_value('email_footer' );
	}

	/**
	 * Get Site Name.
	 *
	 * @return string Sitename of current token.
	 */
	public function get_sitename() {
		return $this->get_token_value('sitename' );
	}

	/**
	 * Get Redirect URL.
	 *
	 * @return string Redirect url of current token.
	 */
	public function get_redirect_url() {
		return trim( $this->get_token_value('redirect_url' ) );
	}

	/**
	 * Getting redirect URL to customer site.
	 *
	 * @return string Redirect URL.
	 */
	public function get_verified_redirect_url( $energieausweis_id ) {
		$post = get_post( $energieausweis_id );

		$query_args = array(
			'iframe_token' => $this->get_current_token(),
			'access_token' => md5( get_post_meta( $energieausweis_id, 'wpenon_email', true ) ) . '-' . get_post_meta( $energieausweis_id, 'wpenon_secret', true ),
			'slug' => $post->post_name,
		);

		return add_query_arg( $query_args, trailingslashit( $this->get_redirect_url() ) );
	}

	/**
	 * Get affilliate id.
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
	 * Adjusting fallback track referal.
	 */
	public function adjust_fallback_track_referral() {
		if ( ! function_exists( 'affiliate_wp' ) ) {
			return;
		}

		$affiliate_id = $this->get_affiliate_id();
		if ( ! $affiliate_id ) {
			return;
		}

		affiliate_wp()->tracking->referral = $affiliate_id;
	}
}

