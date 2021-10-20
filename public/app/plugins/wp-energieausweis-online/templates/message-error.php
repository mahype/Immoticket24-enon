<?php
/**
 * @package WPENON
 * @version 1.0.0
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

if ( count( $data ) > 0 ) : ?>
  <div class="alert alert-danger">
    <p class="lead">
      <span class="glyphicon glyphicon-exclamation-sign"></span>
      <strong><?php _e( 'Fehlerhafte Eingaben!', 'wpenon' ); ?></strong>
    </p>
    <p>
      <?php _e( 'In den folgenden Feldern wurden ungültige Eingaben festgestellt:', 'wpenon' ); ?>
    </p>
    <ul>
      <?php foreach ( $data as $field_slug => $error ) : ?>
        <li>
          <a href="#<?php echo $field_slug; ?>" class="wpenon-form-link alert-link"><?php echo $error['label']; ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
    <p>
      <?php _e( 'Klicken Sie auf den jeweiligen Punkt um den Fehler zu korrigieren.', 'wpenon' ); ?>
    </p>
  </div>
<?php endif;
