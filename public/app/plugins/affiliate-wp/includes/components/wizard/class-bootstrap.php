<?php
/**
 * Wizard: Bootstrap
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Wizard
 * @copyright   Copyright (c) 2014, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.9
 */

namespace AffWP\Components\Wizard;

use AffWP\Core\License;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for implementing the setup wizard.
 *
 * @since 2.9
 */
class Bootstrap {

	/**
	 * AffiliateWP_Onboarding_Wizard constructor.
	 *
	 * @since 2.9
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'maybe_load_onboarding_wizard' ) );
		add_action( 'admin_menu', array( $this, 'add_dashboard_page' ) );

		// Redirect to onboarding wizard.
		add_action( 'admin_init', array( $this, 'redirect_to_wizard' ) );

		// Add wizard button to General Settings.
		add_filter( 'affwp_settings_general', array( $this, 'add_wizard_button_to_settings' ) );

		add_action( 'wp_ajax_affiliatewp_vue_get_settings', array( $this, 'get_settings' ) );
		add_action( 'wp_ajax_affiliatewp_verify_license', array( $this, 'verify_license' ) );
		add_action( 'wp_ajax_affiliatewp_vue_get_license', array( $this, 'get_license' ) );
		add_action( 'wp_ajax_affiliatewp_vue_get_integrations', array( $this, 'get_integrations' ) );
		add_action( 'wp_ajax_affiliatewp_vue_save_integrations', array( $this, 'save_integrations' ) );
		add_action( 'wp_ajax_affiliatewp_vue_update_settings', array( $this, 'update_setting' ) );
		add_action( 'wp_ajax_affiliatewp_vue_allset', array( $this, 'finish_wizard' ) );
	}

	/**
	 * Checks if the Wizard should be loaded in current context.
	 *
	 * @since 2.9
	 */
	public function maybe_load_onboarding_wizard() {
		// Check for wizard-specific parameter
		// Allow plugins to disable the onboarding wizard
		// Check if current user is allowed to save settings.
		if ( ! isset( $_GET['page'] ) ||
					'affiliatewp-onboarding' !== $_GET['page'] || // WPCS: CSRF ok, input var ok.
					! apply_filters( 'affiliatewp_enable_onboarding_wizard', true ) ||
					! current_user_can( 'manage_affiliate_options' ) ) {
			return;
		}

		// Don't load the interface if doing an ajax call.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		set_current_screen();

		// Remove an action in the Gutenberg plugin ( not core Gutenberg ) which throws an error.
		remove_action( 'admin_print_styles', 'gutenberg_block_editor_admin_print_styles' );

		$this->load_onboarding_wizard();
	}

	/**
	 * Register page through WordPress's hooks.
	 *
	 * @since 2.9
	 */
	public function add_dashboard_page() {
		add_dashboard_page( '', '', 'manage_affiliate_options', 'affiliatewp-onboarding', '' );
	}

	/**
	 * Load the Onboarding Wizard template.
	 *
	 * @since 2.9
	 */
	private function load_onboarding_wizard() {
		$this->enqueue_scripts();

		$this->onboarding_wizard_header();
		$this->onboarding_wizard_content();
		$this->onboarding_wizard_footer();

		exit;
	}

	/**
	 * Redirects users to wizard if first time using wizard.
	 *
	 * @since 2.9
	 */
	public function redirect_to_wizard() {
		if ( affwp_is_admin_page() && isset( $_GET['page'] ) ) {
			$page = sanitize_text_field( $_GET['page'] );

			if ( get_option( 'affwp_trigger_wizard' ) && 'affiliate-wp-wizard' !== $page ) {
				$wizard_url = menu_page_url( 'affiliatewp-onboarding', false );
				wp_safe_redirect( $wizard_url );
			}
		}
	}

	/**
	 * Adds wizard button to general settings.
	 *
	 * @since 2.9
	 *
	 * @param array $settings General settings.
	 * @return array General settings.
	 */
	public function add_wizard_button_to_settings( $settings = array() ) {
		$new_settings = array(
			'wizard_button' => array(
				'name'     => __( 'Setup Wizard', 'affiliate-wp' ),
				'desc'     => '',
				'type'     => 'text',
				'callback' => array( $this, 'render_wizard_button' ),
			),
		);

		return $settings + $new_settings;
	}

