<?php
/**
 * Payout functions
 *
 * @since 1.9
 * @package Affiliate_WP
 */

/**
 * Retrieves a payout object.
 *
 * @since 1.9
 *
 * @param int|AffWP\Affiliate\Payout $payout Payout ID or object.
 * @return AffWP\Affiliate\Payout|false Payout object if found, otherwise false.
 */
function affwp_get_payout( $payout = 0 ) {

	/**
	 * Filters the payout ID or object before it is retrieved.
	 *
	 * Passing a non-null value in the hook callback will effectively preempt retrieving
	 * the payout from the database, returning the passed value instead.
	 *
	 * @since 2.2.2
	 *
	 * @param null                        $payout_before Value to short circuit retrieval of the payout.
	 * @param int|\AffWP\Affiliate\Payout $payout        Payout ID or object passed to affwp_get_payout().
	 */
	$payout_before = apply_filters( 'affwp_get_payout_before', null, $payout );

	if ( null !== $payout_before ) {
		return $payout_before;
	}

	if ( is_object( $payout ) && isset( $payout->payout_id ) ) {
		$payout_id = $payout->payout_id;
	} elseif ( is_numeric( $payout ) ) {
		$payout_id = absint( $payout );
	} else {
		return false;
	}

	return affiliate_wp()->affiliates->payouts->get_object( $payout_id );
}

/**
 * Adds a payout record.
 *
 * @since 1.9
 *
 * @param array $args {
 *     Optional. Arguments for adding a new payout record. Default empty array.
 *
 *     @type int          $affiliate_id  Affiliate ID.
 *     @type int|array    $referrals     Referral ID or array of IDs.
 *     @type string       $amount        Payout amount.
 *     @type string       $payout_method Payout method.
 *     @type string       $status        Payout status. Default 'paid'.
 *     @type string|array $date          Payout date.
 * }
 * @return int|false The ID for the newly-added payout, otherwise false.
 */
function affwp_add_payout( $args = array() ) {

	if ( empty( $args['referrals'] ) || empty( $args['affiliate_id'] ) ) {
		return false;
	}

	if ( $payout = affiliate_wp()->affiliates->payouts->add( $args ) ) {
		return $payout;
	}

	return false;
}

/**
 * Deletes a payout.
 *
 * @since 1.9
 *
 * @param int|\AffWP\Affiliate\Payout $payout Payout ID or object.
 * @return bool True if the payout was successfully deleted, otherwise false.
 */
function affwp_delete_payout( $payout ) {
	if ( ! $payout = affwp_get_payout( $payout ) ) {
		return false;
	}

	// Handle updating paid referrals to unpaid.
	if ( $payout && in_array( $payout->status, array( 'paid', 'processing' ) ) ) {
		$referrals = affiliate_wp()->affiliates->payouts->get_referral_ids( $payout );

		foreach ( $referrals as $referral_id ) {
			if ( 'paid' == affwp_get_referral_status( $referral_id ) ) {
				affwp_set_referral_status( $referral_id, 'unpaid' );
			}
		}
	}

	if ( affiliate_wp()->affiliates->payouts->delete( $payout->ID, 'payout' ) ) {
		/**
		 * Fires immediately after a payout has been deleted.
		 *
		 * @since 1.9
		 *
		 * @param int $payout_id Payout ID.
		 */
		do_action( 'affwp_delete_payout', $payout->ID );

		return true;
	}

	return false;
}

/**
 * Retrieves the referrals associated with a payout.
 *
 * @since 1.9
 *
 * @param int|AffWP\Affiliate\Payout $payout Payout ID or object.
 * @return array|false List of referral objects associated with the payout, otherwise false.
 */
function affwp_get_payout_referrals( $payout = 0 ) {
	if ( ! $payout = affwp_get_payout( $payout ) ) {
		return false;
	}

	$referrals = affiliate_wp()->affiliates->payouts->get_referral_ids( $payout );

	return array_map( 'affwp_get_referral', $referrals );
}

/**
 * Retrieves the status label for a payout.
 *
 * @since 1.6
 *
 * @param int|AffWP\Affiliate\Payout $payout Payout ID or object.
 * @return string|false The localized version of the payout status label, otherwise false.
 */
function affwp_get_payout_status_label( $payout ) {

	if ( ! $payout = affwp_get_payout( $payout ) ) {
		return false;
	}

	$statuses = array(
		'processing' => _x( 'Processing', 'payout', 'affiliate-wp' ),
		'paid'       => _x( 'Paid', 'payout', 'affiliate-wp' ),
		'failed'     => __( 'Failed', 'affiliate-wp' ),
	);

	$label = array_key_exists( $payout->status, $statuses ) ? $statuses[ $payout->status ] : _x( 'Paid', 'payout', 'affiliate-wp' );

	/**
	 * Filters the payout status label.
	 *
	 * @since 1.9
	 *
	 * @param string                 $label  A localized version of the payout status label.
	 * @param AffWP\Affiliate\Payout $payout Payout object.
	 */
	return apply_filters( 'affwp_payout_status_label', $label, $payout );
}

