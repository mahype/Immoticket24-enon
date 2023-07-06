<?php
/**
 * Admin: Creatives Action Callbacks
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Creatives
 * @copyright   Copyright (c) 2021, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2
 */

/**
 * Process the add creative request
 *
 * @since 1.2
 * @return void
 */
function affwp_process_add_creative( $data ) {

	if ( ! is_admin() ) {
		return false;
	}

	if ( ! current_user_can( 'manage_creatives' ) ) {
		wp_die( __( 'You do not have permission to manage creatives', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( affwp_add_creative( $data ) ) {
		wp_safe_redirect( affwp_admin_url( 'creatives', array( 'affwp_notice' => 'creative_added' ) ) );
		exit;
	} else {
		wp_safe_redirect( affwp_admin_url( 'creatives', array( 'affwp_notice' => 'creative_added_failed' ) ) );
		exit;
	}

}
add_action( 'affwp_add_creative', 'affwp_process_add_creative' );

/**
 * Process creative deletion requests
 *
 * @since 1.2
 * @param $data array
 * @return void
 */
function affwp_process_creative_deletion( $data ) {

	if ( ! is_admin() ) {
		return;
	}

	if ( ! current_user_can( 'manage_creatives' ) ) {
		wp_die( __( 'You do not have permission to delete a creative', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( ! wp_verify_nonce( $data['affwp_delete_creatives_nonce'], 'affwp_delete_creatives_nonce' ) ) {
		wp_die( __( 'Security check failed', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( empty( $data['affwp_creative_ids'] ) || ! is_array( $data['affwp_creative_ids'] ) ) {
		wp_die( __( 'No creative IDs specified for deletion', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 400 ) );
	}

	$to_delete = array_map( 'absint', $data['affwp_creative_ids'] );

	foreach ( $to_delete as $creative_id ) {
		affwp_delete_creative( $creative_id );
	}

	wp_safe_redirect( affwp_admin_url( 'creatives', array( 'affwp_notice' => 'creative_deleted' ) ) );
	exit;

}
add_action( 'affwp_delete_creatives', 'affwp_process_creative_deletion' );

/**
 * Process the add affiliate request
 *
 * @since 1.2
 * @return void
 */
function affwp_process_update_creative( $data ) {

	if ( ! is_admin() ) {
		return false;
	}

	if ( ! current_user_can( 'manage_creatives' ) ) {
		wp_die( __( 'You do not have permission to manage creatives', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( affwp_update_creative( $data ) ) {
		wp_safe_redirect( affwp_admin_url( 'creatives', array( 'action' => 'edit_creative', 'affwp_notice' => 'creative_updated', 'creative_id' => $data['creative_id'] ) ) );
		exit;
	} else {
		wp_safe_redirect( affwp_admin_url( 'creatives', array( 'action' => 'edit_creative', 'affwp_notice' => 'creative_update_failed' ) ) );
		exit;
	}

}
add_action( 'affwp_update_creative', 'affwp_process_update_creative' );

/**
 * Update the creative status label to include a clock icon if the creative is scheduled.
 *
 * @since 2.15.0
 *
 * @param string $label    The creative status label.
 * @param object $creative The creative object.
 * @param string $status   The creative status.
 * @return string The creative status label.
 */
function affwp_get_scheduled_status_label( $label, $creative, $status ) {
	// If the creative is not scheduled, return the default label.
	if ( false === affwp_has_scheduling_feature( $creative ) ) {
		switch ( $status ) {
			case 'active':
				return sprintf( '<span class="badge badge-active">%s</span>', $label );
			case 'inactive':
				return sprintf( '<span class="badge badge-inactive">%s</span>', $label );
			case 'scheduled':
				return sprintf( '<span class="badge badge-scheduled">%s</span>', $label );
		}
	}

	// If the creative is scheduled, return the scheduled label.
	switch ( $status ) {
		case 'active':
			return sprintf( '<span class="badge badge-active">%s<span class="dashicons dashicons-clock"></span></span>', $label );
		case 'inactive':
			return sprintf( '<span class="badge badge-inactive">%s<span class="dashicons dashicons-clock"></span></span>', $label );
		case 'scheduled':
			return sprintf( '<span class="badge badge-scheduled">%s<span class="dashicons dashicons-clock"></span></span>', $label );
	}

	// If the creative status is not recognized, return the default label.
	return $label;
}
add_filter( 'affwp_get_creative_status_label', 'affwp_get_scheduled_status_label', 10, 3 );

function affwp_add_scheduled_status_tooltips( $label, $creative, $status ) {
	// If the creative is not scheduled, return the default label.
	if ( false === affwp_has_scheduling_feature( $creative ) ) {
		return $label;
	}

	$start_date = '0000-00-00 00:00:00' === $creative->start_date ? false : $creative->start_date;
	$end_date   = '0000-00-00 00:00:00' === $creative->end_date ? false : $creative->end_date;

	// If creative is active and has both a start AND end date.
	if ( 'active' === $status && false !== $start_date && false !== $end_date ) {
		return affwp_text_tooltip(
			$label,
			sprintf( '<div>%1$s %2$s</div><div>%3$s %4$s</div>',
				__( 'Started:', 'affiliate-wp' ),
				esc_html( affwp_date_i18n( strtotime( $start_date ), 'Y-m-d' ) ),
				__( 'Ends:', 'affiliate-wp' ),
				esc_html( affwp_date_i18n( strtotime( $end_date ), 'Y-m-d' ) ),
			),
			false
		);
	}

	// If a creative is active and only has a start date.
	if ( 'active' === $status && false !== $start_date && false === $end_date ) {
		return affwp_text_tooltip(
			$label,
			sprintf( '<div>%1$s %2$s</div>',
				__( 'Started:', 'affiliate-wp' ),
				esc_html( affwp_date_i18n( strtotime( $start_date ), 'Y-m-d' ) ),
			),
			false
		);
	}

	// If a creative is active and only has an end date.
	if ( 'active' === $status && false === $start_date && false !== $end_date ) {
		return affwp_text_tooltip(
			$label,
			sprintf( '<div>%1$s %2$s</div>',
				__( 'Ends:', 'affiliate-wp' ),
				esc_html( affwp_date_i18n( strtotime( $end_date ), 'Y-m-d' ) ),
			),
			false
		);
	}

	// If a creative is inactive and has both a start AND end date.
	if ( 'inactive' === $status && false !== $start_date && false !== $end_date ) {
		return affwp_text_tooltip(
			$label,
			sprintf( '<div>%1$s %2$s</div><div>%3$s %4$s</div>',
				__( 'Started:', 'affiliate-wp' ),
				esc_html( affwp_date_i18n( strtotime( $start_date ), 'Y-m-d' ) ),
				__( 'Ended:', 'affiliate-wp' ),
				esc_html( affwp_date_i18n( strtotime( $end_date ), 'Y-m-d' ) ),
			),
			false
		);
	}

	// If a creative is inactive and only has an end date.
	if ( 'inactive' === $status && false === $start_date && false !== $end_date ) {
		return affwp_text_tooltip(
			$label,
			sprintf( '<div>%1$s %2$s</div>',
				__( 'Ended:', 'affiliate-wp' ),
				esc_html( affwp_date_i18n( strtotime( $end_date ), 'Y-m-d' ) ),
			),
			false
		);
	}

	// If a creative is scheduled and has both a start AND end date.
	if ( 'scheduled' === $status && false !== $start_date && false !== $end_date ) {
		return affwp_text_tooltip(
			$label,
			sprintf( '<div>%1$s %2$s</div><div>%3$s %4$s</div>',
				__( 'Starts:', 'affiliate-wp' ),
				esc_html( affwp_date_i18n( strtotime( $start_date ), 'Y-m-d' ) ),
				__( 'Ends:', 'affiliate-wp' ),
				esc_html( affwp_date_i18n( strtotime( $end_date ), 'Y-m-d' ) ),
			),
			false
		);
	}

	// If a creative is scheduled and only has a start date.
	if ( 'scheduled' === $status && false !== $start_date && false === $end_date ) {
		return affwp_text_tooltip(
			$label,
			sprintf( '<div>%1$s %2$s</div>',
				__( 'Starts:', 'affiliate-wp' ),
				esc_html( affwp_date_i18n( strtotime( $start_date ), 'Y-m-d' ) ),
			),
			false
		);
	}

	return $label;
}
add_filter( 'affwp_get_creative_status_label', 'affwp_add_scheduled_status_tooltips', 10, 3 );
