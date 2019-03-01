<?php
/**
 * @package WPENON
 * @version 1.0.0
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

$prefix = '';
if ( function_exists( 'edd_get_download_price' ) ) {
  $price = edd_get_download_price( get_the_ID() );
  if ( $price ) {
    $prefix = edd_currency_filter( edd_format_amount( $price ) ) . '&nbsp;&ndash;&nbsp;';
  }
}

?>

<a class="back-to-overview-button btn btn-default" href="<?php echo $data['access_link']; ?>">
  <?php _e( 'Zurück zur Übersicht', 'wpenon' ); ?>
</a>

<?php if ( $data['finalized'] ) : ?>
  <a class="back-to-overview-button btn btn-default" href="<?php echo add_query_arg( 'action', 'pdf-view', $data['access_link'] ); ?>" target="_blank">
    <?php
    if ( $data['paid'] ) {
      _e( 'Energieausweis ansehen', 'wpenon' );
    } else {
      _e( 'Vorschau ansehen', 'wpenon' );
    }
    ?>
  </a>
<?php endif; ?>

<?php if ( $data['finalized'] && ! $data['ordered'] ) : ?>
  <a class="back-to-overview-button btn btn-primary" href="<?php echo add_query_arg( 'action', 'editoverview', $data['access_link'] ); ?>">
    <?php echo $prefix; ?>
    <?php _e( 'Jetzt bestellen', 'wpenon' ); ?>
  </a>
<?php endif; ?>

<p class="lead">
  <strong><?php _e( 'Status:', 'wpenon' ); ?></strong>
  <?php
  if ( $data['finalized'] ) :
    echo '<span class="wpenon-finalized-message">';
    _e( 'Angaben vollständig', 'wpenon' );
    echo '</span>';
  else :
    echo '<span class="wpenon-not-finalized-message">';
    _e( 'Angaben noch unvollständig', 'wpenon' );
    echo '</span>';
  endif;
  ?>
</p>
<p>
  <small>
    <?php
    if ( $data['finalized'] ) :
      _e( 'Ihre Angaben für diesen Energieausweis sind vollständig, sodass dieser nun berechnet und bestellt werden kann.', 'wpenon' );
      _e( 'Beachten Sie, dass Sie die Angaben im Nachhinein nicht mehr ändern können. Achten Sie also auf die Korrektheit der Angaben.', 'wpenon' );
    else :
      _e( 'Ihre Angaben für diesen Energieausweis sind noch unvollständig. Erst bei vollständigen Daten kann der Energieausweis berechnet und bestellt werden.', 'wpenon' );
    endif;
    ?>
  </small>
</p>

<?php if ( $data['allow_changes_after_order'] && $data['ordered'] && ! $data['paid'] ) : ?>
  <p>
    <?php _e( 'Sie haben für diesen Energieausweis eine Bestellung aufgegeben, welche noch nicht bezahlt ist.', 'wpenon' ); ?>
    <?php _e( 'Aus Sicherheitsgründen können Sie die Angaben im Energieausweis erst dann wieder bearbeiten, wenn die Bezahlung erfolgt ist.', 'wpenon' ); ?>
  </p>
<?php elseif ( ! $data['allow_changes_after_order'] && $data['ordered'] ) : ?>
  <p>
    <?php _e( 'Sie haben für diesen Energieausweis bereits eine Bestellung aufgegeben. Ihre Eingaben sind daher zum jetzigen Zeitpunkt nicht mehr änderbar. Vor dem Kauf haben Sie die Korrektheit der Daten bestätigt.', 'wpenon' ); ?>
  </p>
<?php else : ?>
  <?php wpenon_get_view()->displaySubTemplate( 'message-error', '', $data['errors'] ); ?>
  <?php wpenon_get_view()->displaySubTemplate( 'message-warning', '', $data['warnings'] ); ?>

  <form id="wpenon-generate-form" class="form-horizontal" role="form" action="<?php echo $data['action_url']; ?>" method="post" enctype="multipart/form-data" onsubmit="setFormSubmitting()" novalidate>
	<?php do_action( 'wpenon_form_start', $data ); ?>

    <p><?php printf( __( 'Erforderliche Felder für die Dateneingabe sind durch %s markiert.', 'wpenon' ), '<span class="required">*</span>' ); ?></p>

    <?php wpenon_get_view()->displaySubTemplate( 'schematabs', '', $data['schema'] ); ?>

    <?php wpenon_get_view()->displaySubTemplate( 'schemafields', '', $data['additional'] ); ?>

    <p class="text-right">
      <button type="submit" id="wpenon-edit-submit" class="btn btn-primary"><?php _e( 'Änderungen speichern', 'wpenon' ); ?></button>
    </p>
	<?php do_action( 'wpenon_form_end', $data ); ?>
  </form>
<?php endif; ?>

