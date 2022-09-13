<?php
/**
 * Emails: Functions
 *
 * @package     AffiliateWP
 * @subpackage  Emails
 * @copyright   Copyright (c) 2015, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.6
 */

// phpcs:disable PEAR.Functions.FunctionCallSignature.EmptyLine -- Formatting this was is ok here.
// phpcs:disable PEAR.Functions.FunctionCallSignature.FirstArgumentPosition -- Formatting this was is ok here.

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get a list of available email templates
 *
 * @since 1.6
 * @return array
 */
function affwp_get_email_templates() {
	return affiliate_wp()->emails->get_templates();
}

/**
 * Get a formatted HTML list of all available tags
 *
 * @since 1.6
 * @return string $list HTML formated list
 */
function affwp_get_emails_tags_list() {
	// The list
	$list = '';

	// Get all tags
	$email_tags = affiliate_wp()->emails->get_tags();

	// Check
	if( count( $email_tags ) > 0 ) {
		foreach( $email_tags as $email_tag ) {
			$list .= '{' . $email_tag['tag'] . '} - ' . $email_tag['description'] . '<br />';
		}
	}

	// Return the list
	return $list;
}


/**
 * Email template tag: name
 * The affiliate's name
 *
 * @param int $affiliate_id
 * @return string name
 */
function affwp_email_tag_name( $affiliate_id = 0 ) {
	return affiliate_wp()->affiliates->get_affiliate_name( $affiliate_id );
}


/**
 * Email template tag: username
 * The affiliate's username on the site
 *
 * @param int $affiliate_id
 * @return string username
 */
function affwp_email_tag_user_name( $affiliate_id = 0 ) {
	$user_info = get_userdata( affwp_get_affiliate_user_id( $affiliate_id ) );

	return $user_info->user_login;
}


/**
 * Email template tag: user_email
 * The affiliate's email
 *
 * @param int $affiliate_id
 * @return string email
 */
function affwp_email_tag_user_email( $affiliate_id = 0 ) {
	return affwp_get_affiliate_email( $affiliate_id );
}


/**
 * Email template tag: website
 * The affiliate's website
 *
 * @param int $affiliate_id
 * @return string website
 */
function affwp_email_tag_website( $affiliate_id = 0 ) {
	$user_info = get_userdata( affwp_get_affiliate_user_id( $affiliate_id ) );

	return $user_info->user_url;
}


/**
 * Email template tag: promo_method
 * The affiliate promo method
 *
 * @param int $affiliate_id
 * @return string promo_method
 */
function affwp_email_tag_promo_method( $affiliate_id = 0 ) {
	return get_user_meta( affwp_get_affiliate_user_id( $affiliate_id ), 'affwp_promotion_method', true );
}

/**
 * Email template tag: affwp_email_tag_rejection_reason
 * The affiliate rejection reason
 *
 * @param int $affiliate_id Affiliate ID.
 * @return string rejection_reason
 */
function affwp_email_tag_rejection_reason( $affiliate_id ) {
	$reason = affwp_get_affiliate_meta( $affiliate_id, '_rejection_reason', true );
	if( empty( $reason ) ) {
		$reason = '';
	}
	return $reason;
}


/**
 * Email template tag: login_url
 * The affiliate login URL
 *
 * @return string login_url
 */
function affwp_email_tag_login_url() {
	return esc_url( affiliate_wp()->login->get_login_url() );
}


/**
 * Email template tag: amount
 * The amount of an affiliate transaction
 *
 * @return string amount
 */
function affwp_email_tag_amount( $affiliate_id, $referral ) {
	return html_entity_decode( affwp_currency_filter( $referral->amount ), ENT_COMPAT, 'UTF-8' );
}


/**
 * Email template tag: sitename
 * Your site name
 *
 * @return string sitename
 */
function affwp_email_tag_site_name() {
	return wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
}

/**
 * Email template tag: referral URL
 * Affiliate's referral URL
 *
 * @return string referral_url
 */
function affwp_email_tag_referral_url( $affiliate_id = 0 ) {
	return affwp_get_affiliate_referral_url( array( 'affiliate_id' => $affiliate_id ) );
}

/**
 * Email template tag: affiliate ID
 * Affiliate's ID
 *
 * @return int affiliate ID
 */
function affwp_email_tag_affiliate_id( $affiliate_id = 0 ) {
	return $affiliate_id;
}

