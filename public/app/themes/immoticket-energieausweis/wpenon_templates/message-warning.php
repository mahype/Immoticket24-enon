<?php
/**
 * @package WPENON
 * @version 1.0.0
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

if ( count( $data ) > 0 ) : ?>
  <div class="alert alert-warning">
    <p class="lead">
      <span class="glyphicon glyphicon-warning-sign"></span>
      <strong><?php _e( 'Überprüfung empfohlen!', 'wpenon' ); ?></strong>
    </p>
    <p>
      <?php _e( 'Bitte überprüfen Sie Ihre Eingaben in den folgenden Feldern auf Korrektheit:', 'wpenon' ); ?>
    </p>
    <ul>
      <?php foreach ( $data as $field_slug => $warning ) : ?>
        <li>
          <a href="#<?php echo $field_slug; ?>" class="wpenon-form-link alert-link"><?php echo $warning['label']; ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
    <p>
      <?php _e( 'Klicken Sie auf den jeweiligen Punkt um die Warnung zu überprüfen.', 'wpenon' ); ?>
    </p>
  </div>
<?php endif;