	/**
	 * Renders wizard button for settings.
	 *
	 * @since 2.9
	 */
	public function render_wizard_button() {
		$wizard_url = menu_page_url( 'affiliatewp-onboarding', false );
		?>
		<a href="<?php echo esc_url( $wizard_url ); ?>" class="button"><?php esc_html_e( 'Launch Setup Wizard', 'affiliate-wp' ); ?></a><br>
		<p class="description"><?php esc_html_e( 'Use our configuration wizard to properly set up AffiliateWP (with just a few clicks).', 'affiliate-wp' ); ?></p>
		<?php
	}


	/**
	 * AJAX callback to get current settings.
	 *
	 * @since 2.9
	 */
	public function get_settings() {
		check_ajax_referer( 'affwpwizard-admin-nonce', 'nonce' );

		$data = array(
			'currencies'         => affwp_get_currencies(),
			'currency'           => affiliate_wp()->settings->get( 'currency', 'USD' ),
			'revoke_on_refund'   => affiliate_wp()->settings->get( 'revoke_on_refund' ),
			'referral_rate'      => affiliate_wp()->settings->get( 'referral_rate', 20 ),
			'referral_rate_type' => affiliate_wp()->settings->get( 'referral_rate_type', 'percentage' ),
			'flat_rate_basis'    => affiliate_wp()->settings->get( 'flat_rate_basis', 'per_product' ),
			'referral_var'       => affiliate_wp()->settings->get( 'referral_var', 'ref' ),
			'referral_format'    => affiliate_wp()->settings->get( 'referral_format', 'id' ),
			'cookie_exp'         => affiliate_wp()->settings->get( 'cookie_exp', 30 ),
		);

		wp_send_json( $data );
	}

	/**
	 * AJAX callback to verify if license is valid.
	 *
	 * @since 2.9
	 */
	public function verify_license() {
		check_ajax_referer( 'affwpwizard-admin-nonce', 'nonce' );

		$license_key = ! empty( $_POST['license'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['license'] ) ) ) : false;

		// Check if user entered a license key.
		if ( ! $license_key ) {
			wp_send_json_error(
				array(
					'error' => esc_html__( 'Please enter a license key.', 'affiliate-wp' ),
				)
			);
		}

		// Check if user has permissions.
		if ( ! current_user_can( 'manage_affiliate_options' ) ) {
			wp_send_json_error(
				array(
					'error' => esc_html__( 'You are not allowed to verify a license key.', 'affiliate-wp' ),
				)
			);
		}

		// Activate license.
		$license_activation = ( new License\License_Data() )->activation_status( $license_key );

		// Check if activation request has failed.
		if ( is_null( $license_activation ) ) {
			wp_send_json_error(
				array(
					'error' => esc_html__( 'There was an error connecting to the remote key API. Please try again later.', 'affiliate-wp' ),
				)
			);
		}

		if ( false === $license_activation['license_status'] ) {
			wp_send_json_error(
				array(
					'error' => $license_activation['affwp_message'],
				)
			);
		}

		$license_data = $license_activation['license_data'];
		$license_key  = $license_activation['license_key'];

		if ( 'valid' !== $license_data->license || empty( $license_data->success ) ) {
			wp_send_json_error(
				array(
					'error' => __( 'This license key doesn&#8217;t appear to be valid. Try again?', 'affiliate-wp' ),
				)
			);
		}

		affiliate_wp()->settings->set( array(
			'license_status' => $license_data,
			'license_key'    => $license_key,
		), true );

		set_transient( 'affwp_license_check', $license_data->license, DAY_IN_SECONDS );

