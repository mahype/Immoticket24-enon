<?php
/**
 * Plugin Name: Edd payment reseller invoice purchase
 * Plugin URI: http://www.awesome.ug
 * Description: Edd payment reseller invoice purchase.
 * Version: 1.0.0
 * Author: Frank Neumann-Staude, Awesome UG
 * Author URI: http://www.awesome.ug
 * Author Email: frank@awesome.ug
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Verhindert den direkten Zugriff auf die Datei
}

function edd_kauf_auf_rechnung_register_gateway( $gateways ) {
	$gateways['kauf_auf_rechnung'] = array(
		'admin_label'    => 'Kauf auf Rechnung',
		'checkout_label' => 'Kauf auf Rechnung',
        'supports'       => array(
            'buy_now'
        )
	);

	return $gateways;
}
add_filter( 'edd_payment_gateways', 'edd_kauf_auf_rechnung_register_gateway' );

function edd_kauf_auf_rechnung_process_payment( $purchase_data ) {
	if ( ! wp_verify_nonce( $purchase_data['gateway_nonce'], 'edd-gateway' ) ) {
		wp_die( __( 'Nonce verification has failed', 'easy-digital-downloads' ), __( 'Error', 'easy-digital-downloads' ), array( 'response' => 403 ) );
	}

	// Get Reseller Data
	$cartid      = $purchase_data['cart_details'][0]['id'];
	$reseller_id = get_post_meta( $cartid, 'reseller_id', true );
	$reseller    = new \Enon_Reseller\Models\Data\Post_Meta_General( $reseller_id );

	foreach ( $purchase_data['cart_details'] as $key => $item ) {
		$cart_details = $item;
		$ausweis_type = get_post_meta( (int) $cart_details['id'], 'wpenon_type', true );
		$new_price    = false;
		if ( $ausweis_type == 'bw' ) {
			$new_price = $reseller->get_price_bw_reseller();
		} elseif ( $ausweis_type == 'vw' ) {
			$new_price = $reseller->get_price_vw_reseller();
		}
		if ( $new_price ) {
			$cart_details['item_price'] = (float) $new_price;
			$cart_details['subtotal']   = (float) $new_price;
			$cart_details['tax']        = (float) $cart_details['subtotal'] / 119 * 19;
			$cart_details['price']      = (float) $new_price;
		}
		$purchase_data['cart_details'][ $key ] = $cart_details;
	}

	$payment_data = array(
		'price'        => $purchase_data['price'],
		'date'         => $purchase_data['date'],
		'user_email'   => $reseller->get_contact_email(),
		'user_info'    => array(
            'business_name'    => $reseller->get_company_name(),
			'first_name' => $reseller->get_contact_firstname(),
			'last_name'  => $reseller->get_contact_lastname(),
			'address'    => array(
				'line1'   => $reseller->get_address_line1(),
				'city'    => $reseller->get_address_city(),
				'zip'     => $reseller->get_address_plz(),
				'country' => 'DE',
			),
			'phone'      => '',
		),
		'purchase_key' => $purchase_data['purchase_key'],
		'currency'     => edd_get_currency(),
		'downloads'    => $purchase_data['downloads'],
		'cart_details' => $purchase_data['cart_details'],
		'status'       => 'pending',
	);

	// Record the pending payment
	$payment_id = edd_insert_payment( $payment_data );

	if ( $payment_id ) {
		edd_update_payment_status( $payment_id, 'publish' );
		// Empty the shopping cart
		edd_empty_cart();
		edd_send_to_success_page();
	} else {
		edd_record_gateway_error( __( 'Payment Error', 'easy-digital-downloads' ), sprintf( __( 'Payment creation failed while processing a manual (free or test) purchase. Payment data: %s', 'easy-digital-downloads' ), json_encode( $payment_data ) ), $payment );
		// If errors are present, send the user back to the purchase page so they can be corrected
		edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] );
	}
}

add_action( 'edd_gateway_kauf_auf_rechnung', 'edd_kauf_auf_rechnung_process_payment' );

add_action( 'edd_kauf_auf_rechnung_cc_form', '__return_false' );
