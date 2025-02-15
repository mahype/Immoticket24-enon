<?php
/**
 * Integrations: Paid Memberships Pro
 *
 * @package     AffiliateWP
 * @subpackage  Integrations
 * @copyright   Copyright (c) 2016, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.8
 */

/**
 * Implements an integration for Paid Memberships Pro.
 *
 * @since 1.8
 *
 * @see Affiliate_WP_Base
 */
class Affiliate_WP_PMP extends Affiliate_WP_Base {

	/**
	 * Whether membership-level referrals are enabled.
	 *
	 * @since 1.8
	 * @access public
	 * @var bool
	 */
	public $level_referrals_enabled;

	/**
	 * Membership-level referrals settings.
	 *
	 * @since 1.8
	 * @access public
	 * @var array
	 */
	public $level_referrals_settings = array();

	/**
	 * The context for referrals. This refers to the integration that is being used.
	 *
	 * @since 1.2
	 * @since 2.21.1 Using hook pmpro_updated_order to keep order and referral status synchronized.
	 */
	public $context = 'pmp';

	public function init() {

		add_action( 'pmpro_added_order', array( $this, 'add_pending_referral' ), 10 );
		add_action( 'pmpro_updated_order', array( $this, 'update_referral_status' ), 10 );
		add_action( 'pmpro_delete_order', array( $this, 'revoke_referral_on_delete' ), 10, 2 );
		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );
		add_filter( 'pmpro_orders_show_affiliate_ids', '__return_true' );

		// Coupon support
		add_action( 'pmpro_discount_code_after_settings', array( $this, 'coupon_option' ) );
		add_action( 'pmpro_save_discount_code', array( $this, 'save_affiliate_coupon' ) );

