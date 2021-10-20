<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */
?>

<?php if ( $data['ordered'] ) : ?>
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
<?php endif; ?>

<p class="lead">
  <?php _e( 'Hier sehen Sie eine Übersicht zu den von Ihnen getätigten Angaben.', 'wpenon' ); ?>
  <?php if ( $data['purchase_function'] && ! $data['allow_changes_after_order'] ) : ?>
    <?php _e( 'Bitte prüfen Sie diese vor dem Fertigstellen der Bestellung auf Korrektheit!', 'wpenon' ); ?>
  <?php endif; ?>
</p>

<?php wpenon_get_view()->displaySubTemplate( 'schematablist', '', $data['schema'] ); ?>

<?php if ( ! $data['ordered'] ) : ?>
  <a href="<?php echo $data['edit_url']; ?>" class="btn btn-default fix-entries-button"><?php _e( 'Angaben korrigieren', 'wpenon' ); ?></a>
<?php endif; ?>

<?php if ( $data['purchase_function'] ) : ?>
  <p>
    <input type="checkbox" id="wpenon-double-checked-entries" value="1">
    <label for="wpenon-double-checked-entries">
      <?php _e( 'Alle Angaben wurden von mir auf Korrektheit überprüft.', 'wpenon' ); ?>
    </label>
  </p>
  <?php echo call_user_func( $data['purchase_function'], array() ); ?>
<?php endif; ?>
