<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */
?>

<?php wpenon_get_view()->displaySubTemplate( 'back-button', '', $data['access_link'] ); ?>

<p class="lead">
  <strong><?php _e( 'Status:', 'wpenon' ); ?></strong>
  <?php
  if ( $data['finalized'] ) :
    _e( 'Angaben vollständig', 'wpenon' );
  else :
    _e( 'Angaben noch unvollständig', 'wpenon' );
  endif;
  ?>
</p>
<p>
  <small>
    <?php
    if ( $data['finalized'] ) :
      _e( 'Ihre Angaben für diesen Energieausweis sind vollständig, sodass dieser nun berechnet und bestellt werden kann.', 'wpenon' );
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
  <?php wpenon_get_view()->displaySubTemplate( 'access-box', '', $data['access_link'] ); ?>
  <?php wpenon_get_view()->displaySubTemplate( 'message-error', '', $data['errors'] ); ?>
  <?php wpenon_get_view()->displaySubTemplate( 'message-warning', '', $data['warnings'] ); ?>

  <form id="wpenon-generate-form" class="form-horizontal" role="form" action="<?php echo $data['action_url']; ?>" method="post" enctype="multipart/form-data" novalidate>
    
    <p><?php printf( __( 'Erforderliche Felder für die Dateneingabe sind durch %s markiert.', 'wpenon' ), '<span class="required">*</span>' ); ?></p>
    
    <?php wpenon_get_view()->displaySubTemplate( 'schematabs', '', $data['schema'] ); ?>

    <?php wpenon_get_view()->displaySubTemplate( 'schemafields', '', $data['additional'] ); ?>

    <p class="text-right">
      <button type="submit" id="wpenon-edit-submit" class="btn btn-primary"><?php _e( 'Änderungen speichern', 'wpenon' ); ?></button>
    </p>

  </form>
<?php endif; ?>
