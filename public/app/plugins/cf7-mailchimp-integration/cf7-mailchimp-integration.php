<?php
/**
 * Plugin Name: CF7 MailChimp Integration
 * Plugin URI: https://wordpress.org/plugins/cf7-mailchimp-integration
 * Description: Add Contact Form 7 submission creators to your MailChimp list automatically.
 * Author: Felix Arntz
 * Author URI: https://leaves-and-love.net
 * Version: 1.0.0
 * Text Domain: cf7-mailchimp-integration
 */

function cf7mci_init() {
	if ( ! class_exists( 'WPCF7' ) ) {
		return;
	}

	add_action( 'wpcf7_before_send_mail', 'cf7mci_process_form_submission', 1000, 3 );
}
add_action( 'plugins_loaded', 'cf7mci_init' );

function cf7mci_get_api_key() {
	$api_key = defined( 'CF7_MAILCHIMP_API_KEY' ) ? CF7_MAILCHIMP_API_KEY : '';

	return apply_filters( 'cf7_mailchimp_api_key', $api_key );
}

function cf7mci_process_form_submission( $contact_form, $abort, $submission ) {
	if ( $abort ) {
		return;
	}

	$list_id = $contact_form->additional_setting( 'mailchimp_list_id', 1 );
	if ( empty( $list_id ) ) {
		return;
	}

	$list_id = array_pop( $list_id );

	$email_field = $contact_form->additional_setting( 'mailchimp_email_field', 1 );
	if ( empty( $email_field ) ) {
		return;
	}

	$email_field = array_pop( $email_field );

	$email = $submission->get_posted_data( $email_field );
	if ( empty( $email ) ) {
		return;
	}

	$name_field = $contact_form->additional_setting( 'mailchimp_name_field', 1 );
	if ( ! empty( $name_field ) ) {
		$name_field = array_pop( $name_field );
		$name       = $submission->get_posted_data( $name_field );
	} else {
		$name = '';
	}

	cf7mci_subscribe_to_list( $list_id, $email, $name );
}

function cf7mci_subscribe_to_list( $list_id, $email, $name = '' ) {
	return cf7mci_set_member_list_status( $list_id, 'subscribed', $email, $name );
}

function cf7mci_unsubscribe_from_list( $list_id, $email, $name = '' ) {
	return cf7mci_set_member_list_status( $list_id, 'unsubscribed', $email, $name );
}

function cf7mci_set_member_list_status( $list_id, $status, $email, $name = '' ) {
	$api_key = cf7mci_get_api_key();
	if ( empty( $api_key ) ) {
		return false;
	}

	$user_data = array(
		'email_address' => $email,
		'status'        => $status,
	);

	if ( ! empty( $name ) ) {
		$merge_fields = array();

		if ( strpos( $name, ' ' ) ) {
			list( $merge_fields['FNAME'], $merge_fields['LNAME'] ) = explode( ' ', $name, 2 );
		} else {
			$merge_fields['FNAME'] = $name;
			$merge_fields['LNAME'] = '';
		}

		$user_data['merge_fields'] = $merge_fields;
	}

	$data_center = explode( '-', $api_key );
	$data_center = array_pop( $data_center );

	$request_uri = 'https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/';
	$args        = array(
		'headers' => array( 'Authorization' => 'apikey ' . $api_key ),
		'body'    => json_encode( $user_data ),
	);

	$response = wp_remote_post( $request_uri, $args );
	if ( is_wp_error( $response ) ) {
		return false;
	}

	$response = json_decode( wp_remote_retrieve_body( $response ) );

	return true;
}
