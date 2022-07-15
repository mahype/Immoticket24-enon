<?php
/**
 * This template is used to display the purchase summary with [edd_receipt]
 */
global $edd_receipt_args;

$payment   = get_post( $edd_receipt_args['id'] );

wp_enon_log( sprintf( 'Showing purchase summary with ID #%s', $edd_receipt_args['id'] ) );
wp_enon_log( sprintf( 'Showing purchase summary with Payment data %s', print_r( $payment, true ) ) );
wp_enon_log( sprintf( 'Showing purchase summary with Args data %s', print_r( $edd_receipt_args, true ) ) );
wp_enon_log( sprintf( 'Showing purchase summary with Session data %s', $_SESSION['edd']['edd_cart']) );

if( empty( $payment ) ) : ?>
	<div class="edd_errors edd-alert edd-alert-error">
		<?php _e( 'The specified receipt ID appears to be invalid', 'easy-digital-downloads' ); ?>
	</div>
	<?php

else:

$meta      = edd_get_payment_meta( $payment->ID );
$cart      = edd_get_payment_meta_cart_details( $payment->ID, true );
$user      = edd_get_payment_meta_user_info( $payment->ID );
$email     = edd_get_payment_user_email( $payment->ID );
$status    = edd_get_payment_status( $payment, true );

?>
<table id="edd_purchase_receipt" class="edd-table">
	<thead>
		<?php do_action( 'edd_payment_receipt_before', $payment, $edd_receipt_args ); ?>
	</thead>
</table>

<?php do_action( 'edd_payment_receipt_after_table', $payment, $edd_receipt_args ); ?>

<?php endif;