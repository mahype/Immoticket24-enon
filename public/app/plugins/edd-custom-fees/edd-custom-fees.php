<?php
/**
 * Plugin Name: EDD Custom Fees
 * Plugin URI: https://wordpress.org/plugins/edd-custom-fees
 * Description: Add custom fees to all products on Easy Digital Downloads.
 * Author: Felix Arntz
 * Author URI: http://leaves-and-love.net
 * Version: 1.0.0
 * Text Domain: edd-custom-fees
 * Domain Path: /languages/
 */

function eddcf_init() {
	if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		return;
	}

	load_plugin_textdomain( 'edd-custom-fees', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	add_action( 'edd_checkout_form_top', 'eddcf_show_fees_selection', -2 );
	add_action( 'wp_footer', 'eddcf_render_fees_modals' );
	add_action( 'edd_add_email_tags', 'eddcf_add_email_tag' );
	add_action( 'wp_enqueue_scripts', 'eddcf_enqueue_scripts' );
}
add_action( 'plugins_loaded', 'eddcf_init' );

function eddcf_get_custom_fees() {
	/**
	 * Fee example:
	 * array(
	 * 		'id'			=> 'some_identifier',
	 * 		'label'			=> 'Some fee',
	 * 		'amount'		=> 4.99,
	 * 		'description'	=> 'This is a fee.',
	 * 		'description_cb'=> 'some_callback_function',
	 * 		'email_note'	=> 'You will receive this thing soon.',
	 * )
	 */
	$_fees = apply_filters( 'eddcf_custom_fees', array() );

	$fees = array();
	foreach ( $_fees as $_fee ) {
		$_fee['type'] = 'item';
		$fees[ $_fee['id'] ] = $_fee;
	}

	return $fees;
}

function eddcf_filter_custom_fees( $fees, $cart ) {
	return apply_filters( 'eddcf_filter_custom_fees', $fees, $cart );
}

function eddcf_show_fees_selection() {
	// this extension requires AJAX
	if ( edd_is_ajax_disabled() ) {
		return;
	}

	if ( ! edd_is_checkout() ) {
		return;
	}

	$fees = eddcf_get_custom_fees();
	$fees = eddcf_filter_custom_fees( $fees, EDD()->cart );
	if ( 0 === count( $fees ) ) {
		return;
	}

	$cart_fees = EDD()->fees->get_fees( 'item' );

	$headline = apply_filters( 'eddcf_custom_fees_headline', __( 'Additional Services', 'edd-custom-fees' ) );

	$description = apply_filters( 'eddcf_custom_fees_description', __( 'Get even more from your order by purchasing additional services.', 'edd-custom-fees' ) );

	//TODO: Falls description_cb gegeben, Fragezeichen einblenden
	//TODO: und die Description als Bootstrap Modal im wp_footer rendern

	?>
	<fieldset id="edd_custom_fees_selection">
		<span><legend><?php echo $headline ?></legend></span>
		<p><?php echo $description; ?></p>
		<?php foreach ( $fees as $fee ) : ?>
			<p>
				<?php if ( isset( $fee['description_cb'] ) && is_callable( $fee['description_cb'] ) ) : ?>
					<span class="label label-info" style="float:right;margin-left:8px;cursor:pointer;" data-toggle="modal" data-target="#modal_<?php echo $fee['id']; ?>"> </span>
				<?php endif; ?>
				<input type="checkbox" id="edd_custom_fee_<?php echo $fee['id']; ?>" name="edd_custom_fees[]" class="eddcf-custom-fee" value="<?php echo $fee['id']; ?>"<?php echo ( isset( $cart_fees[ $fee['id'] ] ) ? ' checked' : '' ); ?>>
				<label style="display:inline;" for="edd_custom_fee_<?php echo $fee['id']; ?>" class="edd-label">
					<span><?php echo $fee['label']; ?></span>
					<span>&nbsp;&ndash;&nbsp;</span>
					<span><?php echo edd_currency_filter( edd_format_amount( $fee['amount'] ) ); ?></span>
					<?php if ( ! empty( $fee['label_note'] ) ) : ?>
						<br>
						<span style="display:inline-block;margin-left:24px;font-size:14px;font-weight:300;"><?php echo $fee['label_note']; ?></span>
					<?php endif; ?>
				</label>
				<?php if ( isset( $fee['description'] ) && ! empty( $fee['description'] ) ) : ?>
					<span class="edd-description"><?php echo $fee['description']; ?></span>
				<?php endif; ?>
			</p>
		<?php endforeach; ?>
	</fieldset>
	<?php
}