		wp_send_json_success(
			array(
				'message'      => __( 'Congratulations! This site is now receiving automatic updates.', 'affiliate-wp' ),
				'license_type' => 'Pro',
				'status'       => $license_data,
			)
		);
	}

	/**
	 * AJAX callback to get license details.
	 *
	 * @since 2.9
	 */
	public function get_license() {
		check_ajax_referer( 'affwpwizard-admin-nonce', 'nonce' );

		$status = affiliate_wp()->settings->get( 'license_status', '' );
		$status = is_object( $status ) ? $status->license : $status;
		$license_key = affiliate_wp()->settings->get_license_key();

		$site_license = array(
			'key'         => $license_key,
			'status'      => $status,
			'is_invalid'  => 'valid' !== $status,
			'type'        => 'valid' === $status ? 'Pro' : '',
		);

		wp_send_json( array(
			'site'    => $site_license,
			'network' => array(),
		) );
	}

	/**
	 * Get current list of AffiliateWP integrations.
	 *
	 * @since 2.9
	 */
	public function get_integrations() {
		check_ajax_referer( 'affwpwizard-admin-nonce', 'nonce' );

		// Set integrations sections.
		$integrations_sections = array(
			'ecommerce'  => array(
				'title'        => __( 'eCommerce integrations', 'affiliate-wp' ),
				'subtitle'     => '',
				'integrations' => array( 'woocommerce', 'edd', 'stripe', 'paypal', 'wpeasycart' ),
			),
			'membership' => array(
				'title'        => __( 'Membership integrations', 'affiliate-wp' ),
				'subtitle'     => '',
				'integrations' => array( 'membermouse', 'memberpress', 'optimizemember', 'pmp', 'pms', 'rcp', 's2member' ),
			),
			'form'       => array(
				'title'        => __( 'Form integrations', 'affiliate-wp' ),
				'subtitle'     => '',
				'integrations' => array( 'caldera-forms', 'contactform7', 'formidablepro', 'gravityforms', 'ninja-forms', 'wpforms' ),
			),
			'invoice'    => array(
				'title'        => __( 'Invoice integrations', 'affiliate-wp' ),
				'subtitle'     => '',
				'integrations' => array( 'sproutinvoices', 'wp-invoice' ),
			),
			'course'     => array(
				'title'        => __( 'Course integrations', 'affiliate-wp' ),
				'subtitle'     => '',
				'integrations' => array( 'lifterlms', 'zippycourses' ),
			),
			'donation'   => array(
				'title'        => __( 'Donation integrations', 'affiliate-wp' ),
				'subtitle'     => '',
				'integrations' => array( 'give' ),
			),
		);

		// Get enabled integrations.
		$all_integrations     = affiliate_wp()->integrations->get_integrations();
		$enabled_integrations = affiliate_wp()->integrations->get_enabled_integrations();
		$enabled_keys         = array_keys( $enabled_integrations );

		// Build integrations array with all details.
		$integrations = array_map(
			function( $integration_section ) use ( $all_integrations, $enabled_keys ) {
				$section = $integration_section;

				$section_integrations = array_map(
					function( $integration ) use ( $all_integrations, $enabled_keys ) {
						$title   = isset( $all_integrations[ $integration ] ) ? $all_integrations[ $integration ] : '';
						$checked = in_array( $integration, $enabled_keys, true ) ? true : false;

						return array(
							'feature'     => $integration,
							'title'       => $title,
							'checked'     => $checked,
							'description' => '',
							'faux'        => false,
						);
					},
					$section['integrations']
				);

				$section['integrations'] = $section_integrations;

				return $section;
			},
			$integrations_sections
		);

		wp_send_json( $integrations );
	}

	/**
	 * AJAX callback to save selected integrations.
	 *
	 * @since 2.9
	 */
	public function save_integrations() {
		check_ajax_referer( 'affwpwizard-admin-nonce', 'nonce' );

		// Build array of enabled integrations.
		$integrations     = affiliate_wp()->integrations->get_integrations();
		$enabled          = isset( $_POST['integrations'] ) ? explode( ',', $_POST['integrations'] ) : array();
		$new_integrations = array_filter(
			$integrations,
			function( $integration ) use ( $enabled ) {
				return in_array( $integration, $enabled, true );
			},
			ARRAY_FILTER_USE_KEY
		);

		// Add selected integrations into settings.
		$settings = affiliate_wp()->settings->get_all();
		if ( is_array( $settings ) && isset( $new_integrations ) ) {
			$settings['integrations'] = $new_integrations;
			
			// Update settings.
			update_option( 'affwp_settings', $settings );
		}

		wp_send_json_success();
	}

	/**
	 * AJAX callback to update selected setting.
	 *
	 * @since 2.9
	 */
	public function update_setting() {
		check_ajax_referer( 'affwpwizard-admin-nonce', 'nonce' );

		// Get POST vars.
		$setting = isset( $_POST['setting'] ) ? sanitize_text_field( $_POST['setting'] ) : '';

		$settings = affiliate_wp()->settings->get_all();

		if ( 'referral_var' === $setting ) {
			$settings['referral_var'] = affiliate_wp()->settings->sanitize_referral_variable( $_POST['value'], 'referral_var' );
		}
		if ( 'referral_format' === $setting ) {
			$settings['referral_format'] = sanitize_text_field( $_POST['value'] );
		}
		if ( 'cookie_exp' === $setting ) {
			if ( empty( $_POST['value'] ) || $_POST['value'] < 0 ) {
				$settings['cookie_exp'] = 1;
			} else {
				$settings['cookie_exp'] = intval( $_POST['value'] );
			}
		}
		if ( 'currency' === $setting ) {
			$settings['currency'] = sanitize_text_field( $_POST['value'] );
		}
		if ( 'revoke_on_refund' === $setting ) {
			$settings['revoke_on_refund'] = isset( $_POST['value'] ) && '1' === sanitize_text_field( $_POST['value'] ) ? 1 : 0;
		}
		if ( 'referral_rate' === $setting ) {
			$settings['referral_rate'] = floatval( $_POST['value'] );
		}
		if ( 'referral_rate_type' === $setting ) {
			$settings['referral_rate_type'] = sanitize_text_field( $_POST['value'] );
		}
		if ( 'flat_rate_basis' === $setting ) {
			$settings['flat_rate_basis'] = sanitize_text_field( $_POST['value'] );
		}

		// Add yourself as an affiliate.
		if ( 'add_yourself' === $setting ) {
			$user_id = get_current_user_id();

			$params = array(
				'user_id' => $user_id,
				'status'  => 'active',
			);
			affwp_add_affiliate( $params );
		}

		// Update settings.
		update_option( 'affwp_settings', $settings );

		wp_send_json_success();
	}

	/**
	 * Ajax endpoint for the final step of the wizard.
	 *
	 * @since 2.9
	 */
	public function finish_wizard() {
		check_ajax_referer( 'affwpwizard-admin-nonce', 'nonce' );

		// Mark the wizard completed.
		update_option( 'affwp_has_run_wizard', 1 );
		update_option( 'affwp_trigger_wizard', false );

		// Send JSON response.
		wp_send_json_success();
	}

	/**
	 * Load the scripts needed for the Onboarding Wizard.
	 *
	 * @since 2.9
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'affwpwizard-vue-style', plugins_url( '/assets/vue/wizard/dist/css/wizard.css', AFFILIATEWP_PLUGIN_FILE ), array(), AFFILIATEWP_VERSION );
		wp_register_script( 'affwpwizard-vue-script', plugins_url( '/assets/vue/wizard/dist/js/wizard.js', AFFILIATEWP_PLUGIN_FILE ), array(), AFFILIATEWP_VERSION, true );
		wp_enqueue_script( 'affwpwizard-vue-script' );

		wp_localize_script(
			'affwpwizard-vue-script',
			'affwpwizard',
			array(
				'ajax'           => add_query_arg( 'page', 'affiliatewp-onboarding', admin_url( 'admin-ajax.php' ) ),
				'nonce'          => wp_create_nonce( 'affwpwizard-admin-nonce' ),
				'affwpAdminUrl'  => affwp_admin_url(),
				'network'        => is_network_admin(),
				'translations'   => $this->get_jed_locale_data( 'mi-vue-app' ),
				'assets'         => plugins_url( '/assets/vue/wizard', AFFILIATEWP_PLUGIN_FILE ),
				'wizard_url'     => is_network_admin() ? network_admin_url( 'index.php?page=affiliatewp-onboarding' ) : admin_url( 'index.php?page=affiliatewp-onboarding' ),
				'exit_url'       => affwp_admin_url(),
				'plugin_version' => AFFILIATEWP_VERSION,
				'site_url'       => get_site_url(),
				'logo'           => AFFILIATEWP_PLUGIN_URL . 'assets/images/affiliatewp-1.svg',
			)
		);

	}

	/**
	 * Outputs the simplified header used for the Onboarding Wizard.
	 *
	 * @since 2.9
	 */
	public function onboarding_wizard_header() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width"/>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<title><?php esc_html_e( 'AffiliateWP &rsaquo; Onboarding Wizard', 'affiliate-wp' ); ?></title>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_print_scripts' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="affwpwizard-onboarding">
		<?php
	}

	/**
	 * Outputs the content of the current step.
	 *
	 * @since 2.9
	 */
	public function onboarding_wizard_content() {
		$admin_url = is_network_admin() ? network_admin_url() : admin_url();

		$this->error_page( 'affwpwizard-vue-onboarding-wizard', '<a href="' . $admin_url . '">' . esc_html__( 'Return to Dashboard', 'affiliate-wp' ) . '</a>' );
		$this->inline_js();
	}

	/**
	 * Outputs the simplified footer used for the Onboarding Wizard.
	 *
	 * @since 2.9
	 */
	public function onboarding_wizard_footer() {
		?>
		<?php wp_print_scripts( 'affwpwizard-vue-script' ); ?>
		</body>
		</html>
		<?php
	}

	/**
	 * Error page HTML.
	 *
	 * @since 2.9
	 *
	 * @param string $id Page div ID.
	 * @param string $footer Additional HTML code for footer.
	 * @param string $margin Margin for error div.
	 **/
	public function error_page( $id = 'affwpwizard-vue-onboarding-wizard', $footer = '', $margin = '82px 0' ) {
		$logo_image = AFFILIATEWP_PLUGIN_URL . 'assets/images/affwp-onboarding-logo.png';
	?>
	<style type="text/css">
			#affwpwizard-settings-area {
				visibility: hidden;
				animation: loadAffiliateWPSettingsNoJSView 0s 2s forwards;
			}

			@keyframes loadAffiliateWPSettingsNoJSView{
				to   { visibility: visible; }
			}
	</style>
	<!--[if IE]>
			<style>
					#affwpwizard-settings-area{
							visibility: visible !important;
					}
			</style>
	<![endif]-->
	<div id="<?php echo $id; ?>">
			<div id="affwpwizard-settings-area" class="affwpwizard-settings-area mi-container" style="font-family:'Helvetica Neue', 'HelveticaNeue-Light', 'Helvetica Neue Light', Helvetica, Arial, 'Lucida Grande', sans-serif;margin: auto;width: 750px;max-width: 100%;">
					<div id="affwpwizard-settings-error-loading-area">
							<div class="" style="text-align: center; background-color: #fff;border: 1px solid #D6E2EC; padding: 15px 50px 30px; color: #777777; margin: <?php echo esc_attr( $margin ); ?>">
									<div class="" style="border-bottom: 0;padding: 5px 20px 0;">
											<img class="" src="<?php echo esc_attr( $logo_image ); ?>" alt="" style="max-width: 100%;width: 240px;padding: 30px 0 15px;">
									</div>
									<div id="affwpwizard-error-js">
											<h3 class="" style="font-size: 20px;color: #434343;font-weight: 500;line-height:1.4;"><?php esc_html_e( 'Ooops! It Appears JavaScript Didn’t Load', 'affiliate-wp' ); ?></h3>
											<p class="info" style="line-height: 1.5;margin: 1em 0;font-size: 16px;color: #434343;padding: 5px 20px 20px;"><?php esc_html_e( 'There seems to be an issue running JavaScript on your website, which AffiliateWP is crafted in to give you the best experience possible.', 'affiliate-wp' ); ?></p>
					<p class="info"style="line-height: 1.5;margin: 1em 0;font-size: 16px;color: #434343;padding: 5px 20px 20px;">
						<?php
						// Translators: Placeholders make the text bold.
						printf( esc_html__( 'If you are using an %1$sad blocker%2$s, please disable or whitelist the current page to load AffiliateWP correctly.', 'affiliate-wp' ), '<strong>', '</strong>' );
						?>
					</p>
											<div style="display: none" id="affwpwizard-nojs-error-message">
													<div class="" style="  border: 1px solid #E75066;
																															border-left: 3px solid #E75066;
																															background-color: #FEF8F9;
																															color: #E75066;
																															font-size: 14px;
																															padding: 18px 18px 18px 21px;
																															font-weight: 300;
																															text-align: left;">
															<strong style="font-weight: 500;" id="affwpwizard-alert-message"></strong>
													</div>
													<p class="" style="font-size: 14px;color: #777777;padding-bottom: 15px;"><?php esc_html_e( 'Copy the error message above and paste it in a message to the AffiliateWP support team.', 'affiliate-wp' ); ?></p>
											</div>
									</div>
									<div id="affwpwizard-error-browser" style="display: none">
											<h3 class="" style="font-size: 20px;color: #434343;font-weight: 500;"><?php esc_html_e( 'Your browser version is not supported', 'affiliate-wp' ); ?></h3>
											<p class="info" style="line-height: 1.5;margin: 1em 0;font-size: 16px;color: #434343;padding: 5px 20px 20px;"><?php esc_html_e( 'You are using a browser which is no longer supported by AffiliateWP. Please update or use another browser in order to access the plugin settings.', 'affiliate-wp' ); ?></p>
											<a href="https://www.monsterinsights.com/docs/browser-support-policy/" target="_blank" style="margin-left: auto;background-color: #54A0E0;border-color: #3380BC;border-bottom-width: 2px;color: #fff;border-radius: 3px;font-weight: 500;transition: all 0.1s ease-in-out;transition-duration: 0.2s;padding: 14px 35px;font-size: 16px;margin-top: 10px;margin-bottom: 20px; text-decoration: none; display: inline-block;">
													<?php esc_html_e( 'View supported browsers', 'affiliate-wp' ); ?>
											</a>
									</div>
							</div>
					</div>
		<div style="text-align: center;">
			<?php echo wp_kses_post( $footer ); ?>
		</div>
			</div>
	</div>
		<?php
	}

	/**
	 * Attempt to catch the js error preventing the Vue app from loading and displaying that message for better support.
	 *
	 * @since 2.9
	 */
	public function inline_js() {
		?>
		<script type="text/javascript">
			var ua = window.navigator.userAgent;
			var msie = ua.indexOf( 'MSIE ' );
			if ( msie > 0 ) {
				var browser_error = document.getElementById( 'affwpwizard-error-browser' );
				var js_error = document.getElementById( 'affwpwizard-error-js' );
				js_error.style.display = 'none';
				browser_error.style.display = 'block';
			} else {
				window.onerror = function myErrorHandler( errorMsg, url, lineNumber ) {
									/* Don't try to put error in container that no longer exists post-vue loading */
					var message_container = document.getElementById( 'affwpwizard-nojs-error-message' );
									if ( ! message_container ) {
											return false;
									}
					var message = document.getElementById( 'affwpwizard-alert-message' );
					message.innerHTML = errorMsg;
					message_container.style.display = 'block';
					return false;
				}
			}
		</script>
		<?php
	}

	/**
	 * Returns Jed-formatted localization data. Added for backwards-compatibility.
	 *
	 * @since 2.9
	 *
	 * @param  string $domain Translation domain.
	 * @return array Array of Jed-formatted localization data.
	 */
	public function get_jed_locale_data( $domain ) {
		$translations = get_translations_for_domain( $domain );

		$locale = array(
			'' => array(
				'domain' => $domain,
				'lang'   => is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale(),
			),
		);

		if ( ! empty( $translations->headers['Plural-Forms'] ) ) {
			$locale['']['plural_forms'] = $translations->headers['Plural-Forms'];
		}

		foreach ( $translations->entries as $msgid => $entry ) {
			$locale[ $msgid ] = $entry->translations;
		}

		return $locale;
	}

}