/**
 * Email template tag: referral rate
 * The affiliate's referral rate as shown from Affiliate -> Affiliates
 *
 * @since 1.9
 * @return string referral_rate
 */
function affwp_email_tag_referral_rate( $affiliate_id = 0 ) {
	return affwp_get_affiliate_rate( $affiliate_id, true );
}

/**
 * Email template tag: review URL
 * Affiliate's review page URL
 *
 * @since 1.9
 * @return string URL to the review page
 */
function affwp_email_tag_review_url( $affiliate_id = 0 ) {
	return affwp_admin_url( 'affiliates', array( 'affiliate_id' => absint( $affiliate_id ), 'action' => 'review_affiliate' ) );
}

/**
 * Email template tag: registration_coupon
 * The affiliate's registration coupon
 *
 * @since 2.6
 *
 * @param int $affiliate_id Affiliate ID.
 * @return string Affiliate registration coupon, or empty string if none.
 */
function affwp_email_tag_registration_coupon( $affiliate_id = 0 ) {
	$coupon_code = '';

	$coupons = affwp_get_dynamic_affiliate_coupons( $affiliate_id, false );

	if ( ! empty( $coupons ) ) {
		$coupon = reset( $coupons );

		$coupon_code = affwp_get_affiliate_coupon_code( $affiliate_id, $coupon->coupon_id );
	}

	return $coupon_code;
}

/**
 * Get the landing page of the referral
 *
 * @since 1.9
 * @return string URL to the landing page
 */
function affwp_email_tag_get_landing_page( $affiliate_id, $referral ) {
    return esc_url( affiliate_wp()->visits->get_column_by( 'url', 'visit_id', $referral->visit_id ) );
}

/**
 * Gets the campaign (if set) of the referral.
 *
 * @since 1.9.4
 *
 * @param int             $affiliate_id Affiliate ID.
 * @param \AffWP\Referral $referral     Referral object.
 * @return string Referral campaign, or (no campaign) if none.
 */
function affwp_email_tag_campaign_name( $affiliate_id, $referral ) {
	return empty( $referral->campaign ) ? __( '(no campaign)', 'affiliate-wp' ) : esc_html( $referral->campaign );
}

/**
 * Determine if New Referral Notifications can be sent to the affiliate
 *
 * @since 2.2
 * @uses affwp_email_notification_enabled()
 * @param int $affiliate_id The affiliate's ID
 *
 * @return boolean True if new referral notifications are enabled, false otherwise.
 */
function affwp_email_referral_notifications( $affiliate_id = 0 ) {

	$enabled = false;

	if ( true === affwp_email_notification_enabled( 'affiliate_new_referral_email', $affiliate_id ) ) {
		$enabled = true;
	}

	return (bool) $enabled;

}

/**
 * Determine if a specific email notification is enabled.
 *
 * @since 2.2
 * @param string $email_notification The email notification to check.
 * @param int $affiliate_id The affiliate's ID
 *
 * @return boolean True if the email notification is enabled, false otherwise.
 */
function affwp_email_notification_enabled( $email_notification = '', $affiliate_id = 0 ) {

	$enabled = false;

	if ( array_key_exists( $email_notification, affwp_get_enabled_email_notifications() ) ) {
		$enabled = true;
	}

	/**
	 * Filters whether the email notification is enabled.
	 *
	 * @since 2.2
	 *
	 * @param bool   $enabled            Whether the email notification is enabled.
	 * @param string $email_notification Email notification slug.
	 * @param int    $affiliate_id       Affiliate ID.
	 */
	return (bool) apply_filters( 'affwp_email_notification_enabled', $enabled, $email_notification, $affiliate_id );

}

/**
 * Get the email notifications settings array.
 *
 * @since 2.2
 *
 * @return array $email_notifications
 */
function affwp_get_enabled_email_notifications() {

	$email_notifications = affiliate_wp()->settings->get( 'email_notifications' );

	if ( is_array( $email_notifications ) ) {
		return $email_notifications;
	}

	// Return empty array.
	return array();

}

/**
 * Get the latest unsent DYK blurb.
 *
 * @since 2.9.6
 *
 * @return array Empty if we could not get them.
 *
 * @TODO Cache this data so we can send unsent blurbs in the case of
 *       it not being available.
 */
