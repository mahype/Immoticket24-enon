<?php
/**
 * @package WPENON
 * @version 1.0.0
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */
?>

<?php wpenon_get_view()->displaySubTemplate( 'back-button', '', $data['access_link'] ); ?>

<p>
  <?php
  if ( $data['finalized'] && ! $data['ordered'] ) :
    echo '<span class="wpenon-finalized-message">';
    _e( 'Die Angaben für Ihren Energieausweis sind nun vollständig, sodass Sie diesen nun auf dieser Seite bestellen können.', 'wpenon' ) . ' ';
    echo '</span>';
  elseif ( $data['ordered'] ) :
    _e( 'Auf dieser Seite sehen Sie die Rechnungsübersicht für diesen Energieausweis.', 'wpenon' );
  else :
    _e( 'Die Angaben für Ihren Energieausweis sind noch nicht vollständig. Sie können den Ausweis erst dann bestellen, wenn Sie alle nötigen Eingaben getätigt haben.', 'wpenon' );
  endif;
  ?>
</p>

<?php wpenon_get_view()->displaySubTemplate( 'access-box', '', $data['access_link'] ); ?>

<?php if ( $data['payment'] !== null ) : ?>

  <h3>
    <?php echo get_the_title( $data['payment']->ID ); ?>
    <?php if ( $data['receipt_url'] ) : ?>
      <small><a href="<?php echo $data['receipt_url']; ?>" target="_blank"><?php _e( '(Rechnung ansehen)', 'wpenon' ); ?></a></small>
    <?php endif; ?>
  </h3>

  <h4>
    <?php echo sprintf( __( 'Zahlungsstatus: %s', 'wpenon' ), edd_get_payment_status( $data['payment'], true ) ); ?> 
  </h4>
  <?php if ( edd_is_payment_complete( $data['payment']->ID ) ) : ?>
    <p><?php printf( __( 'Sie haben die Rechnung am %1$s via %2$s beglichen.', 'wpenon' ), \WPENON\Util\Format::date( edd_get_payment_completed_date( $data['payment']->ID ) ), edd_get_gateway_checkout_label( edd_get_payment_gateway( $data['payment']->ID ) ) ); ?></p>
  <?php elseif ( edd_get_payment_status( $data['payment'] ) == 'pending' ) : ?>
    <p><?php _e( 'Bitte begleichen Sie die Rechnung umgehend, um Ihren rechtsgültigen Energieausweis zu erhalten.', 'wpenon' ); ?></p>
    <?php wpenon_get_view()->displaySubTemplate( 'table-col', '', $data['bank_account_data'] ); ?>
  <?php endif; ?>
  
  <?php if ( $data['receipt_url'] ) : ?>
    <h4>
      <?php _e( 'PDF-Rechnung', 'wpenon' ); ?>
    </h4>
    <div class="embed-responsive" style="margin-bottom:20px;padding-bottom:140.48%;">
      <iframe src="<?php echo $data['receipt_url']; ?>" style="width:100%; height: 800px;"></iframe>
    </div>
  <?php endif; ?>

  <?php if ( /*$data['receipt_function']*/ false ) : ?>
    <h4>
      <?php _e( 'Zahlungsdetails', 'wpenon' ); ?>
    </h4>
    <?php echo call_user_func( $data['receipt_function'], array(
      'payment_key'   => edd_get_payment_key( $data['payment']->ID ),
      'products'      => false,
    ) ); ?>
  <?php endif; ?>

<?php else: ?>

  <?php if ( $data['purchase_function'] ) :
    $ausweistyp = __( 'Verbrauchsausweis', 'immoticketenergieausweis' );
    if ( substr( $data['template_suffix'], 0, 1 ) == 'b' ) {
      $ausweistyp = __( 'Bedarfsausweis', 'immoticketenergieausweis' );
    }
    echo call_user_func( $data['purchase_function'], array(
      'class'         => 'btn btn-lg',
      'price'         => true,
      'direct'        => true,
      'text'          => sprintf( __( '%s in den Warenkorb legen', 'immoticketenergieausweis' ), $ausweistyp ),
    ) );
  endif; ?>

<?php endif; ?>

<?php if ( ! empty( $data['payments'] ) ) : ?>
  <p class="text-right">
    <a class="btn btn-default" data-toggle="collapse" href="#payment-history">
      <?php _e( 'Rechnungs-Archiv anzeigen', 'wpenon' ); ?>
    </a>
  </p>
  <div id="payment-history" class="collapse">
    <h4><?php _e( 'Ihre neuesten Rechnungen', 'wpenon' ); ?></h4>
    <table class="table">
      <thead>
        <tr>
          <th><?php _e( 'Nummer', 'wpenon' ); ?></th>
          <th><?php _e( 'Betrag', 'wpenon' ); ?></th>
          <th><?php _e( 'Datum', 'wpenon' ); ?></th>
          <th><?php _e( 'Status', 'wpenon' ); ?></th>
          <th><?php _e( 'Aktionen', 'wpenon' ); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ( $data['payments'] as $payment ) : ?>
          <?php
          $receipt_url = add_query_arg( array(
            'action' => 'receipt-view',
            'id'     => $payment->ID,
          ), $data['action_url'] );
          ?>
          <tr>
            <td><?php echo get_the_title( $payment->ID ); ?></td>
            <td><?php echo edd_payment_amount( $payment->ID ); ?></td>
            <td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $payment->post_date ) ); ?></td>
            <td><?php echo edd_get_payment_status( $payment, true ); ?></td>
            <td><a href="<?php echo esc_url( $receipt_url ); ?>" target="_blank"><?php _e( 'PDF ansehen', 'wpenon' ); ?></a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
