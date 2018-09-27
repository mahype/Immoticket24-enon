<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */
?>

<?php wpenon_get_view()->displaySubTemplate( 'back-button', '', $data['access_link'] ); ?>

<p class="lead">
  <?php _e( 'Hier sehen Sie eine Übersicht zu den von Ihnen getätigten Angaben.', 'wpenon' ); ?>
  <?php if ( $data['purchase_function'] && ! $data['allow_changes_after_order'] ) : ?>
    <?php _e( 'Bitte beachten Sie, dass Sie die Angaben im Nachhinein nicht mehr ändern können. Überprüfen Sie sie deshalb vor dem Bestellvorgang nochmals, um eventuelle Fehler auszuschließen.', 'wpenon' ); ?>
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
