<?php
/**
 * Plugin Name: EDD Klick-Tipp Integration
 * Plugin URI: https://wordpress.org/plugins/edd-klicktipp-integration
 * Description: Add new Easy Digital Downloads customers to your Klick-Tipp user list automatically.
 * Author: Felix Arntz
 * Author URI: https://leaves-and-love.net
 * Version: 1.0.0
 * Text Domain: edd-klicktipp-integration
 */

function eddkti_init() {
	if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		return;
	}

	require_once plugin_dir_path( __FILE__ ) . 'klicktipp.api.inc';

	add_action( 'edd_post_insert_customer', 'eddkti_add_customer', 100, 1 );
	add_action( 'edd_insert_payment', 'eddkti_add_remove_payment_customer', 100, 1 );
	add_filter( 'edd_log_types', 'eddkti_add_log_type', 10, 1 );
}
add_action( 'plugins_loaded', 'eddkti_init' );

function eddkti_get_username() {
	$username = defined( 'EDD_KLICKTIPP_USERNAME' ) ? EDD_KLICKTIPP_USERNAME : '';

	return apply_filters( 'edd_klicktipp_username', $username );
}

function eddkti_get_password() {
	$password = defined( 'EDD_KLICKTIPP_PASSWORD' ) ? EDD_KLICKTIPP_PASSWORD : '';

	return apply_filters( 'edd_klicktipp_password', $password );
}

function eddkti_get_list_id() {
	$list_id = defined( 'EDD_KLICKTIPP_LIST_ID' ) ? EDD_KLICKTIPP_LIST_ID : 0;

	return apply_filters( 'edd_klicktipp_list_id', $list_id );
}

function eddkti_get_tag_id() {
	$tag_id = defined( 'EDD_KLICKTIPP_TAG_ID' ) ? EDD_KLICKTIPP_TAG_ID : 0;

	return apply_filters( 'edd_klicktipp_tag_id', $tag_id );
}

function eddkti_add_remove_payment_customer( $payment_id ) {
	$payment = edd_get_payment( $payment_id );
	if ( ! $payment || ! $payment->ID || ! $payment->customer_id ) {
		return;
	}

	eddkti_add_customer( $payment->customer_id );
}

function eddkti_add_customer( $customer_id ) {
	global $edd_logs;

	$username = eddkti_get_username();
	$password = eddkti_get_password();

	if ( empty( $username ) || empty( $password ) ) {
		return;
	}

	$customer = new EDD_Customer( $customer_id );
	if ( ! $customer->id ) {
		return;
	}

	$agreed_to_subscribe = apply_filters( 'eddkti_agreed_to_subscribe', true, $customer );

	$customer_data = get_object_vars( $customer );

	$connector = new KlicktippConnector();
	$connector->login( $username, $password );

	if ( ! $connector->subscriber_search( $customer_data['email'] ) ) {
		if ( ! $agreed_to_subscribe ) {
			$connector->logout();
			return;
		}

		if ( strpos( $customer_data['name'], ' ' ) ) {
			list( $customer_data['first_name'], $customer_data['last_name'] ) = explode( ' ', $customer_data['name'], 2 );
		} else {
			$customer_data['first_name'] = $customer_data['name'];
			$customer_data['last_name']  = '';
		}

		$field_mappings = array(
			'fieldFirstName' => 'first_name',
			'fieldLastName'  => 'last_name',
		);

		$fields = $connector->field_index();

		$field_values = array();
		foreach ( $field_mappings as $klicktipp_field => $data_field ) {
			if ( isset( $fields[ $klicktipp_field ] ) && isset( $customer_data[ $data_field ] ) ) {
				$field_values[ $klicktipp_field ] = $customer_data[ $data_field ];
			}
		}

		$subscriber = $connector->subscribe( $customer_data['email'], eddkti_get_list_id(), eddkti_get_tag_id(), $field_values );

		$edd_logs->insert_log( array(
			'log_type'     => 'klicktipp',
			'post_title'   => $customer_data['email'],
			'post_content' => $subscriber ? json_encode( $subscriber ) : $connector->get_last_error(),
		) );
	} else {
		if ( ! $agreed_to_subscribe ) {
			error_log( sprintf( 'Customer %s will be unsubscribed because they did not agree to subscribe.', $customer_data['email'] ) );
			$result = $connector->unsubscribe( $customer_data['email'] );

			$edd_logs->insert_log( array(
				'log_type'     => 'klicktipp',
				'post_title'   => $customer_data['email'],
				'post_content' => $result ? 'Unsubscribed.' : 'Unsubscribe error.',
			) );
		}
	}

	$connector->logout();
}

function eddkti_add_customers_bulk( $customer_ids ) {
	global $edd_logs;

	$username = eddkti_get_username();
	$password = eddkti_get_password();

	if ( empty( $username ) || empty( $password ) ) {
		return;
	}

	$connector = new KlicktippConnector();
	$connector->login( $username, $password );

	foreach ( $customer_ids as $customer_id ) {
		$customer = new EDD_Customer( $customer_id );
		if ( ! $customer->id ) {
			continue;
		}

		$agreed_to_subscribe = apply_filters( 'eddkti_agreed_to_subscribe', true, $customer );

		$customer_data = get_object_vars( $customer );

		if ( ! $connector->subscriber_search( $customer_data['email'] ) ) {
			if ( ! $agreed_to_subscribe ) {
				continue;
			}

			if ( strpos( $customer_data['name'], ' ' ) ) {
				list( $customer_data['first_name'], $customer_data['last_name'] ) = explode( ' ', $customer_data['name'], 2 );
			} else {
				$customer_data['first_name'] = $customer_data['name'];
				$customer_data['last_name']  = '';
			}

			$field_mappings = array(
				'fieldFirstName' => 'first_name',
				'fieldLastName'  => 'last_name',
			);

			$fields = $connector->field_index();

			$field_values = array();
			foreach ( $field_mappings as $klicktipp_field => $data_field ) {
				if ( isset( $fields[ $klicktipp_field ] ) && isset( $customer_data[ $data_field ] ) ) {
					$field_values[ $klicktipp_field ] = $customer_data[ $data_field ];
				}
			}

			$subscriber = $connector->subscribe( $customer_data['email'], eddkti_get_list_id(), eddkti_get_tag_id(), $field_values );

			$edd_logs->insert_log( array(
				'log_type'     => 'klicktipp',
				'post_title'   => $customer_data['email'],
				'post_content' => $subscriber ? json_encode( $subscriber ) : $connector->get_last_error(),
			) );
		} else {
			if ( ! $agreed_to_subscribe ) {
				$result = $connector->unsubscribe( $customer_data['email'] );

				$edd_logs->insert_log( array(
					'log_type'     => 'klicktipp',
					'post_title'   => $customer_data['email'],
					'post_content' => $result ? 'Unsubscribed.' : 'Unsubscribe error.',
				) );
			}
		}
	}

	$connector->logout();
}

function eddkti_add_log_type( $log_types ) {
	$log_types[] = 'klicktipp';

	return $log_types;
}
