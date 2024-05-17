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

 
// Fügen Sie diesen Code zu Ihrer functions.php-Datei oder einem benutzerdefinierten Plugin hinzu

add_action( 'edd_pre_process_purchase', 'custom_apply_hidden_discount' );

function custom_apply_hidden_discount() {
    // Definieren Sie den Rabattbetrag
    $discount_amount = (float) 50.00; // Beispiel für einen Rabattbetrag von $5.00

    // Ändern Sie die Preise der einzelnen Artikel im Warenkorb
    $cart = edd_get_cart_contents();
    foreach ( $cart as $key => &$item ) {
        // Beispiel: Rabatt von $5.00 auf jeden Artikel anwenden
        $item['price'] = max( 0, $item['price'] - $discount_amount );
    }

    // Aktualisieren Sie den Warenkorb
    EDD()->session->set( 'edd_cart', $cart );

    // Neuen Gesamtbetrag berechnen
    $new_total = 0;
    foreach ( $cart as $item ) {
        $new_total += $item['price'] * $item['quantity'];
    }

    // Aktualisieren Sie den Gesamtbetrag in der Session
    EDD()->session->set( 'edd_cart_total', $new_total );
}


function edd_kauf_auf_rechnung_register_gateway( $gateways ) {
	$gateways['kauf_auf_rechnung'] = array(
		'admin_label'    => 'Kauf auf Rechnung',
		'checkout_label' => 'Kauf auf Rechnung',
	);

	return $gateways;
}
add_filter( 'edd_payment_gateways', 'edd_kauf_auf_rechnung_register_gateway' );

function edd_kauf_auf_rechnung_process_payment( $purchase_data ) {
	if ( ! wp_verify_nonce( $purchase_data['gateway_nonce'], 'edd-gateway' ) ) {
		wp_die( __( 'Nonce verification has failed', 'easy-digital-downloads' ), __( 'Error', 'easy-digital-downloads' ), array( 'response' => 403 ) );
	}
    //edd_set_cart_discount('a00006be99');

	// Get Reseller Data
	$cartid      = $purchase_data['cart_details'][0]['id'];
	$reseller_id = get_post_meta( $cartid, 'reseller_id', true );
	$reseller    = new \Enon_Reseller\Models\Data\Post_Meta_General( $reseller_id );

	foreach( $purchase_data['cart_details'] as $key => $item ) {
		$cart_details = $item;
		$cart_details['item_price'] = (float) 34.56;
		$cart_details['subtotal'] = (float) 34.56;
		$cart_details['tax'] = (float) $cart_details['subtotal'] / 119 * 19;
		$cart_details['price'] = (float) 34.56;
		$purchase_data['cart_details'][$key] = $cart_details;
	}
	
	$payment_data = array(
		'price'        => $purchase_data['price'],
		'date'         => $purchase_data['date'],
		'user_email'   => $reseller->get_contact_email(),
		'user_info'    => array(
			'first_name' => $reseller->get_contact_name(),
			'last_name'  => '',
			'address'    => array(
				'line1'   => $reseller->get_address_line1(),
				'city'    => $reseller->get_address_city(),
				'zip'     => $reseller->get_address_plz(),
				'country' => 'DE',
			),
			'phone'      => '',
            //'discount'     => c,
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
 