function affwp_get_latest_unsent_dyk_blurb_for_my_license() {

	$response = wp_remote_get(
		defined( 'AFFWP_EMAIL_SUMMARIES_JSON' )
			? esc_url( AFFWP_EMAIL_SUMMARIES_JSON )
			: 'https://affiliatewp.com/wp-content/email-summaries.json',
		array(
			'sslverify' => defined( 'AFFILIATE_WP_DEBUG' ) && AFFILIATE_WP_DEBUG
				? false // We won't SSLVERIFY on local dev.
				: true, // But we will, in production.
		)
	);

	if ( is_wp_error( $response ) ) {
		return array();
	}

	if ( 200 !== absint( wp_remote_retrieve_response_code( $response ) ) ) {
		return array();
	};

	$json = wp_remote_retrieve_body( $response );

	if ( ! is_string( $json ) || empty( $json ) ) {
		return array();
	}

	$blurbs = json_decode( $json, true );

	if ( ! is_array( $blurbs ) ) {
		return array();
	}

	// Get ready to sort them by ID...
	$blurbs_by_id = array();

	// We'll need sent blurbs so we don't re-send any...
	$sent_blurbs = affwp_get_sent_dyk_blurbs();

	// We also want to just send blurbs important for that user's license...
	$license = new AffWP\Core\License\License_Data();

	foreach ( $blurbs as $blurb ) {

		if ( ! isset(

			// These are required at a minimum.
			$blurb['id'],
			$blurb['title']
		) ) {
			continue;
		}

		if (

			// You are trying to focus this DYK blurb to a license(s).
			isset( $blurb['type'] ) && is_array( $blurb['type'] ) && ! empty( $blurb['type'] ) &&

			// And your license is not in the array.
			! in_array( strtolower( $license->get_license_type( $license->get_license_id() ) ), $blurb['type'], true )
		) {
			continue; // Not for you.
		}

		if ( in_array( absint( $blurb['id'] ), $sent_blurbs, true ) ) {
			continue; // Already sent this one.
		}

		// Not a sent blurb.
		$blurbs_by_id[ $blurb['id'] ] = $blurb;
	}

	// Sort by ID so we get the latest...
	ksort( $blurbs_by_id );

	// Send back the top-most.
	return current( $blurbs_by_id );
}

/**
 * Get sent DYK Blurbs.
 *
 * @since 2.9.6
 *
 * @return array
 */
function affwp_get_sent_dyk_blurbs() {

	$sent_blurbs = get_option( 'affwp_emailed_dyk_blurbs', array() );

	if ( ! is_array( $sent_blurbs ) ) {
		return array(); // Something broke, reset blurbs.
	}

	// Sanitize blurbs (list of ID's).
	return array_map(
		function( $blurb_id ) {
			return absint( $blurb_id );
		},
		$sent_blurbs
	);
}

/**
 * Remember a DYK blurb that we sent.
 *
 * @since 2.9.6
 *
 * @param  int $blurb_id The Blurb ID from JSON.
 * @return bool          False if you didn't pass a proper ID.
 */
function affwp_add_sent_dyk_blurb( $blurb_id ) {

	$blurb_id = absint( $blurb_id );

	if ( 0 === $blurb_id ) {
		return false; // Can't add a nothing blurb.
	}

	$sent_blurbs = affwp_get_sent_dyk_blurbs();

	$sent_blurbs[] = absint( $blurb_id );

	/*
	 * We should always send a DYK blurb that isn't in this option (array),
	 * so update_option() should always return true (new value added).
	 */
	return update_option(
		'affwp_emailed_dyk_blurbs',
		array_map(
			// Sanitize (out) blurbs (list of ID's).
			function( $blurb_id ) {
				return absint( $blurb_id );
			},
			$sent_blurbs
		),
		false
	);
}

/**
 * Send an email summary.
 *
 * @param  string $name       Name of the email summary.
 * @param  string $to         To.
 * @param  string $subject    Subject.
 * @param  string $email_body Content.
 * @param  mixed  $data       Any data associated with the email.
 * @param  bool   $preview    Set to true to preview email instead.
 * @param  string $template   Template.
 *
 * @return bool               True if it was emailed.
 */