/**
 * Retrieves the list of payout methods and corresponding labels.
 *
 * @since 2.4
 *
 * @return array Key/value pairs of payout methods where key is the payout method and the value is the label.
 */
function affwp_get_payout_methods() {

	$payout_methods = array(
		'manual' => __( 'Manual Payout', 'affiliate-wp' ),
	);

	/**
	 * Filters the payout methods.
	 *
	 * @since 2.4
	 *
	 * @param array $payout_methods Payout methods.
	 */
	return apply_filters( 'affwp_payout_methods', $payout_methods );
}

/**
 * Retrieves the label for a payout method.
 *
 * @since 2.4
 *
 * @param string $payout_method Optional, default is manual. Payout method.
 * @return string $label The localized version of the payout method label. If the payout method
 *                       isn't registered, the default 'Manual Payout' label will be returned.
 */
function affwp_get_payout_method_label( $payout_method = '' ) {

	$payout_methods = affwp_get_payout_methods();
	$label          = array_key_exists( $payout_method, $payout_methods ) ? $payout_methods[ $payout_method ] : $payout_methods['manual'];

	/**
	 * Filters the payout method label.
	 *
	 * @since 2.4
	 *
	 * @param string $label         A localized version of the payout method label.
	 * @param string $payout_method Payout method.
	 */
	return apply_filters( 'affwp_payout_method_label', $label, $payout_method );
}

/**
 * Checks if a given payout method is enabled.
 *
 * @since 2.4
 *
 * @param string $payout_method Payout method.
 * @return bool $enabled True if the payout method is enabled. False otherwise.
 */
function affwp_is_payout_method_enabled( $payout_method ) {

	$payout_methods = affwp_get_payout_methods();
	$enabled        = array_key_exists( $payout_method, $payout_methods ) ? true : false;

	/**
	 * Filters the payout method enabled boolean.
	 *
	 * @since 2.4
	 *
	 * @param bool   $enabled       True if the payout method is enabled. False otherwise.
	 * @param string $payout_method Payout method.
	 */
	return (bool) apply_filters( 'affwp_is_payout_method_enabled', $enabled, $payout_method );
}

/**
 * Retrieves a list of all enabled payout methods.
 *
 * @since 2.4
 *
 * @return array Enabled payout methods.
 */
function affwp_get_enabled_payout_methods() {

	$enabled_methods = array();

	foreach ( affwp_get_payout_methods() as $payout_method => $label ) {
		if ( affwp_is_payout_method_enabled( $payout_method ) ) {
			$enabled_methods[] = $payout_method;
		}
	}

	return $enabled_methods;
}

/**
 * Retrieves the list of preview payout request failed reasons and corresponding labels.
 *
 * @since 2.4
 *
 * @return array Key/value pairs of reasons where key is the reason and the value is the label.
 */
function affwp_get_preview_payout_request_failed_reasons() {
	$reasons = array(
		'invalid_account'               => __( 'Invalid affiliate account', 'affiliate-wp' ),
		'invalid_ps_account'            => __( 'Invalid Payouts Service account', 'affiliate-wp' ),
		'minimum_payout'                => __( 'Doesn&#8217;t meet the minimum payout amount', 'affiliate-wp' ),
		'no_ps_account'                 => __( 'Hasn&#8217;t created a Payouts Service account', 'affiliate-wp' ),
		'no_ps_payout_method'           => __( 'Hasn&#8217;t submitted payout method on Payouts Service', 'affiliate-wp' ),
		'no_referrals'                  => __( 'No referrals within the specified date range', 'affiliate-wp' ),
		'ps_account_disabled'           => __( 'Account temporarily disabled on the Payouts Service', 'affiliate-wp' ),
		'unable_to_retrieve_ps_account' => __( 'Unable to retrieve Payouts Service account', 'affiliate-wp' ),
		'user_account_deleted'          => __( 'Affiliate user account deleted', 'affiliate-wp' ),
	);

	/**
	 * Filters the preview payout request failed reasons.
	 *
	 * @since 2.4
	 *
	 * @param array $reasons Array of key/value pairs of reasons.
	 */
	return apply_filters( 'affwp_get_preview_payout_request_failed_reasons', $reasons );
}

/**
 * Retrieves the label for a preview payout request failed reason.
 *
 * @since 2.4
 *
 * @param string $reason Preview payout request failed reason.
 * @return string The localized version of the reason label.
 */
function affwp_get_preview_payout_request_failed_reason_label( $reason ) {

	$reasons = affwp_get_preview_payout_request_failed_reasons();
	$label   = array_key_exists( $reason, $reasons ) ? $reasons[ $reason ] : sanitize_text_field( $reason );

	return $label;
}