		// Membership level referrals
		add_action( 'pmpro_membership_level_after_other_settings', array( $this, 'membership_level_setting' ) );
		add_action( 'pmpro_save_membership_level', array( $this, 'save_membership_level_setting' ) );
	}

	/**
	 * Add a pending referral
	 *
	 * @since   1.5
	 *
	 * @param MemberOrder $order PMP order.
	 */
	public function add_pending_referral( $order ) {
		global $wpdb;

		// Check if an affiliate coupon was used.
		$coupon_affiliate_id = false;
		$is_coupon_referral  = false;
		if ( ! empty( $order->discount_code ) ) {
			$coupon_affiliate_id = $this->get_coupon_affiliate_id( $order->discount_code );
		}

		// Check if referred or coupon.
		if ( ! $this->was_referred() && ! $coupon_affiliate_id ) {
			return; // Referral not created because affiliate not referred and not a coupon.
		}

		// get affiliate ID.
		$affiliate_id = $this->get_affiliate_id( $order->id );
		if ( false !== $coupon_affiliate_id ) {
			$affiliate_id       = $coupon_affiliate_id;
			$is_coupon_referral = true;
		}

		// Get description.
		if ( isset( $order->membership_name ) ) {
			// paid membership level.
			$membership_name = $order->membership_name;
		} else {
			// free membership.
			$membership_name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM $wpdb->pmpro_membership_levels WHERE id = %d LIMIT 1", $order->membership_id ) );
		}

		// Create draft referral.
		$referral_id = $this->insert_draft_referral(
			$affiliate_id,
			array(
				'reference'          => $order->id,
				'description'        => $membership_name,
				'is_coupon_referral' => $is_coupon_referral,
			)
		);
		if ( ! $referral_id ) {
			$this->log( 'Draft referral creation failed.' );
			return;
		}

		// Check membership level.
		$membership_level               = isset( $order->membership_id ) ? (int) $order->membership_id : 0;
		$this->level_referrals_settings = get_option( "_affwp_pmp_product_settings_{$membership_level}", array() );
		if ( ! empty( $this->level_referrals_settings['disabled'] ) ) {
			$this->log( 'Draft referral rejected because referrals have been disabled for this level.' );
			$this->mark_referral_failed( $referral_id );
			return;
		}

		// If the membership-level rate is empty, it's effectively disabled (default rate).
		if ( empty( $this->level_referrals_settings['rate'] ) ) {
			$this->level_referrals_enabled = false;
		} else {
			// Otherwise check if membership-level referrals are explicitly disabled.
			$this->level_referrals_enabled = ( isset( $this->level_referrals_settings['disabled'] ) && false == $this->level_referrals_settings['disabled'] );
		}

		// Customers cannot refer themselves.
		$user = get_userdata( $order->user_id );
		if ( $user instanceof WP_User && $this->is_affiliate_email( $user->user_email, $affiliate_id ) ) {
			$this->log( 'Referral not created because affiliate\'s own account was used.' );
			$this->mark_referral_failed( $referral_id );
			return;
		}

		// Get referral total.
		$referral_total = $this->calculate_referral_amount( $order->subtotal, $order->id, $membership_level, $affiliate_id );

		// Set referral custom var.
		$custom = array();
		if ( 'success' === strtolower( $order->status ) ) {

			$custom = array(
				'order_id' => $order->id,
			);
		}

		// Hydrates the previously created referral.
		$this->hydrate_referral(
			$referral_id,
			array(
				'status'             => 'pending',
				'amount'             => $referral_total,
				'is_coupon_referral' => false !== $coupon_affiliate_id,
				'custom'             => $custom,
			)
		);
		$this->log( sprintf( 'Paid Membership Pro referral #%d updated to pending successfully.', $referral_id ) );

		// Check if referral can be marked complete.
		if ( 'success' === strtolower( $order->status ) ) {
			$this->update_referral_status( $order );
		}

		// Complete referral if subtotal is zero.
		if ( 0 == $order->subtotal ) {
			$this->complete_referral( $order->id );
		}
	}

	/**
	 * Retrieves the rate and type for a specific product.
	 *
	 * @since 1.8
	 * @access public
	 *
	 * @return float Product rate.
	 */
	public function get_product_rate( $product_id = 0, $args = array() ) {
		$affiliate_id = $args['affiliate_id'];

		$rate = '';

		if ( $this->level_referrals_enabled ) {
			$rate = $this->level_referrals_settings['rate'];
		}

		// Product ID is expected to be 0 for PMP.
		$product_id = 0;

		/** This filter is documented in includes/integrations/class-base.php */
		$rate = apply_filters( 'affwp_get_product_rate', $rate, $product_id, $args, $affiliate_id, $this->context );

		$rate = affwp_sanitize_referral_rate( $rate );

		return $rate;
	}

	/**
	 * Used with the pmpro_updated_order hook to synchronize order and referral statuses.
	 *
	 * @since 2.21.1
	 * @param mixed $order The order object.
	 */
	public function update_referral_status( $order ) {

		$order_id     = absint( $order->id );
		$order_status = strtolower( $order->status );

		if ( 'success' === $order_status && $this->complete_referral( $order_id ) ) {

			$referral = affwp_get_referral_by( 'reference', $order_id, $this->context );

			if ( is_wp_error( $referral ) || ! is_a( $referral, '\AffWP\Referral' ) ) {

				affiliate_wp()->utils->log( 'update_referral_status: The referral could not be found.', $referral );

				return;  // Referral cannot be found; log the error and exit.
			}

			// Prevent an infinite loop by temporarily removing the action hook.
			remove_action( 'pmpro_updated_order', array( $this, 'update_referral_status' ), 10 );

			// Create a new MemberOrder instance for the order ID.
			$order = new MemberOrder( $order_id );

			// Set the affiliate ID for the order.
			$order->affiliate_id = $referral->affiliate_id;

			// Format the referral amount and affiliate name.
			$amount = html_entity_decode( affwp_currency_filter( affwp_format_amount( $referral->amount ) ), ENT_QUOTES, 'UTF-8' );
			$name   = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );

			// Create a note for the order with referral details.
			$note = sprintf(
				/* translators: 1: Referral ID, 2: Formatted referral amount, 3: Affiliate name, 4: Referral affiliate ID  */
				__( 'Referral #%1$d for %2$s recorded for %3$s (ID: %4$d).', 'affiliate-wp' ),
				$referral->referral_id,
				$amount,
				$name,
				$referral->affiliate_id
			);

			// Append the note to the existing order notes, handling empty notes.
			$order->notes = empty( $order->notes )
				? $note
				: "{$order->notes}\n\n{$note}";

			// Save the updated order.
			$order->saveOrder();

			// Add the action hook back after the order is updated.
			add_action( 'pmpro_updated_order', array( $this, 'update_referral_status' ), 10 );

			return; // Nothing more to do here.
		}

		// Check if the order status is 'refunded' and revocation on refund is enabled, or if it is in an array of specific statuses.
		if (
			( 'refunded' === $order_status && affiliate_wp()->settings->get( 'revoke_on_refund' ) ) ||
			in_array( $order_status, array( 'token', 'error', 'review', 'pending' ), true )
		) {

			// Reject the referral associated with the order.
			$this->reject_referral( $order_id );
		}
	}

	/**
	 * Marks a referral as complete.
	 *
	 * @deprecated 2.21.1 Use update_referral_status() instead.
	 * @see update_referral_status()
	 *
	 * @param mixed $order Order details or object.
	 */
	public function mark_referral_complete( $order ) {

		affiliatewp_deprecate_function() (
			__FUNCTION__,
			__( 'Use update_referral_status() instead.', 'affiliate-wp' ),
			'2.21.1'
		);

		if( 'success' !== strtolower( $order->status ) ) {
			return;
		}

		$this->complete_referral( $order->id );

		$referral = affwp_get_referral_by( 'reference', $order->id, $this->context );

		if ( ! is_wp_error( $referral ) ) {
			$order    = new MemberOrder( $order->id );

			// Prevent infinite loop
			remove_action( 'pmpro_updated_order', array( $this, 'mark_referral_complete' ), 10 );

			$order->affiliate_id = $referral->affiliate_id;
			$amount              = html_entity_decode( affwp_currency_filter( affwp_format_amount( $referral->amount ) ), ENT_QUOTES, 'UTF-8' );
			$name                = affiliate_wp()->affiliates->get_affiliate_name( $referral->affiliate_id );
			/* translators: 1: Referral ID, 2: Formatted referral amount, 3: Affiliate name, 4: Referral affiliate ID  */
			$note                = sprintf( __( 'Referral #%1$d for %2$s recorded for %3$s (ID: %4$d).', 'affiliate-wp' ),
					$referral->referral_id,
					$amount,
					$name,
					$referral->affiliate_id
			);

			if( empty( $order->notes ) ) {
				$order->notes = $note;
			} else {
				$order->notes = $order->notes . "\n\n" . $note;
			}

			$order->saveOrder();
		} else {
			affiliate_wp()->utils->log( 'mark_referral_complete: The referral could not be found.', $referral );
		}
	}


	public function revoke_referral_on_delete( $order_id, $order ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$this->reject_referral( $order_id );

	}

	public function reference_link( $reference, $referral ) {

		if( empty( $referral->context ) || 'pmp' != $referral->context ) {

			return $reference;

		}

		$url = admin_url( 'admin.php?page=pmpro-orders&order=' . $reference );

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}

	/**
	 * Shows the affiliate drop down on the discount edit / add screens
	 *
	 * @access  public
	 * @since   1.7.5
	 */
	public function coupon_option( $edit ) {

		global $wpdb;

		add_filter( 'affwp_is_admin_page', '__return_true' );
		affwp_admin_scripts();

		$user_id   = 0;
		$user_name = '';

		if( $edit > 0 ) {
			$table = $wpdb->prefix . 'affiliate_wp_affiliatemeta';
			$affiliate_id = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE meta_key = %s", 'affwp_discount_pmp_' . $edit ) );
		} else {
			$affiliate_id = false;
		}
		if( $affiliate_id ) {
			$user_id      = affwp_get_affiliate_user_id( $affiliate_id );
			$user         = get_userdata( $user_id );
			$user_name    = $user ? $user->user_login : '';
		}
		?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top"><label for="user_name"><?php _e( 'Affiliate Discount?', 'affiliate-wp' ); ?></label></th>
					<td class="form-field affwp-pmp-coupon-field">
						<span class="affwp-ajax-search-wrap">
							<span class="affwp-pmp-coupon-input-wrap">
								<input type="text" name="user_name" id="user_name" value="<?php echo esc_attr( $user_name ); ?>" class="affwp-user-search" data-affwp-status="active" autocomplete="off" style="width:150px" />
							</span>
							<small class="pmpro_lite"><?php _e( 'If you would like to connect this discount to an affiliate, enter the name of the affiliate it belongs to.', 'affiliate-wp' ); ?></small>
						</span>
						<?php wp_nonce_field( 'affwp_pmp_coupon_nonce', 'affwp_pmp_coupon_nonce' ); ?>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Saves an affiliate coupon
	 *
	 * @access  public
	 * @since   1.7.5
	 */
	public function save_affiliate_coupon( $save_id = 0 ) {

		global $wpdb;

		if( empty( $_REQUEST['affwp_pmp_coupon_nonce'] ) ) {
			return;
		}

		if( ! wp_verify_nonce( $_REQUEST['affwp_pmp_coupon_nonce'], 'affwp_pmp_coupon_nonce' ) ) {
			return;
		}

		// Store a copy of the username (if present) for use after processing.
		$user_name = empty( $_POST['user_name'] ) ? '' : sanitize_text_field( $_POST['user_name'] );

		$data = affiliate_wp()->utils->process_request_data( $_POST, 'user_name' );

		$coupon = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM {$wpdb->pmpro_discount_codes} WHERE code = %s LIMIT 1",
			esc_sql( $_REQUEST['code'] )
		) );

		$affiliate_id = affwp_get_affiliate_id( $data['user_id'] );

		if( empty( $user_name ) ) {
			affwp_delete_affiliate_meta( $affiliate_id, 'affwp_discount_pmp_' . $coupon->id ?? 0 );
			return;
		}


		affwp_update_affiliate_meta( $affiliate_id, 'affwp_discount_pmp_' . $coupon->id ?? 0, $coupon->code ?? '' );
	}

	/**
	 * Get the affiliate associated with a coupon
	 *
	 * @access  public
	 * @since   1.7.5
	 */
	public function get_coupon_affiliate_id( $coupon_code ) {
		global $wpdb;

		$affiliate_id = false;

		if( ! empty( $coupon_code ) && pmpro_checkDiscountCode( $coupon_code ) ) {
			$table        = $wpdb->prefix . 'affiliate_wp_affiliatemeta';
			$affiliate_id = $wpdb->get_var( $wpdb->prepare( "SELECT affiliate_id FROM $table WHERE meta_value = %s", $coupon_code ) );
		}

		return $affiliate_id;
	}

	/**
	 * Outputs membership level referral settings.
	 *
	 * @since 1.8
	 * @access public
	 */
	public function membership_level_setting() {
		$level = isset( $_REQUEST['edit'] ) ? intval( $_REQUEST['edit'] ) : 0;

		if ( ! $level ) {
			return;
		}

		$default_rate = affiliate_wp()->settings->get( 'referral_rate', 20 );

		$affwp_pmp_settings = get_option( "_affwp_pmp_product_settings_{$level}", array() );

		$rate     = ! empty( $affwp_pmp_settings['rate'] ) ? $affwp_pmp_settings['rate'] : '';
		$disabled = empty( $affwp_pmp_settings['disabled'] ) ? false : true;
		?>
		<h3 class="topborder"><?php _e( 'Affiliate Settings', 'affiliate-wp' );?></h3>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top">
						<label for="affwp_pmp_referral_rate"><?php _e( 'Referral Rate', 'affiliate-wp' );?>:</label>
					</th>
					<td>
						<input id="affwp_pmp_referral_rate" class="regular-text" name="affwp_pmp_referral_rate" type="number" min="0" max="999999999" step="0.01" placeholder="<?php echo esc_attr( $default_rate ); ?>" value="<?php echo esc_attr( $rate ); ?>" />
						<p class="description">
							<?php
							/* translators: Referral rate */
							printf( __( 'The membership-level referral rate, such as 20 for 20%%. Affiliate-level referral rates will override this value. If left blank, the site default value of %s will be used.', 'affiliate-wp' ), esc_html( $default_rate ) );
							?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">
						<label for="affwp_pmp_disable_referrals"><?php _e( 'Disable Referrals', 'affiliate-wp' );?>:</label>
					</th>
					<td><input id="affwp_pmp_disable_referrals" name="affwp_pmp_disable_referrals" type="checkbox" value="yes" <?php checked( $disabled, true ); ?> /> <label for="affwp_pmp_disable_referrals"><?php _e( 'Check to disable per-membership referrals.', 'affiliate-wp' );?></label></td>
				</tr>
			</tbody>
		</table>
		<?php wp_nonce_field( 'affwp_pmp_membership_referrals_nonce', 'affwp_pmp_membership_referrals_nonce' );
	}

	/**
	 * Saves membership level affiliate settings.
	 *
	 * @since 1.8
	 * @access public
	 *
	 * @param int $level_id Level ID.
	 */
	public function save_membership_level_setting( $level_id ) {
		if ( ! $level_id || empty( $_REQUEST['affwp_pmp_membership_referrals_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['affwp_pmp_membership_referrals_nonce'], 'affwp_pmp_membership_referrals_nonce' ) ) {
			return;
		}

		$rate     = isset( $_REQUEST['affwp_pmp_referral_rate'] ) ? sanitize_text_field( $_REQUEST['affwp_pmp_referral_rate'] ) : '';
		$disabled = (bool) isset( $_REQUEST['affwp_pmp_disable_referrals'] );

		$settings = array(
			'rate'     => $rate,
			'disabled' => $disabled,
		);

		update_option( "_affwp_pmp_product_settings_{$level_id}", $settings );
	}

	/**
	 * Runs the check necessary to confirm this plugin is active.
	 *
	 * @since 2.5
	 *
	 * @return bool True if the plugin is active, false otherwise.
	 */
	function plugin_is_active() {
		return class_exists( 'MemberOrder' );
	}
}

new Affiliate_WP_PMP();