function eddcf_render_fees_modals() {
	if ( edd_is_ajax_disabled() ) {
		return;
	}

	if ( ! edd_is_checkout() ) {
		return;
	}

	$fees = eddcf_get_custom_fees();
	$fees = eddcf_filter_custom_fees( $fees, EDD()->cart );
	if ( 0 === count( $fees ) ) {
		return;
	}

	foreach ( $fees as $fee ) {
		if ( ! isset( $fee['description_cb'] ) || ! is_callable( $fee['description_cb'] ) ) {
			continue;
		}
		?>
		<div class="modal fade" id="modal_<?php echo $fee['id']; ?>" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title"><?php echo $fee['label']; ?></h4>
					</div>
					<div class="modal-body">
						<?php call_user_func( $fee['description_cb'] ); ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

function eddcf_add_email_tag() {
	edd_add_email_tag( 'custom_fees_notes', __( 'Notes for the customer about the custom fees associated with the payment.', 'edd-custom-fees' ), 'text/html' == EDD()->emails->get_content_type() ? 'eddcf_parse_custom_fees_notes_email_tag' : 'eddcf_parse_custom_fees_notes_email_tag_plain' );
	edd_add_email_tag( 'custom_fees_information', __( 'A list of custom fees associated with the payment.', 'edd-custom-fees' ), 'text/html' == EDD()->emails->get_content_type() ? 'eddcf_parse_custom_fees_information_email_tag' : 'eddcf_parse_custom_fees_information_email_tag_plain' );
}

function eddcf_parse_custom_fees_notes_email_tag( $payment_id ) {
	return wpautop( eddcf_parse_custom_fees_notes_email_tag_plain( $payment_id ) );
}

function eddcf_parse_custom_fees_notes_email_tag_plain( $payment_id ) {
	$payment_fees = edd_get_payment_fees( $payment_id, 'item' );
	if ( 0 === count( $payment_fees ) ) {
		return '';
	}

	$fees = eddcf_get_custom_fees();

	$output = '';

	foreach ( $payment_fees as $payment_fee ) {
		if ( isset( $fees[ $payment_fee['id'] ] ) && isset( $fees[ $payment_fee['id'] ]['email_note'] ) && ! empty( $fees[ $payment_fee['id'] ]['email_note'] ) ) {
			$output .= $fees[ $payment_fee['id'] ]['email_note'] . "\n\n";
		}
	}

	return $output;
}

function eddcf_parse_custom_fees_information_email_tag( $payment_id ) {
	$payment_fees = edd_get_payment_fees( $payment_id, 'item' );
	if ( 0 === count( $payment_fees ) ) {
		return '';
	}

	$fees = eddcf_get_custom_fees();

	foreach ( $payment_fees as $key => $payment_fee ) {
		if ( ! isset( $fees[ $payment_fee['id'] ] ) ) {
			unset( $payment_fees[ $key ] );
		}
	}

	if ( 0 === count( $payment_fees ) ) {
		return '';
	}

	$payment_fees = array_values( $payment_fees );

	$output = '<p>' . __( 'The customer also purchased the following additional services:', 'edd-custom-fees' ) . '</p>';
	$output .= '<ul>';
	foreach ( $payment_fees as $payment_fee ) {
		$output .= '<li>' . $payment_fee['label'] . '</li>';
	}
	$output .= '</ul>';

	return $output;
}

function eddcf_parse_custom_fees_information_email_tag_plain( $payment_id ) {
	$payment_fees = edd_get_payment_fees( $payment_id, 'item' );
	if ( 0 === count( $payment_fees ) ) {
		return '';
	}

	$fees = eddcf_get_custom_fees();

	foreach ( $payment_fees as $key => $payment_fee ) {
		if ( ! isset( $fees[ $payment_fee['id'] ] ) ) {
			unset( $payment_fees[ $key ] );
		}
	}

	if ( 0 === count( $payment_fees ) ) {
		return '';
	}

	$payment_fees = array_values( $payment_fees );

	$output = __( 'The customer also purchased the following additional services:', 'edd-custom-fees' ) . "\n\n";

	foreach ( $payment_fees as $payment_fee ) {
		$output .= "\t" . $payment_fee['label'] . "\n";
	}

	$output .= "\n";

	return $output;
}

function eddcf_enqueue_scripts() {
	if ( edd_is_ajax_disabled() ) {
		return;
	}

	if ( ! edd_is_checkout() ) {
		return;
	}

	wp_enqueue_script( 'edd-custom-fees', plugin_dir_url( __FILE__ ) . 'edd-custom-fees.js', array( 'jquery', 'wp-util', 'edd-checkout-global' ), '1.0.0', true );
}

function eddcf_ajax_add_fee() {
	if ( ! isset( $_POST['fee_id'] ) ) {
		wp_send_json_error( array( 'message' => __( 'Missing fee ID.', 'edd-custom-fees' ) ) );
	}

	$fee_id = sanitize_text_field( $_POST['fee_id'] );

	$fees = eddcf_get_custom_fees();
	if ( ! isset( $fees[ $fee_id ] ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid fee ID.', 'edd-custom-fees' ) ) );
	}

	$fee = array_intersect_key( $fees[ $fee_id ], array_flip( array( 'id', 'amount', 'label', 'type' ) ) );

	$response = array(
		'fees'		=> EDD()->fees->add_fee( $fee ),
	);

	wp_send_json_success( $response );
}
add_action( 'wp_ajax_eddcf_add_fee', 'eddcf_ajax_add_fee' );
add_action( 'wp_ajax_nopriv_eddcf_add_fee', 'eddcf_ajax_add_fee' );

function eddcf_ajax_remove_fee() {
	if ( ! isset( $_POST['fee_id'] ) ) {
		wp_send_json_error( array( 'message' => __( 'Missing fee ID.', 'edd-custom-fees' ) ) );
	}

	$fee_id = sanitize_text_field( $_POST['fee_id'] );

	$response = array(
		'fees'		=> EDD()->fees->remove_fee( $fee_id ),
	);

	wp_send_json_success( $response );
}
add_action( 'wp_ajax_eddcf_remove_fee', 'eddcf_ajax_remove_fee' );
add_action( 'wp_ajax_nopriv_eddcf_remove_fee', 'eddcf_ajax_remove_fee' );