function affwp_email_summary(
	$name,
	$to,
	$subject,
	$email_body,
	$data = null,
	$preview = false,
	$template = 'summaries'
) {

	if ( $preview ) {
		check_admin_referer( 'preview_email_summary', '_wpnonce' );
	}

	$emailer = new Affiliate_WP_Emails();

	$emailer->template = $template;

	if ( $preview ) {

		/**
		 * This filter is documented below (same as filtering the email body below).
		 *
		 * @since 2.9.6
		 */
		die( apply_filters( "affwp_notify_{$name}_summary_body", $emailer->build_email( $email_body ) ) ); // phpcs:ignore -- Okay to die here without escaping.
	}

	$sent = $emailer->send(

		/**
		 * Filter who the Monthly Summary Emails are sent to.
		 *
		 * Default to admin email if they haven't set a separate manager email set.
		 *
		 * @since 2.9.6
		 *
		 * @param string $email The email address.
		 */
		apply_filters( "affwp_notify_{$name}_summary_to_email", $to ),

		/**
		 * Filter the subject of the Monthly Performance Summary Report.
		 *
		 * @since 2.9.6
		 *
		 * @param string $subject Subject.
		 */
		apply_filters( "affwp_notify_{$name}_summary_email_subject", $subject ),

		/**
		 * Filter the content of the Monthly Performance Summary.
		 *
		 * @since 2.9.6
		 *
		 * @param string $body Email body.
		 */
		apply_filters( "affwp_notify_{$name}_summary_email_body", $email_body )
	);

	/**
	 * When the Sumamry Email is sent.
	 *
	 * @since 2.9.6
	 *
	 * @param string $sent Sent status.
	 * @param mixed  $data Any data related to this email.
	 */
	do_action( "affwp_notify_{$name}_summary_email_sent", $sent, $data );

	return $sent;
}

/**
 * Are we previewing an email summary?
 *
 * @param  string $name Name of email summary.
 * @return bool
 */
function affwp_is_summary_email_preview( $name ) {

	if ( ! is_string( $name ) ) {
		return false;
	}

	return (
		is_admin() && // Within the admin.
		isset( $_GET['preview'] ) && // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We're not using the data.
		isset( $_GET[ "affwp_notify_{$name}_summary" ] ) && // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We're not using the data.
		isset( $_GET['_wpnonce'] ) && // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We're not using the data.
		current_user_can( 'manage_affiliate_options' ) // Only admins can see the preview.
	);
}

/**
 * Get the Monthly Administrative Performance Report Markup (Content).
 *
 * @param  array $timeframe {
 *     Timeframe.
 *     @type string $start Start time in Y-m-d format.
 *     @type string $end   End time in Y-m-d format.
 * }
 * @return string            HTML Markup for email body.
 */
function affwp_get_monthly_administrative_email_template_html( $timeframe ) {

	if ( ! is_array( $timeframe ) ) {
		return __( 'Invalid timeframe.', 'affiliate-wp' );
	}

	// Based on includes/admin/reports/tabs/class-sales-reports-tab.php::active_integration_supports_sales().
	$has_active_integration_supports_sales_repoorting = in_array(
		true,
		array_map(
			function( $supported_integration_id ) {

				$integration_data = affiliate_wp()->integrations->get( $supported_integration_id );

				// If active, will return true and will be at least one integration that is active that supports sales reporting.
				return method_exists( $integration_data, 'is_active' )
					&& $integration_data->is_active();

			},
			affiliate_wp()->integrations->query(
				array(
					'supports' => 'sales_reporting',
					'status'   => 'enabled',
					'fields'   => 'ids',
				)
			)
		),
		true
	);

	ob_start(); // Start email content...

	?>

	<div class="content">

		<!-- Welcome message -->
		<p><strong><?php esc_html_e( 'Hi there!', 'affiliate-wp' ); ?></strong></p>
		<p><?php esc_html_e( "Let's see how your affiliate program has performed over the last 30 days.", 'affiliate-wp' ); ?></p>

		<?php if ( $has_active_integration_supports_sales_repoorting ) : // Only if there is an integration active that supports sales. ?>

			<!-- Gross Affiliate-generated Revenue (own table) -->
			<table class="data" style="border-collapse: collapse; border: 2px solid #eee; margin: 20px 0; width: 100%; table-layout: fixed;">
				<tr style="text-align: center">
					<td style="border-right: 2px solid #eee; padding: 5px">

						<p style="margin-bottom: 0;">
							<strong>
								<?php esc_html_e( 'Total Program Revenue', 'affiliate-wp' ); ?>
							</strong>
						</p>

						<p style="font-size: 24px; margin-top: 10px;">
							<strong>
								<?php

								// Data.
								echo esc_html(
									affwp_currency_filter(
										affwp_format_amount(
											affiliate_wp()->referrals->sales->get_revenue_by_referral_status(
												array(
													'paid',
													'unpaid',
												),
												null,
												$timeframe
											)
										)
									)
								);

								?>
							</strong>
						</p>
					</td>
				</tr>
			</table>

		<?php endif; ?>

		<!-- Three other data points (also own table)... -->
		<table class="data" style="border-collapse: collapse; border: 2px solid #eee; margin: 20px 0;table-layout: fixed; width: 100%;">
			<tr style="text-align: center">

				<!-- New Approved Affiliates -->
				<td style="border-right: 2px solid #eee; padding: 10px; width: 33%;">

					<p style="margin-bottom: 0;">
						<strong>
							<?php echo wp_kses_post( __( 'New Approved<br>Affiliates', 'affiliate-wp' ) ); ?>
						</strong>
					</p>

					<p style="font-size: 24px; margin-top: 10px;">
						<strong>
							<?php

								// Data.
								echo absint(
									affiliate_wp()->affiliates->count(
										array(
											'date' => $timeframe,
											'status' => 'active'
										)
									)
								);

							?>
						</strong>
					</p>
				</td>

				<!-- Unpaid Earnings -->
				<td style="border-right: 2px solid #eee; padding: 10px; width: 33%;">

					<p style="margin-bottom: 0;">
						<strong>
							<?php echo wp_kses_post( __( 'Unpaid<br>Earnings', 'affiliate-wp' ) ); ?>
						</strong>
					</p>

					<p style="font-size: 24px; margin-top: 10px;">
						<strong>
							<?php

							// Data.
							echo esc_html(
								affwp_currency_filter(
									affwp_format_amount( affiliate_wp()->referrals->unpaid_earnings( $timeframe, 0, false ) )
								)
							);

							?>
						</strong>
					</p>
				</td>

				<!-- Paid Earnings -->
				<td style="padding: 10px; width: 33%;">

					<p style="margin-bottom: 0;">
						<strong>
							<?php echo wp_kses_post( __( 'Paid<br>Earnings', 'affiliate-wp' ) ); ?>
						</strong>
					</p>

					<p style="font-size: 24px; margin-top: 10px;">
						<strong>
							<?php

							// Data.
							echo esc_html(
								affwp_currency_filter(
									affwp_format_amount( affiliate_wp()->referrals->paid_earnings( $timeframe, 0, false ) )
								)
							);

							?>
						</strong>
					</p>
				</td>
			</tr>
		</table> <!-- / Three other data points (also own table)... -->

	</div><!-- / Content -->

	<?php
	/*
	 * Did you Know Blurb...
	 */

	$dyk_blurb = isset( $_GET['no_dyk'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- We're not using the data.
		? array() // Never show a DYK blurb when previewing.
		: affwp_get_latest_unsent_dyk_blurb_for_my_license();

	// Only if we got a valid DYK blurb from above...
	if ( isset( $dyk_blurb['id'] ) ) {
		?>

		<div style="margin: 60px 0 0;">

			<p style="text-align: center;">📣 <?php esc_html_e( 'Pro tip from our expert:', 'affiliate-wp' ); ?></p>

			<div style="padding: 32px; border: 2px solid #eee;">

				<p><strong><?php echo esc_html( $dyk_blurb['title'] ); ?></strong></p>
				<p><?php echo esc_html( wp_strip_all_tags( $dyk_blurb['content'] ) ); ?></p>

				<?php if ( isset( $dyk_blurb['url'] ) ) : ?>
					<p><a style="color: #E35043;" href="<?php echo esc_url( $dyk_blurb['url'] ); ?>" rel="noopener noreferrer"><?php echo esc_html( ( isset( $dyk_blurb['button'] ) && ! empty( $dyk_blurb['button'] ) ) ? $dyk_blurb['button'] : __( 'Learn More', 'affiliate-wp' ) ); ?></a></p>
				<?php endif; ?>
			</div>
		</div>

		<?php
	}

	/*
	 * Collect the content and preview or email...
	 */

	return ob_get_clean();
}
